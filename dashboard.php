<?php
session_start();
include "db.php";

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// Fetch user data from database
$user_id = $_SESSION['user_id'];
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
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8f9fa;
    }
    .dashboard-container {
      margin-top: 50px;
      padding: 30px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .welcome {
      font-size: 24px;
      font-weight: 600;
    }
    .logout-btn {
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="container dashboard-container text-center">
    <h2 class="welcome">Welcome, <?php echo htmlspecialchars($fullname); ?>!</h2>
    <p>You have successfully logged in to your dashboard.</p>
    
    <div class="row justify-content-center mt-4">
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Profile</h5>
            <p class="card-text">Access your profile and update info.</p>
            <a href="#" class="btn btn-outline-primary btn-sm">View Profile</a>
          </div>
        </div>
      </div>

      <div class="col-md-4 mt-3 mt-md-0">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Settings</h5>
            <p class="card-text">Customize your preferences.</p>
            <a href="#" class="btn btn-outline-secondary btn-sm">Settings</a>
          </div>
        </div>
      </div>
    </div>

    <a href="logout.php" class="btn btn-danger logout-btn">Logout</a>
  </div>
</body>
</html>
