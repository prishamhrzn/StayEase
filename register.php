<?php
// register.php — User Registration
require_once 'config.php';

if (isLoggedIn()) redirect(SITE_URL . '/index.php');

$errors = [];
$data   = ['name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = sanitize($_POST['name']     ?? '');
    $email    = sanitize($_POST['email']    ?? '');
    $password = $_POST['password']          ?? '';
    $confirm  = $_POST['confirm_password']  ?? '';

    $data = ['name' => $name, 'email' => $email];

    // Validation
    if (strlen($name) < 2)        $errors['name']     = 'Name must be at least 2 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Enter a valid email address.';
    if (strlen($password) < 8)    $errors['password'] = 'Password must be at least 8 characters.';
    if ($password !== $confirm)   $errors['confirm']  = 'Passwords do not match.';

    if (empty($errors)) {
        // Check duplicate email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'An account with this email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$name, $email, $hash]);

            $userId = $pdo->lastInsertId();
            $_SESSION['user_id']    = $userId;
            $_SESSION['user_name']  = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role']  = 'user';
            session_regenerate_id(true);

            flash('success', "Welcome to StayEase, $name! Start exploring hotels. 🏨");
            redirect(SITE_URL . '/index.php');
        }
    }
}

$pageTitle = 'Create Account';
include 'includes/header.php';
?>

<div class="form-page">
    <div class="form-card" style="max-width:520px">
        <div class="form-card-header">
            <a href="<?= SITE_URL ?>" class="logo">Stay<span>Ease</span></a>
            <h2>Create Your Account</h2>
            <p>Join thousands of travellers discovering Nepal</p>
        </div>

        <form method="POST" action="register.php" data-validate-form novalidate>

            <div class="form-group">
                <label for="name">Full Name</label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="name" name="name" class="form-control <?= isset($errors['name']) ? 'error' : '' ?>"
                           placeholder="Your full name"
                           value="<?= htmlspecialchars($data['name']) ?>"
                           data-validate="required|minLen:2"
                           data-label="Name">
                </div>
                <span class="form-error <?= isset($errors['name']) ? 'visible' : '' ?>"><?= $errors['name'] ?? '' ?></span>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'error' : '' ?>"
                           placeholder="you@example.com"
                           value="<?= htmlspecialchars($data['email']) ?>"
                           data-validate="required|email"
                           data-label="Email">
                </div>
                <span class="form-error <?= isset($errors['email']) ? 'visible' : '' ?>"><?= $errors['email'] ?? '' ?></span>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control <?= isset($errors['password']) ? 'error' : '' ?>"
                           placeholder="Minimum 8 characters"
                           data-validate="required|minLen:8"
                           data-label="Password">
                </div>
                <span class="form-error <?= isset($errors['password']) ? 'visible' : '' ?>"><?= $errors['password'] ?? '' ?></span>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password"
                           class="form-control <?= isset($errors['confirm']) ? 'error' : '' ?>"
                           placeholder="Repeat your password"
                           data-validate="required|match:password"
                           data-label="Confirm password">
                </div>
                <span class="form-error <?= isset($errors['confirm']) ? 'visible' : '' ?>"><?= $errors['confirm'] ?? '' ?></span>
            </div>

            <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:.5rem">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>

        <div class="form-divider">or</div>
        <p class="text-center" style="font-size:.9rem;color:var(--clr-text-muted)">
            Already have an account?
            <a href="login.php" style="color:var(--clr-forest);font-weight:600">Sign in</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
