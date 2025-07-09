<?php
session_start();
include "db.php";

// Check if user is logged in and is a regular user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: login.php");
  exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$fullname = "";

$stmt = $conn->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($fullname);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: #f4f4f4;
      padding: 40px;
    }

    .dashboard-container {
      max-width: 800px;
      margin: 0 auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .dashboard-container h1 {
      font-size: 24px;
      margin-bottom: 20px;
      color: #333;
    }

    .info-box {
      background-color: #f0f5f4;
      border-left: 5px solid #4CAF50;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 8px;
      color: #333;
    }

    .logout-btn {
      padding: 10px 20px;
      background: #d33;
      color: white;
      text-decoration: none;
      border-radius: 6px;
      font-weight: 500;
    }

    .logout-btn:hover {
      background: #a00;
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <h1>Welcome, <?php echo htmlspecialchars($fullname); ?>!</h1>

    <div class="info-box">
      <p>This is your user dashboard.</p>
      <p>You can view your adoption status, update your profile, and explore available pets.</p>
    </div>

    <a href="logout.php" class="logout-btn">Logout</a>
  </div>
</body>
</html>
