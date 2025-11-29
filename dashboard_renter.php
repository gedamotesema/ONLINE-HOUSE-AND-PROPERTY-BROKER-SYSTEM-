<?php
// dashboard_renter.php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_login();
if (!is_renter()) {
    header("Location: index.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Fetch favorites
$stmt = $pdo->prepare("SELECT f.*, p.title, p.location, p.price, p.type FROM favorites f JOIN properties p ON f.property_id = p.id WHERE f.user_id=?");
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll();

// Fetch inquiries with owner name
$stmt2 = $pdo->prepare("
    SELECT i.*, p.title, u.username AS owner_name 
    FROM inquiries i 
    JOIN properties p ON i.property_id = p.id 
    JOIN users u ON p.owner_id = u.id 
    WHERE i.sender_id = ? 
    ORDER BY i.created_at DESC
");
$stmt2->execute([$user_id]);
$inquiries = $stmt2->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Renter Dashboard - Rental Broker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .inquiry-card { background:white; border-radius:16px; box-shadow:0 8px 25px rgba(0,0,0,0.1); margin-bottom:1.5rem; overflow:hidden; }
        .inquiry-header { background:#3b82f6; color:white; padding:1rem 1.5rem; }
        .chat-container { max-height:400px; overflow-y:auto; padding:1rem; background:#f8f9fa; border-radius:0 0 16px 16px; }
        .chat-bubble { max-width:80%; padding:10px 15px; margin:8px 0; border-radius:15px; font-size:0.95rem; }
        .msg-renter { background:#dbeafe; align-self:flex-end; border-radius:15px 15px 0 15px; }
        .msg-owner { background:#f3f4f6; align-self:flex-start; border-radius:15px 15px 15px 0; }
        .reply-form textarea { resize:none; }
    </style>
</head>
<body>
<?php include 'views/shared/header.php'; ?>

<main class="container py-5">
    <h2 class="mb-4 display-6">Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>

    <div class="row g-5">
        <!-- My Favorites -->
        <div class="col-lg-5">
            <h4 class="mb-3"><i class="fas fa-heart text-danger"></i> My Favorite Properties</h4>
            <?php if ($favorites): ?>
                <div class="list-group">
                    <?php foreach ($favorites as $fav): ?>
                        <a href="property.php?id=<?= $fav['property_id'] ?>" class="list-group-item list-group-item-action p-3">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <h6 class="mb-1 fw-bold"><?= htmlspecialchars($fav['title']) ?></h6>
                                <span class="badge bg-success fs-6">$<?= number_format($fav['price']) ?></span>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($fav['location']) ?> 
                                • <?= ucfirst($fav['type']) ?>
                            </small>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 bg-light rounded border">
                    <i class="fas fa-heart fa-4x text-muted mb-3"></i>
                    <p class="text-muted">You haven't saved any properties yet.</p>
                    <a href="index.php" class="btn btn-outline-primary">Browse Properties</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Conversations with Owners -->
        <div class="col-lg-7">
            <h4 class="mb-4"><i class="fas fa-comments text-primary"></i> My Conversations</h4>

            <?php if ($inquiries): ?>
                <?php foreach ($inquiries as $inq): ?>
                    <?php
                    // FIXED: Correct UNION query with proper column names
                    $conv_stmt = $pdo->prepare("
                        SELECT 
                            id AS inquiry_id,
                            'renter' AS sender_type, 
                            message AS text, 
                            created_at 
                        FROM inquiries 
                        WHERE id = ?
                        
                        UNION ALL
                        
                        SELECT 
                            inquiry_id,
                            'owner' AS sender_type, 
                            reply AS text, 
                            created_at 
                        FROM inquiry_replies 
                        WHERE inquiry_id = ?
                        
                        ORDER BY created_at ASC
                    ");
                    $conv_stmt->execute([$inq['id'], $inq['id']]);
                    $conversation = $conv_stmt->fetchAll();
                    ?>

                    <div class="inquiry-card">
                        <div class="inquiry-header d-flex justify-content-between align-items-center">
                            <strong><?= htmlspecialchars($inq['title']) ?></strong>
                            <span class="badge <?= $inq['status'] === 'pending' ? 'bg-warning' : 'bg-success' ?> text-white fs-6">
                                <?= ucfirst($inq['status']) ?>
                            </span>
                        </div>

                        <div class="p-3 pb-0">
                            <small class="text-muted d-block mb-3">
                                <i class="fas fa-user"></i> Owner: <strong><?= htmlspecialchars($inq['owner_name']) ?></strong>
                                • Started <?= date('M j, Y \a\t g:i A', strtotime($inq['created_at'])) ?>
                            </small>

                            <!-- Chat Messages -->
                            <div class="chat-container">
                                <?php foreach ($conversation as $msg): ?>
                                    <div class="d-flex <?= $msg['sender_type'] === 'renter' ? 'justify-content-end' : 'justify-content-start' ?> mb-3">
                                        <div class="chat-bubble <?= $msg['sender_type'] === 'renter' ? 'msg-renter' : 'msg-owner' ?>">
                                            <small class="d-block fw-bold text-primary mb-1">
                                                <?= $msg['sender_type'] === 'renter' ? 'You' : 'Owner' ?>
                                            </small>
                                            <small class="text-muted d-block mb-1" style="font-size:0.75rem;">
                                                <?= date('g:i A - M j', strtotime($msg['created_at'])) ?>
                                            </small>
                                            <?= nl2br(htmlspecialchars($msg['text'])) ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Reply Form -->
                            <form method="post" action="renter_reply.php" class="reply-form p-3 border-top bg-white">
                                <input type="hidden" name="inquiry_id" value="<?= $inq['id'] ?>">
                                <div class="input-group">
                                    <textarea name="message" class="form-control" rows="2" 
                                              placeholder="Type your reply..." required></textarea>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-paper-plane"></i> Send
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-5 bg-light rounded border">
                    <i class="fas fa-comments fa-4x text-muted mb-4"></i>
                    <h5>No conversations yet</h5>
                    <p class="text-muted">Send an inquiry from a property page to start chatting with the owner.</p>
                    <a href="index.php" class="btn btn-primary mt-3">Find Properties</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'views/shared/footer.php'; ?>
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>