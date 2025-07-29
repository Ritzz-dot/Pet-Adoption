<?php
$page_title = "Adoption Applications";
require 'includes/db.php';

/* ────────── Handle Approve / Reject ────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $req_id = (int) $_POST['req_id'];
  $status = $_POST['action'] === 'approve' ? 'approved' : 'rejected';
  $remark = trim($_POST['remark'] ?? '');

  $stmt = $conn->prepare("UPDATE adoption_requests SET status = ?, remark = ? WHERE req_id = ?");
  $stmt->bind_param('ssi', $status, $remark, $req_id);
  $stmt->execute();
  $stmt->close();

  header("Location: applications.php?msg=updated");
  exit;
}

/* ────────── Fetch Applications with Joins ────────── */
$sql = "SELECT 
    r.req_id,
    r.request_date,
    r.status,
    r.remark,
    p.name AS pet_name,
    p.image_path,
    u.email,
    d.full_name AS applicant_name,
    d.age,
    d.reason_to_adopt
FROM adoption_requests r
JOIN pets p ON r.pet_id = p.pet_id
JOIN users u ON r.user_id = u.id
JOIN adoption_application_details d ON r.req_id = d.request_id
ORDER BY r.request_date DESC";

$result = mysqli_query($conn, $sql);

$applications = [];
while ($row = $result->fetch_assoc()) $applications[] = $row;
?>
<?php ob_start(); ?>

<h1 class="text-3xl font-bold mb-6">Adoption Applications</h1>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
  <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">Status updated successfully.</div>
<?php endif; ?>

<!-- Card View -->
<div class="space-y-6">
<?php foreach ($applications as $app): ?>
  <div class="bg-white p-6 rounded-xl shadow flex flex-col md:flex-row gap-6 items-start md:items-center justify-between">
    
    <!-- Pet Info -->
    <div class="flex items-center gap-4">
      <img src="<?= htmlspecialchars($app['image_path']) ?>" class="w-20 h-20 object-cover rounded-full border" alt="Pet Image" />
      <div>
        <h2 class="text-lg font-semibold"><?= htmlspecialchars($app['pet_name']) ?></h2>
        <span class="font-semibold"><?= htmlspecialchars($app['applicant_name'] ?? '') ?></span>
        <p class="text-sm text-gray-400"><?= date('d M Y, h:i A', strtotime($app['request_date'])) ?></p>
      </div>
    </div>

    <!-- Status + Action -->
    <div class="flex-1 md:text-right space-y-3 md:space-y-0 md:flex md:flex-col md:items-end">
      <a href="applicant_details.php?req_id=<?= $app['req_id'] ?>"
         class="inline-block mb-2 px-4 py-1 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-full transition">
         View Details
      </a>

      <span class="px-3 py-1 rounded-full text-sm font-medium
        <?= $app['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' ?>
        <?= $app['status'] === 'approved' ? 'bg-green-100 text-green-800' : '' ?>
        <?= $app['status'] === 'rejected' ? 'bg-red-100 text-red-800' : '' ?>">
        <?= ucfirst($app['status']) ?>
      </span>

      <?php if (!empty($app['remark'])): ?>
        <p class="text-sm text-gray-600 italic mt-2">Remark: <?= htmlspecialchars($app['remark']) ?></p>
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
</div>

<!-- Tailwind UI Utilities -->
<style>
  .input-field {
    @apply px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500;
  }
  .btn {
    @apply px-4 py-2 rounded-md text-sm font-medium transition;
  }
  .btn-primary {
    @apply bg-blue-600 text-white hover:bg-blue-700;
  } 
  .btn-danger {
    @apply bg-red-500 text-white hover:bg-red-600;
  }
</style>

<?php
$page_content = ob_get_clean();
include 'includes/admin_layout.php';
?>
