<?php
include "db.php";

$fullname = $email = $phone = $password = $confirm = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $fullname = trim($_POST["fullname"]);
  $email = trim($_POST["email"]);
  $phone = trim($_POST["phone"]);
  $password = $_POST["password"];
  $confirm = $_POST["confirm"];

  // Input validation
  if (empty($fullname) || empty($email) || empty($phone) || empty($password) || empty($confirm)) {
    $error = "All fields are required.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Invalid email format.";
  } elseif (!preg_match('/^\d{10}$/', $phone)) {
    $error = "Phone number must be exactly 10 digits.";
  } elseif (
    !preg_match('@[A-Z]@', $password) || 
    !preg_match('@[a-z]@', $password) || 
    !preg_match('@[0-9]@', $password) || 
    !preg_match('@[^\w]@', $password) || 
    strlen($password) < 8
  ) {
    $error = "Password must be at least 8 characters long and include an uppercase letter, lowercase letter, number, and special character.";
  } elseif ($password !== $confirm) {
    $error = "Passwords do not match.";
  } else {
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      $error = "Email is already registered.";
    } else {
      // Insert new user
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO users (fullname, email, phone, password) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssss", $fullname, $email, $phone, $hashedPassword);

      if ($stmt->execute()) {
        header("Location: registration.php?registered=true");
        exit();
      } else {
        $error = "Registration failed. Please try again.";
      }
    }
    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sign Up</title>
  <link rel="stylesheet" href="registration.css" />
  <style>
    @keyframes rotateLoader {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .splash-screen {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .splash-screen img {
      width: 80px;
      height: 80px;
      animation: rotateLoader 1s linear infinite;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="image-section">
      <img src="registration.jpg" alt="registration img" />
    </div>
    <div class="form-section">
      <h2>Create your account</h2>
      <br>

      <?php if (!empty($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <input type="text" name="fullname" placeholder="Full Name" value="<?php echo htmlspecialchars($fullname); ?>" required />
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required />
        <input type="tel" name="phone" placeholder="Phone Number" value="<?php echo htmlspecialchars($phone); ?>" required />
        <input type="password" name="password" placeholder="Password" required />
        <input type="password" name="confirm" placeholder="Confirm Password" required />
        <button type="submit" class="signup-btn">Sign Up</button>
      </form>

      <p class="login-text">Already have an account? <a href="login.php">Login</a></p>
    </div>
  </div>

  <?php if (isset($_GET['registered']) && $_GET['registered'] === "true"): ?>
    <script>
      document.querySelector(".form-section").style.display = "none";
      const splash = document.createElement("div");
      splash.className = "splash-screen";
      splash.innerHTML = '<img src="splash.jpg" alt="Loading...">';
      document.body.appendChild(splash);

      setTimeout(() => {
        window.location.href = "login.php";
      }, 2000);
    </script>
  <?php endif; ?>
</body>
</html>
