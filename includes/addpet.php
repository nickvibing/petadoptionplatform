<?php

require_once __DIR__ . "/db_connect.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pet_name = $_POST['pet_name'];
    $pet_type = $_POST['pet_type'];
    $breed = $_POST['breed'];
    $age  = $_POST['age'];
    $gender = $_POST['gender'];
    $size  = $_POST['size'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];
    $provider_id = $_POST['provider_id'];

    $sql = "INSERT INTO pets
        (pet_name, pet_type, breed, age, gender, size, description, image_url, provider_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
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

    if ($stmt->execute()) {
        header("Location: ../public/dashboard.php?status=success&message=Pet+Added");
        exit();
    } else {
        header("Location: ../public/dashboard.php?status=error&message=Failed+to+add+pet");
        exit();
    }
}

?>