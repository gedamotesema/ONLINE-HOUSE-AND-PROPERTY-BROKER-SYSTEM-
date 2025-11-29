<?php
// edit_property.php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';
require_login();
if (!is_owner()) {
    header("Location: index.php");
    exit;
}

$owner_id = $_SESSION['user_id'];
$prop_id = $_GET['id'] ?? null;
if (!$prop_id) {
    header("Location: dashboard_owner.php");
    exit;
}

// Fetch property to edit
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id=? AND owner_id=?");
$stmt->execute([$prop_id, $owner_id]);
$property = $stmt->fetch();
if (!$property) {
    header("Location: dashboard_owner.php");
    exit;
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $price = $_POST['price'] ?? '';
    $type = $_POST['type'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $availability = $_POST['availability'] ?? '';
    $csrf = $_POST['csrf'] ?? '';

    // Validate
    if (strlen($title) < 3) $error = "Title too short.";
    elseif (strlen($location) < 3) $error = "Location required.";
    elseif (!in_array($type,['apartment','house','condo'])) $error = "Invalid type.";
    elseif (!is_numeric($price) || $price <= 0) $error = "Invalid price.";
    elseif (strlen($description) < 10) $error = "Description too short.";
    elseif (!csrf_check($csrf)) $error = "Invalid CSRF token.";
    else {
        // Handle new images if provided
        $images = $property['images'] ? explode(',', $property['images']) : [];
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $i => $filename) {
                $tmp = $_FILES['images']['tmp_name'][$i];
                $size = $_FILES['images']['size'][$i];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif'];
                if (!in_array($ext, $allowed) || $size > 2097152) {
                    $error = "Invalid image type/size (max 2MB, jpg/png/gif).";
                    break;
                }
                $newName = uniqid('prop_', true) . '.' . $ext;
                move_uploaded_file($tmp, "uploads/properties/" . $newName);
                $images[] = $newName;
            }
        }
        if (!$error) {
            $img_csv = implode(',', $images);
            $stmt = $pdo->prepare("UPDATE properties SET title=?, location=?, price=?, type=?, description=?, images=?, availability=? WHERE id=? AND owner_id=?");
            $stmt->execute([$title, $location, $price, $type, $description, $img_csv, $availability, $prop_id, $owner_id]);
            $success = "Property updated!";
            // Refresh data
            $stmt = $pdo->prepare("SELECT * FROM properties WHERE id=? AND owner_id=?");
            $stmt->execute([$prop_id, $owner_id]);
            $property = $stmt->fetch();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Property - Rental Broker</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<?php include 'views/shared/header.php'; ?>
<main class="container py-4">
    <h2>Edit Property</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input required type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($property['title']) ?>" minlength="3">
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Location</label>
            <input required type="text" name="location" id="location" class="form-control" value="<?= htmlspecialchars($property['location']) ?>" minlength="3">
        </div>
        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select name="type" id="type" class="form-select" required>
                <option value="apartment"<?= $property['type']=='apartment'?' selected':'' ?>>Apartment</option>
                <option value="house"<?= $property['type']=='house'?' selected':'' ?>>House</option>
                <option value="condo"<?= $property['type']=='condo'?' selected':'' ?>>Condo</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price (USD)</label>
            <input required type="number" name="price" id="price" class="form-control" min="1" step="any" value="<?= htmlspecialchars($property['price']) ?>">
        </div>
        <div class="mb-3">
            <label for="availability" class="form-label">Availability</label>
            <select name="availability" id="availability" class="form-select">
                <option value="available"<?= $property['availability']=='available'?' selected':'' ?>>Available</option>
                <option value="not available"<?= $property['availability']=='not available'?' selected':'' ?>>Not Available</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea required name="description" id="description" class="form-control" minlength="10" rows="3"><?= htmlspecialchars($property['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Current Images</label><br>
            <?php
            $images = $property['images'] ? explode(',', $property['images']) : [];
            foreach ($images as $img):
            ?>
                <img src="uploads/properties/<?= htmlspecialchars($img) ?>" alt="" style="max-width:90px;max-height:70px;" class="me-1 mb-1">
            <?php endforeach; ?>
        </div>
        <div class="mb-3">
            <label for="images" class="form-label">Add More Images</label>
            <input type="file" name="images[]" id="images" class="form-control" multiple accept=".jpg,.jpeg,.png,.gif">
            <div class="form-text">Add new images if needed (up to 2MB each).</div>
        </div>
        <button type="submit" class="btn btn-primary">Update Property</button>
        <a href="dashboard_owner.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</main>
<?php include 'views/shared/footer.php'; ?>
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
