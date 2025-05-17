<?php
session_start();
include 'db.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_id'])) {
        $userId = intval($_POST['user_id']);

        // Optional: Skip deleting messages if they are not tied to users by user_id
        // Delete related user data (skip messages if not linked)
        $stmt2 = $conn->prepare("DELETE FROM scores WHERE user_id = ?");
        $stmt2->bind_param("i", $userId);
        $stmt2->execute();

        $stmt3 = $conn->prepare("DELETE FROM user_progress WHERE user_id = ?");
        $stmt3->bind_param("i", $userId);
        $stmt3->execute();

        // Delete user
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            header("Location: ../admin_dashboard.php?msg=User+deleted");
            exit();
        } else {
            echo "Failed to delete user: " . $stmt->error;
        }
    } else {
        echo "User ID not provided.";
    }
} else {
    echo "Invalid request.";
}
?>
