<!-- admin_dashboard.php -->
<?php
session_start();
include "db.php";

// Redirect if not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="admin.css" />
</head>
<body>
  <div class="admin-container">
    <aside class="sidebar">
      <h2>Admin Panel</h2>
      <ul>
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="manage_pets.php">Manage Pets</a></li>
        <li><a href="view_requests.php">Adoption Requests</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </aside>

    <main class="dashboard">
      <h1>Welcome, Admin!</h1>
      <div class="stats">
        <div class="card">Total Pets: <?php // fetch count ?></div>
        <div class="card">Total Requests: <?php // fetch count ?></div>
        <div class="card">Approved: <?php // fetch count ?></div>
        <div class="card">Pending: <?php // fetch count ?></div>
      </div>
    </main>
  </div>
</body>
</html>
