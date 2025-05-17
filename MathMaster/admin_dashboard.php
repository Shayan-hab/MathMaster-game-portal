<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.html");
    exit();
}
include 'php/db.php';

// Fetch users
$users = $conn->query("SELECT id, name, email, created_at FROM users");

// Fetch messages
$messages = $conn->query("SELECT id, name, email, message FROM messages");

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="css/admin_dashboard_styles.css" />
</head>
<body>
  <div class="container">
    <h2>Admin Dashboard</h2>

    <div style="text-align: right; margin-bottom: 20px;">
      <form action="php/admin_logout.php" method="POST">
        <button type="submit" class="logout-btn">Logout</button>
      </form>
    </div>

    <div class="section">
      <h3>Registered Users</h3>
      <table>
        <tr><th>ID</th><th>Name</th><th>Email</th><th>Created At</th><th>Action</th></tr>
        <?php while ($row = $users->fetch_assoc()): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= $row['created_at'] ?></td>
            <td>
              <form method="POST" action="php/delete_user.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                <button type="submit">Delete</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    </div>

    <div class="section">
      <h3>Contact Messages</h3>
      <table>
  <tr><th>Name</th><th>Email</th><th>Message</th><th>Action</th></tr>
  <?php while ($msg = $messages->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($msg['name']) ?></td>
      <td><?= htmlspecialchars($msg['email']) ?></td>
      <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
      <td>
        <form method="POST" action="php/delete_message.php" onsubmit="return confirm('Delete this message?');">
          <input type="hidden" name="message_id" value="<?= $msg['id'] ?>">
          <button type="submit">Delete</button>
        </form>
      </td>
    </tr>
  <?php endwhile; ?>
</table>

    </div>
  </div>
  </div> 
  <div class="spacer"></div> 
</body>
</html>
