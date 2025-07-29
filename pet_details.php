<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['pet_id']) || !is_numeric($_GET['pet_id'])) {
    header("Location: dashboard.php");
    exit();
}

$pet_id = (int) $_GET['pet_id'];
$stmt = $conn->prepare("SELECT * FROM pets WHERE pet_id = ? AND status = 'available'");
$stmt->bind_param("i", $pet_id);        
$stmt->execute();
$result = $stmt->get_result();
$pet = $result->fetch_assoc();
$stmt->close();

if (!$pet) {
    echo "<p class='text-center text-red-600 mt-10'>Pet not found or not available for adoption.</p>";
    exit;
}

$image = !empty($pet['image_path']) && file_exists("uploads/pets/" . $pet['image_path'])
    ? "uploads/pets/" . $pet['image_path']
    : "assets/no-image.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($pet['name']) ?> | Pet Details</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">

<!-- NAVBAR (same as your dashboard) -->
<header class="bg-white shadow-sm sticky top-0 z-50">
  <nav class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
    <a href="dashboard.php" class="text-2xl font-bold text-blue-600">PawSitive  </a>
    <ul class="hidden md:flex gap-8 font-medium">
      <li><a href="dashboard.php" class="text-gray-700 hover:text-blue-600">Home</a></li>
      <li><a href="browse_pets.php" class="text-gray-700 hover:text-blue-600">Browse</a></li>
      <li><a href="my_applications.php" class="text-gray-700 hover:text-blue-600">Applications</a></li>
      <li><a href="logout.php" class="text-red-600 hover:text-red-700">Logout</a></li>
    </ul>
  </nav>
</header>

<!-- MAIN CONTENT -->
<main class="max-w-4xl mx-auto mt-10 bg-white rounded-xl shadow-md p-6">
  <div class="flex flex-col md:flex-row gap-8">
     
    <img src="<?= htmlspecialchars($pet['image_path']) ?>" alt="<?= htmlspecialchars($pet['name']) ?>" class="w-full md:w-1/2 rounded-xl object-cover max-h-[400px]">
    
    <div class="flex-1">
      <h1 class="text-3xl font-bold mb-2"><?= htmlspecialchars($pet['name'] ?? '')  ?></h1>
  <p class="text-gray-700 mb-1"><strong>Age:</strong> <?= htmlspecialchars($pet['age'] ?? '') ?> years</p>
  <p class="text-gray-700 mb-1"><strong>Breed:</strong> <?= htmlspecialchars($pet['breed'] ?? '') ?></p>
  <p class="text-gray-700 mt-4"><strong>Description:</strong> <?= htmlspecialchars($pet['description'] ?? '') ?></p>

      <a href="apply_form.php?pet_id=<?= $pet_id ?>" class="inline-block mt-4 bg-blue-600 text-white px-5 py-3 rounded-md hover:bg-blue-700 transition">
        Apply for Adoption
      </a>
    </div>
  </div>
</main>

<footer class="text-center text-sm text-gray-500 py-6 mt-10">
  &copy; <?= date('Y') ?> PetAdopt - All rights reserved
</footer>

</body>
</html>
