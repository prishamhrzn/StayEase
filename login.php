<?php
// login.php — User Login
require_once 'config.php';

if (isLoggedIn()) redirect(SITE_URL . '/index.php');

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($_POST['email']    ?? '');
    $password = $_POST['password'] ?? '';
    $redirect = sanitize($_POST['redirect'] ?? '');

    // Basic validation
    if (empty($email))    $errors['email']    = 'Email is required.';
    if (empty($password)) $errors['password'] = 'Password is required.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // For demo: allow password "password" for all seeded users
        $passwordOk = $user && (
            password_verify($password, $user['password']) ||
            ($password === 'password' && $user['role'] === 'user') ||
            ($password === 'Admin@123' && $user['role'] === 'admin')
        );

        if ($passwordOk) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email']= $user['email'];
            $_SESSION['user_role'] = $user['role'];
            session_regenerate_id(true);

            flash('success', 'Welcome back, ' . htmlspecialchars($user['name']) . '! 👋');
            $dest = ($redirect && strpos($redirect, SITE_URL) === 0) ? $redirect
                  : ($user['role'] === 'admin' ? SITE_URL . '/admin/dashboard.php' : SITE_URL . '/index.php');
            redirect($dest);
        } else {
            $errors['general'] = 'Invalid email or password. Please try again.';
        }
    }
}

$redirect = sanitize($_GET['redirect'] ?? '');
$pageTitle = 'Login';
include 'includes/header.php';
?>

<div class="form-page">
    <div class="form-card">
        <div class="form-card-header">
            <a href="<?= SITE_URL ?>" class="logo">Stay<span>Ease</span></a>
            <h2>Welcome Back</h2>
            <p>Sign in to manage your bookings</p>
        </div>

        <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-error"><?= htmlspecialchars($errors['general']) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" data-validate-form novalidate>
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'error' : '' ?>"
                           placeholder="you@example.com"
                           value="<?= htmlspecialchars($email) ?>"
                           data-validate="required|email"
                           data-label="Email">
                </div>
                <?php if (isset($errors['email'])): ?>
                <span class="form-error visible"><?= $errors['email'] ?></span>
                <?php else: ?>
                <span class="form-error"></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password"
                           class="form-control <?= isset($errors['password']) ? 'error' : '' ?>"
                           placeholder="Enter your password"
                           data-validate="required"
                           data-label="Password">
                </div>
                <?php if (isset($errors['password'])): ?>
                <span class="form-error visible"><?= $errors['password'] ?></span>
                <?php else: ?>
                <span class="form-error"></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:.5rem">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <div class="form-divider">or</div>
        <p class="text-center" style="font-size:.9rem;color:var(--clr-text-muted)">
            Don't have an account?
            <a href="register.php" style="color:var(--clr-forest);font-weight:600">Create one free</a>
        </p>

        <!-- Demo credentials -->
        <div style="margin-top:1.5rem;padding:1rem;background:var(--clr-bg);border-radius:var(--radius-sm);font-size:.8rem;color:var(--clr-text-muted)">
            <strong style="color:var(--clr-text)">Demo Credentials:</strong><br>
            Admin: <code>admin@stayease.com</code> / <code>Admin@123</code><br>
            User: Register a new account or use any registered email with <code>password</code>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
