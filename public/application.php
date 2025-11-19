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
    <style>
        
        .hero-section {
            background: #bd2440;
            background: linear-gradient(135deg, rgba(189,36,64,1) 0%, rgba(235,160,176,1) 50%, rgba(232,237,83,1) 100%);
            color: white;
            padding: 60px 20px;
            text-align: center;
            margin-bottom: 40px;
        }

        .hero-section h1 {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .hero-section p {
            font-size: 20px;
            opacity: 0.9;
        }

        /* --- FORM CONTAINER --- */
        .form-container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .form-container label {
            font-size: 18px;
            font-weight: 600;
        }

        .form-container textarea {
            width: 100%;
            height: 150px;
            margin-top: 10px;
            padding: 15px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        .submit-btn {
            margin-top: 20px;
            width: 100%;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            background: #bd2440;
            color: white;
            transition: transform 0.2s, background 0.2s;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            background: #a81f38;
        }
    </style>
</head>
<body>
<div class="hero-section">
    <h1>Apply to Adopt <?php echo $pet['pet_name']; ?></h1>
    <p>Send a message to the pet provider about why you're the perfect match!</p>
</div>

<!-- APPLICATION FORM -->
<div class="form-container">
    <form method="POST">
        <label>Message to Provider:</label>
        <textarea name="message" required></textarea>

        <button type="submit" class="submit-btn">Submit Application</button>
    </form>
</div>

</body>
</html>
