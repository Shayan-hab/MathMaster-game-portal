<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../admin_login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'])) {
    include 'db.php';

    $message_id = intval($_POST['message_id']);
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();

    $stmt->close();
    $conn->close();
}

header("Location: ../admin_dashboard.php");
exit();
