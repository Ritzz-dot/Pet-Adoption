<?php
$page_title = "Manage Pets";
require __DIR__ . "/includes/db.php";

$err = '';
$success = '';

/* ───────── ADD NEW PET ───────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_pet'])) {
    $name  = trim($_POST['name']  ?? '');
    $breed = trim($_POST['breed'] ?? '');
    $age   = (int) ($_POST['age'] ?? 0);

    if ($name === '' || $breed === '' || $age <= 0) {
        $err = "Please fill in all fields.";
    } elseif (empty($_FILES['image']['name'])) {
        $err = "Please select an image.";
    } else {
        $allowed = ['image/jpeg','image/png','image/gif'];
        if (!in_array($_FILES['image']['type'], $allowed)) {
            $err = "Unsupported image format. Upload JPG, PNG, or GIF.";
        } else {
            $dir = 'uploads/pets/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);

            $ext  = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $file = uniqid('pet_', true) . '.' . $ext;
            $path = $dir . $file;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $path)) {
                $err = "Failed to move uploaded file.";
            } else {
                $stmt = $conn->prepare("INSERT INTO pets (name, breed, age, image_path) VALUES (?,?,?,?)");
                $stmt->bind_param('ssis', $name, $breed, $age, $path);
                $stmt->execute();
                $stmt->close();
                $success = "Pet added successfully!";
            }
        }
    }
}

/* ───────── DELETE PET ───────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_pet'])) {
    $pet_id = (int)$_POST['pet_id'];
    $row = $conn->query("SELECT image_path FROM pets WHERE pet_id = $pet_id")->fetch_assoc();
    if ($row) @unlink($row['image_path']);
    $conn->query("DELETE FROM pets WHERE pet_id = $pet_id");
}

/* ───────── FETCH PETS ───────── */
$pets = $conn->query("SELECT * FROM pets ORDER BY pet_id DESC")->fetch_all(MYSQLI_ASSOC);
?>
<?php ob_start(); ?>

<h1 class="text-3xl font-bold mb-6">Manage Pets</h1>

<?php if ($err): ?>
  <div class="bg-red-100 text-red-700 p-3 rounded mb-6"><?= $err ?></div>
<?php elseif ($success): ?>
  <div class="bg-green-100 text-green-700 p-3 rounded mb-6"><?= $success ?></div>
<?php endif; ?>

<!-- Add New Pet -->
<form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-2xl shadow-md mb-8 space-y-4">
  <h2 class="text-xl font-semibold">Add New Pet</h2>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <input name="name"  type="text"   placeholder="Pet Name"  class="input-field" required>
    <input name="breed" type="text"   placeholder="Breed"     class="input-field" required>
    <input name="age"   type="number" placeholder="Age (yrs)" class="input-field" min="1" required>
    <input name="image" type="file"   accept="image/*"        class="input-field col-span-1 md:col-span-2" required>
  </div>
  <button name="add_pet" class="btn btn-primary">Add Pet</button>
</form>

<!-- Pet Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
<?php foreach ($pets as $pet): ?>
  <div class="bg-white p-4 rounded-2xl shadow text-center">
    <img src="<?= htmlspecialchars($pet['image_path']) ?>" class="w-32 h-32 object-cover rounded-full mx-auto border mb-3" />
    <h3 class="text-lg font-semibold"><?= htmlspecialchars($pet['name']) ?></h3>
    <p class="text-gray-600"><?= htmlspecialchars($pet['breed']) ?> – <?= (int)$pet['age'] ?> yrs</p>

    <div class="mt-3 flex justify-center gap-3">
      <!-- Edit page to be implemented -->
      <a href="edit_pet.php?pet_id=<?= $pet['pet_id'] ?>" class="btn btn-secondary">Edit</a>
      <form method="POST" onsubmit="return confirm('Delete this pet?');">
        <input type="hidden" name="pet_id" value="<?= $pet['pet_id'] ?>">
        <button name="delete_pet" class="btn btn-danger">Delete</button>
      </form>
    </div>
  </div>
<?php endforeach; ?>
</div>

<!-- Tailwind helpers -->
<style>
  .input-field { @apply w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500; }
  .btn { @apply px-4 py-2 rounded-md font-medium transition; }
  .btn-primary { @apply bg-blue-600 text-white hover:bg-blue-700; }
  .btn-secondary { @apply bg-gray-300 text-black hover:bg-gray-400; }
  .btn-danger { @apply bg-red-500 text-white hover:bg-red-600; }
</style>

<?php
$page_content = ob_get_clean();
include __DIR__ . '/includes/admin_layout.php';
?>
