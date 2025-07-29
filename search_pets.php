<?php
require 'includes/db.php';

$search = $_GET['search'] ?? '';
$search = mysqli_real_escape_string($conn, $search);

$sql = "SELECT pet_id, name, breed, age, image_path FROM pets WHERE status = 'available'";
if (!empty($search)) {
    $sql .= " AND (name LIKE '%$search%' OR breed LIKE '%$search%')";
}

$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {
    while ($pet = $res->fetch_assoc()) {
        echo '<a href="pet_details.php?pet_id=' . $pet['pet_id'] . '" class="bg-white rounded-lg shadow hover:shadow-lg overflow-hidden transition">';
        echo '<img src="' . htmlspecialchars($pet['image_path']) . '" class="w-full h-48 object-cover" />';
        echo '<div class="p-4">';
        echo '<h3 class="text-lg font-semibold">' . htmlspecialchars($pet['name']) . '</h3>';
        echo '<p class="text-sm text-gray-600">' . htmlspecialchars($pet['breed']) . ' • ' . intval($pet['age']) . ' yrs</p>';
        echo '</div></a>';
    }
} else {
    echo '<p class="text-center text-gray-500 col-span-full">No pets found.</p>';
}
?>
