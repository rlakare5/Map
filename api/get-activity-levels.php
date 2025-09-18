<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_GET['activity_id'])) {
    echo json_encode(['error' => 'Activity ID not specified']);
    exit;
}

$activity_id = intval($_GET['activity_id']);
$conn = getConnection();

$stmt = $conn->prepare("SELECT * FROM activity_levels WHERE activity_id = ? ORDER BY points ASC");
$stmt->bind_param("i", $activity_id);
$stmt->execute();
$result = $stmt->get_result();

$levels = [];
while ($row = $result->fetch_assoc()) {
    $levels[] = $row;
}

echo json_encode(['levels' => $levels]);
$conn->close();
?>