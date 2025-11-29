<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';
require_once 'includes/csrf.php';

$get_properties_query = $pdo->prepare("
    SELECT p.*, u.username AS owner 
    FROM properties p 
    LEFT JOIN users u ON p.owner_id = u.id 
    WHERE p.availability = 'available' 
    ORDER BY p.created_at DESC
");
$get_properties_query->execute();
$property_list = $get_properties_query->fetchAll();

$is_logged_in = isset($_SESSION['user_id']);
$current_user_role = $is_logged_in ? $_SESSION['role'] : 'guest';
$current_username = $is_logged_in ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home | GNK Housing Agency</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="assets/css/styles.css?v=10">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- NAVIGATION -->
<nav class="navbar">
    <div class="container">
        <a href="index.php" class="logo">
            <i class="fas fa-home"></i> GNK Housing
        </a>

        <button class="mobile-toggle" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>

        <div class="nav-menu">
            <ul class="nav-links">
                <li><a href="index.php" class="active">Home</a></li>
                <li><a href="properties.php">Properties</a></li>
            </ul>

            <ul class="nav-auth">
                <?php if ($is_logged_in): ?>
                    <li class="user-greeting">
                        <i class="fas fa-user-circle"></i>
                        <?= htmlspecialchars($current_username) ?>
                        <small>(<?= ucfirst($current_user_role) ?>)</small>
                    </li>
                    <?php if ($current_user_role === 'renter'): ?>
                        <li><a href="dashboard_renter.php">Dashboard</a></li>
                    <?php elseif ($current_user_role === 'owner'): ?>
                        <li><a href="dashboard_owner.php">Dashboard</a></li>
                    <?php elseif ($current_user_role === 'admin'): ?>
                        <li><a href="admin.php">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="btn-logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="btn-primary">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- HERO + SEARCH -->
<section class="hero">
    <div class="container">
        <h1>Find Your Dream Home Today</h1>
        <p class="subtitle">Premium apartments, houses, and condos – all in one place.</p>

        <form method="get" action="index.php" class="search-bar">
            <!-- LOCATION DROPDOWN -->
            <select name="location">
                <option value="">All Locations</option>
                <option value="Hossana"    <?= ($_GET['location'] ?? '') === 'Hossana'    ? 'selected' : '' ?>>Hossana</option>
                <option value="Hawassa"    <?= ($_GET['location'] ?? '') === 'Hawassa'    ? 'selected' : '' ?>>Hawassa</option>
                <option value="Addis Ababa"<?= ($_GET['location'] ?? '') === 'Addis Ababa'? 'selected' : '' ?>>Addis Ababa</option>
            </select>

            <!-- PROPERTY TYPE -->
            <select name="type">
                <option value="">Property Type</option>
                <option value="apartment" <?= ($_GET['type'] ?? '') === 'apartment' ? 'selected' : '' ?>>Apartment</option>
                <option value="house"     <?= ($_GET['type'] ?? '') === 'house'     ? 'selected' : '' ?>>House</option>
                <option value="condo"     <?= ($_GET['type'] ?? '') === 'condo'     ? 'selected' : '' ?>>Condo</option>
            </select>

            <!-- PRICE RANGE -->
            <input type="number" name="min_price" placeholder="Min Price" 
                   value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
            <input type="number" name="max_price" placeholder="Max Price" 
                   value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">

            <!-- SEARCH BUTTON -->
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </div>
</section>

<!-- PROPERTIES GRID -->
<section class="properties-section">
    <div class="container">
        <div class="properties-grid">
            <?php
            $has_results = false;
            foreach ($property_list as $p):
                // FILTERING LOGIC (updated for exact location match)
                if (!empty($_GET['location']) && $p['location'] !== $_GET['location']) continue;
                if (!empty($_GET['type']) && $p['type'] !== $_GET['type']) continue;
                if (!empty($_GET['min_price']) && $p['price'] < $_GET['min_price']) continue;
                if (!empty($_GET['max_price']) && $p['price'] > $_GET['max_price']) continue;

                $has_results = true;
                $img = !empty($p['images']) ? 'uploads/properties/' . trim(explode(',', $p['images'])[0]) : 'assets/img/placeholder.jpg';
            ?>
            <article class="property-card">
                <div class="property-image">
                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                    <span class="badge"><?= ucfirst($p['type']) ?></span>
                </div>
                <div class="property-content">
                    <h3><?= htmlspecialchars($p['title']) ?></h3>
                    <p class="location"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($p['location']) ?></p>
                    <p class="price">$<?= number_format($p['price']) ?></p>
                    <p class="description"><?= htmlspecialchars(substr($p['description'], 0, 120)) ?>...</p>
                    
                    <div class="property-footer">
                        <span class="owner"><i class="fas fa-user"></i> <?= htmlspecialchars($p['owner']) ?></span>
                        <div class="actions">
                            <a href="property.php?id=<?= $p['id'] ?>" class="btn-view">View Details</a>
                            <?php if ($is_logged_in && $current_user_role === 'renter'): ?>
                                <a href="favorite.php?id=<?= $p['id'] ?>&action=add" class="btn-fav">
                                    <i class="fas fa-heart"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>

            <?php if (!$has_results): ?>
                <p class="no-results">No properties match your search. Try adjusting filters.</p>
            <?php endif; ?>
        </div>

        <div class="text-center mt-6">
            <a href="properties.php" class="btn-primary btn-large">
                View All Properties <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="container">
        <p>&copy; <?= date("Y") ?> GNK Housing Agency • Made for academic purposes</p>
    </div>
</footer>

<script src="assets/js/main.js"></script>
</body>
</html>