<?php
// hotels.php — Hotel Listing Page
require_once 'config.php';
$pageTitle = 'Browse Hotels';

// ── Build query with filters ──────────────────────────────
$where  = ['1=1'];
$params = [];

$location = sanitize($_GET['location'] ?? '');
$minPrice = (float)($_GET['min_price'] ?? 0);
$maxPrice = (float)($_GET['max_price'] ?? 99999);
$minRating = (float)($_GET['rating'] ?? 0);
$sortBy    = in_array($_GET['sort'] ?? '', ['price_asc','price_desc','rating_desc','name_asc'])
           ? $_GET['sort'] : 'rating_desc';

if ($location) {
    $where[]  = 'location LIKE :loc';
    $params[':loc'] = "%$location%";
}
if ($minPrice > 0) { $where[] = 'price >= :minp'; $params[':minp'] = $minPrice; }
if ($maxPrice < 99999) { $where[] = 'price <= :maxp'; $params[':maxp'] = $maxPrice; }
if ($minRating > 0) { $where[] = 'rating >= :minr'; $params[':minr'] = $minRating; }

$orderMap = [
    'price_asc'   => 'price ASC',
    'price_desc'  => 'price DESC',
    'rating_desc' => 'rating DESC',
    'name_asc'    => 'name ASC',
];
$order = $orderMap[$sortBy];
$sql = "SELECT * FROM hotels WHERE " . implode(' AND ', $where) . " ORDER BY $order";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$hotels = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="page-hero">
    <h1>Our Hotels</h1>
    <p><?= count($hotels) ?> propert<?= count($hotels) === 1 ? 'y' : 'ies' ?> found
       <?= $location ? " in <strong>" . htmlspecialchars($location) . "</strong>" : '' ?>
    </p>
</div>

<div class="container">
    <!-- Filters -->
    <form class="filters-bar" id="filterForm" method="GET" action="hotels.php">
        <div class="filter-group" style="flex:2;min-width:200px">
            <label>Search Location</label>
            <input type="text" name="location" placeholder="City or region…"
                   value="<?= htmlspecialchars($location) ?>">
        </div>
        <div class="filter-group">
            <label>Min Price (NPR)</label>
            <input type="number" name="min_price" placeholder="0"
                   value="<?= $minPrice ?: '' ?>" min="0" step="100">
        </div>
        <div class="filter-group">
            <label>Max Price (NPR)</label>
            <input type="number" name="max_price" placeholder="Any"
                   value="<?= $maxPrice < 99999 ? $maxPrice : '' ?>" min="0" step="100">
        </div>
        <div class="filter-group">
            <label>Min Rating</label>
            <select name="rating">
                <option value="">Any</option>
                <?php foreach ([3,3.5,4,4.5] as $r): ?>
                <option value="<?= $r ?>" <?= $minRating == $r ? 'selected' : '' ?>><?= $r ?>+ ★</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>Sort By</label>
            <select name="sort">
                <option value="rating_desc" <?= $sortBy === 'rating_desc' ? 'selected' : '' ?>>Highest Rated</option>
                <option value="price_asc"   <?= $sortBy === 'price_asc'   ? 'selected' : '' ?>>Price: Low → High</option>
                <option value="price_desc"  <?= $sortBy === 'price_desc'  ? 'selected' : '' ?>>Price: High → Low</option>
                <option value="name_asc"    <?= $sortBy === 'name_asc'    ? 'selected' : '' ?>>Name A–Z</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Apply
        </button>
        <a href="hotels.php" class="btn btn-outline">Reset</a>
    </form>

    <!-- Hotel Grid -->
    <?php if (empty($hotels)): ?>
    <div class="empty-state">
        <i class="fas fa-hotel"></i>
        <h3>No hotels match your search</h3>
        <p class="text-muted mt-1">Try different filters or <a href="hotels.php" style="color:var(--clr-forest)">browse all hotels</a>.</p>
    </div>
    <?php else: ?>
    <div class="hotels-grid" style="margin-bottom:4rem">
        <?php foreach ($hotels as $hotel): ?>
        <div class="hotel-card">
            <div class="hotel-card-img">
                <img src="<?= SITE_URL ?>/images/hotels/<?= htmlspecialchars($hotel['image']) ?>"
                     alt="<?= htmlspecialchars($hotel['name']) ?>"
                     onerror="this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600&q=70'">
                <span class="hotel-card-badge">★ <?= number_format($hotel['rating'], 1) ?></span>
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
                <p class="text-muted" style="font-size:.88rem;line-height:1.5;margin-bottom:auto">
                    <?= htmlspecialchars(substr($hotel['description'] ?? '', 0, 90)) ?>…
                </p>
                <div class="hotel-card-footer">
                    <div class="hotel-price">
                        <strong><?= formatPrice($hotel['price']) ?></strong>
                        <span> / night</span>
                    </div>
                    <a href="hotel-detail.php?id=<?= $hotel['id'] ?>" class="btn btn-primary btn-sm">
                        Book Now
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
