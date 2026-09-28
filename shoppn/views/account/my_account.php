<?php
//My account page 
require_once __DIR__ . '/../../core/core.php';
require_login();

$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

$name    = $_SESSION['customer_name'] ?? '';
$email   = $_SESSION['customer_email'] ?? '';
$initial = mb_strtoupper(mb_substr($name, 0, 1));

$page_title = 'My account | shoppn';
require __DIR__ . '/../layout/header.php';
?>
<main id="main" class="wrap account-page">

    <?php if ($success): ?>
        <p class="alert alert-success" role="status"><?= e($success) ?></p>
    <?php endif; ?>

    <section class="account-banner">
        <span class="avatar" aria-hidden="true"><?= e($initial) ?></span>
        <div>
            <h1><?= e($name) ?></h1>
            <p><?= e($email) ?></p>
        </div>
        <span class="badge"><?= is_admin() ? 'Administrator' : 'Customer' ?></span>
    </section>

    <div class="account-grid">
        <section class="card">
            <h2>Account details</h2>
            <dl class="details">
                <div><dt>Full name</dt><dd><?= e($name) ?></dd></div>
                <div><dt>Email</dt><dd><?= e($email) ?></dd></div>
                <div><dt>Account type</dt><dd><?= is_admin() ? 'Administrator' : 'Customer' ?></dd></div>
            </dl>
        </section>

        <section class="card">
            <h2>Quick links</h2>
            <div class="quick-links">
                <a class="btn" href="<?= url() ?>">Continue shopping</a>
    
                <a class="btn btn-outline" href="<?= url('logout.php') ?>">Log out</a>
            </div>
        </section>
    </div>
</main>
<?php require __DIR__ . '/../layout/footer.php'; ?>