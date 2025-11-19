<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include required files
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/db_connect.php";

// Ensure user is logged in and is a provider
requireLogin();
if ($_SESSION['user_role'] !== 'provider') {
    header("Location: ../public/dashboard.php?status=error&message=Unauthorized");
    exit();
}

// Get provider_id from session (set during login)
$provider_id = getProviderId();

if (!$provider_id) {
    header("Location: ../public/dashboard.php?status=error&message=Provider+ID+not+found.+Please+logout+and+login+again");
    exit();
}

$conn = createConnection();

// Only handle POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Collect and sanitize input
    $pet_name    = trim($_POST['pet_name'] ?? '');
    $pet_type    = $_POST['pet_type'] ?? '';
    $breed       = trim($_POST['breed'] ?? '');
    $age         = intval($_POST['age'] ?? 0);
    $gender      = $_POST['gender'] ?? '';
    $size        = $_POST['size'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $image_url   = trim($_POST['image_url'] ?? '');

    // Basic validation
    if (empty($pet_name) || empty($pet_type) || $age <= 0) {
        header("Location: ../public/dashboard.php?status=error&message=Please+fill+all+required+fields");
        exit();
    }

    // Prepare SQL statement

    $stmt = $conn->prepare("INSERT INTO pets (pet_name, pet_type, breed, age, gender, size, description, image_url, provider_id)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Check if prepare() succeeded
    if (!$stmt) {
        header("Location: ../public/dashboard.php?status=error&message=Failed+to+prepare+statement");
        exit();
    }

    // Bind parameters (s = string, i = integer)
    $stmt->bind_param(
        "sssissssi",
        $pet_name,
        $pet_type,
        $breed,
        $age,
        $gender,
        $size,
        $description,
        $image_url,
        $provider_id
    );

    // Execute statement and redirect based on result
    if ($stmt->execute()) {
        header("Location: ../public/dashboard.php?status=success&message=Pet+added+successfully");
    } else {
        header("Location: ../public/dashboard.php?status=error&message=Failed+to+add+pet");
    }

    $stmt->close();
    $conn->close();
} else {
    // Block non-POST requests
    header("Location: ../public/dashboard.php?status=error&message=Invalid+request");
    exit();
}

?>
