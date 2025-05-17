<?php
require 'db.php'; // Your existing database connection file
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    // Check if the email exists in users table
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        // Generate secure token
        $token = bin2hex(random_bytes(32));
        $expires_at = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Store token in password_resets table
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $token, $expires_at);
        $stmt->execute();

        // Create password reset link
        $reset_link = "http://localhost/math-game-project/reset-password.html?token=" . $token;

        // Configure PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'shayanrasool71@gmail.com'; 
            $mail->Password = 'ghat zsug rudd zgoe';    
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('yourgmail@gmail.com', 'Math Master');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "
                <h2>Reset Your Password</h2>
                <p>Click the link below to reset your password:</p>
                <a href='$reset_link'>$reset_link</a>
                <p>This link will expire in 1 hour.</p>
            ";

            $mail->send();
            echo "<script>alert('Reset link sent to your email.'); window.location.href = '../login.html';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Failed to send reset email: {$mail->ErrorInfo}'); window.location.href = '../forgot-password.html';</script>";
        }
    } else {
        // Generic message for privacy
        echo "<script>alert('If your email exists in our system, a reset link has been sent.'); window.location.href = '../login.html';</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: ../login.html");
    exit();
}
?>
