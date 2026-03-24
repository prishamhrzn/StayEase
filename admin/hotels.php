<?php
// admin/hotels.php — Manage Hotels
define('IN_ADMIN', true);
require_once '../config.php';
requireAdmin();

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Remove image file if not default
    $imgStmt = $pdo->prepare("SELECT image FROM hotels WHERE id=?");
    $imgStmt->execute([$id]);
    $row = $imgStmt->fetch();
    if ($row && $row['image'] !== 'default.jpg') {
        @unlink(UPLOAD_DIR . $row['image']);
    }
    $pdo->prepare("DELETE FROM hotels WHERE id=?")->execute([$id]);
    flash('success', 'Hotel deleted successfully.');
    redirect(SITE_URL . '/admin/hotels.php');
}

$hotels = $pdo->query("SELECT * FROM hotels ORDER BY created_at DESC")->fetchAll();
$pageTitle = 'Manage Hotels';
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
        <a href="hotels.php"    class="sidebar-link active"><i class="fas fa-hotel"></i> All Hotels</a>
        <a href="add-hotel.php" class="sidebar-link"><i class="fas fa-plus"></i> Add Hotel</a>
        <h3>Users</h3>
        <a href="users.php"     class="sidebar-link"><i class="fas fa-users"></i> All Users</a>
        <h3>Account</h3>
        <a href="<?= SITE_URL ?>/index.php"  class="sidebar-link"><i class="fas fa-home"></i> View Site</a>
        <a href="<?= SITE_URL ?>/logout.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>

    <main class="admin-main">
        <div class="admin-header">
            <h2>Hotels <span style="font-size:1rem;font-weight:400;color:var(--clr-text-muted)">(<?= count($hotels) ?>)</span></h2>
            <a href="add-hotel.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Hotel</a>
        </div>

        <div class="admin-section">
            <div style="overflow-x:auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Image</th><th>Name</th><th>Location</th>
                            <th>Price/Night</th><th>Rating</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($hotels as $h): ?>
                        <tr>
                            <td>
                                <img src="<?= SITE_URL ?>/images/hotels/<?= htmlspecialchars($h['image']) ?>"
                                     alt="" style="width:60px;height:44px;object-fit:cover;border-radius:6px"
                                     onerror="this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?w=120&q=60'">
                            </td>
                            <td><strong><?= htmlspecialchars($h['name']) ?></strong></td>
                            <td><i class="fas fa-map-marker-alt" style="color:var(--clr-gold)"></i> <?= htmlspecialchars($h['location']) ?></td>
                            <td><?= formatPrice($h['price']) ?></td>
                            <td>
                                <span style="color:var(--clr-gold)">★</span>
                                <?= number_format($h['rating'],1) ?>
                            </td>
                            <td>
                                <div style="display:flex;gap:.4rem">
                                    <a href="<?= SITE_URL ?>/hotel-detail.php?id=<?= $h['id'] ?>"
                                       class="btn btn-outline btn-sm" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit-hotel.php?id=<?= $h['id'] ?>"
                                       class="btn btn-sm" style="background:#DBEAFE;color:#1E40AF" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="hotels.php?delete=<?= $h['id'] ?>"
                                       class="btn btn-danger btn-sm"
                                       data-confirm="Delete '<?= htmlspecialchars($h['name']) ?>'? This will also remove all associated bookings!"
                                       title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</div>
<?php include '../includes/footer.php'; ?>
