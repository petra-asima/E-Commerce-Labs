<?php
// Shown when a payment could not be confirmed (Task 13).
require_once __DIR__ . '/../core/core.php';

$error = $_SESSION['error'] ?? 'Your payment could not be completed.';
unset($_SESSION['error']);

$page_title = 'Payment not completed | shoppn';
require __DIR__ . '/layout/header.php';
?>
<main id="main" class="wrap cart-page pay-page">
    <div class="result-head">
        <span class="material-symbols-outlined result-icon result-icon--error" aria-hidden="true">error</span>
        <div>
            <h1 class="page-title">Payment not completed</h1>
            <p class="muted">No order was placed. Your cart has been kept so you can try again.</p>
        </div>
    </div>

    <p class="alert alert-error" role="alert"><?= e($error) ?></p>

    <div class="cart-actions">
        <div class="cart-actions-edit">
            <a class="btn btn-ghost" href="<?= url('views/cart.php') ?>">Back to cart</a>
        </div>
        <div class="cart-actions-go">
            <a class="btn" href="<?= url('views/payment.php') ?>">Try again</a>
        </div>
    </div>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>