<?php
// admin/dashboard.php — Admin Dashboard
define('IN_ADMIN', true);
require_once '../config.php';
requireAdmin();

// Fetch stats
$totalHotels   = $pdo->query("SELECT COUNT(*) FROM hotels")->fetchColumn();
$totalBookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$totalUsers    = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$revenue       = $pdo->query("SELECT COALESCE(SUM(total_price),0) FROM bookings WHERE status='confirmed'")->fetchColumn();

// Recent bookings
$recentBookings = $pdo->query(
    "SELECT b.*, u.name AS user_name, h.name AS hotel_name
     FROM bookings b JOIN users u ON b.user_id=u.id JOIN hotels h ON b.hotel_id=h.id
     ORDER BY b.created_at DESC LIMIT 10"
)->fetchAll();

$pageTitle = 'Admin Dashboard';
include '../includes/header.php';
?>
<style>
body { background: #F3F4F6; }
.page-content { padding-top: var(--nav-h); }
</style>

<div class="page-content">
<div class="admin-layout">
    <!-- Sidebar -->
    <nav class="admin-sidebar">
        <h3>Overview</h3>
        <a href="dashboard.php" class="sidebar-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="bookings.php"  class="sidebar-link"><i class="fas fa-calendar-check"></i> Bookings</a>

        <h3>Hotels</h3>
        <a href="hotels.php"     class="sidebar-link"><i class="fas fa-hotel"></i> All Hotels</a>
        <a href="add-hotel.php"  class="sidebar-link"><i class="fas fa-plus"></i> Add Hotel</a>

        <h3>Users</h3>
        <a href="users.php" class="sidebar-link"><i class="fas fa-users"></i> All Users</a>

        <h3>Account</h3>
        <a href="<?= SITE_URL ?>/index.php"  class="sidebar-link"><i class="fas fa-home"></i> View Site</a>
        <a href="<?= SITE_URL ?>/logout.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>

    <!-- Main Content -->
    <main class="admin-main">
        <div class="admin-header">
            <div>
                <h2>Dashboard</h2>
                <p class="text-muted">Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>!</p>
            </div>
            <a href="add-hotel.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Hotel
            </a>
        </div>

        <!-- Stat Cards -->
        <div class="stat-cards">
            <div class="stat-card">
                <div class="stat-card-icon green"><i class="fas fa-hotel"></i></div>
                <div class="stat-card-info">
                    <p>Total Hotels</p>
                    <strong><?= $totalHotels ?></strong>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon gold"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-card-info">
                    <p>Total Bookings</p>
                    <strong><?= $totalBookings ?></strong>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon blue"><i class="fas fa-users"></i></div>
                <div class="stat-card-info">
                    <p>Registered Users</p>
                    <strong><?= $totalUsers ?></strong>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon red"><i class="fas fa-coins"></i></div>
                <div class="stat-card-info">
                    <p>Total Revenue</p>
                    <strong style="font-size:1.1rem"><?= formatPrice($revenue) ?></strong>
                </div>
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="admin-section">
            <div class="admin-section-header">
                <h3><i class="fas fa-clock"></i> Recent Bookings</h3>
                <a href="bookings.php" class="btn btn-outline btn-sm">View All</a>
            </div>
            <div style="overflow-x:auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th><th>Guest</th><th>Hotel</th>
                            <th>Check-In</th><th>Check-Out</th>
                            <th>Total</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentBookings as $b): ?>
                        <tr>
                            <td><?= str_pad($b['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars($b['user_name']) ?></td>
                            <td><?= htmlspecialchars($b['hotel_name']) ?></td>
                            <td><?= date('d M Y', strtotime($b['check_in'])) ?></td>
                            <td><?= date('d M Y', strtotime($b['check_out'])) ?></td>
                            <td><?= formatPrice($b['total_price']) ?></td>
                            <td>
                                <span class="badge <?= match($b['status']) {
                                    'confirmed'=>'badge-success','pending'=>'badge-warning','cancelled'=>'badge-danger',default=>'badge-warning'
                                } ?>"><?= ucfirst($b['status']) ?></span>
                            </td>
                            <td>
                                <a href="bookings.php?update_status=<?= $b['id'] ?>&status=confirmed"
                                   class="btn btn-sm" style="background:#D1FAE5;color:#065F46">✓</a>
                                <a href="bookings.php?update_status=<?= $b['id'] ?>&status=cancelled"
                                   class="btn btn-danger btn-sm"
                                   data-confirm="Cancel this booking?">✗</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recentBookings)): ?>
                        <tr><td colspan="8" style="text-align:center;color:var(--clr-text-muted);padding:2rem">No bookings yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</div>

<?php include '../includes/footer.php'; ?>
