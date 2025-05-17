<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
  http_response_code(403);
  echo json_encode(["error" => "Unauthorized"]);
  exit;
}

$userId = $_SESSION['user_id'];

$sql = "SELECT s.user_id, u.name, u.avatar, s.level, s.score, s.percentage
        FROM scores s
        JOIN users u ON s.user_id = u.id
        ORDER BY s.score DESC, s.percentage DESC
        LIMIT 10";

$result = $conn->query($sql);
$data = [];

while ($row = $result->fetch_assoc()) {
  // Fallback if avatar is null or empty
  if (empty($row['avatar'])) {
    // Random fallback from available avatars
    $avatars = ['avatar1.png', 'avatar2.png'];
    $row['avatar'] = $avatars[array_rand($avatars)];
  }

  // Add flag if this is the current user
  $row['current_user'] = ($row['user_id'] == $userId);
  
  $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
