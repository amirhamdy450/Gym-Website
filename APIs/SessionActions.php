<?php
// APIs/SessionActions.php
require_once '../Includes/Auth.php';
require_once '../Includes/DB.php';

header('Content-Type: application/json');

if (!AuthIsLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$UserId = AuthId();
$Input = json_decode(file_get_contents('php://input'), true);

if (!$Input || !isset($Input['action']) || !isset($Input['sessionId'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid Request']);
    exit;
}

$Action = $Input['action'];
$SessionId = $Input['sessionId'];
$Type = $Input['type'] ?? 'Private'; // 'Private' or 'Program'

try {
    if ($Action === 'cancel') {
        if ($Type === 'Private') {
            handlePrivateCancellation($pdo, $UserId, $SessionId);
        } else {
            // Future: Handle Program Session Cancellation
            echo json_encode(['success' => false, 'error' => 'Program cancellation not supported yet']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Unknown Action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function handlePrivateCancellation($pdo, $UserId, $SessionId)
{
    // 1. Verify Ownership & Status using 'privatesessions' table
    $Stmt = $pdo->prepare("
        SELECT * FROM privatesessions 
        WHERE id = ? AND UserId = ? AND Status = 'Confirmed'
    ");
    $Stmt->execute([$SessionId, $UserId]);
    $Booking = $Stmt->fetch();

    if (!$Booking) {
        throw new Exception("Booking not found or already cancelled.");
    }

    // 2. Check Time Logic (e.g., Refund only if > 24h)
    // StartTime is directly in privatesessions
    $StartTime = new DateTime($Booking['StartTime']);
    $Now = new DateTime();

    // Calculate difference in hours
    $Interval = $Now->diff($StartTime);
    $TotalHoursDiff = ($Interval->days * 24) + $Interval->h;

    // Logic: Refund if future session AND > 24h notice
    // Note: $Interval->invert is 1 if $Now > $StartTime (past session)
    $IsFuture = $StartTime > $Now;
    $IsRefundable = $IsFuture && ($TotalHoursDiff >= 24);

    $pdo->beginTransaction();

    try {
        // 3. Update Status
        $UpdateStmt = $pdo->prepare("UPDATE privatesessions SET Status = 'Cancelled' WHERE id = ?");
        $UpdateStmt->execute([$SessionId]);

        // 4. Refund Credit (Private Session)
        $Msg = "Session cancelled.";
        if ($IsRefundable) {
            $RefundStmt = $pdo->prepare("UPDATE users SET WalletPrivateSessions = WalletPrivateSessions + 1 WHERE id = ?");
            $RefundStmt->execute([$UserId]);
            $Msg .= " Credit refunded.";
        } else {
            $Msg .= " No refund (less than 24h notice).";
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => $Msg, 'refunded' => $IsRefundable]);
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}
