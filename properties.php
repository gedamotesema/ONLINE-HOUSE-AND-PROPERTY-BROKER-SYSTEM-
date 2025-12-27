<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$whereClause = "status = 'available'";
$params = [];

if (!empty($_GET['search'])) {
    $whereClause .= " AND (title LIKE ? OR location LIKE ?)";
    $params[] = "%" . $_GET['search'] . "%";
    $params[] = "%" . $_GET['search'] . "%";
}

if (!empty($_GET['type'])) {
    $whereClause .= " AND type = ?";
    $params[] = $_GET['type'];
}

if (!empty($_GET['max_price'])) {
    $price_val = (float) $_GET['max_price'];
    if ($price_val >= 0) {
        $whereClause .= " AND price <= ?";
        $params[] = $price_val;
    }
}

$stmt = $pdo->prepare("SELECT * FROM properties WHERE $whereClause ORDER BY created_at DESC");
$stmt->execute($params);
$properties = $stmt->fetchAll();
?>
<?php require_once 'includes/header.php'; ?>

<h1 class="text-center">Browse Properties</h1>

<div class="glass-panel" style="padding: 1.5rem; margin: 2rem 0;">
    <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <input type="text" name="search" placeholder="Search by title or location..."
            value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" style="flex: 2; margin-bottom: 0;">

        <select name="type" style="flex: 1; margin-bottom: 0;">
            <option value="">All Types</option>
            <option value="apartment" <?php if (($_GET['type'] ?? '') == 'apartment')
                echo 'selected'; ?>>Apartment
            </option>
            <option value="house" <?php if (($_GET['type'] ?? '') == 'house')
                echo 'selected'; ?>>House</option>
            <option value="villa" <?php if (($_GET['type'] ?? '') == 'villa')
                echo 'selected'; ?>>Villa</option>
        </select>

        <input type="number" name="max_price" placeholder="Max Price (Birr)" min="0"
            value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>" style="flex: 1; margin-bottom: 0;">

        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="properties.php" class="btn btn-outline">Reset</a>
    </form>
</div>

<div class="property-grid">
    <?php if (count($properties) > 0): ?>
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
                    <span
                        style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.7); color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem;">
                        <?php echo ucfirst($prop['type']); ?>
                    </span>
                </div>
                <div class="property-info">
                    <h3><?php echo htmlspecialchars($prop['title']); ?></h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($prop['location']); ?>
                    </p>
                    <p class="price" style="font-size: 1.2rem; color: var(--accent-color); font-weight: bold;">
                        <?php echo formatPrice($prop['price']); ?>/mo
                    </p>
                    <div class="property-actions" style="margin-top: 1rem;">
                        <a href="property_details.php?id=<?php echo $prop['id']; ?>" class="btn btn-outline"
                            style="width: 100%;">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center" style="grid-column: 1 / -1; font-size: 1.2rem; color: var(--text-secondary);">No properties
            found matching your criteria.</p>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>