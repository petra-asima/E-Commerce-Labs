<?php
// Task 13: confirm the payment with Paystack's server, then save the order.
// Never trust the browser alone: anyone could type ...process_payment_action.php?ref=fake
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../core/paystack_keys.php';
require_once __DIR__ . '/../core/receipt_mailer.php';
require_once __DIR__ . '/../controllers/CartController.php';

/** Send the customer to the failed page with a message, then stop. */
function payment_failed(string $message): void
{
    $_SESSION['error'] = $message;
    redirect('views/payment_failed.php');
}

if (!is_logged_in()) {
    $_SESSION['error'] = 'Please log in to pay for your order.';
    redirect('views/login.php');
}

// 1. The reference must look valid and be the one our payment page created
$reference = trim($_GET['ref'] ?? '');
if (!preg_match('/^[A-Za-z0-9_-]{6,100}$/', $reference)) {
    payment_failed('The payment reference is missing or not valid.');
}
if (empty($_SESSION['pay_ref']) || !hash_equals($_SESSION['pay_ref'], $reference)) {
    payment_failed('This payment does not match your checkout. Please start again from the payment page.');
}

// 2. Work out what the customer should have paid (from the cart, not from the browser)
$cartCtrl = new CartController();
$ip       = get_ip();
if (empty($cartCtrl->getCartItems($ip))) {
    payment_failed('Your cart is empty, so there is nothing to pay for.');
}
$total    = $cartCtrl->getCartTotal($ip);
$expected = (int) round($total * 100);   // in pesewas

// 3. Ask Paystack's Verify API whether this payment really went through
$url     = 'https://api.paystack.co/transaction/verify/' . rawurlencode($reference);
$headers = ['Authorization: Bearer ' . PAYSTACK_SECRET_KEY, 'Cache-Control: no-cache'];

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,     // give the response back as a string
    CURLOPT_HTTPHEADER     => $headers,
    CURLOPT_TIMEOUT        => 30,
]);
$response  = curl_exec($ch);
$curlError = curl_error($ch);
unset($ch);

if ($response === false) {
    error_log('Paystack verify could not connect: ' . $curlError);
    payment_failed('We could not reach Paystack to confirm your payment. Reference: ' . $reference . '.');
}

$result = json_decode($response, true);
$data   = $result['data'] ?? [];

// Only a successful payment of the right amount, in cedis, counts
$confirmed = !empty($result['status'])
    && ($data['status'] ?? '') === 'success'
    && (int) ($data['amount'] ?? 0) === $expected
    && ($data['currency'] ?? '') === 'GHS';

if (!$confirmed) {
    error_log('Paystack did not confirm ' . $reference . ': ' . substr((string) $response, 0, 300));
    payment_failed('Paystack did not confirm this payment, so no order was made.');
}

// 4. Paid: save the order. The reference can only be used once.
unset($_SESSION['pay_ref']);
$orderId = $cartCtrl->processPayment((int) $_SESSION['customer_id'], $ip, $reference, (float) $total);

if (!$orderId) {
    payment_failed('Your payment went through, but we could not save the order. Please contact us with reference ' . $reference . '.');
}

$_SESSION['payment_success_order_id'] = (int) $orderId;
$_SESSION['receipt_email_status'] = 'failed';
try {
    $order = $cartCtrl->getOrderById((int) $orderId);
    if ($order && send_order_receipt_email(
        (string) ($_SESSION['customer_email'] ?? ''),
        (string) ($_SESSION['customer_name'] ?? ''),
        $order
    )) {
        $_SESSION['receipt_email_status'] = 'sent';
    }
} catch (Throwable $ex) {
    error_log('Receipt processing failed for order #' . (int) $orderId . ': ' . $ex->getMessage());
}

redirect('views/payment_success.php?order_id=' . (int) $orderId);