<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection
require_once __DIR__ . "/db_connect.php";

$conn = createConnection();

// Only handle POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Collect and sanitize input
    $pet_name    = $_POST['pet_name'] ?? '';
    $pet_type    = $_POST['pet_type'] ?? '';
    $breed       = $_POST['breed'] ?? '';
    $age         = $_POST['age'] ?? '';
    $gender      = $_POST['gender'] ?? '';
    $size        = $_POST['size'] ?? '';
    $description = $_POST['description'] ?? '';
    $image_url   = $_POST['image_url'] ?? '';
    $provider_id = $_POST['provider_id'] ?? '';

    // Basic validation
    if (empty($pet_name) || empty($pet_type) || empty($age) || empty($provider_id)) {
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
