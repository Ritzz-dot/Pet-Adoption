<?php
session_start();
include("includes/db.php");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $pet_id = $_POST['pet_id'];

    // Insert into adoption_requests
    $stmt = $conn->prepare("INSERT INTO adoption_requests (user_id, pet_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $pet_id);
    $stmt->execute();
    $request_id = $stmt->insert_id;

    // Get application form inputs
    $full_name = $_POST['full_name'];
    $age = $_POST['age'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    $email = $_POST['email'];
    $occupation = $_POST['occupation'];
    $previous_experience = $_POST['previous_pet_experience'];
    $reason = $_POST['reason_to_adopt'];
    $has_pets = $_POST['has_other_pets'];
    $home_type = $_POST['home_type'];
    $household_members = $_POST['household_members'];
    $caretaker = $_POST['primary_caretaker'];
    $financially_ready = $_POST['financially_ready'];

    // Insert into adoption_application_details
   $stmt2 = $conn->prepare("INSERT INTO adoption_application_details (
    request_id, full_name, age, address, contact_number, email, occupation,
    previous_pet_experience, reason_to_adopt, has_other_pets, home_type,
    household_members, primary_caretaker, financially_ready, user_id, pet_id
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt2->bind_param("isisssssssssisii",
    $request_id,
    $full_name,
    $age,
    $address,
    $contact_number,
    $email,
    $occupation,
    $previous_experience,
    $reason,
    $has_pets,
    $home_type,
    $household_members,
    $caretaker,
    $financially_ready,
    $user_id,
    $pet_id
);


    $stmt2->execute();

    echo "<script>alert('Application submitted successfully!'); window.location.href='dashboard.php';</script>";
    exit;
}


// Get pet_id from URL
$pet_id = isset($_GET['pet_id']) ? intval($_GET['pet_id']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Adoption Application</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md p-8">
    <h1 class="text-2xl font-bold mb-4">Pet Adoption Application</h1>
    <form method="POST" class="space-y-4">
      <input type="hidden" name="pet_id" value="<?= $pet_id ?>">

      <div>
        <label class="block font-semibold">Full Name</label>
        <input name="full_name" required class="w-full border px-4 py-2 rounded">
      </div>

      <div>
        <label class="block font-semibold">Age</label>
        <input name="age" type="number" required class="w-full border px-4 py-2 rounded">
      </div>

      <div>
        <label class="block font-semibold">Address</label>
        <textarea name="address" required class="w-full border px-4 py-2 rounded"></textarea>
      </div>

      <div>
        <label class="block font-semibold">Contact Number</label>
        <input name="contact_number" required class="w-full border px-4 py-2 rounded">
      </div>

      <div>
        <label class="block font-semibold">Email ID</label>
        <input name="email" type="email" required class="w-full border px-4 py-2 rounded">
      </div>

      <div>
        <label class="block font-semibold">Occupation</label>
        <input name="occupation" required class="w-full border px-4 py-2 rounded">
      </div>

      <div>
        <label class="block font-semibold">Do you have any previous pet experience?</label>
        <select name="previous_pet_experience" required class="w-full border px-4 py-2 rounded">
          <option value="">--Select--</option>
          <option>Yes</option>
          <option>No</option>
        </select>
      </div>

      <div>
        <label class="block font-semibold">Why do you want to adopt a pet?</label>
        <textarea name="reason_to_adopt" required class="w-full border px-4 py-2 rounded"></textarea>
      </div>

      <div>
        <label class="block font-semibold">Do you have other pets currently?</label>
        <select name="has_other_pets" required class="w-full border px-4 py-2 rounded">
          <option value="">--Select--</option>
          <option>Yes</option>
          <option>No</option>
        </select>
      </div>

      <div>
        <label class="block font-semibold">Do you live in a rented or owned home?</label>
        <select name="home_type" required class="w-full border px-4 py-2 rounded">
          <option value="">--Select--</option>
          <option>Rented</option>
          <option>Owned</option>
        </select>
      </div>

      <div>
        <label class="block font-semibold">How many members in your household?</label>
        <input name="household_members" type="number" required class="w-full border px-4 py-2 rounded">
      </div>

      <div>
        <label class="block font-semibold">Who will be the primary caretaker of the pet?</label>
        <input name="primary_caretaker" required class="w-full border px-4 py-2 rounded">
      </div>

      <div>
        <label class="block font-semibold">Are you financially ready to support a pet?</label>
        <select name="financially_ready" required class="w-full border px-4 py-2 rounded">
          <option value="">--Select--</option>
          <option>Yes</option>
          <option>No</option>
          <option>Maybe</option>
        </select>
      </div>

      <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded">
        Submit Application
      </button>
    </form>
  </div>
</body>
</html>
