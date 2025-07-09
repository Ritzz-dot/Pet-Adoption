<?php
session_start();
include "db.php";

// Validate admin access
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

// Fetch stats from DB
$totalPets = 0;
$pendingAdoptions = 0;
$newUsers = 0;

// Total Pets
$result = $conn->query("SELECT COUNT(*) FROM pets");
if ($result) {
  $totalPets = $result->fetch_row()[0];
}

// Pending Adoptions
$result = $conn->query("SELECT COUNT(*) FROM adoption_requests WHERE status = 'pending'");
if ($result) {
  $pendingAdoptions = $result->fetch_row()[0];
}

// New Users
$result = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
if ($result) {
  $newUsers = $result->fetch_row()[0];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin_dashboard.css" />
</head>
<body>
  <div class="sidebar">
    <h2>PetAdoption Admin</h2>
    <ul>
      <li><a href="admin_dashboard.php" class="active">Dashboard</a></li>
      <li><a href="pet.php">Pets</a></li>
      <li><a href="applications.php">Applications</a></li>
      <li><a href="users.php">Users</a></li>
      <li><a href="admins_setting.php">Settings</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>

  <div class="main">
    <h1>Dashboard</h1>
    <div class="cards">
      <div class="card">
        <h3>Total Pets</h3>
        <p><?php echo $totalPets; ?></p>
      </div>
      <div class="card">
        <h3>Pending Adoptions</h3>
        <p><?php echo $pendingAdoptions; ?></p>
      </div>
      <div class="card">
        <h3>New Users</h3>
        <p><?php echo $newUsers; ?></p>
      </div>
    </div>

    <div class="chart">
      <h4>Adoption Trends</h4>
      <p>+15% in last 3 months</p>
      <div class="chart-img"></div>
    </div>

    <div class="chart">
      <h4>User Activity</h4>
      <p>+10% in last 3 months</p>
      <div class="chart-img"></div>
    </div>

    <div class="btns">
      <button class="btn-green" onclick="location.href='pet.php'">Add New Pet</button>
      <button class="btn-gray" onclick="location.href='view_requests.php'">Review Pending Requests</button>
    </div>
  </div>
</body>
</html>
