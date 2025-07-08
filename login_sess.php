<?php
session_start();
include "db.php";

// Handle splash screen redirect based on role
if (isset($_GET['loggedin']) && $_GET['loggedin'] === "true") {
    if (!isset($_SESSION['role'])) {
        header("Location: login.php");
        exit();
    }

    $redirectPage = ($_SESSION['role'] === 'admin') ? "admin_dashboard.php" : "dashboard.php";
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Redirecting...</title>
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
      <div class="splash-screen">
        <img src="splash.jpg" alt="Loading..." />
      </div>
      <script>
        setTimeout(() => {
          window.location.href = "<?php echo $redirectPage; ?>";
        }, 2000);
      </script>
    </body>
    </html>
    <?php
    exit();
}

// Normal login handling
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = "Email and password are required.";
        header("Location: login.php");
        exit();
    }

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

            // Redirect to splash screen
            header("Location: login_sess.php?loggedin=true");
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid password.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "No user found with that email.";
        header("Location: login.php");
        exit();
    }

    $stmt->close();
}
?>
