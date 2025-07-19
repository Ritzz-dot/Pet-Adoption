<?php
//session_start();

// Optional: Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pet Adoption | User</title>
    <link rel="stylesheet" href="assets/css/tailwind.css"> <!-- or your actual CSS path -->
</head>
<body class="bg-gray-100 font-sans">

    <!-- Navbar -->
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="dashboard.php" class="text-2xl font-bold text-indigo-600">PetAdopt</a>
            <ul class="flex space-x-6 text-gray-700">
                <li><a href="dashboard.php" class="hover:text-indigo-600">Home</a></li>
                <li><a href="browse_pets.php" class="hover:text-indigo-600">Browse Pets</a></li>
                <li><a href="my_applications.php" class="hover:text-indigo-600">My Applications</a></li>
                <li><a href="logout.php" class="hover:text-red-600">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Page content starts -->
    <div class="container mx-auto px-4 py-6">

            </div> <!-- end of container -->
</body>
</html>

