<?php
require_once  "../includes/config.php";
require_once  "../includes/auth.php";
require_once  "../includes/db_connect.php";

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$pet_id = intval($_GET['pet_id']);
$conn = createConnection();

// Fetch pet info
$stmt = $conn->prepare("SELECT pet_name FROM pets WHERE pet_id = ?");
$stmt->bind_param("i", $pet_id);
$stmt->execute();
$pet = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $userId = getUserId();
    $message = $_POST['message'];

    $stmt = $conn->prepare(
        "INSERT INTO adoption_applications (pet_id, user_id, message)
         VALUES (?, ?, ?)"
    );
    $stmt->bind_param("iis", $pet_id, $userId, $message);
    $stmt->execute();

    header("Location: dashboard.php?submitted=1");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Adopt <?php echo $pet['pet_name']; ?></title>
</head>
<body>
<h1>Adoption Application for <?php echo $pet['pet_name']; ?></h1>

<form method="POST">
    <label>Message to provider:</label><br>
    <textarea name="message" required></textarea><br><br>

    <button type="submit">Submit Application</button>
</form>

</body>
</html>
