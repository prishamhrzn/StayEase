<?php
// booking.php — Process & confirm booking
require_once 'config.php';
requireLogin();

$errors = [];
$bookingId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hotelId  = (int)($_POST['hotel_id']  ?? 0);
    $checkIn  = sanitize($_POST['check_in']  ?? '');
    $checkOut = sanitize($_POST['check_out'] ?? '');
    $guests   = (int)($_POST['guests'] ?? 1);
    $userId   = $_SESSION['user_id'];

    // Validate
    if (!$hotelId)            $errors[] = 'Invalid hotel.';
    if (!$checkIn)            $errors[] = 'Check-in date is required.';
    if (!$checkOut)           $errors[] = 'Check-out date is required.';
    if ($checkOut <= $checkIn) $errors[] = 'Check-out must be after check-in.';
    if ($guests < 1 || $guests > 20) $errors[] = 'Guest count must be between 1 and 20.';

    if (empty($errors)) {
        // Fetch hotel price
        $hStmt = $pdo->prepare("SELECT price FROM hotels WHERE id = ?");
        $hStmt->execute([$hotelId]);
        $hotel = $hStmt->fetch();
        if (!$hotel) $errors[] = 'Hotel not found.';

        if (empty($errors)) {
            $nights = (int)(( strtotime($checkOut) - strtotime($checkIn) ) / 86400);
            $total  = $hotel['price'] * $nights;

            $stmt = $pdo->prepare(
                "INSERT INTO bookings (user_id, hotel_id, check_in, check_out, guests, total_price, status)
                 VALUES (?, ?, ?, ?, ?, ?, 'confirmed')"
            );
            $stmt->execute([$userId, $hotelId, $checkIn, $checkOut, $guests, $total]);
            $bookingId = $pdo->lastInsertId();
        }
    }

    if (!empty($errors)) {
        flash('error', implode(' ', $errors));
        redirect(SITE_URL . "/hotel-detail.php?id=$hotelId");
    }
} else {
    redirect(SITE_URL . '/hotels.php');
}

// Fetch booking details for confirmation
$stmt = $pdo->prepare(
    "SELECT b.*, h.name AS hotel_name, h.location, h.price, h.image
     FROM bookings b
     JOIN hotels h ON b.hotel_id = h.id
     WHERE b.id = ? AND b.user_id = ?"
);
$stmt->execute([$bookingId, $_SESSION['user_id']]);
$booking = $stmt->fetch();

$pageTitle = 'Booking Confirmed';
include 'includes/header.php';
?>

<div class="confirmation-page">
    <div class="confirmation-card">
        <div class="confirmation-icon">
            <i class="fas fa-check"></i>
        </div>
        <h2 style="color:var(--clr-forest);margin-bottom:.5rem">Booking Confirmed!</h2>
        <p style="color:var(--clr-text-muted)">
            Your reservation at <strong><?= htmlspecialchars($booking['hotel_name']) ?></strong> has been confirmed.
            A confirmation will be sent to <strong><?= htmlspecialchars($_SESSION['user_email']) ?></strong>.
        </p>

        <table class="booking-summary-table">
            <tr>
                <td>Booking ID</td>
                <td>#<?= str_pad($booking['id'], 5, '0', STR_PAD_LEFT) ?></td>
            </tr>
            <tr>
                <td>Hotel</td>
                <td><?= htmlspecialchars($booking['hotel_name']) ?></td>
            </tr>
            <tr>
                <td>Location</td>
                <td><?= htmlspecialchars($booking['location']) ?></td>
            </tr>
            <tr>
                <td>Check-In</td>
                <td><?= date('D, d M Y', strtotime($booking['check_in'])) ?></td>
            </tr>
            <tr>
                <td>Check-Out</td>
                <td><?= date('D, d M Y', strtotime($booking['check_out'])) ?></td>
            </tr>
            <tr>
                <td>Guests</td>
                <td><?= $booking['guests'] ?></td>
            </tr>
            <tr>
                <td>Total Price</td>
                <td style="color:var(--clr-gold);font-size:1.2rem"><?= formatPrice($booking['total_price']) ?></td>
            </tr>
            <tr>
                <td>Status</td>
                <td><span class="badge badge-success">Confirmed</span></td>
            </tr>
        </table>

        <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap;margin-top:1.5rem">
            <a href="my-bookings.php" class="btn btn-primary">
                <i class="fas fa-list"></i> My Bookings
            </a>
            <a href="hotels.php" class="btn btn-outline">
                <i class="fas fa-search"></i> Browse More Hotels
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
