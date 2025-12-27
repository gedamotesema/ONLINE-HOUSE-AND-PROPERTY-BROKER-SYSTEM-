<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$conv_param = $_GET['conversation_id'] ?? 0;
$is_new_mode = ($conv_param === 'new');
$curr_conv_id = (int) $conv_param;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg = sanitize($_POST['message'] ?? '');

    if ($msg) {
        $prop_id = (int) ($_GET['property_id'] ?? 0);
        $recip_id = (int) ($_GET['recipient_id'] ?? 0);

        if (($curr_conv_id === 0 || $is_new_mode) && $prop_id && $recip_id) {
            $stmt = $pdo->prepare("SELECT id FROM conversations WHERE property_id = ? AND ((user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?))");
            $stmt->execute([$prop_id, $user_id, $recip_id, $recip_id, $user_id]);
            $exists = $stmt->fetch();

            if ($exists) {
                $curr_conv_id = $exists['id'];
            } else {
                $stmt = $pdo->prepare("INSERT INTO conversations (property_id, user1_id, user2_id) VALUES (?, ?, ?)");
                $stmt->execute([$prop_id, $user_id, $recip_id]);
                $curr_conv_id = $pdo->lastInsertId();
            }
        } elseif ($curr_conv_id > 0) {
            $check = $pdo->prepare("SELECT id FROM conversations WHERE id = ? AND (user1_id = ? OR user2_id = ?)");
            $check->execute([$curr_conv_id, $user_id, $user_id]);
            if (!$check->fetch()) {
                die("Access Denied");
            }
        }

        if ($curr_conv_id > 0) {
            $stmt = $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, message) VALUES (?, ?, ?)");
            $stmt->execute([$curr_conv_id, $user_id, $msg]);

            redirect("messages.php?conversation_id=$curr_conv_id");
        }
    }
}

$stmt = $pdo->prepare("
        SELECT c.id, c.property_id, p.title, 
            CASE WHEN c.user1_id = ? THEN u2.name ELSE u1.name END as other_name,
            CASE WHEN c.user1_id = ? THEN u2.profile_picture ELSE u1.profile_picture END as other_avatar
        FROM conversations c
        JOIN properties p ON c.property_id = p.id
        JOIN users u1 ON c.user1_id = u1.id
        JOIN users u2 ON c.user2_id = u2.id
        WHERE c.user1_id = ? OR c.user2_id = ?
        ORDER BY c.created_at DESC
    ");
$stmt->execute([$user_id, $user_id, $user_id, $user_id]);
$conversations = $stmt->fetchAll();

$messages = [];
if ($curr_conv_id) {
    $check = $pdo->prepare("SELECT id FROM conversations WHERE id = ? AND (user1_id = ? OR user2_id = ?)");
    $check->execute([$curr_conv_id, $user_id, $user_id]);
    if ($check->fetch()) {
        $stmt = $pdo->prepare("SELECT m.*, u.name as sender_name, u.profile_picture as sender_avatar FROM messages m JOIN users u ON m.sender_id = u.id WHERE conversation_id = ? ORDER BY created_at ASC");
        $stmt->execute([$curr_conv_id]);
        $messages = $stmt->fetchAll();
    } else {
        $curr_conv_id = 0;
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="container" style="height: calc(100vh - 140px); display: flex; gap: 2rem;">

    <div class="glass-panel" style="width: 320px; padding: 1rem; overflow-y: auto;">
        <h3 style="margin-bottom: 1rem;">Messages</h3>
        <ul style="list-style: none;">
            <?php foreach ($conversations as $conv): ?>
                <li style="margin-bottom: 0.5rem;">
                    <a href="?conversation_id=<?php echo $conv['id']; ?>"
                        style="display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 8px; background: <?php echo $conv['id'] == $curr_conv_id ? 'rgba(212, 175, 55, 0.1)' : 'rgba(255,255,255,0.03)'; ?>; color: var(--text-primary); border: 1px solid <?php echo $conv['id'] == $curr_conv_id ? 'var(--accent-color)' : 'transparent'; ?>;">

                        <?php $avatar = !empty($conv['other_avatar']) ? $conv['other_avatar'] : 'https://via.placeholder.com/50?text=U'; ?>
                        <img src="<?php echo htmlspecialchars($avatar); ?>"
                            style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">

                        <div style="overflow: hidden;">
                            <span
                                style="font-weight: bold; display: block;"><?php echo htmlspecialchars($conv['other_name']); ?></span>
                            <small
                                style="opacity: 0.7; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;"><?php echo htmlspecialchars($conv['title']); ?></small>
                        </div>
                    </a>
                </li>
            <?php endforeach; ?>
            <?php if (empty($conversations))
                echo "<p style='color: #888;'>No conversations yet.</p>"; ?>
        </ul>
    </div>

    <div class="glass-panel" style="flex: 1; display: flex; flex-direction: column; padding: 0; overflow: hidden;">
        <?php if ($curr_conv_id || $is_new_mode): ?>

            <div id="msg-area"
                style="flex: 1; padding: 1.5rem; overflow-y: auto; display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach ($messages as $m): ?>
                    <?php $is_me = $m['sender_id'] == $user_id; ?>
                    <div
                        style="align-self: <?php echo $is_me ? 'flex-end' : 'flex-start'; ?>; max-width: 75%; display: flex; gap: 10px; flex-direction: <?php echo $is_me ? 'row-reverse' : 'row'; ?>;">

                        <?php $avatar = !empty($m['sender_avatar']) ? $m['sender_avatar'] : 'https://via.placeholder.com/40?text=U'; ?>
                        <img src="<?php echo htmlspecialchars($avatar); ?>"
                            style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; align-self: flex-end;">

                        <div>
                            <div
                                style="background: <?php echo $is_me ? 'var(--accent-color)' : 'rgba(255,255,255,0.1)'; ?>; color: <?php echo $is_me ? '#000' : '#fff'; ?>; padding: 10px 15px; border-radius: 12px; <?php echo $is_me ? 'border-bottom-right-radius: 2px;' : 'border-bottom-left-radius: 2px;'; ?>">
                                <?php echo nl2br(htmlspecialchars($m['message'])); ?>
                            </div>
                            <small
                                style="display: block; font-size: 0.7rem; color: #888; margin-top: 4px; text-align: <?php echo $is_me ? 'right' : 'left'; ?>;">
                                <?php echo date('H:i', strtotime($m['created_at'])); ?>
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($messages) && !$is_new_mode)
                    echo "<p class='text-center' style='color: #888;'>Start the conversation!</p>"; ?>

                <?php if ($is_new_mode): ?>
                    <p class="text-center" style="color: var(--accent-color); margin-top: 2rem;">
                        <i class="fas fa-handshake" style="font-size: 2rem; margin-bottom: 1rem;"></i><br>
                        Start a new conversation with the landlord.
                    </p>
                <?php endif; ?>
            </div>

            <form method="POST" style="padding: 1rem; background: rgba(0,0,0,0.2); display: flex; gap: 1rem;">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                <input type="text" name="message" placeholder="Type a message..." required
                    style="margin-bottom: 0; border-radius: 20px;">
                <button type="submit" class="btn btn-primary"
                    style="border-radius: 50%; width: 50px; height: 50px; padding: 0; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>

            <script>
                const msgArea = document.getElementById('msg-area');
                msgArea.scrollTop = msgArea.scrollHeight;
            </script>

        <?php else: ?>
            <div
                style="flex: 1; display: flex; align-items: center; justify-content: center; color: var(--text-secondary);">
                Select a conversation to start chatting
            </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>