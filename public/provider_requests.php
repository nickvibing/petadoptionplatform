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
</head>
<body>
<h1>Adoption Requests</h1>

<?php if ($result->num_rows === 0): ?>
    <p>No adoption requests yet.</p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Pet</th>
            <th>Applicant</th>
            <th>Message</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>Actions</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['pet_name']) ?></td>
            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
            <td><?= ucfirst($row['status']) ?></td>
            <td><?= date("F j, Y", strtotime($row['created_at'])) ?></td>
            <td>
                <?php if ($row['status'] === "pending"): ?>
                    <a href="process_application.php?id=<?= $row['application_id'] ?>&action=approve">Approve</a> |
                    <a href="process_application.php?id=<?= $row['application_id'] ?>&action=reject">Reject</a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php endif; ?>
</body>
</html>
