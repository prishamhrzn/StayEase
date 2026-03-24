<?php
// my-bookings.php — User's booking history
require_once 'config.php';
requireLogin();

// Cancel booking action
if ($_GET['cancel'] ?? null) {
    $bid = (int)$_GET['cancel'];
    $stmt = $pdo->prepare(
        "UPDATE bookings SET status='cancelled'
         WHERE id=? AND user_id=? AND status='confirmed'"
    );
    $stmt->execute([$bid, $_SESSION['user_id']]);
    flash('info', 'Booking #' . str_pad($bid, 5, '0', STR_PAD_LEFT) . ' has been cancelled.');
    redirect(SITE_URL . '/my-bookings.php');
}

$stmt = $pdo->prepare(
    "SELECT b.*, h.name AS hotel_name, h.location, h.image, h.price
     FROM bookings b
     JOIN hotels h ON b.hotel_id = h.id
     WHERE b.user_id = ?
     ORDER BY b.created_at DESC"
);
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();

$pageTitle = 'My Bookings';
include 'includes/header.php';
?>

<div class="page-hero">
    <h1>My Bookings</h1>
    <p>View and manage all your reservations</p>
</div>

<div class="container" style="padding:3rem 2rem">

    <?php if (empty($bookings)): ?>
    <div class="empty-state">
        <i class="fas fa-calendar-times"></i>
        <h3>No bookings yet</h3>
        <p class="mt-1">Ready to plan your next adventure?</p>
        <a href="hotels.php" class="btn btn-primary mt-3">
            <i class="fas fa-hotel"></i> Browse Hotels
        </a>
    </div>
    <?php else: ?>

    <div class="bookings-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Hotel</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                    <th>Guests</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $b): ?>
                <tr>
                    <td><strong><?= str_pad($b['id'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:.75rem">
                            <img src="<?= SITE_URL ?>/images/hotels/<?= htmlspecialchars($b['image']) ?>"
                                 alt="" style="width:48px;height:36px;object-fit:cover;border-radius:4px"
                                 onerror="this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?w=100&q=60'">
                            <div>
                                <div style="font-weight:600;font-size:.9rem"><?= htmlspecialchars($b['hotel_name']) ?></div>
                                <div style="font-size:.78rem;color:var(--clr-text-muted)">
                                    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($b['location']) ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td><?= date('d M Y', strtotime($b['check_in'])) ?></td>
                    <td><?= date('d M Y', strtotime($b['check_out'])) ?></td>
                    <td><?= $b['guests'] ?></td>
                    <td><strong><?= formatPrice($b['total_price']) ?></strong></td>
                    <td>
                        <?php
                        $badgeClass = match($b['status']) {
                            'confirmed'  => 'badge-success',
                            'pending'    => 'badge-warning',
                            'cancelled'  => 'badge-danger',
                            default      => 'badge-warning',
                        };
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= ucfirst($b['status']) ?></span>
                    </td>
                    <td>
                        <div style="display:flex;gap:.4rem;flex-wrap:wrap">
                            <a href="hotel-detail.php?id=<?= $b['hotel_id'] ?>" class="btn btn-outline btn-sm">
                                <i class="fas fa-eye"></i>
                            </a>
                            <?php if ($b['status'] === 'confirmed' && strtotime($b['check_in']) > time()): ?>
                            <a href="my-bookings.php?cancel=<?= $b['id'] ?>"
                               class="btn btn-danger btn-sm"
                               data-confirm="Cancel booking #<?= str_pad($b['id'],5,'0',STR_PAD_LEFT) ?>? This cannot be undone.">
                                <i class="fas fa-times"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
