<?php
session_start();
include(__DIR__ . "/includes/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST["email"]);
  $password = $_POST["password"];

  if (empty($email) || empty($password)) {
    $_SESSION['login_error'] = "Email and password are required.";
    header("Location: login.php");
    exit;
  }

  // Fetch user
  $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows === 1) {
    $stmt->bind_result($user_id, $hashed_password, $role);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
      $_SESSION['user_id'] = $user_id;
      $_SESSION['role'] = $role;

      if ($role === 'admin') {
        // Check 2FA for admin
        $result = $conn->query("SELECT two_factor FROM admin_settings WHERE admin_id = $user_id");
        $row = $result ? $result->fetch_assoc() : null;
        $two_factor_enabled = $row && (int)$row['two_factor'] === 1;

        if ($two_factor_enabled) {
          
          $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
          $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

          // Save OTP to DB
          $stmt2 = $conn->prepare("REPLACE INTO admin_otp (admin_id, otp_code, expires_at) VALUES (?, ?, ?)");
          $stmt2->bind_param("iss", $user_id, $otp, $expiry);
          $stmt2->execute();
          $stmt2->close();

          // Send OTP via email
          $email_result = $conn->query("SELECT email FROM users WHERE id = $user_id")->fetch_assoc();
          $to = $email_result['email']; 
          $subject = "Your Admin Login OTP";
          $body = "Your OTP for Pet Adoption Admin Login is: $otp\n\nIt is valid for 5 minutes.";
          mail($to, $subject, $body); // configure properly

          $_SESSION['pending_2fa'] = $user_id;
          header("Location: verify_otp.php");
          exit;
        } else {
          // No 2FA enabled, go to dashboard
          $_SESSION['admin_id'] = $user_id;
          header("Location: admin_dashboard.php");
          exit;
        }
      } else {
        // Regular user login
        header("Location: dashboard.php");
        exit;
      }
    } else {
      $_SESSION['login_error'] = "Invalid password.";
      header("Location: login.php");
      exit;
    }
  } else {
    $_SESSION['login_error'] = "No user found with that email.";
    header("Location: login.php");
    exit;
  }

  $stmt->close();
}
?>

<!-- If this page is accessed directly without form submission -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="login.css" />
</head>
<body>
  <div class="container">
    <div class="form-section">
      <h2>Login</h2>
      <div class="error-message">Please access this page via the login form.</div>
      <a href="login.php" class="login-btn">Go to Login</a>
    </div>
  </div>
</body>
</html>
