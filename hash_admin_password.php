<?php
require __DIR__ . "/includes/db.php";

// Manually set admin ID and plain password (what you know)
$admin_id = 1;
$plain = 'admin123'; // Replace with your actual plain password

$hashed = password_hash($plain, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("si", $hashed, $admin_id);
$stmt->execute();
$stmt->close();

echo "✔ Admin password hashed and updated.";
