<?php
session_start();
include 'db.php';

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    if (password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: ../admin_dashboard.php");
        exit();
    }
}

echo "Invalid credentials!";
?>
