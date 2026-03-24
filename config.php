<?php
// ============================================================
// config.php — Database Connection & Global Configuration
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Change to your MySQL username
define('DB_PASS', '');            // Change to your MySQL password
define('DB_NAME', 'stayease_db');

define('SITE_NAME', 'StayEase');
define('SITE_URL',  'http://localhost/stayease'); // Adjust as needed
define('UPLOAD_DIR', __DIR__ . '/images/hotels/');
define('UPLOAD_URL', SITE_URL . '/images/hotels/');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Database Connection (PDO) ──────────────────────────────
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
}

// ── Helper Functions ───────────────────────────────────────

/**
 * Sanitize input to prevent XSS
 */
function sanitize(string $data): string {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Check if user is logged in; redirect if not
 */
function requireLogin(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . SITE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

/**
 * Check if logged-in user is an admin
 */
function requireAdmin(): void {
    requireLogin();
    if (($_SESSION['user_role'] ?? '') !== 'admin') {
        header('Location: ' . SITE_URL . '/index.php');
        exit;
    }
}

/**
 * Return true if a user is currently logged in
 */
function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

/**
 * Return true if logged-in user is admin
 */
function isAdmin(): bool {
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'admin';
}

/**
 * Flash message helper — set or get a one-time message
 */
function flash(string $key, string $msg = '', string $type = 'success'): string {
    if ($msg) {
        $_SESSION['flash'][$key] = ['msg' => $msg, 'type' => $type];
        return '';
    }
    if (isset($_SESSION['flash'][$key])) {
        $data = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return "<div class=\"alert alert-{$data['type']}\">{$data['msg']}</div>";
    }
    return '';
}

/**
 * Redirect helper
 */
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

/**
 * Format price in NPR
 */
function formatPrice(float $price): string {
    return 'NPR ' . number_format($price, 0);
}
?>
