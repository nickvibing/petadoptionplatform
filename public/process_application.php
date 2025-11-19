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
$providerId = getProviderId();

if (!$providerId) {
    die("Provider ID not found in session");
}

$conn = createConnection();

if ($action === "approve") {
    $status = "approved";
} elseif ($action === "reject") {
    $status = "rejected";
} else {
    die("Invalid action");
}

// Security check: Verify that this application belongs to a pet owned by this provider
$checkStmt = $conn->prepare("
    SELECT a.application_id
    FROM adoption_applications a
    JOIN pets p ON p.pet_id = a.pet_id
    WHERE a.application_id = ? AND p.provider_id = ?
");
$checkStmt->bind_param("ii", $appId, $providerId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    die("Unauthorized: This application does not belong to your pets");
}
$checkStmt->close();

// Update the application status
$stmt = $conn->prepare("UPDATE adoption_applications SET status=? WHERE application_id=?");
$stmt->bind_param("si", $status, $appId);
$stmt->execute();

header("Location: provider_requests.php");
exit();
