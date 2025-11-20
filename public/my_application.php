<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db_connect.php';

requireLogin();

$userId = getUserId();
$conn = createConnection();

$sql = "
    SELECT a.application_id, a.status, a.created_at, 
           p.pet_name, p.pet_type
    FROM adoption_applications a
    JOIN pets p ON p.pet_id = a.pet_id
    WHERE a.user_id = ?
    ORDER BY a.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Applications</title>

    <style>
        /* --- HERO HEADER --- */
        .hero-section {
            background: linear-gradient(135deg, rgba(189,36,64,1) 0%, rgba(235,160,176,1) 50%, rgba(232,237,83,1) 100%);
            color: white;
            padding: 50px 20px;
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .hero-section h1 {
            font-size: 42px;
            margin: 0;
        }

        .back-btn {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
        }

        .back-btn:hover {
            background: rgba(255,255,255,0.3);
        }

        /* --- MAIN CONTENT --- */
        .requests-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 10px 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 25px;
        }

        /* --- REQUEST CARD --- */
        .request-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-left: 6px solid #bd2440;
        }

        .request-card h3 {
            font-size: 22px;
            margin-bottom: 10px;
        }

        .request-meta {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
            display: inline-block;
            margin-bottom: 15px;
        }

        .status-pending { background: #ffebcc; color: #a66b00; }
        .status-approved { background: #d4f7d4; color: #2e7d32; }
        .status-rejected { background: #ffd6d6; color: #b71c1c; }

        /* Reset body margins */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        /* Empty state message */
        .empty-message {
            font-size: 20px;
            text-align: center;
            color: #666;
            grid-column: 1 / -1;
        }
    </style>
</head>

<body>

<!-- HEADER -->
<div class="hero-section">
    <a href="dashboard.php" class="back-btn">← Dashboard</a>
    <h1>My Adoption Requests</h1>
</div>

<!-- CONTENT -->
<div class="requests-container">

<?php if ($result->num_rows === 0): ?>
    <p class="empty-message">You have not submitted any applications yet.</p>

<?php else: ?>
    <?php while ($row = $result->fetch_assoc()): ?>

        <div class="request-card">
            <h3><?= htmlspecialchars($row['pet_name']) ?></h3>

            <div class="request-meta">
                Type: <strong><?= ucfirst(htmlspecialchars($row['pet_type'])) ?></strong><br>
                Submitted: <?= date("F j, Y", strtotime($row['created_at'])) ?>
            </div>

            <div class="status-badge 
                <?= $row['status'] === 'pending' ? 'status-pending' : ($row['status'] === 'approved' ? 'status-approved' : 'status-rejected') ?>">
                <?= ucfirst($row['status']) ?>
            </div>
        </div>

    <?php endwhile; ?>
<?php endif; ?>

</div>

</body>
</html>