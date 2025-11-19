<?php
require_once __DIR__ . "/db_connect.php";
$conn = createConnection();

$type = $_GET['type'] ?? 'dog';

$stmt = $conn->prepare("SELECT * FROM pets WHERE pet_type = ? AND is_available = 1");
$stmt->bind_param("s", $type);
$stmt->execute();
$result = $stmt->get_result();

$pets = [];
while ($row = $result->fetch_assoc()) {
    $pets[] = $row;
}

echo json_encode($pets);
closeConnection($conn);
?>
