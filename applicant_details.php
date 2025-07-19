<?php
include('includes/db.php');
session_start();

if (!isset($_GET['req_id'])) {
    echo "Invalid request.";
    exit;
}

$req_id = $_GET['req_id'];

$stmt = $conn->prepare("
    SELECT ar.*, u.fullname AS user_name, u.email, u.phone, p.name AS pet_name, p.image_path 
    FROM adoption_requests ar
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
<body class="bg-gray-100 min-h-screen p-6">
  <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-xl p-6">
    <div class="flex items-center gap-6">
      <img src="<?= htmlspecialchars($data['image_path']) ?>" alt="Pet Image" class="w-24 h-24 object-cover rounded-full border">
      <div>
        <h2 class="text-2xl font-semibold"><?= htmlspecialchars($data['pet_name']) ?></h2>
        <p class="text-sm text-gray-600">Requested by: <strong><?= htmlspecialchars($data['user_name']) ?></strong> (<?= htmlspecialchars($data['email']) ?>)</p>
      </div>
    </div>

    <hr class="my-6">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
      <div>
        <p><strong>Contact Number:</strong> <?= htmlspecialchars($data['phone']) ?></p>
        <p><strong>Application Date:</strong> <?= date("d M Y, h:i A", strtotime($data['request_date'])) ?></p>
        <p><strong>Status:</strong> <?= ucfirst($data['status']) ?></p>
        <p><strong>Remark:</strong> <?= htmlspecialchars($data['remark']) ?: "—" ?></p>
      </div>

      <div>
        <p><strong>Previous Pet Experience:</strong> <?= htmlspecialchars($data['previous_pet_experience']) ?></p>
        <p><strong>Reason to Adopt:</strong> <?= htmlspecialchars($data['reason_to_adopt']) ?></p>
        <p><strong>Has Other Pets:</strong> <?= htmlspecialchars($data['has_other_pets']) ?></p>
        <p><strong>Home Type:</strong> <?= htmlspecialchars($data['home_type']) ?></p>
      </div>

      <div>
        <p><strong>Household Members:</strong> <?= htmlspecialchars($data['household_members']) ?></p>
        <p><strong>Primary Caretaker:</strong> <?= htmlspecialchars($data['primary_caretaker']) ?></p>
        <p><strong>Financially Ready:</strong> <?= htmlspecialchars($data['financially_ready']) ?></p>
      </div>
    </div>

    <div class="mt-6 flex gap-4">
      <form action="approve_request.php" method="post">
        <input type="hidden" name="req_id" value="<?= $req_id ?>">
        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Approve</button>
      </form>
      <form action="reject_request.php" method="post">
        <input type="hidden" name="req_id" value="<?= $req_id ?>">
        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Reject</button>
      </form>
    </div>
  </div>
</body>
</html>
