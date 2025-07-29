<?php
include("includes/db.php");
session_start();

if (!isset($_GET['pet_id']) || empty($_GET['pet_id'])) {
    die("Pet ID is required.");
}

$pet_id = intval($_GET['pet_id']);
$query = $conn->prepare("SELECT * FROM pets WHERE pet_id = ?");
$query->bind_param("i", $pet_id);
$query->execute();
$result = $query->get_result();
$pet = $result->fetch_assoc();

if (!$pet) {
    die("Pet not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['pet_name'];
    $age = $_POST['age'];
    $breed = $_POST['breed'];
    $description = $_POST['description'];

    $image_path = $pet['image_path'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/pets/";
        $image_name = "pet_" . time() . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $image_name;
        }
    }

    $update = $conn->prepare("UPDATE pets SET name=?, age=?, breed=?, description=?, image_path=? WHERE pet_id=?");
    $update->bind_param("sisssi", $name, $age, $breed, $description, $image_path, $pet_id);
    if ($update->execute()) {
        header("Location: pet.php?msg=Pet updated");
        exit;
    } else {
        echo "Error updating pet.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Pet</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">

  <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md p-6">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Edit Pet Details</h2>

    <div class="flex justify-center mb-6">
      <img src="<?= htmlspecialchars($pet['image_path']) ?>" alt="Current Pet Image" class="h-32 w-32 object-cover rounded-full border shadow">
    </div>

    <form action="" method="POST" enctype="multipart/form-data" class="space-y-5">
      <div>
        <label class="block mb-1 font-medium text-gray-700">Pet Name</label>
        <input type="text" name="pet_name" value="<?= htmlspecialchars($pet['name']) ?>" class="w-full border border-gray-300 p-2 rounded-md" required>
      </div>

      <div>
        <label class="block mb-1 font-medium text-gray-700">Age</label>
        <input type="number" name="age" value="<?= htmlspecialchars($pet['age']) ?>" class="w-full border border-gray-300 p-2 rounded-md" required>
      </div>

      <div>
        <label class="block mb-1 font-medium text-gray-700">Breed</label>
        <input type="text" name="breed" value="<?= htmlspecialchars($pet['breed']) ?>" class="w-full border border-gray-300 p-2 rounded-md" required>
      </div>

      <div>
        <label class="block mb-1 font-medium text-gray-700">Description</label>
        <textarea name="description" rows="4" class="w-full border border-gray-300 p-2 rounded-md" required><?= htmlspecialchars($pet['description']) ?></textarea>
      </div>

      <div>
        <label class="block mb-1 font-medium text-gray-700">Update Image</label>
        <input type="file" name="image" class="w-full border border-gray-300 p-2 rounded-md">
        <p class="text-sm text-gray-500 mt-1">Leave empty to keep current image.</p>
      </div>

      <div class="text-center">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md shadow">
          Update Pet
        </button>
      </div>
    </form>
  </div>
</body>
</html>
