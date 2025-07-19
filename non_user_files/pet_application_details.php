<?php
session_start();
include "includes/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pet_id = isset($_GET['pet_id']) ? intval($_GET['pet_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];

    // Collect answers
    $answers = [];
    for ($i = 1; $i <= 13; $i++) {
        $answers[] = htmlspecialchars($_POST["q$i"] ?? '');
    }

    $remark = implode(" | ", $answers);

    $stmt = $conn->prepare("INSERT INTO adoption_requests (user_id, pet_id, remark, status) VALUES (?, ?, ?, 'pending')");
    $stmt->bind_param("iis", $user_id, $pet_id, $remark);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: user_home.php"); // Redirect to home (can change later)
        exit;
    } else {
        $error = "There was an error submitting your application.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Adoption Application</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
  <div class="max-w-4xl mx-auto mt-10 p-8 bg-white shadow rounded-lg">
    <h2 class="text-2xl font-bold mb-6">Adoption Application</h2>

    <?php if (!empty($error)): ?>
      <p class="text-red-600 font-semibold mb-4"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
      <div>
        <label class="block font-semibold">Q1. Full Name</label>
        <input type="text" name="q1" required class="w-full border rounded px-4 py-2">
      </div>

      <div>
        <label class="block font-semibold">Q2. Age</label>
        <input type="number" name="q2" required class="w-full border rounded px-4 py-2">
      </div>

      <div>
        <label class="block font-semibold">Q3. Address</label>
        <input type="text" name="q3" required class="w-full border rounded px-4 py-2">
      </div>

      <div>
        <label class="block font-semibold">Q4. Contact Number</label>
        <input type="text" name="q4" required class="w-full border rounded px-4 py-2">
      </div>

      <div>
        <label class="block font-semibold">Q5. Email ID</label>
        <input type="email" name="q5" required class="w-full border rounded px-4 py-2">
      </div>

      <div>
        <label class="block font-semibold">Q6. Occupation</label>
        <input type="text" name="q6" required class="w-full border rounded px-4 py-2">
      </div>

      <div>
        <label class="block font-semibold">Q7. Do you have any previous pet experience?</label>
        <label><input type="radio" name="q7" value="Yes" required> Yes</label>
        <label class="ml-4"><input type="radio" name="q7" value="No"> No</label>
      </div>

      <div>
        <label class="block font-semibold">Q8. Why do you want to adopt a pet?</label>
        <input type="text" name="q8" required class="w-full border rounded px-4 py-2">
      </div>

      <div>
        <label class="block font-semibold">Q9. Do you have other pets currently?</label>
        <label><input type="radio" name="q9" value="Yes" required> Yes</label>
        <label class="ml-4"><input type="radio" name="q9" value="No"> No</label>
      </div>

      <div>
        <label class="block font-semibold">Q10. Do you live in a rented or owned home?</label>
        <label><input type="radio" name="q10" value="Rented" required> Rented</label>
        <label class="ml-4"><input type="radio" name="q10" value="Owned"> Owned</label>
      </div>

      <div>
        <label class="block font-semibold">Q11. How many members in your household?</label>
        <input type="number" name="q11" required class="w-full border rounded px-4 py-2">
      </div>

      <div>
        <label class="block font-semibold">Q12. Who will be the primary caretaker of the pet?</label>
        <input type="text" name="q12" required class="w-full border rounded px-4 py-2">
      </div>

      <div>
        <label class="block font-semibold">Q13. Are you financially ready to support a pet?</label>
        <label><input type="radio" name="q13" value="Yes" required> Yes</label>
        <label class="ml-4"><input type="radio" name="q13" value="No"> No</label>
        <label class="ml-4"><input type="radio" name="q13" value="Maybe"> Maybe</label>
      </div>

      <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Submit Application</button>
    </form>
  </div>
</body>
</html>
