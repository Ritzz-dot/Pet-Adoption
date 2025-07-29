<?php
include('includes/db.php');
session_start();

if (!isset($_GET['req_id'])) {
    echo "Invalid request.";
    exit;
}

$req_id = $_GET['req_id'];

$stmt = $conn->prepare("
    SELECT 
        ar.*, 
        aad.*, 
        u.fullname AS user_name, 
        u.email, 
        u.phone, 
        p.name AS pet_name, 
        p.image_path 
    FROM adoption_requests ar
    JOIN adoption_application_details aad ON ar.req_id = aad.request_id
    JOIN users u ON ar.user_id = u.id
    JOIN pets p ON ar.pet_id = p.pet_id
    WHERE ar.req_id = ?
");

$stmt->bind_param("i", $req_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Application not found.";
    exit;
}

$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Application Details</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen py-10 px-4">
  <div class="max-w-5xl mx-auto bg-white shadow-lg rounded-lg p-8 space-y-6">

    <!-- Header Section -->
    <div class="flex items-center gap-6 border-b pb-6">
      <img src="<?= htmlspecialchars($data['image_path']) ?>" alt="Pet Image" class="w-28 h-28 object-cover rounded-full border">
      <div>
        <h2 class="text-2xl font-semibold"><?= htmlspecialchars($data['pet_name']) ?></h2>
        <p class="text-gray-600 mt-1">Requested by: <strong><?= htmlspecialchars($data['user_name']) ?></strong></p>
        <p class="text-gray-500"><?= htmlspecialchars($data['email']) ?> | <?= htmlspecialchars($data['phone']) ?></p>
      </div>
    </div>

    <!-- Application Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
      <div class="space-y-2">
        <p><strong>Application Date:</strong> <?= date("d M Y, h:i A", strtotime($data['request_date'])) ?></p>
        <p><strong>Status:</strong> <span class="capitalize"><?= htmlspecialchars($data['status']) ?></span></p>
        <!--<p><strong>Remark:</strong> <?= htmlspecialchars($data['remark']) ?: "—" ?></p> -->
        <p><strong>Home Type:</strong> <?= htmlspecialchars($data['home_type']) ?></p>
        <p><strong>Household Members:</strong> <?= htmlspecialchars($data['household_members']) ?></p>
      </div>

      <div class="space-y-2">
        <p><strong>Previous Pet Experience:</strong> <?= htmlspecialchars($data['previous_pet_experience']) ?></p>
        <p><strong>Reason to Adopt:</strong> <?= htmlspecialchars($data['reason_to_adopt']) ?></p>
        <p><strong>Has Other Pets:</strong> <?= htmlspecialchars($data['has_other_pets']) ?></p>
        <p><strong>Primary Caretaker:</strong> <?= htmlspecialchars($data['primary_caretaker']) ?></p>
        <p><strong>Financially Ready:</strong> <?= htmlspecialchars($data['financially_ready']) ?></p>
      </div>
    </div>  

    <!-- Action Buttons -->
<!-- Approve & Reject Section -->
<div class="flex flex-col gap-2 mt-6">
    <div class="flex gap-4 items-start">
        <!-- Approve Button -->
        <form action="approval.php" method="post">
            <input type="hidden" name="req_id" value="<?= $req_id ?>">
            <input type="hidden" name="action" value="approve">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg">Approve</button>
        </form>

        <!-- Reject Button + Remark -->
        <form action="approval.php" method="post" class="flex flex-col gap-2">
            <input type="hidden" name="req_id" value="<?= $req_id ?>">
            <input type="hidden" name="action" value="reject">
            <textarea name="remark" rows="2" required placeholder="Reason for rejection" class="w-64 border rounded-md px-3 py-2 text-sm resize-none"></textarea>
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-lg w-fit">Reject</button>
        </form>
        
    </div>
</div>

  </div>
</body>
</html>
