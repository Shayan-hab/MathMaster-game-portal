<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $avatars = ['avatar1.jpg', 'avatar2.jpg', 'avatar3.jpg'];
    $avatar = $avatars[array_rand($avatars)];

    // Check if email already exists
    $checkQuery = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $checkResult = $stmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo "Email already registered!";
    } else {
        
        $insertQuery = "INSERT INTO users (name, email, password, avatar) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssss", $name, $email, $hashedPassword, $avatar);

        if ($stmt->execute()) {
            $userId = $stmt->insert_id;

            // Also fix: level_number → should be just level
            $progressQuery = "INSERT INTO user_progress (user_id, level) VALUES (?, 1)";
            $stmt2 = $conn->prepare($progressQuery);
            $stmt2->bind_param("i", $userId);
            $stmt2->execute();

            header("Location: ../login.html?success=1");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}
?>
