<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require_once '../includes/db_connect.php';

requireLogin();

// Only providers can use this
if ($_SESSION['user_role'] !== 'provider') {
    die("Unauthorized");
}

$providerId = getProviderId();
$conn = createConnection();

$sql = "
    SELECT a.application_id, a.status, a.message, 
           a.created_at,
           u.first_name, u.last_name, 
           p.pet_name
    FROM adoption_applications a
    JOIN pets p ON p.pet_id = a.pet_id
    JOIN users u ON u.user_id = a.user_id
    WHERE p.provider_id = ?
    ORDER BY a.created_at DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $providerId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Adoption Requests</title>

    <style>
        /* --- HERO HEADER --- */
        .hero-section {
            background: linear-gradient(135deg, rgba(189,36,64,1) 0%, rgba(235,160,176,1) 50%, rgba(232,237,83,1) 100%);
            color: white;
            padding: 50px 20px;
            text-align: center;
            margin-bottom: 40px;
        }

        .hero-section h1 {
            font-size: 42px;
            margin: 0;
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

        .message-box {
            background: #fafafa;
            padding: 12px;
            border-radius: 8px;
            font-size: 15px;
            margin-bottom: 18px;
            border: 1px solid #e0e0e0;
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

        /* --- BUTTONS --- */
        .actions {
            margin-top: 10px;
        }

        .action-btn {
            padding: 10px 18px;
            font-size: 14px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s;
            margin-right: 10px;
            display: inline-block;
        }

        .approve-btn {
            background: #2ecc71;
            color: white;
        }

        .approve-btn:hover {
            background: #27ae60;
        }

        .reject-btn {
            background: #e74c3c;
            color: white;
        }

        .reject-btn:hover {
            background: #c0392b;
        }
    </style>
</head>

<body>

<!-- HEADER -->
<div class="hero-section">
    <h1>Adoption Requests</h1>
</div>

<!-- CONTENT -->
<div class="requests-container">

<?php if ($result->num_rows === 0): ?>
    <p style="font-size: 20px; text-align:center;">No adoption requests yet.</p>

<?php else: ?>
    <?php while ($row = $result->fetch_assoc()): ?>

        <div class="request-card">
            <h3><?= htmlspecialchars($row['pet_name']) ?></h3>

            <div class="request-meta">
                Applicant: <strong><?= htmlspecialchars($row['first_name'] . " " . $row['last_name']) ?></strong><br>
                Submitted: <?= date("F j, Y", strtotime($row['created_at'])) ?>
            </div>

            <div class="status-badge 
                <?= $row['status'] === 'pending' ? 'status-pending' : ($row['status'] === 'approved' ? 'status-approved' : 'status-rejected') ?>">
                <?= ucfirst($row['status']) ?>
            </div>

            <div class="message-box">
                <?= nl2br(htmlspecialchars($row['message'])) ?>
            </div>

            <div class="actions">
                <?php if ($row['status'] === "pending"): ?>
                    <a class="action-btn approve-btn"
                       href="process_application.php?id=<?= $row['application_id'] ?>&action=approve">
                        Approve
                    </a>

                    <a class="action-btn reject-btn"
                       href="process_application.php?id=<?= $row['application_id'] ?>&action=reject">
                        Reject
                    </a>
                <?php endif; ?>
            </div>
        </div>

    <?php endwhile; ?>
<?php endif; ?>

</div>

</body>
</html>

