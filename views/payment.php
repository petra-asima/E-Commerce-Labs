<?php
// Payment page (Task 13): shows the cart summary and opens the Paystack pop-up.
// Gets data through CartController — no SQL here.
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../core/paystack_keys.php';
require_once __DIR__ . '/../controllers/CartController.php';

// An order needs a customer and an email, so the customer must be logged in
if (!is_logged_in()) {
    $_SESSION['error'] = 'Please log in to pay for your order.';
    redirect('views/login.php');
}

$cartCtrl = new CartController();
$ip       = get_ip();
$items    = $cartCtrl->getCartItems($ip);

if (empty($items)) {
    $_SESSION['cart_notice'] = ['type' => 'error', 'text' => 'Your cart is empty. Add a product before paying.'];
    redirect('views/cart.php');
}

$total    = $cartCtrl->getCartTotal($ip);
$pesewas  = (int) round($total * 100);   // Paystack wants the amount in pesewas (GH₵ × 100)

// A new one-time reference for this payment attempt. The action checks that the
// same reference comes back, so nobody can reuse or make up a reference.
$_SESSION['pay_ref'] = 'SHOPPN-' . strtoupper(bin2hex(random_bytes(6)));

// Values printed inside JavaScript are made safe with json_encode
$jsFlags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP;

$page_title = 'Payment | shoppn';
require __DIR__ . '/layout/header.php';
?>
<main id="main" class="wrap cart-page pay-page">
    <div class="section-head">
        <h1 class="page-title">Payment</h1>
        <p class="muted">Check your order, then pay securely with Paystack.</p>
    </div>

    <section class="panel panel--flush" aria-label="Order summary">
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
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <span class="cart-product">
                                    <?php if (!empty($item['product_image'])): ?>
                                        <img src="<?= url('images/products/' . rawurlencode($item['product_image'])) ?>" alt="">
                                    <?php endif; ?>
                                    <?= e($item['product_title']) ?>
                                </span>
                            </td>
                            <td><?= (int) $item['qty'] ?></td>
                            <td class="col-unit">GH₵ <?= number_format((float) $item['product_price'], 2) ?></td>
                            <td>GH₵ <?= number_format($item['qty'] * $item['product_price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="cart-subtotal">
                        <th scope="row" colspan="2">Total</th>
                        <td class="col-unit"></td>
                        <td>GH₵ <?= number_format($total, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </section>

    <p id="pay-message" class="alert alert-error" role="alert" hidden></p>

    <div class="cart-actions">
        <div class="cart-actions-edit">
            <a class="btn btn-ghost" href="<?= url('views/cart.php') ?>">Back to cart</a>
        </div>
        <div class="cart-actions-go">
            <button type="button" class="btn" id="pay-btn">Pay GH₵ <?= number_format($total, 2) ?></button>
        </div>
    </div>

    <p class="muted pay-note">
        You are paying as <strong><?= e($_SESSION['customer_email'] ?? '') ?></strong>.
        Your receipt will be sent to this email by Paystack.
    </p>
</main>

<!-- Paystack pop-up (Brief fix: the brief mixes the old v1 script with the new v2 names onSuccess/onCancel, so this uses v2) -->
<script src="https://js.paystack.co/v2/inline.js"></script>
<script>
function payWithPaystack() {
    var message = document.getElementById('pay-message');
    message.hidden = true;

    var paystack = new PaystackPop();
    paystack.newTransaction({
        key: <?= json_encode(PAYSTACK_PUBLIC_KEY, $jsFlags) ?>,
        email: <?= json_encode($_SESSION['customer_email'] ?? '', $jsFlags) ?>,
        amount: <?= $pesewas ?>,
        currency: 'GHS',
        reference: <?= json_encode($_SESSION['pay_ref'], $jsFlags) ?>,
        onSuccess: function (transaction) {
            // The server checks the payment with Paystack before saving the order
            window.location = <?= json_encode(url('actions/process_payment_action.php'), $jsFlags) ?> +
                '?ref=' + encodeURIComponent(transaction.reference);
        },
        onCancel: function () {
            message.textContent = 'Payment cancelled. Nothing was charged. You can try again when you are ready.';
            message.hidden = false;
        }
    });
}
document.getElementById('pay-btn').addEventListener('click', payWithPaystack);
</script>
<?php require __DIR__ . '/layout/footer.php'; ?>