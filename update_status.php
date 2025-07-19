<?php
include "includes/db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $req_id = $_POST['req_id'];
    $remark = $_POST['remark'];
    $action = $_POST['action']; // approve or reject

    if (in_array($action, ['approve', 'reject'])) {
        $stmt = $conn->prepare("UPDATE adoption_requests SET status = ?, remark = ? WHERE req_id = ?");
        $stmt->bind_param("ssi", $action, $remark, $req_id);
        $stmt->execute();
    }
    header("Location: applications.php");
    exit();
}
?>
