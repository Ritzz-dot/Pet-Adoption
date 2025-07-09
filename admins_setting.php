<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Settings</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
  <div class="sidebar">
    <h2>PetAdoption Admin</h2>
    <ul>
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="pet.php">Pets</a></li>
      <li><a href="applications.php">Applications</a></li>
      <li><a href="users.php">Users</a></li>
      <li><a href="admins_setting.php" class="active">Settings</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>
  <div class="main">
    <h1>Settings</h1>

    <!-- Change Password -->
    <section class="settings-section">
      <h3>Change Password</h3>
      <form action="change_password.php" method="POST">
        <input type="password" name="old_password" placeholder="Old Password" required>
        <input type="password" name="new_password" placeholder="New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
        <button type="submit">Update Password</button>
      </form>
    </section>

    <!-- Update Admin Info -->
    <section class="settings-section">
      <h3>Update Profile Info</h3>
      <form action="update_profile.php" method="POST">
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <button type="submit">Update Info</button>
      </form>
    </section>

    <!-- Theme Toggle -->
    <section class="settings-section">
      <h3>Theme Settings</h3>
      <label>
        <input type="checkbox" id="theme-toggle"> Enable Dark Mode
      </label>
    </section>

    <!-- Export Data -->
    <section class="settings-section">
      <h3>Export Data</h3>
      <button onclick="window.location.href='export_users.php'">Export Users CSV</button>
      <button onclick="window.location.href='export_pets.php'">Export Pets CSV</button>
      <button onclick="window.location.href='export_requests.php'">Export Requests CSV</button>
    </section>

    <!-- Site-Wide Notices -->
    <section class="settings-section">
      <h3>Post Site Notice</h3>
      <form action="post_notice.php" method="POST">
        <textarea name="notice" placeholder="Write your message to all users..."></textarea>
        <button type="submit">Post Notice</button>
      </form>
    </section>

    <!-- Reset System -->
    <section class="settings-section">
      <h3>Reset System</h3>
      <form action="reset_system.php" method="POST" onsubmit="return confirm('Are you sure you want to reset all data?')">
        <button type="submit" class="btn-red">Reset All Data</button>
      </form>
    </section>

    <!-- Optional: 2FA -->
    <section class="settings-section">
      <h3>Two-Factor Authentication (2FA)</h3>
      <label>
        <input type="checkbox" name="2fa_enabled"> Enable 2FA (Coming Soon)
      </label>
    </section>

    <!-- Optional: Auto Logout -->
    <section class="settings-section">
      <h3>Auto-Logout Timer</h3>
      <select name="auto_logout">
        <option value="15">15 minutes</option>
        <option value="30">30 minutes</option>
        <option value="60">60 minutes</option>
      </select>
    </section>

    <!-- Optional: Profile Picture -->
    <section class="settings-section">
      <h3>Update Profile Picture</h3>
      <form action="upload_avatar.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="avatar" accept="image/*">
        <button type="submit">Upload Picture</button>
      </form>
    </section>

  </div>
</body>
</html>
