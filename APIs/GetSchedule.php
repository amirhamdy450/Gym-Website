<?php
// APIs/GetSchedule.php
require_once '../Includes/Auth.php';
require_once '../Includes/DB.php';

header('Content-Type: application/json');

if (!AuthIsLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$UserId = AuthId();
$Month = $_GET['month'] ?? date('m');
$Year = $_GET['year'] ?? date('Y');

// Validate Input
if (!is_numeric($Month) || !is_numeric($Year)) {
    echo json_encode(['success' => false, 'error' => 'Invalid Date Parameters']);
    exit;
}

// Calculate Date Range for the query
$StartDate = "$Year-$Month-01";
$EndDate = date('Y-m-t', strtotime($StartDate));

try {
    $Sessions = [];

    // 1. Fetch Private Sessions
    $PrivateStmt = $pdo->prepare("
        SELECT ps.id, ps.Category as Title, ps.StartTime, ps.DurationMinutes, ps.Status, ps.Notes,
               'Private' as Type, CONCAT(u.FirstName, ' ', u.LastName) as InstructorName, 'Private Studio' as Location
        FROM privatesessions ps
        LEFT JOIN users u ON ps.InstructorId = u.id
        WHERE ps.UserId = ? 
          AND ps.Status IN ('Confirmed', 'Pending') 
          AND DATE(ps.StartTime) BETWEEN ? AND ?
    ");
    $PrivateStmt->execute([$UserId, $StartDate, $EndDate]);
    while ($Row = $PrivateStmt->fetch(PDO::FETCH_ASSOC)) {
        $Sessions[] = $Row;
    }

    // 2. Fetch Program Bookings
    $ProgramStmt = $pdo->prepare("
        SELECT pb.id, gp.Title, s.StartTime, s.DurationMinutes, pb.Status, '' as Notes,
               'Program' as Type, CONCAT(u.FirstName, ' ', u.LastName) as InstructorName, gp.Room as Location
        FROM programbookings pb
        JOIN programsessions s ON pb.SessionId = s.id
        JOIN groupprograms gp ON s.ProgramId = gp.id
        LEFT JOIN users u ON s.InstructorId = u.id
        WHERE pb.UserId = ? 
          AND pb.Status = 'Confirmed'
          AND DATE(s.StartTime) BETWEEN ? AND ?
    ");
    $ProgramStmt->execute([$UserId, $StartDate, $EndDate]);
    while ($Row = $ProgramStmt->fetch(PDO::FETCH_ASSOC)) {
        $Sessions[] = $Row;
    }

    echo json_encode(['success' => true, 'sessions' => $Sessions]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
