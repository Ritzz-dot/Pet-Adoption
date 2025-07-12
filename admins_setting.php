<?php
require 'includes/db.php';
session_start();
$admin_id = $_SESSION['admin_id'] ?? 1;

// Ensure default row exists
$conn->query("INSERT IGNORE INTO admin_settings (admin_id) VALUES ($admin_id)");

function flash($msg) {
  $_SESSION['flash'] = $msg;
  header("Location: admin_settings.php");
  exit;
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($_POST['action'] === 'upload_avatar' && !empty($_FILES['avatar']['name'])) {
    $dir = 'uploads/avatars/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $filename = 'admin_'.$admin_id.'.'.$ext;
    $path = $dir.$filename;
    move_uploaded_file($_FILES['avatar']['tmp_name'], $path);
    $stmt = $conn->prepare("UPDATE admin_settings SET profile_picture=? WHERE admin_id=?");
    $stmt->bind_param('si', $path, $admin_id);
    $stmt->execute(); $stmt->close();
    flash("Profile picture updated!");
  }

  if ($_POST['action'] === 'change_password') {
    $current = $_POST['current_pwd'];
    $new     = $_POST['new_pwd'];
    $confirm = $_POST['confirm_pwd'];
    $user = $conn->query("SELECT password FROM users WHERE id=$admin_id")->fetch_assoc();
    if (!$user || !password_verify($current, $user['password'])) flash("Current password incorrect!");
    if ($new !== $confirm) flash("Password confirmation mismatch!");
    $hashed = password_hash($new, PASSWORD_BCRYPT);
    $conn->query("UPDATE users SET password='$hashed' WHERE id=$admin_id");
    flash("Password changed successfully.");
  }

  if ($_POST['action'] === 'update_contact') {
    $email = $_POST['email']; $phone = $_POST['phone'];
    $stmt = $conn->prepare("UPDATE users SET email=?, phone=? WHERE id=?");
    $stmt->bind_param('ssi', $email, $phone, $admin_id);
    $stmt->execute(); $stmt->close();
    flash("Contact information saved.");
  }

  if (isset($_POST['toggle_dark'])) {
    $conn->query("UPDATE admin_settings SET dark_mode = 1 - dark_mode WHERE admin_id=$admin_id");
    flash("Theme preference updated.");
  }

  if (isset($_POST['toggle_2fa'])) {
    $conn->query("UPDATE admin_settings SET two_factor = 1 - two_factor WHERE admin_id=$admin_id");
    flash("2FA setting updated.");
  }

  if (isset($_POST['set_auto_logout'])) {
    $minutes = max(1, (int)$_POST['minutes']);
    $conn->query("UPDATE admin_settings SET auto_logout=$minutes WHERE admin_id=$admin_id");
    flash("Auto-logout timer updated.");
  }

  if (isset($_POST['export_csv']) || isset($_POST['export_json'])) {
    $rows = $conn->query("SELECT * FROM pets")->fetch_all(MYSQLI_ASSOC);
    if (isset($_POST['export_csv'])) {
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename="pets.csv"');
      $out = fopen('php://output', 'w');
      fputcsv($out, array_keys($rows[0] ?? []));
      foreach ($rows as $r) fputcsv($out, $r);
      exit;
    } else {
      header('Content-Type: application/json');
      header('Content-Disposition: attachment; filename="pets.json"');
      echo json_encode($rows, JSON_PRETTY_PRINT); exit;
    }
  }

  if (isset($_POST['broadcast_notice'])) {
    $msg = trim($_POST['notice']);
    if ($msg) {
      $stmt = $conn->prepare("INSERT INTO site_notices (message) VALUES (?)");
      $stmt->bind_param('s', $msg);
      $stmt->execute(); $stmt->close();
      flash("Notice broadcasted.");
    }
  }

  if (isset($_POST['reset_settings'])) {
    $conn->query("UPDATE admin_settings SET dark_mode=0, two_factor=0, auto_logout=15, profile_picture=NULL WHERE admin_id=$admin_id");
    flash("Settings reset to default.");
  }

  if (isset($_POST['clear_logs'])) {
    $conn->query("DELETE FROM activity_logs WHERE admin_id=$admin_id");
    flash("Logs cleared.");
  }
}

// Fetch current settings
$settings = $conn->query("SELECT * FROM admin_settings WHERE admin_id=$admin_id")->fetch_assoc() ?? [];
$user = $conn->query("SELECT email, phone FROM users WHERE id=$admin_id")->fetch_assoc() ?? [];
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
?>
<?php ob_start(); ?>

<h1 class="text-3xl font-bold mb-6">Admin Settings</h1>

<?php if ($flash): ?>
  <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<!-- Profile Picture Upload -->
<form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-2xl shadow-md mb-6 space-y-4">
  <h2 class="text-xl font-semibold">Profile Picture</h2>
  <?php if (!empty($settings['profile_picture'])): ?>
    <img src="<?= htmlspecialchars($settings['profile_picture']) ?>" class="h-20 w-20 rounded-full border">
  <?php endif; ?>
  <input type="file" name="avatar" accept="image/*" class="input-field">
  <button name="action" value="upload_avatar" class="btn btn-primary">Upload</button>
</form>

<!-- Change Password -->
<form method="POST" class="bg-white p-6 rounded-2xl shadow-md mb-6 space-y-4">
  <h2 class="text-xl font-semibold">Change Password</h2>
  <input type="password" name="current_pwd" placeholder="Current Password" class="input-field" required>
  <input type="password" name="new_pwd" placeholder="New Password" class="input-field" required>
  <input type="password" name="confirm_pwd" placeholder="Confirm New Password" class="input-field" required>
  <button name="action" value="change_password" class="btn btn-primary">Save Password</button>
</form>

<!-- Update Contact Info -->
<form method="POST" class="bg-white p-6 rounded-2xl shadow-md mb-6 space-y-4">
  <h2 class="text-xl font-semibold">Contact Info</h2>
  <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" class="input-field" required>
  <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="input-field" required>
  <button name="action" value="update_contact" class="btn btn-primary">Update Info</button>
</form>

<!-- Theme Toggle -->
<form method="POST" class="bg-white p-6 rounded-2xl shadow-md mb-6 flex justify-between items-center">
  <h2 class="text-xl font-semibold">Dark Mode</h2>
  <button name="toggle_dark" class="btn btn-secondary"><?= $settings['dark_mode'] ? 'Disable' : 'Enable' ?></button>
</form>

<!-- 2FA Toggle -->
<form method="POST" class="bg-white p-6 rounded-2xl shadow-md mb-6 flex justify-between items-center">
  <h2 class="text-xl font-semibold">Two-Factor Authentication</h2>
  <button name="toggle_2fa" class="btn btn-secondary"><?= $settings['two_factor'] ? 'Disable' : 'Enable' ?></button>
</form>

<!-- Auto Logout -->
<form method="POST" class="bg-white p-6 rounded-2xl shadow-md mb-6 space-y-4">
  <h2 class="text-xl font-semibold">Auto Logout Timer</h2>
  <input type="number" name="minutes" value="<?= (int)($settings['auto_logout'] ?? 15) ?>" min="1" class="input-field w-32">
  <button name="set_auto_logout" class="btn btn-primary">Save Timer</button>
</form>

<!-- Export Buttons -->
<form method="POST" class="bg-white p-6 rounded-2xl shadow-md mb-6 space-x-4">
  <h2 class="text-xl font-semibold mb-2">Export Data</h2>
  <button name="export_csv" class="btn btn-secondary">Export as CSV</button>
  <button name="export_json" class="btn btn-secondary">Export as JSON</button>
</form>

<!-- Broadcast Notice -->
<form method="POST" class="bg-white p-6 rounded-2xl shadow-md mb-6 space-y-4">
  <h2 class="text-xl font-semibold">Broadcast Site-Wide Notice</h2>
  <textarea name="notice" class="input-field w-full" rows="3" placeholder="Enter notice..."></textarea>
  <button name="broadcast_notice" class="btn btn-primary">Send Notice</button>
</form>

<!-- Reset Settings -->
<form method="POST" class="bg-white p-6 rounded-2xl shadow-md mb-6">
  <h2 class="text-xl font-semibold mb-3">Reset to Defaults</h2>
  <button name="reset_settings" class="btn btn-danger">Reset All Settings</button>
</form>

<!-- Clear Logs -->
<form method="POST" class="bg-white p-6 rounded-2xl shadow-md mb-6">
  <h2 class="text-xl font-semibold mb-3">Clear Admin Logs</h2>
  <button name="clear_logs" class="btn btn-danger">Clear Activity Logs</button>
</form>

<!-- Tailwind Helpers -->
<style>
  .input-field { @apply w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500; }
  .btn         { @apply px-4 py-2 rounded-md text-sm font-medium transition; }
  .btn-primary { @apply bg-blue-600 text-white hover:bg-blue-700; }
  .btn-secondary { @apply bg-gray-300 text-black hover:bg-gray-400; }
  .btn-danger  { @apply bg-red-500 text-white hover:bg-red-600; }
</style>

<?php
$page_content = ob_get_clean();
include 'includes/admin_layout.php';
?>
