<?php
// hotel-detail.php — Hotel Details & Booking Form
require_once 'config.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { redirect(SITE_URL . '/hotels.php'); }

$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->execute([$id]);
$hotel = $stmt->fetch();
if (!$hotel) { redirect(SITE_URL . '/hotels.php'); }

// Parse amenities
$amenities = array_filter(array_map('trim', explode(',', $hotel['amenities'] ?? '')));

// Amenity icons mapping
$amenityIcons = [
    'Free WiFi' => 'fas fa-wifi', 'Swimming Pool' => 'fas fa-swimming-pool',
    'Spa' => 'fas fa-spa', 'Gym' => 'fas fa-dumbbell', 'Restaurant' => 'fas fa-utensils',
    'Bar' => 'fas fa-cocktail', 'Room Service' => 'fas fa-concierge-bell',
    'Parking' => 'fas fa-parking', 'Airport Shuttle' => 'fas fa-shuttle-van',
    'Lake View' => 'fas fa-water', 'Kayaking' => 'fas fa-ship',
    'Cycling' => 'fas fa-bicycle', 'Yoga' => 'fas fa-om',
    'Heritage Tours' => 'fas fa-landmark', 'Garden' => 'fas fa-leaf',
    'Library' => 'fas fa-book', 'Rooftop Terrace' => 'fas fa-building',
    'Rooftop' => 'fas fa-building', 'Trekking Guide' => 'fas fa-hiking',
    'Bonfire' => 'fas fa-fire', 'Safari Tours' => 'fas fa-paw',
    'Nature Walks' => 'fas fa-tree', 'Elephant Bathing' => 'fas fa-elephant',
    'Tour Desk' => 'fas fa-map', 'Locker' => 'fas fa-lock',
    '24hr Reception' => 'fas fa-clock',
];

$pageTitle = $hotel['name'];
include 'includes/header.php';
?>

<!-- Breadcrumb -->
<div style="padding: calc(var(--nav-h) + 1rem) 2rem .5rem;max-width:1280px;margin:0 auto">
    <nav class="breadcrumb">
        <a href="<?= SITE_URL ?>">Home</a>
        <i class="fas fa-chevron-right"></i>
        <a href="<?= SITE_URL ?>/hotels.php">Hotels</a>
        <i class="fas fa-chevron-right"></i>
        <span><?= htmlspecialchars($hotel['name']) ?></span>
    </nav>
</div>

<!-- Hero Image -->
<div class="detail-hero" style="margin-top:0">
    <img src="<?= SITE_URL ?>/images/hotels/<?= htmlspecialchars($hotel['image']) ?>"
         alt="<?= htmlspecialchars($hotel['name']) ?>"
         onerror="this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1600&q=80'">
    <div class="detail-hero-content">
        <h1><?= htmlspecialchars($hotel['name']) ?></h1>
        <div class="detail-hero-meta">
            <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($hotel['location']) ?></span>
            <span><i class="fas fa-star" style="color:var(--clr-gold)"></i> <?= number_format($hotel['rating'],1) ?> / 5.0</span>
            <span><i class="fas fa-tag"></i> <?= formatPrice($hotel['price']) ?> / night</span>
        </div>
    </div>
</div>

<!-- Detail Layout -->
<div class="detail-layout">
    <!-- Left: Info -->
    <div>
        <h2>About <?= htmlspecialchars($hotel['name']) ?></h2>
        <p style="margin-top:1rem;color:var(--clr-text-muted);line-height:1.8;font-size:1rem">
            <?= nl2br(htmlspecialchars($hotel['description'])) ?>
        </p>

        <?php if (!empty($amenities)): ?>
        <div style="margin-top:2.5rem">
            <h3>Amenities & Facilities</h3>
            <div class="amenities-grid">
                <?php foreach ($amenities as $am): ?>
                <div class="amenity-item">
                    <i class="<?= $amenityIcons[$am] ?? 'fas fa-check' ?>"></i>
                    <?= htmlspecialchars($am) ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Policies -->
        <div style="margin-top:2.5rem;padding:1.5rem;background:var(--clr-bg);border-radius:var(--radius-md);border:1px solid var(--clr-border)">
            <h4 style="margin-bottom:1rem"><i class="fas fa-info-circle" style="color:var(--clr-forest)"></i> Hotel Policies</h4>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;font-size:.88rem;color:var(--clr-text-muted)">
                <div><strong style="color:var(--clr-text)">Check-In:</strong> 2:00 PM</div>
                <div><strong style="color:var(--clr-text)">Check-Out:</strong> 11:00 AM</div>
                <div><strong style="color:var(--clr-text)">Pets:</strong> Not Allowed</div>
                <div><strong style="color:var(--clr-text)">Smoking:</strong> Designated Areas Only</div>
                <div><strong style="color:var(--clr-text)">Cancellation:</strong> 48 Hours Free</div>
                <div><strong style="color:var(--clr-text)">Children:</strong> Welcome</div>
            </div>
        </div>
    </div>

    <!-- Right: Booking Card -->
    <div>
        <div class="booking-card">
            <div class="booking-card-price">
                <?= formatPrice($hotel['price']) ?>
                <span>/ night</span>
            </div>

            <?php if (!isLoggedIn()): ?>
            <div style="text-align:center;padding:1.5rem 0">
                <p style="color:var(--clr-text-muted);margin-bottom:1rem;font-size:.9rem">
                    Please log in to book this hotel.
                </p>
                <a href="login.php?redirect=hotel-detail.php?id=<?= $hotel['id'] ?>" class="btn btn-primary btn-full">
                    <i class="fas fa-lock"></i> Login to Book
                </a>
                <p style="font-size:.82rem;color:var(--clr-text-muted);margin-top:.75rem">
                    No account? <a href="register.php" style="color:var(--clr-forest);font-weight:600">Register free</a>
                </p>
            </div>
            <?php else: ?>
            <form action="booking.php" method="POST" data-validate-form>
                <input type="hidden" name="hotel_id" value="<?= $hotel['id'] ?>">
                <input type="hidden" id="pricePerNight" value="<?= $hotel['price'] ?>">

                <div class="form-group">
                    <label>Check-In Date</label>
                    <input type="date" name="check_in" id="check_in" class="form-control"
                           data-validate="required"
                           data-label="Check-in date"
                           value="<?= sanitize($_GET['check_in'] ?? '') ?>">
                    <span class="form-error"></span>
                </div>

                <div class="form-group">
                    <label>Check-Out Date</label>
                    <input type="date" name="check_out" id="check_out" class="form-control"
                           data-validate="required"
                           data-label="Check-out date"
                           value="<?= sanitize($_GET['check_out'] ?? '') ?>">
                    <span class="form-error"></span>
                </div>

                <div class="form-group">
                    <label>Number of Guests</label>
                    <select name="guests" class="form-control"
                            data-validate="required">
                        <?php for ($i=1; $i<=8; $i++): ?>
                        <option value="<?= $i ?>" <?= (($_GET['guests'] ?? 1) == $i) ? 'selected' : '' ?>>
                            <?= $i ?> Guest<?= $i > 1 ? 's' : '' ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <?php if (!empty($_GET['check_in']) && !empty($_GET['check_out'])): ?>
                <div id="totalPriceDisplay" style="background:var(--clr-bg);padding:.75rem 1rem;border-radius:var(--radius-sm);font-size:.9rem;color:var(--clr-forest);font-weight:600;margin-bottom:1rem;text-align:center"></div>
                <?php else: ?>
                <div id="totalPriceDisplay" style="background:var(--clr-bg);padding:.75rem 1rem;border-radius:var(--radius-sm);font-size:.9rem;color:var(--clr-forest);font-weight:600;margin-bottom:1rem;text-align:center;min-height:2.5rem"></div>
                <?php endif; ?>

                <button type="submit" class="btn btn-gold btn-full btn-lg">
                    <i class="fas fa-calendar-check"></i> Confirm Booking
                </button>

                <p style="font-size:.78rem;color:var(--clr-text-muted);text-align:center;margin-top:.75rem">
                    <i class="fas fa-lock"></i> Free cancellation within 48 hours
                </p>
            </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
