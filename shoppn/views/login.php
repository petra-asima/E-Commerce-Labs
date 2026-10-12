<?php
// Login form view. Submits to actions/login_action.php.
require_once __DIR__ . '/../core/core.php';

// Already logged in? Go home.
if (is_logged_in()) {
    redirect('index.php');
}

// Display any $_SESSION['error'], then unset it
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);

$page_title = 'Log in | shoppn';
$minimal_header = true;
require __DIR__ . '/layout/header.php';
?>
<main id="main" class="auth">
    <aside class="auth-art">
        <div class="auth-art-inner">
            <h2>Every cloth carries a story.</h2>
            <p>Wax prints, kente and ready-to-wear, sourced from Ghana's makers.</p>
            <ul class="auth-points">
                <li>Shop authentic wax prints and kente</li>
                <li>Save your details for faster checkout</li>
                <li>See all your orders in one place</li>
            </ul>
        </div>
    </aside>

    <div class="auth-panel">
        <div class="auth-card">
            <h1>Welcome back</h1>
            <p class="auth-lead">Log in to see your account and orders.</p>

            <?php if ($error): ?>
                <p class="alert alert-error" role="alert"><?= e($error) ?></p>
            <?php endif; ?>

            <form id="login-form" method="POST" action="<?= url('actions/login_action.php') ?>" novalidate>
                <div class="field">
                    <label for="customer_email">Email</label>
                    <input id="customer_email" name="customer_email" type="email" maxlength="50"
                           autocomplete="email">
                    <span class="field-error" id="email-error" aria-live="polite"></span>
                </div>

                <div class="field">
                    <label for="customer_pass">Password</label>
                    <input id="customer_pass" name="customer_pass" type="password"
                           autocomplete="current-password">
                    <span class="field-error" id="pass-error" aria-live="polite"></span>
                </div>

                <button type="submit" class="btn btn-block">Log in</button>
            </form>

            <p class="form-switch">New to shoppn? <a href="<?= url('views/register.php') ?>">Create an account</a></p>
        </div>
    </div>
</main>

<script src="<?= url('js/validate.js') ?>" defer></script>
<?php require __DIR__ . '/layout/footer.php'; ?>
