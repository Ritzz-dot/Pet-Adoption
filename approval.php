<?php
include('includes/db.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['req_id'], $_POST['action'])) {
    $req_id = intval($_POST['req_id']);
    $action = $_POST['action'];

    if ($action === 'approve') {
        $status = 'approved';
        $remark = 'Application approved.';
    } elseif ($action === 'reject') {
        $status = 'rejected';
        $remark = isset($_POST['remark']) ? trim($_POST['remark']) : 'Application rejected.';
    } else {
        echo "Invalid action.";
        exit;
    }

    $stmt = $conn->prepare("UPDATE adoption_requests SET status = ?, remark = ? WHERE req_id = ?");
    $stmt->bind_param("ssi", $status, $remark, $req_id);

    if ($stmt->execute()) {
        header("Location: applications.php?msg=" . ucfirst($status));
        exit;
    } else {
        echo "Error updating request.";
    }
} else {
    echo "Invalid request.";
}
