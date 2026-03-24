<?php
// admin/users.php — Manage Users
define('IN_ADMIN', true);
require_once '../config.php';
requireAdmin();

// Promote / demote role
if (isset($_GET['toggle_role'])) {
    $uid  = (int)$_GET['toggle_role'];
    if ($uid !== (int)$_SESSION['user_id']) { // protect self
        $stmt = $pdo->prepare("SELECT role FROM users WHERE id=?");
        $stmt->execute([$uid]);
        $row = $stmt->fetch();
        $newRole = ($row && $row['role'] === 'admin') ? 'user' : 'admin';
        $pdo->prepare("UPDATE users SET role=? WHERE id=?")->execute([$newRole, $uid]);
        flash('success', "User role updated to '$newRole'.");
    }
    redirect(SITE_URL . '/admin/users.php');
}

// Delete user
if (isset($_GET['delete'])) {
    $uid = (int)$_GET['delete'];
    if ($uid !== (int)$_SESSION['user_id']) {
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$uid]);
        flash('success', 'User deleted.');
    }
    redirect(SITE_URL . '/admin/users.php');
}

$search = sanitize($_GET['search'] ?? '');
$where  = ['1=1'];
$params = [];
if ($search) {
    $where[]   = '(name LIKE :s OR email LIKE :s2)';
    $params[':s']  = "%$search%";
    $params[':s2'] = "%$search%";
}

$stmt = $pdo->prepare(
    "SELECT u.*, COUNT(b.id) AS booking_count
     FROM users u
     LEFT JOIN bookings b ON u.id = b.user_id
     WHERE " . implode(' AND ', $where) . "
     GROUP BY u.id
     ORDER BY u.created_at DESC"
);
$stmt->execute($params);
$users = $stmt->fetchAll();

$pageTitle = 'Manage Users';
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
        <a href="add-hotel.php" class="sidebar-link"><i class="fas fa-plus"></i> Add Hotel</a>
        <h3>Users</h3>
        <a href="users.php"     class="sidebar-link active"><i class="fas fa-users"></i> All Users</a>
        <h3>Account</h3>
        <a href="<?= SITE_URL ?>/index.php"  class="sidebar-link"><i class="fas fa-home"></i> View Site</a>
        <a href="<?= SITE_URL ?>/logout.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>

    <main class="admin-main">
        <div class="admin-header">
            <h2>Users <span style="font-size:1rem;font-weight:400;color:var(--clr-text-muted)">(<?= count($users) ?>)</span></h2>
        </div>

        <!-- Search -->
        <form method="GET" action="users.php" style="display:flex;gap:1rem;margin-bottom:1.5rem">
            <input type="text" name="search" class="form-control" style="max-width:300px"
                   placeholder="Search by name or email…" value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
            <?php if ($search): ?><a href="users.php" class="btn btn-outline">Clear</a><?php endif; ?>
        </form>

        <div class="admin-section">
            <div style="overflow-x:auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Bookings</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                        <tr><td colspan="7" style="text-align:center;padding:2rem;color:var(--clr-text-muted)">No users found.</td></tr>
                        <?php else: ?>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= $u['id'] ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:.75rem">
                                    <div style="width:36px;height:36px;border-radius:50%;background:var(--clr-forest);color:var(--clr-gold);display:grid;place-items:center;font-weight:700;font-size:.85rem;flex-shrink:0">
                                        <?= strtoupper(substr($u['name'],0,1)) ?>
                                    </div>
                                    <strong><?= htmlspecialchars($u['name']) ?></strong>
                                    <?php if ($u['id'] == $_SESSION['user_id']): ?>
                                    <span class="badge badge-success" style="font-size:.68rem">You</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <span class="badge <?= $u['role']==='admin' ? 'badge-warning' : 'badge-success' ?>">
                                    <?= ucfirst($u['role']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="bookings.php?search=<?= urlencode($u['name']) ?>"
                                   style="color:var(--clr-forest);font-weight:600">
                                    <?= $u['booking_count'] ?>
                                </a>
                            </td>
                            <td style="font-size:.85rem;color:var(--clr-text-muted)">
                                <?= date('d M Y', strtotime($u['created_at'])) ?>
                            </td>
                            <td>
                                <div style="display:flex;gap:.4rem">
                                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                    <a href="users.php?toggle_role=<?= $u['id'] ?>"
                                       class="btn btn-sm" style="background:#EDE9FE;color:#5B21B6"
                                       data-confirm="Change role of '<?= htmlspecialchars($u['name']) ?>'?"
                                       title="Toggle Admin">
                                        <i class="fas fa-user-shield"></i>
                                    </a>
                                    <a href="users.php?delete=<?= $u['id'] ?>"
                                       class="btn btn-danger btn-sm"
                                       data-confirm="Delete user '<?= htmlspecialchars($u['name']) ?>' and all their bookings?"
                                       title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <?php else: ?>
                                    <span style="font-size:.8rem;color:var(--clr-text-muted);padding:.4rem .6rem">Current user</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
</div>
<?php include '../includes/footer.php'; ?>
