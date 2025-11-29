<?php
// property.php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$property_id = $_GET['id'] ?? null;
if (!$property_id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT p.*, u.username AS owner FROM properties p JOIN users u ON p.owner_id = u.id WHERE p.id=?");
$stmt->execute([$property_id]);
$prop = $stmt->fetch();

if (!$prop) {
    header("Location: index.php");
    exit;
}

$is_favorite = false;
$user_logged_in = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? null;

if ($user_logged_in && $user_role == 'renter') {
    $stmt2 = $pdo->prepare("SELECT id FROM favorites WHERE user_id=? AND property_id=?");
    $stmt2->execute([$user_id, $property_id]);
    $is_favorite = $stmt2->fetch() ? true : false;
}

// Prepare images
$images = $prop['images'] ? array_filter(explode(',', $prop['images'])) : [];
$has_images = count($images) > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($prop['title']) ?> - Rental Broker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* BEAUTIFUL INTERACTIVE GALLERY - NO CONFLICT */
        .property-gallery {
            margin-top: 20px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .main-img {
            width: 100%;
            height: 420px;
            object-fit: cover;
            cursor: zoom-in;
            transition: transform 0.4s ease;
        }
        .main-img:hover { transform: scale(1.02); }

        .thumbnails {
            display: flex;
            gap: 8px;
            padding: 12px;
            background: #f8f9fa;
            overflow-x: auto;
            scrollbar-width: thin;
        }
        .thumb {
            width: 90px;
            height: 70px;
            object-fit: cover;
            border-radius: 10px;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s;
            flex-shrink: 0;
        }
        .thumb:hover, .thumb.active {
            border-color: #3b82f6;
            transform: scale(1.1);
        }

        .image-counter {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
        }

        /* LIGHTBOX */
        .lightbox {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.95);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            cursor: zoom-out;
        }
        .lightbox img {
            max-width: 95%;
            max-height: 95vh;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.6);
        }
        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 45px;
            color: white;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .main-img { height: 300px; }
            .thumb { width: 70px; height: 55px; }
        }
    </style>
</head>
<body>
<?php include 'views/shared/header.php'; ?>

<main class="container py-5">
    <div class="row g-5">
        <div class="col-lg-6">
            <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($prop['title']) ?></h1>
            <span class="badge bg-primary fs-5 px-4 py-2 mb-3"><?= ucfirst($prop['type']) ?></span>
            
            <div class="bg-light p-4 rounded mb-4">
                <p class="mb-2"><strong>Location:</strong> <?= htmlspecialchars($prop['location']) ?></p>
                <p class="mb-2"><strong>Price:</strong> <span class="text-success fs-4">$<?= number_format($prop['price']) ?></span></p>
                <p class="mb-2"><strong>Status:</strong> <?= ucfirst($prop['availability']) ?></p>
                <p class="mb-3"><strong>Owner:</strong> <?= htmlspecialchars($prop['owner']) ?></p>
                
                <div class="border-top pt-3">
                    <strong>Description:</strong>
                    <p class="mt-2 lead"><?= nl2br(htmlspecialchars($prop['description'])) ?></p>
                </div>
            </div>

            <?php if ($user_logged_in && $user_role === 'renter'): ?>
                <a href="favorite.php?id=<?= $property_id ?>&action=<?= $is_favorite ? 'remove' : 'add' ?>"
                   class="btn btn-lg <?= $is_favorite ? 'btn-warning' : 'btn-outline-warning' ?>">
                    <i class="fas fa-heart"></i> 
                    <?= $is_favorite ? 'Remove from Favorites' : 'Add to Favorites' ?>
                </a>
            <?php endif; ?>
        </div>

        <!-- INTERACTIVE IMAGE GALLERY -->
        <div class="col-lg-6">
            <h3 class="mb-4">Property Photos <?= $has_images ? '(' . count($images) . ')' : '' ?></h3>
            
            <?php if ($has_images): ?>
                <div class="property-gallery position-relative">
                    <img src="uploads/properties/<?= htmlspecialchars(trim($images[0])) ?>" 
                         alt="Main" class="main-img" id="mainImage" onclick="openLightbox(this.src)">
                    <div class="image-counter" id="counter">1 / <?= count($images) ?></div>
                </div>

                <div class="thumbnails mt-3">
                    <?php foreach ($images as $i => $img): 
                        $src = 'uploads/properties/' . trim($img);
                    ?>
                        <img src="<?= htmlspecialchars($src) ?>" 
                             alt="Thumb" 
                             class="thumb <?= $i === 0 ? 'active' : '' ?>"
                             onclick="changeImage('<?= htmlspecialchars($src) ?>', <?= $i + 1 ?>)">
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5 bg-light rounded">
                    <i class="fas fa-image fa-4x text-muted"></i>
                    <p class="mt-3 text-muted">No photos available</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Inquiry Form -->
    <?php if ($user_logged_in && $user_role === 'renter'): ?>
        <hr class="my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h3>Send Inquiry / Booking Request</h3>
                <form method="post" action="inquiry.php" class="mt-4">
                    <input type="hidden" name="property_id" value="<?= $property_id ?>">
                    <div class="mb-3">
                        <textarea name="message" class="form-control" rows="4" 
                                  placeholder="I'm interested in this property. When can I visit?" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</main>

<!-- LIGHTBOX -->
<div class="lightbox" id="lightbox" onclick="this.style.display='none'">
    <span class="lightbox-close" onclick="document.getElementById('lightbox').style.display='none'">×</span>
    <img src="" alt="Full" id="lightboxImg">
</div>

<?php include 'views/shared/footer.php'; ?>

<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
function changeImage(src, num) {
    document.getElementById('mainImage').src = src;
    document.getElementById('counter').innerHTML = num + ' / <?= count($images) ?>';
    document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
    event.target.classList.add('active');
}
function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightbox').style.display = 'flex';
}
document.addEventListener('keydown', e => e.key === 'Escape' && (document.getElementById('lightbox').style.display = 'none'));
</script>
</body>
</html>