<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = trim($_POST["email"]);
  $password = $_POST["password"];

  if (empty($email) || empty($password)) {
    $error = "Email and password are required.";
  } else {
    // Fetch user along with role
    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
      $stmt->bind_result($user_id, $hashed_password, $role);
      $stmt->fetch();

      if (password_verify($password, $hashed_password)) {
        // Store user in session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['role'] = $role;

        // Redirect based on role
        if ($role === 'admin') {
          header("Location: admin_dashboard.php");
        } else {
          header("Location: dashboard.php");
        }
        exit();
      } else {
        $error = "Invalid password.";
      }
    } else {
      $error = "No user found with that email.";
    }

    $stmt->close();
  }
}
?>

<!-- ✅ HTML Login Form -->
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

      <?php if (!empty($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
      <?php endif; ?>

      <form method="POST" action="login_sess.php">
        <input type="email" name="email" placeholder="Email" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit" class="login-btn">Login</button>
      </form>

      <p class="signup-text">Don't have an account? <a href="registration.php">Sign Up</a></p>
    </div>
  </div>
</body>
</html>
