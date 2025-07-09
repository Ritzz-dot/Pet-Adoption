<?php
session_start();
include "db.php";

// Allow only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

// Fetch all non-admin users
$result = $conn->query("SELECT fullname, email, phone, created_at, role FROM users WHERE role != 'admin' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <link rel="stylesheet" href="admin_dashboard.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="sidebar">
    <h2>PetAdoption Admin</h2>
    <ul>
      <li><a href="admin_dashboard.php">Dashboard</a></li>
      <li><a href="pet.php">Pets</a></li>
      <li><a href="applications.php">Applications</a></li>
      <li><a href="users.php" class="active">Users</a></li>
      <li><a href="admins_setting.php">Settings</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>

  <div class="main">
    <h1>Registered Users</h1>

    <table style="width:100%; border-collapse: collapse; background:white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
      <thead style="background:#f5f5f5;">
        <tr>
          <th style="text-align:left; padding:12px;">Full Name</th>
          <th style="text-align:left; padding:12px;">Email</th>
          <th style="text-align:left; padding:12px;">Phone</th>
          <th style="text-align:left; padding:12px;">Registered On</th>
          <th style="text-align:left; padding:12px;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td style="padding:12px;"><?php echo htmlspecialchars($row['fullname']); ?></td>
            <td style="padding:12px;"><?php echo htmlspecialchars($row['email']); ?></td>
            <td style="padding:12px;"><?php echo htmlspecialchars($row['phone']); ?></td>
            <td style="padding:12px;"><?php echo htmlspecialchars($row['created_at']); ?></td>
            <td style="padding:12px;">
              <form action="delete_user.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');" style="display:inline;">
                <input type="hidden" name="email" value="<?php echo $row['email']; ?>">
                <button type="submit" style="background:#e74c3c; color:white; border:none; padding:8px 14px; border-radius:5px; cursor:pointer;">Delete</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
