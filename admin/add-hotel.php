<?php
// admin/add-hotel.php — Add New Hotel
define('IN_ADMIN', true);
require_once '../config.php';
requireAdmin();

$errors = [];
$data   = ['name'=>'','location'=>'','price'=>'','rating'=>'','description'=>'','amenities'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = sanitize($_POST['name']        ?? '');
    $location    = sanitize($_POST['location']    ?? '');
    $price       = (float)($_POST['price']        ?? 0);
    $rating      = (float)($_POST['rating']       ?? 0);
    $description = sanitize($_POST['description'] ?? '');
    $amenities   = sanitize($_POST['amenities']   ?? '');
    $data = compact('name','location','price','rating','description','amenities');

    if (strlen($name) < 3)     $errors['name']     = 'Name must be at least 3 characters.';
    if (empty($location))      $errors['location']  = 'Location is required.';
    if ($price <= 0)           $errors['price']     = 'Price must be greater than 0.';
    if ($rating < 0 || $rating > 5) $errors['rating'] = 'Rating must be 0–5.';

    // Handle image upload
    $imageName = 'default.jpg';
    if (!empty($_FILES['image']['name'])) {
        $file    = $_FILES['image'];
        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (!in_array($ext, $allowed)) {
            $errors['image'] = 'Only JPG, PNG, or WEBP images are allowed.';
        } elseif ($file['size'] > 5 * 1024 * 1024) {
            $errors['image'] = 'Image must be under 5MB.';
        } else {
            $imageName = uniqid('hotel_') . '.' . $ext;
            if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
            if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $imageName)) {
                $errors['image'] = 'Failed to upload image.';
                $imageName = 'default.jpg';
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "INSERT INTO hotels (name, location, price, rating, description, amenities, image)
             VALUES (?,?,?,?,?,?,?)"
        );
        $stmt->execute([$name, $location, $price, $rating, $description, $amenities, $imageName]);
        flash('success', "Hotel '$name' added successfully!");
        redirect(SITE_URL . '/admin/hotels.php');
    }
}

$pageTitle = 'Add Hotel';
include '../includes/header.php';
?>
<style>body{background:#F3F4F6}</style>
<div style="padding-top:var(--nav-h)">
<div class="admin-layout">
    <nav class="admin-sidebar">
        <h3>Overview</h3>
        <a href="dashboard.php" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="bookings.php"  class="sidebar-link"><i class="fas fa-calendar-check"></i> Bookings</a>
        <h3>Hotels</h3>
        <a href="hotels.php"    class="sidebar-link"><i class="fas fa-hotel"></i> All Hotels</a>
        <a href="add-hotel.php" class="sidebar-link active"><i class="fas fa-plus"></i> Add Hotel</a>
        <h3>Users</h3>
        <a href="users.php"     class="sidebar-link"><i class="fas fa-users"></i> All Users</a>
        <h3>Account</h3>
        <a href="<?= SITE_URL ?>/index.php"  class="sidebar-link"><i class="fas fa-home"></i> View Site</a>
        <a href="<?= SITE_URL ?>/logout.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>

    <main class="admin-main">
        <div class="admin-header">
            <div>
                <nav class="breadcrumb">
                    <a href="hotels.php">Hotels</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Add New Hotel</span>
                </nav>
                <h2>Add New Hotel</h2>
            </div>
        </div>

        <div class="admin-section">
            <div class="admin-section-body">
                <form method="POST" enctype="multipart/form-data" data-validate-form novalidate>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem">
                        <div class="form-group">
                            <label>Hotel Name *</label>
                            <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'error':'' ?>"
                                   value="<?= htmlspecialchars($data['name']) ?>"
                                   data-validate="required|minLen:3" data-label="Hotel name" placeholder="e.g. The Grand Himalayan">
                            <span class="form-error <?= isset($errors['name'])?'visible':'' ?>"><?= $errors['name']??'' ?></span>
                        </div>

                        <div class="form-group">
                            <label>Location *</label>
                            <input type="text" name="location" class="form-control <?= isset($errors['location'])?'error':'' ?>"
                                   value="<?= htmlspecialchars($data['location']) ?>"
                                   data-validate="required" data-label="Location" placeholder="City, Country">
                            <span class="form-error <?= isset($errors['location'])?'visible':'' ?>"><?= $errors['location']??'' ?></span>
                        </div>

                        <div class="form-group">
                            <label>Price per Night (NPR) *</label>
                            <input type="number" name="price" class="form-control <?= isset($errors['price'])?'error':'' ?>"
                                   value="<?= htmlspecialchars($data['price']) ?>"
                                   data-validate="required|price" min="1" step="50" placeholder="e.g. 3500">
                            <span class="form-error <?= isset($errors['price'])?'visible':'' ?>"><?= $errors['price']??'' ?></span>
                        </div>

                        <div class="form-group">
                            <label>Rating (0–5)</label>
                            <input type="number" name="rating" class="form-control <?= isset($errors['rating'])?'error':'' ?>"
                                   value="<?= htmlspecialchars($data['rating']) ?>"
                                   data-validate="rating" min="0" max="5" step="0.1" placeholder="e.g. 4.5">
                            <span class="form-error <?= isset($errors['rating'])?'visible':'' ?>"><?= $errors['rating']??'' ?></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4"
                                  placeholder="Describe the hotel experience, surroundings, and highlights…"><?= htmlspecialchars($data['description']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Amenities <small style="color:var(--clr-text-muted)">(comma-separated)</small></label>
                        <input type="text" name="amenities" class="form-control"
                               value="<?= htmlspecialchars($data['amenities']) ?>"
                               placeholder="Free WiFi, Swimming Pool, Spa, Gym, Restaurant…">
                    </div>

                    <div class="form-group">
                        <label>Hotel Image</label>
                        <input type="file" id="hotelImage" name="image" class="form-control <?= isset($errors['image'])?'error':'' ?>"
                               accept="image/jpeg,image/png,image/webp">
                        <span class="form-error <?= isset($errors['image'])?'visible':'' ?>"><?= $errors['image']??'' ?></span>
                        <img id="imagePreview" src="" alt="Preview" style="display:none;margin-top:.75rem;max-height:200px;border-radius:var(--radius-sm)">
                    </div>

                    <div style="display:flex;gap:1rem;margin-top:1.5rem">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus"></i> Add Hotel
                        </button>
                        <a href="hotels.php" class="btn btn-outline btn-lg">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</div>
<?php include '../includes/footer.php'; ?>
