<?php
// Payment confirmation page. The full invoice is available on views/invoice.php.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

if (!is_logged_in()) {
    redirect('views/login.php');
}

$orderId = filter_var($_GET['order_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$order   = $orderId ? (new CartController())->getOrderById($orderId) : false;

if (!$order || ((int) $order['customer_id'] !== (int) $_SESSION['customer_id'] && !is_admin())) {
    $_SESSION['error'] = 'We could not find that order.';
    redirect('views/payment_failed.php');
}

$receiptEmailStatus = $_SESSION['receipt_email_status'] ?? null;
$showInvoicePopup = isset($_SESSION['payment_success_order_id'])
    && (int) $_SESSION['payment_success_order_id'] === (int) $order['order_id'];
unset($_SESSION['receipt_email_status'], $_SESSION['payment_success_order_id']);

$currency = $order['currency'] ?: 'GHS';
$currencyLabel = $currency === 'GHS' ? 'GH₵' : $currency;
$amountPaid = $order['amount_paid'] !== null ? (float) $order['amount_paid'] : 0.0;
$invoiceUrl = url('views/invoice.php?order_id=' . (int) $order['order_id']);

$page_title = 'Payment successful | shoppn';
require __DIR__ . '/layout/header.php';
?>
<main id="main" class="wrap payment-result-page">
    <section class="payment-result-card" aria-labelledby="payment-result-title">
        <span class="material-symbols-outlined payment-result-icon" aria-hidden="true">check_circle</span>
        <p class="invoice-eyebrow">Payment complete</p>
        <h1 id="payment-result-title">Thank you for your order</h1>
        <p class="muted">Your payment has been confirmed and your order is being processed.</p>

        <dl class="payment-result-summary">
            <div>
                <dt>Order</dt>
                <dd>#<?= (int) $order['order_id'] ?></dd>
            </div>
            <div>
                <dt>Invoice</dt>
                <dd>#<?= e($order['invoice_no']) ?></dd>
            </div>
            <div>
                <dt>Amount paid</dt>
                <dd><?= e($currencyLabel) ?> <?= number_format($amountPaid, 2) ?></dd>
            </div>
        </dl>

        <?php if ($receiptEmailStatus === 'sent'): ?>
            <p class="alert alert-success payment-result-email" role="status">
                Your receipt has been emailed to <strong><?= e($order['customer_email']) ?></strong>.
            </p>
        <?php elseif ($receiptEmailStatus === 'failed'): ?>
            <p class="alert alert-error payment-result-email" role="alert">
                Your order is confirmed, but we couldn't email the receipt. You can view and save it here.
            </p>
        <?php endif; ?>

        <div class="payment-result-actions">
            <a class="btn" href="<?= e($invoiceUrl) ?>">View invoice</a>
            <a class="btn btn-ghost" href="<?= url('views/account/my_account.php#order-history') ?>">View order history</a>
        </div>
    </section>

    <?php if ($showInvoicePopup): ?>
        <dialog class="payment-confirmation" id="payment-confirmation"
                aria-labelledby="payment-confirmation-title"
                aria-describedby="payment-confirmation-description"
                data-open-on-load>
            <button type="button" class="payment-confirmation-close" aria-label="Close dialog" data-close-dialog>
                <span class="material-symbols-outlined" aria-hidden="true">close</span>
            </button>
            <span class="material-symbols-outlined payment-result-icon" aria-hidden="true">task_alt</span>
            <p class="invoice-eyebrow">Payment successful</p>
            <h2 id="payment-confirmation-title">Your order is confirmed</h2>
            <p id="payment-confirmation-description">Your invoice is ready. Open it now to review, print, or save a copy.</p>
            <a class="btn" href="<?= e($invoiceUrl) ?>">View invoice</a>
            <button type="button" class="payment-confirmation-dismiss" data-close-dialog>Maybe later</button>
        </dialog>
    <?php endif; ?>
</main>
<script>
document.querySelectorAll('[data-open-on-load]').forEach(function (dialog) {
    if (typeof dialog.showModal === 'function') {
        dialog.showModal();
    } else {
        dialog.setAttribute('open', '');
        dialog.classList.add('payment-confirmation-fallback');
    }

    dialog.querySelectorAll('[data-close-dialog]').forEach(function (button) {
        button.addEventListener('click', function () {
            if (typeof dialog.close === 'function') {
                dialog.close();
            } else {
                dialog.removeAttribute('open');
                dialog.classList.remove('payment-confirmation-fallback');
            }
        });
    });
});
</script>
<?php require __DIR__ . '/layout/footer.php'; ?>
