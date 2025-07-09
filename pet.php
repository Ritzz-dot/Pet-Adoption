<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$dashboardPage = ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'dashboard.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Pets - Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="admin_dashboard.css" />
</head>
<body>
  <div class="sidebar">
    <h2>PetAdoption Admin</h2>
    <ul>
      <li><a href="<?php echo $dashboardPage; ?>">Dashboard</a></li>
      <li><a href="pet.php" class="active">Pets</a></li>
      <li><a href="applications.php">Applications</a></li>
      <li><a href="users.php">Users</a></li>
      <li><a href="admins_setting.php">Settings</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>

  <div class="main">
    <h1>Manage Pets</h1>
    <div class="cards">
      <div class="card">
        <h3>Edit Pet Details</h3>
        <form action="update_pet.php" method="POST" enctype="multipart/form-data">
          <label>Pet Name:</label><br />
          <input type="text" name="name" required /><br /><br />

          <label>Age:</label><br />
          <input type="number" name="age" required /><br /><br />

          <label>Breed:</label><br />
          <input type="text" name="breed" required /><br /><br />

          <label>Description:</label><br />
          <textarea name="description" rows="4" cols="50" required></textarea><br /><br />

          <label>Upload Image:</label><br />
          <input type="file" name="image" accept="image/*" required /><br /><br />

          <button class="btn-green" type="submit">Save Pet</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
