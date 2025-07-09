<?php
session_start();
include "db.php";

// Restrict access to admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all adoption requests
$query = "SELECT ar.pet_id, ar.request_date, ar.status, ar.remark, u.fullname, p.name AS pet_name 
          FROM adoption_requests ar
          JOIN users u ON ar.user_id = u.id
          JOIN pets p ON ar.pet_id = p.pet_id
          ORDER BY ar.request_date DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Adoption Requests</title>
  <link rel="stylesheet" href="admin_dashboard.css" />
  <style>
    .main h1 {
      margin-bottom: 20px;
    }

    .gmail-style-list {
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    .request-row {
      display: flex;
      justify-content: space-between;
      padding: 15px 20px;
      border-bottom: 1px solid #eee;
      align-items: center;
    }

    .request-row:last-child {
      border-bottom: none;
    }

    .request-info {
      flex: 1;
    }

    .request-info h4 {
      margin-bottom: 4px;
      font-size: 16px;
    }

    .request-info p {
      font-size: 14px;
      color: #555;
    }

    .status-badge {
      padding: 6px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
      color: white;
      margin-right: 10px;
    }

    .approved { background: #28a745; }
    .rejected { background: #dc3545; }
    .pending { background: #ffc107; color: #111; }

    .action-form {
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .action-form input[type="text"] {
      padding: 6px 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 13px;
    }

    .action-form button {
      padding: 6px 10px;
      font-size: 13px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      color: white;
    }

    .btn-approve {
      background-color: #28a745;
    }

    .btn-reject {
      background-color: #dc3545;
    }

    .btn-approve:hover {
      background-color: #218838;
    }

    .btn-reject:hover {
      background-color: #c82333;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>PetAdoption Admin</h2>
    <ul>
      <li><a href="admin_dashboard.php">Dashboard</a></li>
      <li><a href="pet.php">Pets</a></li>
      <li><a href="applications.php" class="active">Applications</a></li>
      <li><a href="users.php">Users</a></li>
      <li><a href="admins_setting.php">Settings</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>

  <div class="main">
    <h1>Adoption Requests</h1>

    <div class="gmail-style-list">
      <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="request-row">
            <div class="request-info">
              <h4><?= htmlspecialchars($row['fullname']) ?> → <?= htmlspecialchars($row['pet_name']) ?></h4>
              <p><?= htmlspecialchars($row['date']) ?></p>
              <?php if (!empty($row['remark'])): ?>
                <p><strong>Remark:</strong> <?= htmlspecialchars($row['remark']) ?></p>
              <?php endif; ?>
            </div>

            <div class="action-form">
              <span class="status-badge <?= strtolower($row['status']) ?>">
                <?= $row['status'] ?>
              </span>

              <?php if ($row['status'] === 'Pending'): ?>
                <form method="POST" action="update_status.php" style="display:inline;">
                  <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                  <input type="hidden" name="action" value="approve">
                  <button type="submit" class="btn-approve">Approve</button>
                </form>

                <form method="POST" action="update_status.php" style="display:inline;">
                  <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                  <input type="hidden" name="action" value="reject">
                  <input type="text" name="remark" placeholder="Rejection reason" required>
                  <button type="submit" class="btn-reject">Reject</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="request-row">
          <p>No adoption requests found.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
