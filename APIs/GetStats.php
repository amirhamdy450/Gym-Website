<?php
// APIs/GetStats.php
require_once '../Includes/Auth.php';
require_once '../Includes/DB.php';

header('Content-Type: application/json');

if (!AuthIsLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$UserId = AuthId();
$Range = $_GET['range'] ?? '3m'; // 1m, 3m, 6m, all

$DateCondition = "";
$Params = [$UserId];

switch (strtolower($Range)) {
    case '1m':
        $DateCondition = "AND RecordedAt >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        break;
    case '3m':
        $DateCondition = "AND RecordedAt >= DATE_SUB(NOW(), INTERVAL 3 MONTH)";
        break;
    case '6m':
        $DateCondition = "AND RecordedAt >= DATE_SUB(NOW(), INTERVAL 6 MONTH)";
        break;
    case 'all':
    default:
        $DateCondition = ""; // All time
        break;
}

try {
    $Stmt = $pdo->prepare("
        SELECT Weight, BodyFat, RecordedAt 
        FROM userphysicalstats 
        WHERE UserId = ? $DateCondition
        ORDER BY RecordedAt ASC
    ");
    $Stmt->execute($Params);
    $Data = $Stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $Data
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
