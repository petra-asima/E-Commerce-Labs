<?php
// My account page — logged-in customers only.
// Details come from the database through the controllers (no SQL here).
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/CustomerController.php';
require_once __DIR__ . '/../../controllers/ProductController.php';
require_login();

$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']);

// The customer's saved details
$customerCtrl = new CustomerController();
$me = $customerCtrl->getCustomerById((int) $_SESSION['customer_id']);

$name    = $me['customer_name'] ?? ($_SESSION['customer_name'] ?? '');
$email   = $me['customer_email'] ?? ($_SESSION['customer_email'] ?? '');
$initial = mb_strtoupper(mb_substr($name, 0, 1));
$role    = is_admin() ? 'Administrator' : 'Customer';

// Admins also see live store counts
if (is_admin()) {
    $productCtrl   = new ProductController();
    $brandCount    = count($productCtrl->getAllBrands());
    $categoryCount = count($productCtrl->getAllCategories());
}

$page_title = 'My account | shoppn';
require __DIR__ . '/../layout/header.php';
?>
<main id="main" class="wrap account-page">

    <?php if ($success): ?>
        <p class="alert alert-success" role="status"><?= e($success) ?></p>
    <?php endif; ?>

    <section class="account-banner">
        <span class="avatar" aria-hidden="true"><?= e($initial) ?></span>
        <div class="account-who">
            <h1><?= e($name) ?></h1>
            <p><?= e($email) ?></p>
        </div>
        <span class="badge"><?= $role ?></span>
    </section>

    <div class="account-grid">
        <section class="panel">
            <h2>Your details</h2>
            <dl class="details">
                <div><dt>Full name</dt><dd><?= e($name) ?></dd></div>
                <div><dt>Email</dt><dd><?= e($email) ?></dd></div>
                <div><dt>Phone</dt><dd><?= e($me['customer_contact'] ?? '—') ?></dd></div>
                <div><dt>City</dt><dd><?= e($me['customer_city'] ?? '—') ?></dd></div>
                <div><dt>Country</dt><dd><?= e($me['customer_country'] ?? '—') ?></dd></div>
            </dl>
        </section>

        <section class="panel">
            <h2>Your orders</h2>
            <div class="orders-empty">
                <p>You haven't placed any orders yet.</p>
                <a class="btn" href="<?= url() ?>">Start shopping</a>
            </div>
        </section>
    </div>

    <?php if (is_admin()): ?>
        <section class="panel">
            <h2>Store management</h2>
            <div class="stat-grid">
                <a class="stat" href="<?= url('views/admin/brand.php') ?>">
                    <span class="stat-number"><?= $brandCount ?></span>
                    <span class="stat-label"><?= $brandCount === 1 ? 'Brand' : 'Brands' ?></span>
                    <span class="stat-link">Manage brands</span>
                </a>
                <a class="stat" href="<?= url('views/admin/category.php') ?>">
                    <span class="stat-number"><?= $categoryCount ?></span>
                    <span class="stat-label"><?= $categoryCount === 1 ? 'Category' : 'Categories' ?></span>
                    <span class="stat-link">Manage categories</span>
                </a>
            </div>
        </section>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layout/footer.php'; ?>
