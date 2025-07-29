<?php
include("includes/db.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$query = $conn->prepare("
    SELECT a.req_id, a.request_date, a.status, a.remark, p.name AS pet_name, p.image_path
    FROM adoption_requests a
    JOIN pets p ON a.pet_id = p.pet_id
    WHERE a.user_id = ?
    ORDER BY a.request_date DESC
");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Adoption Applications</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 min-h-screen">
  <div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">My Adoption Applications</h1>

    <?php if ($result->num_rows > 0): ?>
      <div class="space-y-4">
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="bg-white rounded-xl shadow-md p-4 flex items-center gap-4">
            <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="Pet Image" class="h-20 w-20 object-cover rounded-full border">
            <div class="flex-1">
              <h2 class="text-xl font-semibold text-gray-700"><?= htmlspecialchars($row['pet_name']) ?></h2>
              <p class="text-sm text-gray-600">Requested on: <?= date("d M Y, h:i A", strtotime($row['request_date'])) ?></p>
              <p class="text-sm text-gray-600">Status: 
                <?php if ($row['status'] === 'approved'): ?>
                  <span class="text-green-600 font-semibold">Approved</span>
                <?php elseif ($row['status'] === 'rejected'): ?>
                  <span class="text-red-600 font-semibold">Rejected</span>
                <?php else: ?>
                  <span class="text-yellow-600 font-semibold">Pending</span>
                <?php endif; ?>
              </p>
              <?php if (!empty($row['remark'])): ?>
                <p class="text-sm mt-1 text-gray-500 italic">Admin Remark: <?= htmlspecialchars($row['remark']) ?></p>
              <?php endif; ?>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <p class="text-center text-gray-500">You haven't applied for any pets yet.</p>
    <?php endif; ?>
  </div>
</body>
</html>
