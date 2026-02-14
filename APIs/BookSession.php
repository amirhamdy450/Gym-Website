<?php
// APIs/BookSession.php
require_once '../Includes/DB.php';
require_once '../Includes/Auth.php';

header('Content-Type: application/json');

try {
    // 1. Auth Check
    if (!AuthIsLoggedIn()) {
        throw new Exception('Unauthorized');
    }

    $UserId = AuthId();
    $UserRole = $_SESSION['user_role'] ?? '';

    if ($UserRole !== 'Trainee') {
        throw new Exception('Only trainees can book sessions.');
    }

    // 2. Input Validation
    $Input = json_decode(file_get_contents('php://input'), true);

    $InstructorId = $Input['InstructorId'] ?? null;
    $Sessions = $Input['Sessions'] ?? []; // Array of {Date, Time}
    $RepeatWeeks = $Input['RepeatWeeks'] ?? 1; // Default 1 week (no repeat)

    // Legacy support for single Date/Time (if old frontends use it)
    if (empty($Sessions) && isset($Input['Date']) && isset($Input['Time'])) {
        $Sessions[] = ['Date' => $Input['Date'], 'Time' => $Input['Time']];
    }

    if (!$InstructorId || empty($Sessions)) {
        throw new Exception('Missing required fields (Instructor or Sessions).');
    }

    // Validate Repeat
    $RepeatWeeks = (int)$RepeatWeeks;
    if ($RepeatWeeks < 1) $RepeatWeeks = 1;
    if ($RepeatWeeks > 12) throw new Exception("Cannot book more than 12 weeks in advance.");

    // Calculate ALL Session Dates/Times (Total Bookings)
    $AllBookings = []; // Array of DateTime strings
    $UsedDates = [];

    foreach ($Sessions as $Slot) {
        $Date = $Slot['date'] ?? $Slot['Date'] ?? null; // Handle case sensitivity
        $Time = $Slot['time'] ?? $Slot['Time'] ?? null;

        if (!$Date || !$Time) continue;

        // Enforce 1 Booking Per Day (Per Batch)
        if (in_array($Date, $UsedDates)) {
            throw new Exception("You can only book one session per day (Duplicate date: $Date).");
        }
        $UsedDates[] = $Date;

        // Base Date/Time
        $BaseDateTime = new DateTime("$Date $Time");
        $Now = new DateTime();

        // Generate recurring items for THIS slot
        for ($i = 0; $i < $RepeatWeeks; $i++) {
            $CurrentDate = clone $BaseDateTime;
            if ($i > 0) {
                $CurrentDate->modify("+$i week");
            }

            if ($CurrentDate <= $Now) {
                throw new Exception("Cannot book sessions in the past (" . $CurrentDate->format('Y-m-d H:i') . ").");
            }

            $AllBookings[] = $CurrentDate->format('Y-m-d H:i:s');
        }
    }

    $TotalSessions = count($AllBookings);
    if ($TotalSessions === 0) {
        throw new Exception("No valid sessions to book.");
    }

    // --- DATABASE TRANSACTION STARTS ---
    $pdo->beginTransaction();

    // 3. Batch Checks

    // A. Credit Check
    // Logic: First use Subscription Credits, then Wallet Credits.

    $SubStmt = $pdo->prepare("SELECT BalancePrivateSessions FROM subscriptions WHERE UserId = ? AND Status = 'Active' FOR UPDATE");
    $SubStmt->execute([$UserId]);
    $SubBalance = $SubStmt->fetchColumn() ?: 0;

    $UserStmt = $pdo->prepare("SELECT WalletPrivateSessions FROM users WHERE id = ? FOR UPDATE");
    $UserStmt->execute([$UserId]);
    $WalletBalance = $UserStmt->fetchColumn() ?: 0;

    if (($SubBalance + $WalletBalance) < $TotalSessions) {
        throw new Exception("Insufficient credits for $TotalSessions sessions. You have " . ($SubBalance + $WalletBalance) . ".");
    }

    // B. Availability Check Loop
    $ConflictStmt = $pdo->prepare("SELECT id FROM privatesessions WHERE InstructorId = ? AND Status IN ('Confirmed', 'Pending') AND StartTime = ? FOR UPDATE");

    foreach ($AllBookings as $DateTimeStr) {
        $ConflictStmt->execute([$InstructorId, $DateTimeStr]);
        if ($ConflictStmt->fetch()) {
            throw new Exception("Instructor is unavailable on " . date('M d H:i', strtotime($DateTimeStr)));
        }
    }

    // 4. Execution Loop
    $BookedIds = [];
    $InsertSession = $pdo->prepare("INSERT INTO privatesessions (UserId, InstructorId, StartTime, DurationMinutes, Status) VALUES (?, ?, ?, 60, 'Confirmed')");

    // Statements for deduction
    $UpdateSub = $pdo->prepare("UPDATE subscriptions SET BalancePrivateSessions = BalancePrivateSessions - 1 WHERE UserId = ? AND Status = 'Active'");
    $UpdateUser = $pdo->prepare("UPDATE users SET WalletPrivateSessions = WalletPrivateSessions - 1 WHERE id = ?");

    $LogTx = $pdo->prepare("INSERT INTO credit_transactions (UserId, Type, Amount, CreditType, Source, SessionId) VALUES (?, 'Booking', -1, 'PrivateSession', ?, ?)");

    foreach ($AllBookings as $DateTimeStr) {
        // Insert Session
        $InsertSession->execute([$UserId, $InstructorId, $DateTimeStr]);
        $NewSessionId = $pdo->lastInsertId();
        $BookedIds[] = $NewSessionId;

        // Deduct Credit (Update local trackers so we don't over-deduct from one source)
        $Source = '';
        if ($SubBalance > 0) {
            $UpdateSub->execute([$UserId]);
            $SubBalance--;
            $Source = 'Subscription';
        } else {
            $UpdateUser->execute([$UserId]);
            $WalletBalance--;
            $Source = 'Wallet';
        }

        // Log Transaction
        $LogTx->execute([$UserId, $Source, $NewSessionId]);
    }

    $pdo->commit();
    // --- TRANSACTION ENDS ---

    echo json_encode([
        'success' => true,
        'message' => "Successfully booked $TotalSessions session(s)!",
        'SessionIds' => $BookedIds,
        'RemainingCredits' => ($SubBalance + $WalletBalance)
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
