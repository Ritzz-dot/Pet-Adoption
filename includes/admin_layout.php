<!-- includes/admin_layout.php -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $page_title ?? 'Admin Panel' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100 min-h-screen text-gray-800">

  <!-- Sidebar -->
  <aside class="w-64 bg-white border-r shadow-md hidden md:block">
    <div class="p-6 text-xl font-bold text-blue-700">Pet Admin</div>
    <nav class="space-y-2 mt-6 px-4">
      <a href="admin_dashboard.php" class="block px-4 py-2 rounded-lg hover:bg-blue-100">Dashboard</a>
      <a href="pet.php" class="block px-4 py-2 rounded-lg hover:bg-blue-100">Pets</a>
      <a href="applications.php" class="block px-4 py-2 rounded-lg hover:bg-blue-100">Applications</a>
      <a href="admins_setting.php" class="block px-4 py-2 rounded-lg hover:bg-blue-100">Settings</a>
      <a href="logout.php" class="block px-4 py-2 rounded-lg text-red-600 hover:bg-red-100">Logout</a>
    </nav>
  </aside>

  <!-- Main Content -->
  <main class="flex-1 p-6">
    <?= $page_content ?>
  </main>

</body>
</html>
