<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db_connect.php';

requireLogin();

if ($_SESSION['user_role'] !== 'provider') {
    die("Unauthorized");
}

$appId = intval($_GET['id']);
$action = $_GET['action'];

$conn = createConnection();

if ($action === "approve") {
    $status = "approved";
} elseif ($action === "reject") {
    $status = "rejected";
} else {
    die("Invalid action");
}

$stmt = $conn->prepare("UPDATE adoption_applications SET status=? WHERE application_id=?");
$stmt->bind_param("si", $status, $appId);
$stmt->execute();

header("Location: provider_requests.php");
exit();
