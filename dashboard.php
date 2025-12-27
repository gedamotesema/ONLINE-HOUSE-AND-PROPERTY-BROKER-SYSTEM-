<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

if (isAdmin()) {
    redirect('admin_dashboard.php');
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];
?>
<?php require_once 'includes/header.php'; ?>

<div class="row">
    <h1 style="border-bottom: 2px solid var(--accent-color); display: inline-block; padding-bottom: 10px;">Dashboard
    </h1>
    <div style="display: flex; align-items: center; gap: 1rem; margin-top: 1.5rem;">
        <?php if (!empty($_SESSION['user_avatar'])): ?>
            <img src="<?php echo htmlspecialchars($_SESSION['user_avatar']); ?>"
                style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid var(--accent-color);">
        <?php endif; ?>
        <div>
            <p style="font-size: 1.1rem; margin: 0;">Welcome back,</p>
            <p style="font-size: 2rem; font-weight: bold; margin: 0; line-height: 1.2; color: var(--accent-color);">
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </p>
            <p style="font-size: 0.9rem; margin: 0; opacity: 0.7;"><?php echo ucfirst($role); ?></p>
        </div>
    </div>

    <?php if ($role === 'landlord'): ?>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action_type'])) {
                if ($_POST['action_type'] === 'delete_property' || $_POST['action_type'] === 'toggle_status') {
                    $p_id = (int) $_POST['property_id'];
                    $check = $pdo->prepare("SELECT id FROM properties WHERE id = ? AND landlord_id = ?");
                    $check->execute([$p_id, $user_id]);

                    if ($check->fetch()) {
                        if ($_POST['action_type'] === 'delete_property') {
                            $pdo->prepare("DELETE FROM properties WHERE id = ?")->execute([$p_id]);
                            flash('dashboard_msg', "Property deleted successfully.");
                        } elseif ($_POST['action_type'] === 'toggle_status') {
                            $new_status = $_POST['new_status'];
                            if (in_array($new_status, ['available', 'rented'])) {
                                $pdo->prepare("UPDATE properties SET status = ? WHERE id = ?")->execute([$new_status, $p_id]);
                                flash('dashboard_msg', "Property status updated to " . ucfirst($new_status) . ".");
                            }
                        }
                    } else {
                        flash('dashboard_msg', "Access denied or property not found.", 'error');
                    }
                } elseif ($_POST['action_type'] === 'handle_request') {
                    $r_id = (int) $_POST['request_id'];
                    $status = $_POST['status'];
                    $check = $pdo->prepare("
                        SELECT vr.id
                        FROM viewing_requests vr
                        JOIN properties p ON vr.property_id = p.id
                        WHERE vr.id = ? AND p.landlord_id = ?
                    ");
                    $check->execute([$r_id, $user_id]);

                    if ($check->fetch()) {
                        $pdo->prepare("UPDATE viewing_requests SET status = ? WHERE id = ?")->execute([$status, $r_id]);
                        flash('dashboard_msg', "Request marked as " . ucfirst($status));
                    } else {
                        flash('dashboard_msg', "Access denied or request not found.", 'error');
                    }
                }
            }
            redirect('dashboard.php');
        }
        ?>

        <div class="mt-4">

            <?php
            $stmt = $pdo->prepare("
                SELECT vr.*, p.title, p.location, p.price, u.name as tenant_name, u.email as tenant_email, u.profile_picture as tenant_avatar
                FROM viewing_requests vr
                JOIN properties p ON vr.property_id = p.id
                JOIN users u ON vr.tenant_id = u.id
                WHERE p.landlord_id = ? AND vr.status = 'pending'
                ORDER BY vr.created_at DESC
            ");
            $stmt->execute([$user_id]);
            $requests = $stmt->fetchAll();
            ?>

            <?php if (count($requests) > 0): ?>
                <div class="glass-panel" style="margin-bottom: 2rem; border-left: 4px solid var(--accent-color);">
                    <h3 style="margin-bottom: 1rem;"><i class="fas fa-calendar-alt"></i> Pending Viewing Requests</h3>
                    <div style="display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                        <?php foreach ($requests as $req): ?>
                            <div style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px;">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 0.5rem;">
                                    <?php $avatar = !empty($req['tenant_avatar']) ? $req['tenant_avatar'] : 'https://via.placeholder.com/40?text=U'; ?>
                                    <img src="<?php echo htmlspecialchars($avatar); ?>"
                                        style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                    <div>
                                        <strong><?php echo htmlspecialchars($req['tenant_name']); ?></strong>
                                        <div style="font-size: 0.8rem; opacity: 0.7;">Interested in:
                                            <?php echo htmlspecialchars($req['title']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin: 0.5rem 0; font-size: 0.9rem;">
                                    <p><i class="fas fa-clock"></i> <strong>Preferred:</strong>
                                        <?php echo date('M j, Y g:i A', strtotime($req['preferred_date'])); ?></p>
                                    <?php if ($req['message']): ?>
                                        <p style="font-style: italic; opacity: 0.8;">"<?php echo htmlspecialchars($req['message']); ?>"
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div style="display: flex; gap: 0.5rem;">
                                    <form method="POST" style="flex: 1;">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                        <input type="hidden" name="action_type" value="handle_request">
                                        <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                        <input type="hidden" name="status" value="accepted">
                                        <button type="submit" class="btn btn-sm btn-primary" style="width: 100%;">Accept</button>
                                    </form>
                                    <form method="POST" style="flex: 1;">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                        <input type="hidden" name="action_type" value="handle_request">
                                        <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-sm btn-danger" style="width: 100%;">Reject</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2>My Properties</h2>
                <a href="property_manage.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Property</a>
            </div>

            <?php echo flash('dashboard_msg'); ?>

            <?php
            $stmt = $pdo->prepare("SELECT * FROM properties WHERE landlord_id = ? ORDER BY created_at DESC");
            $stmt->execute([$user_id]);
            $properties = $stmt->fetchAll();
            ?>

            <?php if (count($properties) > 0): ?>
                <div class="property-grid">
                    <?php foreach ($properties as $prop): ?>
                        <div class="property-card fade-in">
                            <?php
                            $images = json_decode($prop['images'], true);
                            if (!is_array($images) || empty($images)) {
                                $thumb = 'https://via.placeholder.com/800x600?text=No+Image';
                            } else {
                                $thumb = $images[0];
                            }
                            ?>
                            <div class="property-image" style="background-image: url('<?php echo htmlspecialchars($thumb); ?>');">
                                <span class="badge"
                                    style="position: absolute; top: 10px; right: 10px; background: <?php echo $prop['status'] === 'available' ? 'var(--success-color)' : 'var(--accent-color)'; ?>; color: black; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">
                                    <?php echo ucfirst($prop['status']); ?>
                                </span>
                            </div>
                            <div class="property-info">
                                <h3><?php echo htmlspecialchars($prop['title']); ?></h3>
                                <p class="price" style="font-size: 1.2rem; color: var(--accent-color); margin-bottom: 0.5rem;">
                                    <?php echo formatPrice($prop['price']); ?>/mo
                                </p>

                                <div class="property-actions"
                                    style="margin-top: 1.5rem; display: flex; flex-direction: column; gap: 0.5rem;">
                                    <a href="property_manage.php?id=<?php echo $prop['id']; ?>" class="btn btn-outline btn-sm"
                                        style="width: 100%;">Edit</a>

                                    <form method="POST" style="margin: 0;">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                        <input type="hidden" name="action_type" value="toggle_status">
                                        <input type="hidden" name="property_id" value="<?php echo $prop['id']; ?>">
                                        <?php if ($prop['status'] === 'available'): ?>
                                            <input type="hidden" name="new_status" value="rented">
                                            <button type="submit" class="btn btn-sm"
                                                style="width: 100%; background: rgba(255, 255, 255, 0.1); color: var(--text-primary); border: 1px solid var(--card-border);">Mark
                                                as Rented</button>
                                        <?php else: ?>
                                            <input type="hidden" name="new_status" value="available">
                                            <button type="submit" class="btn btn-sm"
                                                style="width: 100%; background: var(--success-color); color: black;">Mark as
                                                Available</button>
                                        <?php endif; ?>
                                    </form>

                                    <form method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this property? This cannot be undone.');"
                                        style="margin: 0;">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                        <input type="hidden" name="action_type" value="delete_property">
                                        <input type="hidden" name="property_id" value="<?php echo $prop['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" style="width: 100%;">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert">You haven't listed any properties yet.</div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <div class="mt-4">

            <?php
            $stmt = $pdo->prepare("
                SELECT vr.*, p.title, p.price, u.name as landlord_name
                FROM viewing_requests vr
                JOIN properties p ON vr.property_id = p.id
                JOIN users u ON p.landlord_id = u.id
                WHERE vr.tenant_id = ?
                ORDER BY vr.created_at DESC
            ");
            $stmt->execute([$user_id]);
            $my_requests = $stmt->fetchAll();
            ?>

            <?php if (count($my_requests) > 0): ?>
                <div class="glass-panel" style="margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem;">My Viewing Requests</h3>
                    <div style="display: grid; gap: 1rem; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
                        <?php foreach ($my_requests as $req): ?>
                            <div
                                style="background: rgba(255,255,255,0.05); padding: 1rem; border-radius: 8px; border-left: 3px solid <?php echo $req['status'] === 'accepted' ? 'var(--success-color)' : ($req['status'] === 'rejected' ? 'red' : 'orange'); ?>;">
                                <strong><?php echo htmlspecialchars($req['title']); ?></strong>
                                <p style="font-size: 0.9rem; margin: 0.5rem 0;">
                                    Requested: <?php echo date('M j, g:i A', strtotime($req['preferred_date'])); ?>
                                </p>
                                <span class="badge"
                                    style="background: <?php echo $req['status'] === 'accepted' ? 'var(--success-color)' : 'rgba(255,255,255,0.1)'; ?>; color: <?php echo $req['status'] === 'accepted' ? 'black' : 'white'; ?>;">
                                    <?php echo ucfirst($req['status']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <h2>My Favorites</h2>
            <?php
            $stmt = $pdo->prepare("
                SELECT p.*
                FROM favorites f
                JOIN properties p ON f.property_id = p.id
                WHERE f.user_id = ?
            ");
            $stmt->execute([$user_id]);
            $favorites = $stmt->fetchAll();
            ?>

            <?php if (count($favorites) > 0): ?>
                <div class="property-grid">
                    <?php foreach ($favorites as $prop): ?>
                        <div class="property-card fade-in">
                            <?php
                            $images = json_decode($prop['images'], true);
                            if (!is_array($images) || empty($images)) {
                                $thumb = 'https://via.placeholder.com/800x600?text=No+Image';
                            } else {
                                $thumb = $images[0];
                            }
                            ?>
                            <div class="property-image" style="background-image: url('<?php echo htmlspecialchars($thumb); ?>');">
                                <span class="badge"
                                    style="position: absolute; top: 10px; right: 10px; background: <?php echo $prop['status'] === 'available' ? 'var(--success-color)' : 'var(--accent-color)'; ?>; color: black; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">
                                    <?php echo ucfirst($prop['status']); ?>
                                </span>
                            </div>
                            <div class="property-info">
                                <h3><?php echo htmlspecialchars($prop['title']); ?></h3>
                                <p class="price" style="font-size: 1.2rem; color: var(--accent-color); margin-bottom: 0.5rem;">
                                    <?php echo formatPrice($prop['price']); ?>/mo
                                </p>
                                <div class="property-actions" style="margin-top: 1rem;">
                                    <a href="property_details.php?id=<?php echo $prop['id']; ?>" class="btn btn-outline btn-sm"
                                        style="width: 100%;">View Details</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert">You have no favorite properties yet.</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>