<?php
require __DIR__ . '/includes/db.php';
session_start();

if (!isset($_SESSION['pending_2fa'])) {
  header('Location: login.php');
  exit;
}

$admin_id = (int)$_SESSION['pending_2fa'];
$err = '';
$msg = '';

/* ───── Handle OTP verification ───── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
  $otp_input = trim($_POST['otp'] ?? '');

  // Fetch OTP record
  $stmt = $conn->prepare("SELECT otp_code, expires_at FROM admin_otp WHERE admin_id = ?");
  $stmt->bind_param("i", $admin_id);
  $stmt->execute();
  $stmt->bind_result($otp_code, $expires_at);
  $found = $stmt->fetch();
  $stmt->close();

  if (!$found) {
    $err = "No OTP record found. Please login again.";
  } elseif ($otp_code !== $otp_input) {
    $err = "Invalid OTP. Please try again.";
  } elseif (strtotime($expires_at) < time()) {
    $err = "OTP expired. Please login again.";
  } else {
    // OTP correct
    unset($_SESSION['pending_2fa']);
    $_SESSION['admin_id'] = $admin_id;

    // Clear OTP
    $del = $conn->prepare("DELETE FROM admin_otp WHERE admin_id = ?");
    $del->bind_param("i", $admin_id);
    $del->execute(); $del->close();

    header("Location: admin_dashboard.php");
    exit;
  }
}

/* ───── Handle Resend OTP ───── */
if (isset($_POST['resend_otp'])) {
  $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
  $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

  $save = $conn->prepare("REPLACE INTO admin_otp (admin_id, otp_code, expires_at) VALUES (?, ?, ?)");
  $save->bind_param("iss", $admin_id, $otp, $expiry);
  $save->execute(); $save->close();

  $to_row = $conn->query("SELECT email FROM users WHERE id = $admin_id LIMIT 1")->fetch_assoc();
  if ($to_row) {
    $to = $to_row['email'];
    $subject = "Your New Admin Login OTP";
    $body = "Your new OTP is: $otp\n\nIt is valid for 5 minutes.";
    mail($to, $subject, $body);  // configure SMTP for production
  }
  $msg = "A new OTP has been sent to your email.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verify OTP - Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">
  <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
    <h1 class="text-2xl font-bold mb-4 text-center">Verify OTP</h1>
    <p class="text-sm text-gray-600 mb-6 text-center">Enter the 6‑digit OTP sent to your email.</p>

    <?php if ($err): ?>
      <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>
    <?php if ($msg): ?>
      <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <input type="text" name="otp" maxlength="6" pattern="\d{6}" required
        class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
        placeholder="Enter OTP">

      <button type="submit" name="verify_otp"
        class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Verify</button>

      <button type="submit" name="resend_otp"
        class="w-full bg-gray-300 text-black py-2 rounded hover:bg-gray-400">Resend OTP</button>
    </form>
  </div>
</body>
</html>
