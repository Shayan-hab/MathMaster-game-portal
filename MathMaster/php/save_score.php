<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];

// Decode JSON input ( frontend is sending JSON)
$data = json_decode(file_get_contents("php://input"), true);

$level = (int)$data['level'];
$score = (int)$data['score'];
$percentage = (float)$data['percentage'];
$created_at = date('Y-m-d H:i:s');


// Save score
$stmt = $conn->prepare("INSERT INTO scores (user_id, level, score, percentage, created_at)
                        VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iiids", $userId, $level, $score, $percentage, $created_at);
$stmt->execute();

// Always ensure level 1 is inserted for new users (if not already)
$checkLevel1 = $conn->prepare("SELECT * FROM user_progress WHERE user_id = ? AND level = 1");
$checkLevel1->bind_param("i", $userId);
$checkLevel1->execute();
$level1Result = $checkLevel1->get_result();

if ($level1Result->num_rows === 0) {
    $insertLevel1 = $conn->prepare("INSERT INTO user_progress (user_id, level) VALUES (?, 1)");
    $insertLevel1->bind_param("i", $userId);
    $insertLevel1->execute();
}

// Unlock next level if user scored > 60%
if ($percentage > 60 && $level < 30) {
    $nextLevel = $level + 1;

    $check = $conn->prepare("SELECT * FROM user_progress WHERE user_id = ? AND level = ?");
    $check->bind_param("ii", $userId, $nextLevel);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO user_progress (user_id, level) VALUES (?, ?)");
        $insert->bind_param("ii", $userId, $nextLevel);
        $insert->execute();
    }
}

echo json_encode(['success' => true]);
?>
