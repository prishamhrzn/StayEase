<?php
// admin/bookings.php — Manage All Bookings
define('IN_ADMIN', true);
require_once '../config.php';
requireAdmin();

// Handle status update
if (isset($_GET['update_status'], $_GET['status'])) {
    $bid    = (int)$_GET['update_status'];
    $status = in_array($_GET['status'], ['confirmed','pending','cancelled']) ? $_GET['status'] : 'pending';
    $pdo->prepare("UPDATE bookings SET status=? WHERE id=?")->execute([$status, $bid]);
    flash('success', 'Booking #' . str_pad($bid,5,'0',STR_PAD_LEFT) . " status set to '$status'.");
    redirect(SITE_URL . '/admin/bookings.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $bid = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM bookings WHERE id=?")->execute([$bid]);
    flash('success', 'Booking deleted.');
    redirect(SITE_URL . '/admin/bookings.php');
}

// Filters
$statusFilter = in_array($_GET['status_filter'] ?? '', ['confirmed','pending','cancelled','']) ? ($_GET['status_filter'] ?? '') : '';
$searchUser   = sanitize($_GET['search'] ?? '');

$where  = ['1=1'];
$params = [];
if ($statusFilter) { $where[] = 'b.status = :st'; $params[':st'] = $statusFilter; }
if ($searchUser)   { $where[] = '(u.name LIKE :s OR u.email LIKE :s2 OR h.name LIKE :s3)';
    $params[':s'] = "%$searchUser%"; $params[':s2'] = "%$searchUser%"; $params[':s3'] = "%$searchUser%"; }

$sql = "SELECT b.*, u.name AS user_name, u.email AS user_email, h.name AS hotel_name, h.location
        FROM bookings b
        JOIN users u  ON b.user_id  = u.id
        JOIN hotels h ON b.hotel_id = h.id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY b.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();

$pageTitle = 'Manage Bookings';
include '../includes/header.php';
?>
<style>body{background:#F3F4F6}</style>
<div style="padding-top:var(--nav-h)">
<div class="admin-layout">
    <nav class="admin-sidebar">
        <h3>Overview</h3>
        <a href="dashboard.php" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="bookings.php"  class="sidebar-link active"><i class="fas fa-calendar-check"></i> Bookings</a>
        <h3>Hotels</h3>
        <a href="hotels.php"    class="sidebar-link"><i class="fas fa-hotel"></i> All Hotels</a>
        <a href="add-hotel.php" class="sidebar-link"><i class="fas fa-plus"></i> Add Hotel</a>
        <h3>Users</h3>
        <a href="users.php"     class="sidebar-link"><i class="fas fa-users"></i> All Users</a>
        <h3>Account</h3>
        <a href="<?= SITE_URL ?>/index.php"  class="sidebar-link"><i class="fas fa-home"></i> View Site</a>
        <a href="<?= SITE_URL ?>/logout.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>

    <main class="admin-main">
        <div class="admin-header">
            <h2>Bookings <span style="font-size:1rem;font-weight:400;color:var(--clr-text-muted)">(<?= count($bookings) ?>)</span></h2>
        </div>

        <!-- Filters -->
        <form method="GET" action="bookings.php" style="display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;align-items:flex-end">
            <div>
                <label style="font-size:.8rem;font-weight:600;color:var(--clr-text-muted);display:block;margin-bottom:.3rem;text-transform:uppercase;letter-spacing:.05em">Search</label>
                <input type="text" name="search" class="form-control" style="width:220px"
                       placeholder="Guest / hotel name…" value="<?= htmlspecialchars($searchUser) ?>">
            </div>
            <div>
                <label style="font-size:.8rem;font-weight:600;color:var(--clr-text-muted);display:block;margin-bottom:.3rem;text-transform:uppercase;letter-spacing:.05em">Status</label>
                <select name="status_filter" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="confirmed"  <?= $statusFilter==='confirmed'  ? 'selected':'' ?>>Confirmed</option>
                    <option value="pending"    <?= $statusFilter==='pending'    ? 'selected':'' ?>>Pending</option>
                    <option value="cancelled"  <?= $statusFilter==='cancelled'  ? 'selected':'' ?>>Cancelled</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
            <a href="bookings.php" class="btn btn-outline">Reset</a>
        </form>

        <div class="admin-section">
            <div style="overflow-x:auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Guest</th>
                            <th>Hotel</th>
                            <th>Check-In</th>
                            <th>Check-Out</th>
                            <th>Nights</th>
                            <th>Guests</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="10" style="text-align:center;padding:2.5rem;color:var(--clr-text-muted)">
                                <i class="fas fa-calendar-times" style="font-size:2rem;opacity:.3;display:block;margin-bottom:.5rem"></i>
                                No bookings found.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($bookings as $b):
                            $nights = (int)((strtotime($b['check_out']) - strtotime($b['check_in'])) / 86400);
                        ?>
                        <tr>
                            <td><strong><?= str_pad($b['id'],5,'0',STR_PAD_LEFT) ?></strong></td>
                            <td>
                                <div style="font-weight:600;font-size:.88rem"><?= htmlspecialchars($b['user_name']) ?></div>
                                <div style="font-size:.78rem;color:var(--clr-text-muted)"><?= htmlspecialchars($b['user_email']) ?></div>
                            </td>
                            <td>
                                <div style="font-weight:600;font-size:.88rem"><?= htmlspecialchars($b['hotel_name']) ?></div>
                                <div style="font-size:.78rem;color:var(--clr-text-muted)"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($b['location']) ?></div>
                            </td>
                            <td><?= date('d M Y', strtotime($b['check_in'])) ?></td>
                            <td><?= date('d M Y', strtotime($b['check_out'])) ?></td>
                            <td><?= $nights ?></td>
                            <td><?= $b['guests'] ?></td>
                            <td><strong><?= formatPrice($b['total_price']) ?></strong></td>
                            <td>
                                <span class="badge <?= match($b['status']) {
                                    'confirmed'=>'badge-success','pending'=>'badge-warning','cancelled'=>'badge-danger',default=>'badge-warning'
                                } ?>"><?= ucfirst($b['status']) ?></span>
                            </td>
                            <td>
                                <div style="display:flex;gap:.35rem;flex-wrap:wrap">
                                    <?php if ($b['status'] !== 'confirmed'): ?>
                                    <a href="bookings.php?update_status=<?= $b['id'] ?>&status=confirmed"
                                       class="btn btn-sm" style="background:#D1FAE5;color:#065F46" title="Confirm">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <?php endif; ?>
                                    <?php if ($b['status'] !== 'cancelled'): ?>
                                    <a href="bookings.php?update_status=<?= $b['id'] ?>&status=cancelled"
                                       class="btn btn-sm" style="background:#FEF3C7;color:#92400E"
                                       data-confirm="Cancel booking #<?= str_pad($b['id'],5,'0',STR_PAD_LEFT) ?>?" title="Cancel">
                                        <i class="fas fa-ban"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="bookings.php?delete=<?= $b['id'] ?>"
                                       class="btn btn-danger btn-sm"
                                       data-confirm="Permanently delete booking #<?= str_pad($b['id'],5,'0',STR_PAD_LEFT) ?>? This cannot be undone."
                                       title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary totals -->
        <?php if (!empty($bookings)):
            $confirmedTotal = array_sum(array_column(array_filter($bookings, fn($b)=>$b['status']==='confirmed'), 'total_price'));
        ?>
        <div style="display:flex;gap:1rem;justify-content:flex-end;margin-top:1rem;flex-wrap:wrap">
            <div style="background:white;border:1px solid var(--clr-border);border-radius:var(--radius-sm);padding:.75rem 1.25rem;font-size:.88rem">
                Confirmed Revenue: <strong style="color:var(--clr-success)"><?= formatPrice($confirmedTotal) ?></strong>
            </div>
            <div style="background:white;border:1px solid var(--clr-border);border-radius:var(--radius-sm);padding:.75rem 1.25rem;font-size:.88rem">
                Showing: <strong><?= count($bookings) ?></strong> booking<?= count($bookings)!==1?'s':'' ?>
            </div>
        </div>
        <?php endif; ?>
    </main>
</div>
</div>
<?php include '../includes/footer.php'; ?>
