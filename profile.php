<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $bio = sanitize($_POST['bio']);
    $password = $_POST['password'];

    $profile_pic_path = null;
    if (!empty($_FILES['profile_picture']['name'])) {
        $file = $_FILES['profile_picture'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $new_name = uniqid('user_', true) . '.' . $ext;
                $dest = 'uploads/profiles/' . $new_name;
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $profile_pic_path = $dest;
                } else {
                    $error_msg = "Failed to move uploaded file.";
                }
            } else {
                $error_msg = "Invalid image format.";
            }
        } else {
            $error_msg = "Error uploading file.";
        }
    }

    if (empty($error_msg)) {
        try {
            $sql = "UPDATE users SET name = ?, bio = ?";
            $params = [$name, $bio];

            if ($profile_pic_path) {
                $sql .= ", profile_picture = ?";
                $params[] = $profile_pic_path;
            }

            if (!empty($password)) {
                $pwd_check = validatePassword($password);
                if ($pwd_check !== true) {
                    $error_msg = $pwd_check;
                } else {
                    $sql .= ", password = ?";
                    $params[] = password_hash($password, PASSWORD_DEFAULT);
                }
            }

            $sql .= " WHERE id = ?";
            $params[] = $user_id;

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $_SESSION['user_name'] = $name;
            if ($profile_pic_path) {
                $_SESSION['user_avatar'] = $profile_pic_path;
            }

            $success_msg = "Profile updated successfully.";
        } catch (PDOException $e) {
            $error_msg = "Update failed: " . $e->getMessage();
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<?php require_once 'includes/header.php'; ?>

<div class="row" style="max-width: 600px; margin: 0 auto;">
    <h1 class="text-center">Edit Profile</h1>

    <?php if ($success_msg)
        echo "<div class='alert alert-success'>$success_msg</div>"; ?>
    <?php if ($error_msg)
        echo "<div class='alert alert-error'>$error_msg</div>"; ?>

    <form method="POST" class="glass-panel" style="padding: 2rem; margin-top: 2rem;" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

        <div style="text-align: center; margin-bottom: 2rem;">
            <?php
            $avatar = !empty($user['profile_picture']) ? $user['profile_picture'] : 'https://via.placeholder.com/150?text=Avatar';
            ?>
            <div
                style="width: 120px; height: 120px; border-radius: 50%; overflow: hidden; margin: 0 auto 1rem; border: 3px solid var(--accent-color);">
                <img src="<?php echo htmlspecialchars($avatar); ?>"
                    style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <label class="btn btn-outline btn-sm" style="cursor: pointer;">
                Change Photo
                <input type="file" name="profile_picture" style="display: none;" accept="image/*"
                    onchange="document.querySelector('form').submit()">
            </label>
        </div>

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>

        <div class="form-group">
            <label>Bio</label>
            <textarea name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label>New Password (leave blank to keep current)</label>
            <input type="password" name="password" placeholder="New Password">
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">Save Changes</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>