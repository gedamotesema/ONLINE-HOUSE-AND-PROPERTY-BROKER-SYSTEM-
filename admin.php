<?php
// admin.php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_login();
if (!is_admin()) {
    header("Location: index.php");
    exit;
}

// Fetch all users
$stmt_users = $pdo->query("SELECT id, username, email, role, status, created_at FROM users ORDER BY created_at DESC");
$users = $stmt_users->fetchAll();

// Fetch all properties
$stmt_properties = $pdo->query("SELECT p.*, u.username as owner FROM properties p LEFT JOIN users u ON p.owner_id = u.id ORDER BY p.created_at DESC");
$properties = $stmt_properties->fetchAll();

// Fetch all inquiries
$stmt_inq = $pdo->query("SELECT i.*, p.title as property_title, s.username as sender, o.username as owner
    FROM inquiries i
    JOIN properties p ON i.property_id = p.id
    JOIN users s ON i.sender_id = s.id
    JOIN users o ON i.owner_id = o.id
    ORDER BY i.created_at DESC");
$inquiries = $stmt_inq->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Rental Broker</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<?php include 'views/shared/header.php'; ?>
<main class="container my-4">
    <h2>Admin Dashboard</h2>
    <div class="row">
        <div class="col-lg-4">
            <h4>Users</h4>
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-secondary">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['role']) ?></td>
                        <td><?= htmlspecialchars($u['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-lg-4">
            <h4>Properties</h4>
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-secondary">
                    <tr>
                        <th>Title</th>
                        <th>Owner</th>
                        <th>Location</th>
                        <th>Price</th>
                        <th>Type</th>
                        <th>Available</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($properties as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['title']) ?></td>
                        <td><?= htmlspecialchars($p['owner']) ?></td>
                        <td><?= htmlspecialchars($p['location']) ?></td>
                        <td>$<?= number_format($p['price'],2) ?></td>
                        <td><?= htmlspecialchars($p['type']) ?></td>
                        <td><?= htmlspecialchars($p['availability']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-lg-4">
            <h4>Inquiries</h4>
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-secondary">
                    <tr>
                        <th>ID</th>
                        <th>Property</th>
                        <th>Sender</th>
                        <th>Owner</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($inquiries as $i): ?>
                    <tr>
                        <td><?= $i['id'] ?></td>
                        <td><?= htmlspecialchars($i['property_title']) ?></td>
                        <td><?= htmlspecialchars($i['sender']) ?></td>
                        <td><?= htmlspecialchars($i['owner']) ?></td>
                        <td><?= htmlspecialchars($i['status']) ?></td>
                        <td><?= htmlspecialchars($i['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php include 'views/shared/footer.php'; ?>
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
