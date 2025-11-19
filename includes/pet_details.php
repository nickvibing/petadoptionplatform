<?php
require_once __DIR__ . "/db_connect.php";
$conn = createConnection();

$pet_id = intval($_GET['pet_id']);

$stmt = $conn->prepare("SELECT * FROM pets WHERE pet_id = ?");
$stmt->bind_param("i", $pet_id);
$stmt->execute();
$result = $stmt->get_result();

$pet = $result->fetch_assoc();
echo json_encode($pet);

closeConnection($conn);
?>
