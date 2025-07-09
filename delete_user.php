<?php
session_start();
include "db.php";

// Allow only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email'])) {
  $email = $_POST['email'];

  // Prevent admin from deleting himself
  $adminCheck = $conn->prepare("SELECT role FROM users WHERE email = ?");
  $adminCheck->bind_param("s", $email);
  $adminCheck->execute();
  $adminCheck->bind_result($role);
  $adminCheck->fetch();
  $adminCheck->close();

  if ($role === 'admin') {
    echo "You cannot delete another admin.";
    exit();
  }

  $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);

  if ($stmt->execute()) {
    header("Location: users.php");
    exit();
  } else {
    echo "Failed to delete user.";
  }
}
?>
