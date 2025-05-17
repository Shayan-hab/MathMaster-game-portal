<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["token"];
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: ../reset-password.html?token=" . urlencode($token));
        exit();
    }

    // Check if token exists in password_resets table
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Invalid or expired reset token.";
        header("Location: ../login.html");
        exit();
    }

    $row = $result->fetch_assoc();
    $email = $row['email'];

    // Hash new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update user's password
    $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    $update->bind_param("ss", $hashedPassword, $email);
    $update->execute();

    // Remove token from password_resets table
    $delete = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
    $delete->bind_param("s", $token);
    $delete->execute();

    $_SESSION['success'] = "Password reset successfully. You can now log in.";
    header("Location: ../login.html");
    exit();
}
?>
