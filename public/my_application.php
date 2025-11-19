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
</head>
<body>
    <h1>My Adoption Applications</h1>

    <?php if ($result->num_rows === 0): ?>
        <p>You have not submitted any applications yet.</p>
    <?php else: ?>
        <table border="1" cellpadding="8">
            <tr>
                <th>Pet</th>
                <th>Type</th>
                <th>Status</th>
                <th>Submitted</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['pet_name']) ?></td>
                <td><?= ucfirst($row['pet_type']) ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td><?= date("F j, Y", strtotime($row['created_at'])) ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>

</body>
</html>
