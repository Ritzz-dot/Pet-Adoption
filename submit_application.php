<?php
session_start();
include "includes/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $pet_id = $_POST['pet_id'];

    $stmt = $conn->prepare("INSERT INTO adoption_application_details (
        user_id, pet_id, full_name, age, address, contact_number, email,
        occupation, marital_status, family_members, landlord_permission,
        secure_space, owned_pets, alone_hours, willing_to_neuter
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("iisssssssisissi",
        $user_id, $pet_id, $_POST['full_name'], $_POST['age'], $_POST['address'],
        $_POST['contact_number'], $_POST['email'], $_POST['occupation'],
        $_POST['marital_status'], $_POST['family_members'], $_POST['landlord_permission'],
        $_POST['secure_space'], $_POST['owned_pets'], $_POST['alone_hours'], $_POST['willing_to_neuter']
    );

    if ($stmt->execute()) {
        header("Location: success.php"); // Show confirmation
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
