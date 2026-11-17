<?php

// Uses PHP's configured mail transport; set SHOPPN_MAIL_FROM to a verified sender address.
function send_order_receipt_email(string $recipient, string $customerName, array $order): bool
{
    if (!filter_var($recipient, FILTER_VALIDATE_EMAIL) || empty($order['items']) || !is_array($order['items'])) {
        log_error('Receipt email was not sent because the address or order details were invalid.');
        return false;
    }

    $invoiceNo = (string) ($order['invoice_no'] ?? '');
    $orderId   = (int) ($order['order_id'] ?? 0);
    $total     = isset($order['amount_paid']) && $order['amount_paid'] !== null
        ? (float) $order['amount_paid']
        : array_reduce($order['items'], static function (float $sum, array $item): float {
            return $sum + (int) $item['qty'] * (float) $item['product_price'];
        }, 0.0);

    $lines = [
        'Hello ' . ($customerName !== '' ? $customerName : 'there') . ',',
        '',
        'Thank you for shopping with shoppn. Your payment is confirmed.',
        '',
        'Invoice: #' . $invoiceNo,
        'Order: #' . $orderId,
        'Date: ' . date('j M Y', strtotime((string) $order['order_date'])),
        '',
        'Items:',
    ];

    foreach ($order['items'] as $item) {
        $quantity = (int) $item['qty'];
        $price    = (float) $item['product_price'];
        $lines[]  = sprintf(
            '%d x %s — GH₵ %s',
            $quantity,
            (string) $item['product_title'],
            number_format($quantity * $price, 2)
        );
    }

    $lines[] = '';
    $lines[] = 'Total paid: GH₵ ' . number_format($total, 2);
    $lines[] = '';
    $lines[] = 'Keep this email as your payment receipt.';

    $from = getenv('SHOPPN_MAIL_FROM');
    if ($from === false || $from === '') {
        $from = ini_get('sendmail_from') ?: 'no-reply@shoppn.local';
    }
    if (!filter_var($from, FILTER_VALIDATE_EMAIL) || preg_match('/[\r\n]/', $from)) {
        log_error('Receipt email was not sent because SHOPPN_MAIL_FROM is invalid.');
        return false;
    }

    $headers = [
        'From: shoppn receipts <' . $from . '>',
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
    ];

    set_error_handler(static function (int $severity, string $message): bool {
        throw new ErrorException($message, 0, $severity);
    });

    try {
        $sent = mail(
            $recipient,
            'Your shoppn receipt - Invoice #' . $invoiceNo,
            implode("\r\n", $lines),
            implode("\r\n", $headers)
        );
    } catch (Throwable $ex) {
        log_error('Receipt email could not be sent: ' . $ex->getMessage());
        return false;
    } finally {
        restore_error_handler();
    }

    if (!$sent) {
        log_error('Receipt email transport rejected the message for order #' . $orderId . '.');
        return false;
    }

    return true;
}
