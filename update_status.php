<?php
session_start();
include "db.php";

// Ensure only admins can access this
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle approval or rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'], $_POST['action'])) {
    $request_id = intval($_POST['request_id']);
    $action = $_POST['action'];

    // Set status and optional remark
    if ($action === 'approve') {
        $status = 'Approved';
        $remark = '';
    } elseif ($action === 'reject') {
        $status = 'Rejected';
        $remark = isset($_POST['remark']) ? trim($_POST['remark']) : 'Rejected by admin';
    } else {
        // Invalid action
        header("Location: applications.php");
        exit();
    }

    // Update the record in the database
    $stmt = $conn->prepare("UPDATE adoption_requests SET status = ?, remark = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $remark, $request_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: applications.php");
exit();
