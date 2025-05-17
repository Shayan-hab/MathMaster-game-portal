<?php
session_start();

header('Content-Type: application/json');
echo json_encode([
  "loggedIn" => isset($_SESSION['user_id'])
]);
