<?php
session_start();
$error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : "";
unset($_SESSION['login_error']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link rel="stylesheet" href="login.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div class="image-section">
      <img src="login.jpg" alt="login img" />
    </div>

    <div class="form-section">
      <h2>Welcome Back</h2>
      <p>Please login to your account</p>

      <div class="divider"><span>OR</span></div>

      <form id="loginForm" action="login_sess.php" method="POST" novalidate>
        <input type="email" id="email" name="email" placeholder="Email" required />
        <div id="emailError" class="error-message"></div>

        <input type="password" id="password" name="password" placeholder="Password" required />
        <div id="passwordError" class="error-message"></div>

        <?php if (!empty($error)): ?>
          <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <label class="remember">
          <input type="checkbox" id="remember" />
          Remember me
        </label>

        <button type="submit" class="login-btn">Login</button>
      </form>

      <p class="signup-text">Don't have an account? <a href="registration.php">Sign Up</a></p>
    </div>
  </div>

  <script>
    document.getElementById("loginForm").addEventListener("submit", function(e) {
      const email = document.getElementById("email").value.trim();
      const password = document.getElementById("password").value;

      const emailError = document.getElementById("emailError");
      const passwordError = document.getElementById("passwordError");

      let valid = true;
      const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

      emailError.textContent = "";
      passwordError.textContent = "";

      if (!emailRegex.test(email)) {
        emailError.textContent = "Enter a valid email address.";
        valid = false;
      }

      if (password.length < 8) {
        passwordError.textContent = "Password must be at least 8 characters.";
        valid = false;
      }

      if (!valid) {
        e.preventDefault();
      }
    });
  </script>
</body>
</html>
