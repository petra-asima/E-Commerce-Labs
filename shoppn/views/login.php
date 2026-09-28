<?php
//Login form view. Submits to actions/login_action.php.
require_once __DIR__ . '/../core/core.php';

//already logged in? Go home.
if (is_logged_in()) {
    redirect('index.php');
}

//display any $_SESSION['error'], then unset it
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);

$page_title = 'Log in | shoppn';
$minimal_header = true;
require __DIR__ . '/layout/header.php';
?>
<main id="main" class="wrap form-page">
    <h1>Log in</h1>

    <?php if ($error): ?>
        <p class="alert alert-error" role="alert"><?= e($error) ?></p>
    <?php endif; ?>

    <!--simple form, Email + Password -->
    <form id="login-form" class="form-card" method="POST"
          action="<?= url('actions/login_action.php') ?>" novalidate>

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

        <button type="submit" class="btn">Log in</button>
    </form>

    <!--link to Register below the form -->
    <p class="form-switch">Don't have an account? <a href="<?= url('views/register.php') ?>">Register</a></p>
</main>

<script src="<?= url('js/validate.js') ?>" defer></script>
<?php require __DIR__ . '/layout/footer.php'; ?>