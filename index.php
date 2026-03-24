<?php
// index.php — Home Page
require_once 'config.php';
$pageTitle = 'Discover Nepal\'s Finest Hotels';

// Fetch featured hotels
$stmt = $pdo->query("SELECT * FROM hotels ORDER BY rating DESC LIMIT 6");
$featuredHotels = $stmt->fetchAll();

include 'includes/header.php';
?>

<!-- ── Hero ─────────────────────────────────────────────── -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-content">
        <div class="hero-badge">
            <i class="fas fa-star"></i> Nepal's #1 Hotel Booking Platform
        </div>
        <h1>Find Your Perfect<br>Home Away From Home</h1>
        <p>From luxury mountain retreats to charming heritage inns — discover handpicked stays across Nepal's most breathtaking destinations.</p>

        <!-- Search Box -->
        <form class="search-box" id="searchForm" novalidate>
            <div class="search-field">
                <label for="searchLocation"><i class="fas fa-map-marker-alt"></i> Destination</label>
                <input type="text" id="searchLocation" placeholder="Kathmandu, Pokhara…"
                       value="<?= htmlspecialchars($_GET['location'] ?? '') ?>">
            </div>
            <div class="search-field">
                <label for="searchCheckIn"><i class="fas fa-calendar"></i> Check-In</label>
                <input type="date" id="searchCheckIn">
            </div>
            <div class="search-field">
                <label for="searchCheckOut"><i class="fas fa-calendar-check"></i> Check-Out</label>
                <input type="date" id="searchCheckOut">
            </div>
            <div class="search-field">
                <label for="searchGuests"><i class="fas fa-users"></i> Guests</label>
                <select id="searchGuests">
                    <option value="1">1 Guest</option>
                    <option value="2">2 Guests</option>
                    <option value="3">3 Guests</option>
                    <option value="4">4+ Guests</option>
                </select>
            </div>
            <button type="submit" class="btn btn-gold btn-lg">
                <i class="fas fa-search"></i> Search Hotels
            </button>
        </form>
    </div>
</section>

<!-- ── Stats ─────────────────────────────────────────────── -->
<section class="stats-strip">
    <div class="stats-grid">
        <div class="stat-item">
            <h3><span data-target="200" data-suffix="+">0</span></h3>
            <p>Hotels Listed</p>
        </div>
        <div class="stat-item">
            <h3><span data-target="50000" data-suffix="+">0</span></h3>
            <p>Happy Guests</p>
        </div>
        <div class="stat-item">
            <h3><span data-target="25" data-suffix="+">0</span></h3>
            <p>Destinations</p>
        </div>
        <div class="stat-item">
            <h3><span data-target="4.8">0</span>★</h3>
            <p>Average Rating</p>
        </div>
    </div>
</section>

<!-- ── Featured Hotels ───────────────────────────────────── -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Handpicked For You</span>
            <h2>Featured Hotels & Resorts</h2>
            <p>Curated selections from Nepal's most exceptional properties, rated by thousands of guests.</p>
        </div>

        <div class="hotels-grid">
            <?php foreach ($featuredHotels as $hotel): ?>
            <div class="hotel-card">
                <div class="hotel-card-img">
                    <img src="<?= SITE_URL ?>/images/hotels/<?= htmlspecialchars($hotel['image']) ?>"
                         alt="<?= htmlspecialchars($hotel['name']) ?>"
                         onerror="this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=70'">
                    <span class="hotel-card-badge">
                        ★ <?= number_format($hotel['rating'], 1) ?>
                    </span>
                </div>
                <div class="hotel-card-body">
                    <div class="hotel-card-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <?= htmlspecialchars($hotel['location']) ?>
                    </div>
                    <h3 class="hotel-card-title"><?= htmlspecialchars($hotel['name']) ?></h3>
                    <div class="hotel-card-rating">
                        <span class="stars">
                            <?php for ($i=1; $i<=5; $i++): ?>
                                <i class="fa<?= $i <= $hotel['rating'] ? 's' : 'r' ?> fa-star"></i>
                            <?php endfor; ?>
                        </span>
                        <span class="text-muted">(<?= rand(42,312) ?> reviews)</span>
                    </div>
                    <p class="text-muted" style="font-size:.88rem;line-height:1.5">
                        <?= htmlspecialchars(substr($hotel['description'] ?? '', 0, 90)) ?>…
                    </p>
                    <div class="hotel-card-footer">
                        <div class="hotel-price">
                            <strong><?= formatPrice($hotel['price']) ?></strong>
                            <span> / night</span>
                        </div>
                        <a href="hotel-detail.php?id=<?= $hotel['id'] ?>" class="btn btn-primary btn-sm">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <a href="hotels.php" class="btn btn-outline btn-lg">
                <i class="fas fa-th-large"></i> Browse All Hotels
            </a>
        </div>
    </div>
</section>

<!-- ── Why StayEase ──────────────────────────────────────── -->
<section class="section" style="background:var(--clr-forest);color:white;padding:5rem 2rem">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Why Choose Us</span>
            <h2 style="color:white">The StayEase Difference</h2>
            <p style="color:rgba(255,255,255,.7)">We obsess over every detail so your stay exceeds every expectation.</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:2rem;max-width:1000px;margin:0 auto;text-align:center">
            <?php
            $features = [
                ['fas fa-shield-alt',    'Secure Booking',     'Bank-level security on all transactions'],
                ['fas fa-thumbs-up',     'Best Price Promise',  'We match any lower rate you find'],
                ['fas fa-headset',       '24/7 Support',       'Local experts available around the clock'],
                ['fas fa-hand-holding-heart', 'Curated Quality', 'Every hotel personally vetted by our team'],
            ];
            foreach ($features as [$icon, $title, $desc]):
            ?>
            <div>
                <div style="width:64px;height:64px;border-radius:50%;background:rgba(201,168,76,.15);display:grid;place-items:center;margin:0 auto 1rem;font-size:1.4rem;color:var(--clr-gold)">
                    <i class="<?= $icon ?>"></i>
                </div>
                <h4 style="color:white;font-family:var(--font-display);margin-bottom:.4rem"><?= $title ?></h4>
                <p style="color:rgba(255,255,255,.65);font-size:.88rem"><?= $desc ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── Testimonials ──────────────────────────────────────── -->
<section class="section testimonials">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Guest Stories</span>
            <h2>What Our Guests Say</h2>
            <p>Real reviews from real travellers who discovered Nepal with StayEase.</p>
        </div>
        <div class="testimonials-grid">
            <?php
            $testimonials = [
                ['"The Grand Himalayan was beyond our expectations. The mountain views from our suite were absolutely magical. StayEase made the whole booking process effortless."', 'Priya Sharma', 'Delhi, India', 'P'],
                ['"Found the perfect boutique hotel in Bhaktapur through StayEase. The heritage courtyard experience was unlike anything else. Will definitely use again!"', 'James Wilson', 'London, UK', 'J'],
                ['"Booked the jungle retreat in Chitwan for our anniversary. The safari tours were incredible, and the eco-lodge was beautifully designed. 10/10!"', 'Anita Gurung', 'Kathmandu, Nepal', 'A'],
            ];
            foreach ($testimonials as [$quote, $name, $loc, $initial]):
            ?>
            <div class="testimonial-card">
                <p><?= $quote ?></p>
                <div class="testimonial-author">
                    <div class="testimonial-author-avatar"><?= $initial ?></div>
                    <div>
                        <div class="testimonial-author-name"><?= $name ?></div>
                        <div class="testimonial-author-loc"><?= $loc ?></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
