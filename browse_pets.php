<?php
session_start();
require 'includes/db.php';

$petsPerPage = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $petsPerPage;

// Handle search
$search = $_GET['search'] ?? '';
$safeSearch = mysqli_real_escape_string($conn, $search);
$searchCondition = '';
if (!empty($safeSearch)) {
    $searchCondition = "AND (name LIKE '%$safeSearch%' OR breed LIKE '%$safeSearch%')";
}

// Count total pets (for pagination)
$countQuery = "SELECT COUNT(*) as total FROM pets WHERE status = 'available' $searchCondition";
$totalResult = $conn->query($countQuery);
$totalPets = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalPets / $petsPerPage);

// Fetch paginated pets
$sql = "SELECT * FROM pets WHERE status = 'available' $searchCondition ORDER BY pet_id DESC LIMIT $petsPerPage OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Browse Pets</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <!-- Navbar -->
  <nav class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
    <div class="text-2xl font-bold text-blue-600">PawSitive</div>
    <form method="GET" action="browse_pets.php" class="flex space-x-2">
      <input type="text" name="search" placeholder="Search pets..."
             value="<?= htmlspecialchars($search) ?>"
             class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
      <button type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Search</button>
    </form>
  </nav>

  <!-- Page Content -->
  <main class="max-w-7xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-6 text-gray-700">Available Pets</h1>

    <?php if ($result && $result->num_rows > 0): ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <?php while ($row = $result->fetch_assoc()): ?>
          <a href="pet_details.php?pet_id=<?= $row['pet_id'] ?>"
             class="bg-white rounded-xl shadow-md hover:shadow-lg transition duration-300 overflow-hidden">
              <img src="<?= htmlspecialchars($row['image_path']) ?>"
              alt="Pet Image"
              class="w-full h-48 object-cover rounded-t-xl">

              <div class="p-4">
              <h2 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($row['name']) ?></h2>
              <p class="text-sm text-gray-600"><?= htmlspecialchars($row['breed']) ?> • <?= htmlspecialchars($row['age']) ?> years old</p>
              <span class="mt-2 inline-block text-blue-600 text-sm font-medium">View Details →</span>
            </div>
          </a>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="text-gray-600 mt-10 text-center text-lg">
        No pets found. Try a different search.
      </div>
    <?php endif; ?>
  </main>

  <!-- Pagination -->
  <div class="flex justify-center mt-8 space-x-2">
    <?php if ($page > 1): ?>
      <a href="?search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Previous</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="?search=<?= urlencode($search) ?>&page=<?= $i ?>" class="px-3 py-1 <?= $i == $page ? 'bg-blue-500 text-white' : 'bg-gray-200' ?> rounded hover:bg-blue-400"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
      <a href="?search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Next</a>
    <?php endif; ?>
  </div>

</body>
</html>
