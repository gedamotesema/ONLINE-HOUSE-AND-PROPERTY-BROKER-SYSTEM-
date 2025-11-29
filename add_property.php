<?php
// add_property.php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';
require_login();
if (!is_owner()) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $location = trim($_POST['location'] ?? '');  // now comes from dropdown
    $price = $_POST['price'] ?? '';
    $type = $_POST['type'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $availability = $_POST['availability'] ?? 'available';
    $csrf = $_POST['csrf'] ?? '';
    $owner_id = $_SESSION['user_id'];

    // Updated validation for location (must be one of the allowed cities)
    $allowed_locations = ['Hossana', 'Hawassa', 'Addis Ababa'];
    
    if (strlen($title) < 3) $error = "Title too short.";
    elseif (!in_array($location, $allowed_locations)) $error = "Please select a valid location.";
    elseif (!in_array($type, ['apartment', 'house', 'condo'])) $error = "Invalid type.";
    elseif (!is_numeric($price) || $price <= 0) $error = "Invalid price.";
    elseif (strlen($description) < 10) $error = "Description too short.";
    elseif (!csrf_check($csrf)) $error = "Invalid CSRF token.";
    else {
        // Handle image upload (multiple files)
        $images = [];
        if (!empty($_FILES['images']['name'][0])) {
            foreach ($_FILES['images']['name'] as $i => $filename) {
                $tmp = $_FILES['images']['tmp_name'][$i];
                $size = $_FILES['images']['size'][$i];
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif'];
                if (!in_array($ext, $allowed) || $size > 2097152) {
                    $error = "Invalid image type/size (max 2MB, jpg/png/gif only).";
                    break;
                }
                $newName = uniqid('prop_', true) . '.' . $ext;
                move_uploaded_file($tmp, "uploads/properties/" . $newName);
                $images[] = $newName;
            }
        }

        if (!$error) {
            $img_csv = implode(',', $images);
            $stmt = $pdo->prepare("INSERT INTO properties (owner_id, title, location, price, type, description, images, availability, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$owner_id, $title, $location, $price, $type, $description, $img_csv, $availability]);
            $success = "Property added successfully!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Property - Rental Broker</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<?php include 'views/shared/header.php'; ?>
<main class="container py-4">
    <h2>Add New Property</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input required type="text" name="title" id="title" class="form-control" minlength="3" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
        </div>

        <!-- DROPDOWN LOCATION (REPLACES TEXT INPUT) -->
        <div class="mb-3">
            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
            <select name="location" id="location" class="form-select" required>
                <option value="">-- Choose Location --</option>
                <option value="Hossana" <?= (($_POST['location'] ?? '') === 'Hossana') ? 'selected' : '' ?>>Hossana</option>
                <option value="Hawassa" <?= (($_POST['location'] ?? '') === 'Hawassa') ? 'selected' : '' ?>>Hawassa</option>
                <option value="Addis Ababa" <?= (($_POST['location'] ?? '') === 'Addis Ababa') ? 'selected' : '' ?>>Addis Ababa</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select name="type" id="type" class="form-select" required>
                <option value="apartment" <?= ($_POST['type'] ?? '') === 'apartment' ? 'selected' : '' ?>>Apartment</option>
                <option value="house" <?= ($_POST['type'] ?? '') === 'house' ? 'selected' : '' ?>>House</option>
                <option value="condo" <?= ($_POST['type'] ?? '') === 'condo' ? 'selected' : '' ?>>Condo</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price (USD)</label>
            <input required type="number" name="price" id="price" class="form-control" min="1" step="any" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label for="availability" class="form-label">Availability</label>
            <select name="availability" id="availability" class="form-select">
                <option value="available" <?= ($_POST['availability'] ?? 'available') === 'available' ? 'selected' : '' ?>>Available</option>
                <option value="not available" <?= ($_POST['availability'] ?? '') === 'not available' ? 'selected' : '' ?>>Not Available</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea required name="description" id="description" class="form-control" minlength="10" rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="images" class="form-label">Images (jpg, png, gif - max 2MB each, up to 4)</label>
            <input type="file" name="images[]" id="images" class="form-control" multiple accept=".jpg,.jpeg,.png,.gif">
        </div>

        <button type="submit" class="btn btn-primary btn-lg">Add Property</button>
    </form>
</main>
<?php include 'views/shared/footer.php'; ?>
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>