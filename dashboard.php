<?php
session_start();
include __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

// Fetch user name
$stmt = $conn->prepare("SELECT fullname FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($fullname);
$stmt->fetch();
$stmt->close();

// Fetch available pets
$petsRes = $conn->query("SELECT pet_id, name, breed, age, image_path FROM pets WHERE status = 'available' LIMIT 6");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Home | Pet Adoption</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans bg-neutral-50 overflow-x-hidden">

  <!-- NAVBAR -->
  <header class="bg-white shadow-sm sticky top-0 z-50">
    <nav class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
      <a href="dashboard.php" class="text-2xl font-bold text-blue-600">PawSitive</a>
      <ul class="hidden md:flex gap-8 font-medium">
        <li><a href="dashboard.php" class="text-gray-700 hover:text-blue-600">Home</a></li>
        <li><a href="browse_pets.php" class="text-gray-700 hover:text-blue-600">Browse</a></li>
        <li><a href="my_applications.php" class="text-gray-700 hover:text-blue-600">Applications</a></li>
        <li><a href="user_profile.php" class="text-gray-700 hover:text-blue-600">Profile</a></li>
        <li><a href="logout.php" class="text-red-600 hover:text-red-700">Logout</a></li>
      </ul>
      <button id="hamburger" class="md:hidden focus:outline-none">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
    </nav>
    <div id="mobileMenu" class="md:hidden hidden border-t bg-white">
      <a href="dashboard.php" class="block px-6 py-3 hover:bg-gray-50">Home</a>
      <a href="browse_pets.php" class="block px-6 py-3 hover:bg-gray-50">Browse</a>
      <a href="my_applications.php" class="block px-6 py-3 hover:bg-gray-50">Applications</a>
      <a href="user_profile.php" class="block px-6 py-3 hover:bg-gray-50">Profile</a>
      <a href="logout.php" class="block px-6 py-3 text-red-600 hover:bg-gray-50">Logout</a>
    </div>
  </header>

  <!-- HERO -->
  <section class="relative h-[60vh] bg-cover bg-center" style="background-image:url('assets/hero.jpg')">
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative max-w-3xl mx-auto h-full flex flex-col justify-center items-center text-center px-6">
      <h1 class="text-4xl sm:text-5xl font-extrabold text-white mb-4">
        Find Your Forever Friend, <?= htmlspecialchars($fullname) ?>
      </h1>
      <p class="text-lg text-gray-200 mb-6">
        Explore loving pets waiting for a forever home.
      </p>
      <form action="browse_pets.php" method="GET" class="flex w-full max-w-xl gap-3 px-4">
        <input type="text" name="q" placeholder="Search by name or breed" class="flex-1 py-3 px-4 rounded-md">
        <button type="submit" class="bg-blue-600 text-white px-5 py-3 rounded-md">
          Search
        </button>
      </form>
    </div>
  </section>

  <!-- PET CARD GRID -->
  <section class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 py-12 px-6">
    <?php while ($pet = $petsRes->fetch_assoc()): ?>
      <a href="pet_details.php?pet_id=<?= $pet['pet_id'] ?>" class="bg-white rounded-lg shadow hover:shadow-lg overflow-hidden transition">

        <?php
          $image = !empty($pet['image_path']) && file_exists(__DIR__ . "/uploads/pets/" . $pet['image_path'])
            ? "uploads/pets/" . $pet['image_path']
            : "assets/no-image.png"; // fallback image
        ?>
        <img src="uploads/pets/<?= htmlspecialchars($pet['image_path']) ?>" alt="<?= htmlspecialchars($pet['name']) ?>" style="width: 100%; max-height: 250px; object-fit: cover;" />

        <div class="p-4">
          <h3 class="text-lg font-semibold"><?= htmlspecialchars($pet['name']) ?></h3>
          <p class="text-sm text-gray-600"><?= htmlspecialchars($pet['breed']) ?> • <?= intval($pet['age']) ?> yrs</p>
        </div>
      </a>
    <?php endwhile; ?>
  </section>

  <!-- WHY ADOPT -->
  <section class="bg-white py-16 px-6">
    <div class="max-w-6xl mx-auto text-center">
      <h2 class="text-3xl font-bold text-gray-800 mb-6">Why Adopt from Us?</h2>
      <div class="grid md:grid-cols-3 gap-8 text-left">
        <div>
          <h3 class="text-xl font-semibold text-blue-600 mb-2"> Save a Life</h3>
          <p class="text-gray-600 text-sm">Every adoption gives a homeless pet a second chance to live, love, and thrive in a new family.</p>
        </div>
        <div>
          <h3 class="text-xl font-semibold text-cyan-600 mb-2"> Cost-Effective</h3>
          <p class="text-gray-600 text-sm">Adopting from us is often far more affordable than purchasing from breeders or stores.</p>
        </div>
        <div>
          <h3 class="text-xl font-semibold text-emerald-600 mb-2"> Lifelong Companionship</h3>
          <p class="text-gray-600 text-sm">Our pets are waiting to offer unconditional love and loyalty for life.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- HOW IT WORKS -->
  <section class="bg-blue-50 py-16 px-6">
    <div class="max-w-6xl mx-auto text-center">
      <h2 class="text-3xl font-bold text-gray-800 mb-6">How It Works</h2>
      <div class="grid md:grid-cols-3 gap-8 text-left">
        <div>
          <h3 class="text-lg font-semibold mb-2"> 1. Browse Pets</h3>
          <p class="text-gray-600 text-sm">Use filters to find pets that match your lifestyle and home.</p>
        </div>
        <div>
          <h3 class="text-lg font-semibold mb-2"> 2. Apply to Adopt</h3>
          <p class="text-gray-600 text-sm">Submit an adoption request and tell us why you're the right match.</p>
        </div>
        <div>
          <h3 class="text-lg font-semibold mb-2"> 3. Welcome Home</h3>
          <p class="text-gray-600 text-sm">Once approved, your new companion joins your family!</p>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="text-center text-sm text-gray-500 py-6">
    &copy; <?= date('Y') ?> PetAdopt - All rights reserved
  </footer>

  <script>
    const btn = document.getElementById('hamburger');
    const menu = document.getElementById('mobileMenu');
    btn.addEventListener('click', () => menu.classList.toggle('hidden'));
  </script>
</body>
</html>
