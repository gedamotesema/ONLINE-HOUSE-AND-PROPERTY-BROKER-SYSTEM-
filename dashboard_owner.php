<?php
// dashboard_owner.php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_login();
if (!is_owner()) {
    header("Location: index.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Fetch owner's properties
$stmt = $pdo->prepare("SELECT * FROM properties WHERE owner_id=? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$properties = $stmt->fetchAll();

// Fetch inquiries for owner's properties
$stmt2 = $pdo->prepare("SELECT i.*, p.title, u.username AS sender FROM inquiries i
    JOIN properties p ON i.property_id = p.id
    JOIN users u ON i.sender_id = u.id
    WHERE i.owner_id=? ORDER BY i.created_at DESC");
$stmt2->execute([$user_id]);
$inquiries = $stmt2->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owner Dashboard - Rental Broker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include 'views/shared/header.php'; ?>

<main class="container py-4">
    <h2 class="mb-4">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
    
    <div class="row">
        <!-- My Properties -->
        <div class="col-md-8">
            <h4 class="mb-3">My Properties</h4>
            <a href="add_property.php" class="btn btn-success mb-3">Add New Property</a>
            
            <?php if ($properties): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Location</th>
                                <th>Price</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($properties as $prop): ?>
                            <tr>
                                <td><?= htmlspecialchars($prop['title']) ?></td>
                                <td><?= htmlspecialchars($prop['location']) ?></td>
                                <td>$<?= number_format($prop['price'], 2) ?></td>
                                <td><?= ucfirst($prop['type']) ?></td>
                                <td>
                                    <span class="badge <?= $prop['availability'] === 'available' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ucfirst($prop['availability']) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit_property.php?id=<?= $prop['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="delete_property.php?id=<?= $prop['id'] ?>" class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Are you sure you want to delete this property?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> You haven't added any properties yet. 
                    <a href="add_property.php" class="alert-link">Click here to add your first one!</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Inquiries from Renters -->
        <div class="col-md-4">
            <h4 class="mb-3">
                Inquiries from Renters 
                <span class="badge bg-primary fs-6"><?= count($inquiries) ?></span>
            </h4>

            <?php if ($inquiries): ?>
                <div class="list-group" style="max-height: 750px; overflow-y: auto;">
                    <?php foreach ($inquiries as $inq): ?>
                        <?php
                        $reply_stmt = $pdo->prepare("SELECT reply, created_at FROM inquiry_replies WHERE inquiry_id = ? ORDER BY created_at DESC LIMIT 5");
                        $reply_stmt->execute([$inq['id']]);
                        $replies = $reply_stmt->fetchAll();
                        ?>

                        <div class="list-group-item mb-3 border rounded shadow-sm">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <strong class="text-primary"><?= htmlspecialchars($inq['title']) ?></strong>
                                <span class="badge <?= $inq['status'] === 'pending' ? 'bg-warning' : 'bg-success' ?> text-white">
                                    <?= ucfirst($inq['status']) ?>
                                </span>
                            </div>

                            <small class="text-muted d-block mb-2">
                                From: <strong><?= htmlspecialchars($inq['sender']) ?></strong><br>
                                <?= date('M j, Y \a\t g:i A', strtotime($inq['created_at'])) ?>
                            </small>

                            <p class="bg-light p-3 rounded mb-2" style="white-space: pre-wrap; font-size:0.95rem;">
                                <?= nl2br(htmlspecialchars($inq['message'])) ?>
                            </p>

                            <!-- Previous replies -->
                            <?php if ($replies): ?>
                                <details class="mb-2">
                                    <summary class="text-primary" style="cursor:pointer; font-size:0.9rem;">
                                        <i class="fas fa-reply"></i> View your <?= count($replies) ?> reply(ies)
                                    </summary>
                                    <?php foreach ($replies as $r): ?>
                                        <div class="bg-primary text-white p-2 rounded mt-2" style="font-size:0.9rem;">
                                            <small><?= date('M j, Y g:i A', strtotime($r['created_at'])) ?></small><br>
                                            <?= nl2br(htmlspecialchars($r['reply'])) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </details>
                            <?php endif; ?>

                            <!-- Reply Form -->
                            <form method="post" action="owner_reply.php" class="mt-3">
                                <input type="hidden" name="inquiry_id" value="<?= $inq['id'] ?>">
                                <textarea name="reply" class="form-control form-control-sm mb-2" rows="2" 
                                          placeholder="Type your reply here..." required 
                                          <?= $inq['status'] === 'replied' ? 'disabled' : '' ?>></textarea>
                                <button type="submit" class="btn btn-primary btn-sm w-100" 
                                        <?= $inq['status'] === 'replied' ? 'disabled' : '' ?>>
                                    <i class="fas fa-paper-plane"></i> 
                                    <?= $inq['status'] === 'replied' ? 'Already Replied' : 'Send Reply' ?>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 bg-light rounded border">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No inquiries yet.<br>When renters message you, they'll appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'views/shared/footer.php'; ?>
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>