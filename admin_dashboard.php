<?php
$page_title = "Admin Dashboard";
require 'includes/db.php';

// Metrics from DB
$total_users = $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'];
$total_pets  = $conn->query("SELECT COUNT(*) AS count FROM pets")->fetch_assoc()['count'];
$total_apps  = $conn->query("SELECT COUNT(*) AS count FROM adoption_requests")->fetch_assoc()['count'];
$pending_apps = $conn->query("SELECT COUNT(*) AS count FROM adoption_requests WHERE status = 'Pending'")->fetch_assoc()['count'];
?>
<?php ob_start(); ?>

<h1 class="text-3xl font-bold mb-6">Dashboard Overview</h1>

<!-- Stat Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

  <div class="card">
    <div class="text-gray-500 text-sm">Total Users</div>
    <div class="text-4xl font-semibold"><?= $total_users ?></div>
  </div>

  <div class="card">
    <div class="text-gray-500 text-sm">Total Pets</div>
    <div class="text-4xl font-semibold"><?= $total_pets ?></div>
  </div>

  <div class="card">
    <div class="text-gray-500 text-sm">Total Applications</div>
    <div class="text-4xl font-semibold"><?= $total_apps ?></div>
  </div>

  <div class="card">
    <div class="text-gray-500 text-sm">Pending Applications</div>
    <div class="text-4xl font-semibold"><?= $pending_apps ?></div>
  </div>

</div>

<!-- Chart.js Traffic (Optional Dummy Data) -->
<div class="bg-white p-6 rounded-2xl shadow-md">
  <h2 class="text-xl font-semibold mb-4">Website Traffic (Last 7 Days)</h2>
  <canvas id="trafficChart" height="120"></canvas>
</div>

<!-- Tailwind Styles -->
<style>
  .card {
    @apply bg-white p-6 rounded-2xl shadow-md hover:shadow-lg transition;
  }
</style>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('trafficChart').getContext('2d');
new Chart(ctx, {
  type: 'line',
  data: {
    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
    datasets: [{
      label: 'Visits',
      data: [20, 40, 35, 60, 80, 55, 70],
      backgroundColor: 'rgba(59,130,246,0.1)',
      borderColor: 'rgba(59,130,246,1)',
      borderWidth: 2,
      tension: 0.3,
      fill: true,
      pointRadius: 4,
      pointHoverRadius: 6
    }]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: { beginAtZero: true }
    }
  }
});
</script>

<?php
$page_content = ob_get_clean();
include 'includes/admin_layout.php';
?>
