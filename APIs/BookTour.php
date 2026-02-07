<?php
// APIs/BookTour.php
header('Content-Type: application/json');
require_once '../Includes/DB.php';

try {
    // Read JSON Input
    $input = json_decode(file_get_contents('php://input'), true);

    // Validate Input
    if (empty($input['FullName']) || empty($input['Email']) || empty($input['Phone']) || 
        empty($input['LocationId']) || empty($input['TourDate']) || empty($input['SlotId'])) {
        throw new Exception('All fields are required.');
    }

    $FullName = trim($input['FullName']);
    $Email = trim($input['Email']);
    $Phone = trim($input['Phone']);
    $LocationId = (int)$input['LocationId'];
    $TourDate = $input['TourDate'];
    $SlotId = (int)$input['SlotId'];
    $MaxCapacity = 3;

    $pdo->beginTransaction();

    // RACE CONDITION CHECK: Lock the rows for reading or just strict count inside Transaction
    // For simplicity with InnoDB default isolation, we check count again.
    
    $checkStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM tourbookings 
        WHERE LocationId = ? AND TourDate = ? AND SlotId = ? AND Status != 'Cancelled'
        FOR UPDATE
    ");
    $checkStmt->execute([$LocationId, $TourDate, $SlotId]);
    $currentCount = $checkStmt->fetchColumn();

    if ($currentCount >= $MaxCapacity) {
        $pdo->rollBack();
        throw new Exception('Sorry, this time slot was just booked by someone else. Please choose another.');
    }

    // Insert Booking
    $insertStmt = $pdo->prepare("
        INSERT INTO tourbookings (FullName, Email, PhoneNumber, LocationId, TourDate, SlotId, Status)
        VALUES (?, ?, ?, ?, ?, ?, 'Scheduled')
    ");
    $insertStmt->execute([$FullName, $Email, $Phone, $LocationId, $TourDate, $SlotId]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Tour booked successfully!']);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
