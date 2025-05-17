<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode([]);
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch unlocked levels for the logged-in user
$sql = "SELECT level FROM user_progress WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$levels = [];
while ($row = $result->fetch_assoc()) {
    $levels[] = (int)$row['level'];
}

// Ensure Level 1 is always unlocked
if (!in_array(1, $levels)) {
    $levels[] = 1;
}

echo json_encode($levels);
?>
