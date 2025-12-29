<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

requireRole('landlord');

$user_id = $_SESSION['user_id'];
$property = null;
$editing = false;
$error = '';
$success = '';

if (isset($_GET['id'])) {
    $prop_id = (int) $_GET['id'];
    if ($editing) {
        $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ? AND landlord_id = ?");
        $stmt->execute([$prop_id, $user_id]);
        $property = $stmt->fetch();

        if (!$property) {
            flash('global', "Property not found or access denied.", 'error');
            redirect('dashboard.php');
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $price = (float) $_POST['price'];
    $location = sanitize($_POST['location']);
    $type = $_POST['type'];

    $image_paths = [];

    if ($editing && !empty($property['images'])) {
        $existing_images = json_decode($property['images'], true);
        if (is_array($existing_images)) {
            $image_paths = $existing_images;
        }
    }

    if (!empty($_FILES['images']['name'][0])) {
        $total_files = count($_FILES['images']['name']);

        for ($i = 0; $i < $total_files; $i++) {
            if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                if (count($image_paths) >= 5) {
                    $error = "Maximum 5 images allowed. Some images were not uploaded.";
                    break;
                }

                $tmp_name = $_FILES['images']['tmp_name'][$i];
                $name = basename($_FILES['images']['name'][$i]);
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($ext, $allowed)) {
                    $new_filename = uniqid('prop_', true) . '.' . $ext;
                    $upload_path = 'uploads/' . $new_filename;

                    if (move_uploaded_file($tmp_name, $upload_path)) {
                        $image_paths[] = $upload_path;
                    } else {
                        $error = "Failed to upload image: $name";
                    }
                } else {
                    $error = "Invalid file type: $name. Only JPG, PNG, GIF, WEBP allowed.";
                }
            }
        }
    }

    $images_json = json_encode($image_paths);

    try {
        if ($editing) {
            $stmt = $pdo->prepare("UPDATE properties SET title = ?, description = ?, price = ?, location = ?, type = ?, images = ? WHERE id = ? AND landlord_id = ?");
            $stmt->execute([$title, $description, $price, $location, $type, $images_json, $property['id'], $user_id]);
            $success = "Property updated successfully.";

            $property['title'] = $title;
            $property['images'] = $images_json;

        } else {
            $stmt = $pdo->prepare("INSERT INTO properties (landlord_id, title, description, price, location, type, images) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $title, $description, $price, $location, $type, $images_json]);

            $new_id = $pdo->lastInsertId();
            $stmtLog = $pdo->prepare("INSERT INTO system_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
            $stmtLog->execute([$user_id, 'CREATE_PROPERTY', "Created property ID $new_id", $_SERVER['REMOTE_ADDR']]);

            flash('global', "Property listed successfully!");
            redirect('dashboard.php');
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="row" style="max-width: 800px; margin: 0 auto;">
    <h1 class="text-center"><?php echo $editing ? 'Edit Property' : 'List New Property'; ?></h1>

    <?php if ($error)
        echo "<div class='alert alert-error'>$error</div>"; ?>
    <?php if ($success)
        echo "<div class='alert alert-success'>$success</div>"; ?>

    <form method="POST" class="glass-panel" style="padding: 2rem; margin-top: 2rem;" enctype="multipart/form-data"
        onsubmit="this.querySelector('button[type=submit]').disabled = true; this.querySelector('button[type=submit]').textContent = 'Processing...';">
        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

        <div class="form-group">
            <label>Property Title</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($property['title'] ?? ''); ?>" required
                placeholder="e.g. 4X4 class">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div class="form-group">
                <label>Price (Birr/mo)</label>
                <input type="number" step="0.01" name="price"
                    value="<?php echo htmlspecialchars($property['price'] ?? ''); ?>" required placeholder="2500">
            </div>

            <div class="form-group">
                <label>Type</label>
                <select name="type">
                    <?php
                    $types = ['apartment', 'house', 'studio', 'villa'];
                    foreach ($types as $t) {
                        $selected = ($property['type'] ?? '') === $t ? 'selected' : '';
                        echo "<option value='$t' $selected>" . ucfirst($t) . "</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" value="<?php echo htmlspecialchars($property['location'] ?? ''); ?>"
                required placeholder="City, eg hossana ">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="5"
                required><?php echo htmlspecialchars($property['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label>Property Images (Max 5)</label>
            <input type="file" name="images[]" multiple accept="image/*">
            <small style="color: var(--text-secondary);">Supported formats: JPG, PNG, GIF, WEBP. Max 5MB per
                file.</small>

            <?php if ($editing && !empty($property['images'])): ?>
                <div style="margin-top: 1rem; display: flex; gap: 10px; flex-wrap: wrap;">
                    <?php
                    $curr_imgs = json_decode($property['images'], true);
                    if (is_array($curr_imgs)) {
                        foreach ($curr_imgs as $img) {
                            echo "<div style='position: relative; width: 100px; height: 100px;'>
                                    <img src='" . htmlspecialchars($img) . "' style='width: 100%; height: 100%; object-fit: cover; border-radius: 8px;'>
                                  </div>";
                        }
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">
            <?php echo $editing ? 'Save Changes' : 'Publish Listing'; ?>
        </button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
