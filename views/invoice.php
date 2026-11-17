<?php
// Dedicated invoice page for a customer's order.
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

$total = 0.0;
foreach ($order['items'] as $item) {
    $total += (int) $item['qty'] * (float) $item['product_price'];
}
$paid = $order['amount_paid'] !== null ? (float) $order['amount_paid'] : $total;
$issueDate = date('j F Y', strtotime($order['order_date']));
$customerName = $order['customer_name'] ?? '';
$customerEmail = $order['customer_email'] ?? '';
$customerContact = $order['customer_contact'] ?? '';
$customerLocation = trim(($order['customer_city'] ?? '') . ', ' . ($order['customer_country'] ?? ''), ', ');
$currency = $order['currency'] ?: 'GHS';
$currencyLabel = $currency === 'GHS' ? 'GH₵' : $currency;

$page_title = 'Invoice #' . $order['invoice_no'] . ' | shoppn';
require __DIR__ . '/layout/header.php';
?>
<main id="main" class="wrap pay-page invoice-page">
    <h1 class="visually-hidden">Invoice #<?= e($order['invoice_no']) ?></h1>
    <article class="invoice-document" aria-labelledby="invoice-number">
        <header class="invoice-document-head">
            <div class="invoice-seller">
                <p class="invoice-eyebrow">Issued by</p>
                <span class="invoice-wordmark">shoppn</span>
            </div>
            <div class="invoice-title-block">
                <p class="invoice-eyebrow">Payment receipt</p>
                <h2 id="invoice-number">Invoice #<?= e($order['invoice_no']) ?></h2>
                <p class="invoice-issue-date">Issued <?= e($issueDate) ?></p>
                <span class="status-paid"><?= e(ucfirst($order['order_status'])) ?></span>
            </div>
        </header>

        <section class="invoice-details" aria-label="Invoice parties and payment information">
            <section class="invoice-bill-to" aria-labelledby="bill-to-heading">
                <h3 id="bill-to-heading">Billed to</h3>
                <p class="invoice-customer-name"><?= e($customerName) ?></p>
                <p class="invoice-customer-email"><?= e($customerEmail) ?></p>
                <?php if ($customerContact !== ''): ?>
                    <p class="invoice-customer-contact"><?= e($customerContact) ?></p>
                <?php endif; ?>
                <?php if ($customerLocation !== ''): ?>
                    <p class="invoice-customer-location"><?= e($customerLocation) ?></p>
                <?php endif; ?>
            </section>

            <dl class="invoice-facts">
                <div>
                    <dt>Order number</dt>
                    <dd>#<?= (int) $order['order_id'] ?></dd>
                </div>
                <div>
                    <dt>Payment date</dt>
                    <dd><?= e($issueDate) ?></dd>
                </div>
                <div>
                    <dt>Payment method</dt>
                    <dd>Paystack · <?= e($currency) ?></dd>
                </div>
            </dl>
        </section>

        <section class="invoice-line-items" aria-labelledby="invoice-items-heading">
            <div class="invoice-items-heading">
                <h3 id="invoice-items-heading">Items and charges</h3>
                <p>Amounts shown in <?= e($currency) ?>.</p>
            </div>
            <div class="table-wrap">
                <table class="table invoice-table">
                    <caption class="visually-hidden">Items included in invoice #<?= e($order['invoice_no']) ?></caption>
                    <thead>
                        <tr>
                            <th scope="col">Description</th>
                            <th scope="col">Qty</th>
                            <th scope="col">Unit price</th>
                            <th scope="col">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td class="invoice-item-name"><?= e($item['product_title']) ?></td>
                                <td><?= (int) $item['qty'] ?></td>
                                <td><?= e($currencyLabel) ?> <?= number_format((float) $item['product_price'], 2) ?></td>
                                <td><?= e($currencyLabel) ?> <?= number_format((int) $item['qty'] * (float) $item['product_price'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="invoice-total-area" aria-label="Invoice totals">
            <dl class="invoice-totals">
                <div>
                    <dt>Subtotal</dt>
                    <dd><?= e($currencyLabel) ?> <?= number_format($total, 2) ?></dd>
                </div>
                <div class="invoice-grand-total">
                    <dt>Total paid</dt>
                    <dd><?= e($currencyLabel) ?> <?= number_format($paid, 2) ?></dd>
                </div>
                <div class="invoice-balance">
                    <dt>Balance due</dt>
                    <dd><?= e($currencyLabel) ?> 0.00</dd>
                </div>
            </dl>
        </section>

        <footer class="invoice-document-footer">
            <div>
                <h3>Payment received</h3>
                <p>Your payment has been recorded successfully. Keep this invoice for your records.</p>
            </div>
            <span class="invoice-thank-you">Thank you for shopping with shoppn.</span>
        </footer>
    </article>

    <div class="cart-actions invoice-actions">
        <div class="cart-actions-edit">
            <button type="button" class="btn btn-ghost" onclick="window.print()">
                <span class="material-symbols-outlined" aria-hidden="true">print</span>
                Print / save invoice
            </button>
        </div>
        <div class="cart-actions-go">
            <a class="btn btn-ghost" href="<?= url('views/account/my_account.php#order-history') ?>">
                View order history
            </a>
            <a class="btn" href="<?= url() ?>">Continue shopping</a>
        </div>
    </div>
</main>
<?php require __DIR__ . '/layout/footer.php'; ?>