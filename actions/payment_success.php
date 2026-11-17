<?php
// Invoice page (Task 13): shown after a confirmed payment.
// Gets data through CartController — no SQL here.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

if (!is_logged_in()) {
    redirect('views/login.php');
}

$orderId = filter_var($_GET['order_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$order   = $orderId ? (new CartController())->getOrderById($orderId) : false;

// Customers may only see their own invoices (admins can see any)
if (!$order || ((int) $order['customer_id'] !== (int) $_SESSION['customer_id'] && !is_admin())) {
    $_SESSION['error'] = 'We could not find that order.';
    redirect('views/payment_failed.php');
}

// Total of the products on the order
$total = 0;
foreach ($order['items'] as $item) {
    $total += $item['qty'] * $item['product_price'];
}
$paid = $order['amount_paid'] !== null ? (float) $order['amount_paid'] : $total;

$page_title = 'Invoice #' . $order['invoice_no'] . ' | shoppn';
require __DIR__ . '/layout/header.php';
?>
<main id="main" class="wrap cart-page pay-page">
    <div class="result-head">
        <span class="material-symbols-outlined result-icon" aria-hidden="true">check_circle</span>
        <div>
            <h1 class="page-title">Thank you, your payment was successful</h1>
            <p class="muted">Your order has been placed. Keep this invoice for your records.</p>
        </div>
    </div>

    <section class="panel panel--flush invoice" aria-label="Invoice">
        <dl class="invoice-meta">
            <div><dt>Invoice number</dt><dd>#<?= e($order['invoice_no']) ?></dd></div>
            <div><dt>Order number</dt><dd><?= (int) $order['order_id'] ?></dd></div>
            <div><dt>Date</dt><dd><?= e(date('j M Y', strtotime($order['order_date']))) ?></dd></div>
            <div><dt>Status</dt><dd><span class="status-paid"><?= e(ucfirst($order['order_status'])) ?></span></dd></div>
            <div><dt>Customer</dt><dd><?= e($_SESSION['customer_name'] ?? '') ?></dd></div>
        </dl>

        <div class="table-wrap">
            <table class="table pay-table">
                <thead>
                    <tr>
                        <th scope="col">Product</th>
                        <th scope="col">Qty</th>
                        <th scope="col" class="col-unit">Unit price</th>
                        <th scope="col">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                        <tr>
                            <td><?= e($item['product_title']) ?></td>
                            <td><?= (int) $item['qty'] ?></td>
                            <td class="col-unit">GH₵ <?= number_format((float) $item['product_price'], 2) ?></td>
                            <td>GH₵ <?= number_format($item['qty'] * $item['product_price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="cart-subtotal">
                        <th scope="row" colspan="2">Total paid</th>
                        <td class="col-unit"></td>
                        <td>GH₵ <?= number_format($paid, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>

    <div class="cart-actions">
        <div class="cart-actions-edit">
            <button type="button" class="btn btn-ghost" onclick="window.print()">Print invoice</button>
        </div>
        <div class="cart-actions-go">
            <a class="btn" href="<?= url() ?>">Continue shopping</a>
        </div>
    </div>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>