<?php
// APIs/GetTourSlots.php
header('Content-Type: application/json');
require_once '../Includes/DB.php';

try {
    $LocationId = $_GET['locationId'] ?? null;
    $Date = $_GET['date'] ?? null;

    if (!$LocationId || !$Date) {
        throw new Exception('Missing parameters');
    }

    // 1. Get all active time slots
    $stmt = $pdo->query("SELECT id, SlotTime FROM tourtimeslots WHERE IsActive = 1 ORDER BY SlotTime ASC");
    $AllSlots = $stmt->fetchAll();

    // 2. Get booking counts for this specific date and location
    // We group by SlotId to get counts efficiently
    $countStmt = $pdo->prepare("
        SELECT SlotId, COUNT(*) as BookedCount 
        FROM tourbookings 
        WHERE LocationId = ? AND TourDate = ? AND Status != 'Cancelled'
        GROUP BY SlotId
    ");
    $countStmt->execute([$LocationId, $Date]);
    $BookedCounts = $countStmt->fetchAll(PDO::FETCH_KEY_PAIR); // [SlotId => Count]

    // 3. Prepare Response
    $Response = [];
    $MaxCapacity = 3; // Strict rule per user

    foreach ($AllSlots as $slot) {
        $slotId = $slot['id'];
        $currentBookings = $BookedCounts[$slotId] ?? 0;
        
        // Format time (e.g., 09:00:00 -> 09:00 AM)
        $displayTime = date('h:i A', strtotime($slot['SlotTime']));

        $Response[] = [
            'id' => $slotId,
            'DisplayTime' => $displayTime,
            'IsFull' => ($currentBookings >= $MaxCapacity),
            'Remaining' => $MaxCapacity - $currentBookings
        ];
    }

    echo json_encode($Response);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
