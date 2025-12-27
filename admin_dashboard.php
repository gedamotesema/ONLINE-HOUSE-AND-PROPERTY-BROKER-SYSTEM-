<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireRole('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $uid = (int) $_POST['user_id'];
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$uid]);
        flash('admin', "User ID $uid deleted.");
    }
    if (isset($_POST['delete_property'])) {
        $pid = (int) $_POST['property_id'];
        $stmt = $pdo->prepare("DELETE FROM properties WHERE id = ?");
        $stmt->execute([$pid]);
        flash('admin', "Property ID $pid deleted.");
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<h1>Admin Dashboard</h1>
<?php echo flash('admin'); ?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">

    <div class="glass-panel" style="padding: 1.5rem;">
        <h2>Users</h2>
        <?php
        $users = $pdo->query("SELECT id, name, email, role FROM users ORDER BY created_at DESC LIMIT 10")->fetchAll();
        ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u['name']); ?> <br>
                            <small><?php echo htmlspecialchars($u['email']); ?></small>
                        </td>
                        <td><?php echo ucfirst($u['role']); ?></td>
                        <td>
                            <?php if ($u['role'] !== 'admin'): ?>
                                <form method="POST" onsubmit="return confirm('Are you sure?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <button type="submit" name="delete_user" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="glass-panel" style="padding: 1.5rem;">
        <h2>Recent Logs</h2>
        <?php
        $logs = $pdo->query("SELECT l.*, u.name FROM system_logs l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT 10")->fetchAll();
        ?>
        <ul style="list-style: none;">
            <?php foreach ($logs as $log): ?>
                <li style="border-bottom: 1px solid var(--card-border); padding: 10px 0;">
                    <small style="color: var(--accent-color);"><?php echo $log['created_at']; ?></small><br>
                    <strong><?php echo htmlspecialchars($log['action']); ?></strong>
                    by <?php echo htmlspecialchars($log['name'] ?? 'Guest'); ?>
                    <br>
                    <small style="color: var(--text-secondary);"><?php echo htmlspecialchars($log['details']); ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

</div>

<div class="glass-panel" style="padding: 1.5rem; margin-top: 2rem;">
    <h2>All Properties</h2>
    <?php
    $props = $pdo->query("SELECT p.*, u.name as landlord FROM properties p JOIN users u ON p.landlord_id = u.id ORDER BY p.created_at DESC LIMIT 10")->fetchAll();
    ?>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Landlord</th>
                <th>Price</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($props as $p): ?>
                <tr>
                    <td><?php echo htmlspecialchars($p['title']); ?></td>
                    <td><?php echo htmlspecialchars($p['landlord']); ?></td>
                    <td><?php echo formatPrice($p['price']); ?></td>
                    <td><?php echo ucfirst($p['status']); ?></td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Delete this property?');">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                            <input type="hidden" name="property_id" value="<?php echo $p['id']; ?>">
                            <button type="submit" name="delete_property" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>