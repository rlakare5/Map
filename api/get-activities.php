<?php
require_once '../config/database.php';
header('Content-Type: application/json');

if (!isset($_GET['category'])) {
    echo json_encode(['error' => 'Category not specified']);
    exit;
}

$category = sanitize($_GET['category']);
$conn = getConnection();

$stmt = $conn->prepare("SELECT * FROM activities_master WHERE category_id = ? ORDER BY activity_name");
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();

$activities = [];
while ($row = $result->fetch_assoc()) {
    $activities[] = $row;
}

echo json_encode(['activities' => $activities]);
$conn->close();
?>