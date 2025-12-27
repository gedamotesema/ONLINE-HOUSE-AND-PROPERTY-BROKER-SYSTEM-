<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$prop_id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT p.*, u.name as landlord_name, u.email as landlord_email, u.profile_picture FROM properties p JOIN users u ON p.landlord_id = u.id WHERE p.id = ?");
$stmt->execute([$prop_id]);
$property = $stmt->fetch();

if (!$property) {
    die("Property not found.");
}

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_viewing') {
    if (isLoggedIn() && $_SESSION['user_role'] === 'tenant') {
        $date = $_POST['viewing_date'];
        $time = $_POST['viewing_time'];
        $message = sanitize($_POST['message']);

        if ($date && $time) {
            $datetime = $date . ' ' . $time;
            try {
                $check = $pdo->prepare("SELECT id FROM viewing_requests WHERE property_id = ? AND tenant_id = ? AND status = 'pending'");
                $check->execute([$prop_id, $_SESSION['user_id']]);

                if ($check->rowCount() > 0) {
                    $error_msg = "You already have a pending request for this property.";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO viewing_requests (property_id, tenant_id, preferred_date, message) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$prop_id, $_SESSION['user_id'], $datetime, $message]);
                    $success_msg = "Viewing request submitted successfully!";
                }
            } catch (PDOException $e) {
                $error_msg = "Request failed: " . $e->getMessage();
            }
        } else {
            $error_msg = "Please select a date and time.";
        }
    } else {
        $error_msg = "You must be logged in as a tenant to request a viewing.";
    }
}

if (isLoggedIn() && isset($_POST['toggle_favorite'])) {
    $uid = $_SESSION['user_id'];
    $check = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND property_id = ?");
    $check->execute([$uid, $prop_id]);
    if ($check->fetch()) {
        $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND property_id = ?")->execute([$uid, $prop_id]);
        $fav_msg = "Removed from favorites.";
    } else {
        $pdo->prepare("INSERT INTO favorites (user_id, property_id) VALUES (?, ?)")->execute([$uid, $prop_id]);
        $fav_msg = "Added to favorites.";
    }
    flash('prop_msg', $fav_msg);
    redirect("property_details.php?id=$prop_id");
}

if (isLoggedIn() && isset($_POST['start_chat'])) {
    $uid = $_SESSION['user_id'];
    $lid = $property['landlord_id'];

    if ($uid == $lid) {
        flash('prop_msg', "You cannot chat with yourself!", 'error');
    } else {
        $stmtCheck = $pdo->prepare("SELECT id FROM conversations WHERE property_id = ? AND user1_id = ? AND user2_id = ?");
        $stmtCheck->execute([$prop_id, $uid, $lid]);
        $conv = $stmtCheck->fetch();

        if ($conv) {
            redirect("messages.php?conversation_id=" . $conv['id']);
        } else {
            $pdo->prepare("INSERT INTO conversations (property_id, user1_id, user2_id) VALUES (?, ?, ?)")->execute([$prop_id, $uid, $lid]);
            $new_conv_id = $pdo->lastInsertId();
            redirect("messages.php?conversation_id=" . $new_conv_id);
        }
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="container" style="margin-top: 2rem;">
    <?php echo flash('prop_msg'); ?>

    <div class="row" style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">

        <div>
            <?php
            $images = json_decode($property['images'], true);
            if (!is_array($images) || empty($images)) {
                $images = ['https://via.placeholder.com/1200x600?text=No+Image'];
            }
            $main_img = $images[0];
            ?>

            <div id="mainImage"
                style="height: 400px; border-radius: 16px; background-image: url('<?php echo htmlspecialchars($main_img); ?>'); background-size: cover; background-position: center; margin-bottom: 1rem; transition: background-image 0.3s ease-in-out;">
            </div>

            <?php if (count($images) > 1): ?>
                <div style="display: flex; gap: 10px; margin-bottom: 2rem; overflow-x: auto; padding-bottom: 5px;">
                    <?php foreach ($images as $img): ?>
                        <div onclick="changeImage('<?php echo htmlspecialchars($img); ?>')"
                            style="min-width: 80px; height: 60px; border-radius: 8px; background-image: url('<?php echo htmlspecialchars($img); ?>'); background-size: cover; background-position: center; cursor: pointer; border: 2px solid transparent; transition: all 0.2s;"
                            onmouseover="this.style.borderColor='var(--accent-color)'"
                            onmouseout="this.style.borderColor='transparent'">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="margin-bottom: 2rem;"></div>
            <?php endif; ?>

            <script>
                function changeImage(url) {
                    document.getElementById('mainImage').style.backgroundImage = "url('" + url + "')";
                }
            </script>

            <div class="glass-panel" style="padding: 2rem;">
                <h1><?php echo htmlspecialchars($property['title']); ?></h1>
                <p class="price" style="font-size: 1.5rem; color: var(--accent-color); margin: 0.5rem 0;">
                    <?php echo formatPrice($property['price']); ?>/mo
                </p>
                <p style="color: var(--text-secondary); margin-bottom: 1.5rem;"><i class="fas fa-map-marker-alt"></i>
                    <?php echo htmlspecialchars($property['location']); ?></p>

                <h3>Description</h3>
                <p style="line-height: 1.8; color: #ddd;">
                    <?php echo nl2br(htmlspecialchars($property['description'])); ?>
                </p>
            </div>
        </div>

        <div>
            <div class="glass-panel" style="padding: 2rem; position: sticky; top: 120px;">
                <h3>Landlord</h3>
                <div style="display: flex; align-items: center; gap: 15px; margin: 1rem 0;">
                    <?php
                    $landlord_avatar = !empty($property['profile_picture']) ? $property['profile_picture'] : null;
                    ?>

                    <?php if ($landlord_avatar): ?>
                        <div
                            style="width: 50px; height: 50px; border-radius: 50%; overflow: hidden; border: 2px solid var(--accent-color);">
                            <img src="<?php echo htmlspecialchars($landlord_avatar); ?>"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    <?php else: ?>
                        <div
                            style="width: 50px; height: 50px; border-radius: 50%; background: #333; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; font-weight: bold; border: 2px solid var(--card-border);">
                            <?php echo strtoupper(substr($property['landlord_name'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <p style="font-weight: bold;"><?php echo htmlspecialchars($property['landlord_name']); ?></p>
                        <p style="font-size: 0.9rem; color: var(--text-secondary);">Verified Landlord</p>
                    </div>
                </div>

                <?php if (isLoggedIn() && $_SESSION['user_role'] === 'tenant'): ?>
                    <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1);">
                        <h3>Schedule a Viewing</h3>
                        <?php if ($success_msg)
                            echo "<div class='alert alert-success'>$success_msg</div>"; ?>
                        <?php if ($error_msg)
                            echo "<div class='alert alert-error'>$error_msg</div>"; ?>

                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                            <input type="hidden" name="action" value="request_viewing">

                            <div class="form-group">
                                <label>Preferred Date</label>
                                <input type="date" name="viewing_date" required min="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <div class="form-group">
                                <label>Preferred Time</label>
                                <input type="time" name="viewing_time" required>
                            </div>

                            <div class="form-group">
                                <label>Message (Optional)</label>
                                <textarea name="message" rows="2"
                                    placeholder="Hi, I'm interested in viewing this property..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary" style="width: 100%;">Request Viewing</button>
                        </form>
                    </div>
                <?php endif; ?>

                <?php if (isLoggedIn()): ?>
                    <a href="messages.php?conversation_id=new&property_id=<?php echo $property['id']; ?>&recipient_id=<?php echo $property['landlord_id']; ?>"
                        class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                        <i class="fas fa-comment"></i> Message Landlord
                    </a>

                    <form method="POST" style="margin-top: 1rem;">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <button type="submit" name="toggle_favorite" class="btn btn-outline" style="width: 100%;">
                            <i class="far fa-heart"></i> Add to Favorites
                        </button>
                    </form>
                <?php else: ?>
                    <div class="alert">Please <a href="login.php">Login</a> to contact the landlord.</div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>