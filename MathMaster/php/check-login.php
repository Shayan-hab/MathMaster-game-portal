<?php
session_start();
echo isset($_SESSION['user_id']) ? "yes" : "no";
?>
