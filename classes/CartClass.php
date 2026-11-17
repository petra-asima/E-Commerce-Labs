<?php
// Model: all SQL for the cart (Tasks 11–13) lives here.
// No HTML, no echo, no $_POST, no $_SESSION.
//
// Whose cart is it? (Brief fix)
//   • Logged-in customer → the rows with their customer id (c_id), on any device.
//   • Guest (not logged in) → the rows for this IP address that have no customer (c_id IS NULL),
//     as the brief describes.
// So an admin's cart, a customer's cart and a guest's cart on the same laptop are now separate.
require_once __DIR__ . '/../core/db_class.php';

class CartClass extends Database
{
    /**
     * The WHERE part that picks one person's cart rows.
     * @return array [sql, bind types, values]
     */
    private function owner(string $ip, ?int $customerId): array
    {
        if ($customerId) {
            return ['c_id = ?', 'i', [$customerId]];
        }
        return ['ip_add = ? AND c_id IS NULL', 's', [$ip]];
    }

    /** Task 11 — is this product already in this person's cart? */
    public function isInCart(int $productId, string $ip, ?int $customerId = null): bool
    {
        [$where, $types, $values] = $this->owner($ip, $customerId);
        $stmt = $this->conn->prepare("SELECT 1 FROM cart WHERE p_id = ? AND $where LIMIT 1");
        $stmt->bind_param('i' . $types, $productId, ...$values);
        $stmt->execute();
        $stmt->store_result();
        $found = $stmt->num_rows > 0;
        $stmt->close();
        return $found;
    }

    /** Task 11 — add one product row to the cart. */
    public function addToCart(int $productId, int $qty, string $ip, ?int $customerId = null): bool
    {
        try {
            $stmt = $this->conn->prepare('INSERT INTO cart (p_id, ip_add, c_id, qty) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('isii', $productId, $ip, $customerId, $qty);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $ex) {
            error_log('addToCart failed: ' . $ex->getMessage());
            return false;
        }
    }

    /** Task 12 — the cart rows with their product details. */
    public function getCartItems(string $ip, ?int $customerId = null): array
    {
        [$where, $types, $values] = $this->owner($ip, $customerId);
        $stmt = $this->conn->prepare(
            "SELECT cart.p_id, cart.qty, products.*
             FROM cart
             JOIN products ON products.product_id = cart.p_id
             WHERE $where
             ORDER BY products.product_title"
        );
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /** Number of different products in the cart (for the badge on the bag icon). */
    public function getCartCount(string $ip, ?int $customerId = null): int
    {
        [$where, $types, $values] = $this->owner($ip, $customerId);
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS n FROM cart WHERE $where");
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $count = (int) $stmt->get_result()->fetch_assoc()['n'];
        $stmt->close();
        return $count;
    }

    /** Total price of the cart (qty × price for every row). */
    public function getCartTotal(string $ip, ?int $customerId = null): float
    {
        $total = 0.0;
        foreach ($this->getCartItems($ip, $customerId) as $item) {
            $total += $item['qty'] * $item['product_price'];
        }
        return $total;
    }

    /** Task 12 — remove one product from the cart. */
    public function removeFromCart(int $productId, string $ip, ?int $customerId = null): bool
    {
        [$where, $types, $values] = $this->owner($ip, $customerId);
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE p_id = ? AND $where");
        $stmt->bind_param('i' . $types, $productId, ...$values);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /** Task 12 — change the quantity of one product (quantity must be at least 1). */
    public function updateQty(int $productId, int $qty, string $ip, ?int $customerId = null): bool
    {
        if ($qty < 1) {
            return false;
        }
        [$where, $types, $values] = $this->owner($ip, $customerId);
        $stmt = $this->conn->prepare("UPDATE cart SET qty = ? WHERE p_id = ? AND $where");
        $stmt->bind_param('ii' . $types, $qty, $productId, ...$values);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * When a guest logs in, move what they added as a guest into their own cart.
     * If the product is already in their cart, the quantities are added together (up to 99).
     */
    public function mergeGuestCart(string $ip, int $customerId): void
    {
        $this->conn->begin_transaction();
        try {
            $stmt = $this->conn->prepare('SELECT p_id, qty FROM cart WHERE ip_add = ? AND c_id IS NULL');
            $stmt->bind_param('s', $ip);
            $stmt->execute();
            $guestRows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            foreach ($guestRows as $row) {
                $productId = (int) $row['p_id'];
                $qty       = (int) $row['qty'];

                if ($this->isInCart($productId, $ip, $customerId)) {
                    // Already in their cart: add the quantities, then remove the guest row
                    $stmt = $this->conn->prepare('UPDATE cart SET qty = LEAST(qty + ?, 99) WHERE p_id = ? AND c_id = ?');
                    $stmt->bind_param('iii', $qty, $productId, $customerId);
                    $stmt->execute();
                    $stmt->close();

                    $stmt = $this->conn->prepare('DELETE FROM cart WHERE p_id = ? AND ip_add = ? AND c_id IS NULL');
                    $stmt->bind_param('is', $productId, $ip);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    // Not in their cart yet: the guest row becomes theirs
                    $stmt = $this->conn->prepare('UPDATE cart SET c_id = ? WHERE p_id = ? AND ip_add = ? AND c_id IS NULL');
                    $stmt->bind_param('iis', $customerId, $productId, $ip);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            $this->conn->commit();
        } catch (Throwable $ex) {
            $this->conn->rollback();
            error_log('mergeGuestCart failed: ' . $ex->getMessage());
        }
    }

    /**
     * Task 13 — turn the paid cart into an order, all inside ONE database transaction:
     * either every step below is saved, or (if anything fails) nothing is.
     * Paying needs a login, so the cart is found by customer id. $ip is kept to match the brief.
     * $reference is the Paystack reference, only used in the error log.
     * @return int|false  the new order_id, or false if the order could not be saved
     */
    public function processPayment(int $customer_id, string $ip, string $reference, float $amount)
    {
        $this->conn->begin_transaction();
        try {
            // 1. Generate the invoice number
            $invoiceNo = mt_rand(100000, 999999);

            // 2. Create the order (Brief fix: the orders table has no created_at column, so order_date holds the date)
            $stmt = $this->conn->prepare(
                "INSERT INTO orders (customer_id, invoice_no, order_date, order_status)
                 VALUES (?, ?, CURDATE(), 'paid')"
            );
            $stmt->bind_param('ii', $customer_id, $invoiceNo);
            $stmt->execute();
            $orderId = $stmt->insert_id;
            $stmt->close();

            // 3. Copy this customer's cart rows into orderdetails
            $stmt = $this->conn->prepare(
                'INSERT INTO orderdetails (order_id, product_id, qty)
                 SELECT ?, p_id, qty FROM cart WHERE c_id = ?'
            );
            $stmt->bind_param('ii', $orderId, $customer_id);
            $stmt->execute();
            $copied = $stmt->affected_rows;
            $stmt->close();
            if ($copied < 1) {
                throw new RuntimeException('The cart was empty.');
            }

            // Optional: record the payment in the payment table (it is in the database script)
            $currency = 'GHS';
            $stmt = $this->conn->prepare(
                'INSERT INTO payment (amt, customer_id, order_id, currency, payment_date)
                 VALUES (?, ?, ?, ?, CURDATE())'
            );
            $stmt->bind_param('diis', $amount, $customer_id, $orderId, $currency);
            $stmt->execute();
            $stmt->close();

            // 4. Empty this customer's cart
            $stmt = $this->conn->prepare('DELETE FROM cart WHERE c_id = ?');
            $stmt->bind_param('i', $customer_id);
            $stmt->execute();
            $stmt->close();

            $this->conn->commit();
            // 5. Return the new order_id
            return $orderId;
        } catch (Throwable $ex) {
            $this->conn->rollback();   // undo every step above
            error_log('processPayment failed for reference ' . $reference . ': ' . $ex->getMessage());
            return false;
        }
    }

    /** Orders for one customer, with summary fields for account order history. */
    public function getOrdersByCustomerId(int $customer_id): array
    {
        $stmt = $this->conn->prepare(
            'SELECT o.order_id, o.invoice_no, o.order_date, o.order_status,
                    (SELECT SUM(od.qty)
                     FROM orderdetails od
                     WHERE od.order_id = o.order_id) AS item_count,
                    (SELECT p.amt
                     FROM payment p
                     WHERE p.order_id = o.order_id
                     ORDER BY p.pay_id DESC
                     LIMIT 1) AS amount_paid,
                    (SELECT p.currency
                     FROM payment p
                     WHERE p.order_id = o.order_id
                     ORDER BY p.pay_id DESC
                     LIMIT 1) AS currency
             FROM orders o
             WHERE o.customer_id = ?
             ORDER BY o.order_date DESC, o.order_id DESC'
        );
        $stmt->bind_param('i', $customer_id);
        $stmt->execute();
        $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $orders;
    }

    /**
     * Task 13 — one order with its products, for the invoice page.
     * @return array|false  order row plus 'items' (product, qty, price), or false if not found
     */
    public function getOrderById(int $order_id)
    {
        $stmt = $this->conn->prepare(
            'SELECT o.order_id, o.customer_id, o.invoice_no, o.order_date, o.order_status,
                    pay.amt AS amount_paid, pay.currency,
                    c.customer_name, c.customer_email, c.customer_contact,
                    c.customer_city, c.customer_country
             FROM orders o
             LEFT JOIN payment pay ON pay.order_id = o.order_id
             JOIN customer c ON c.customer_id = o.customer_id
             WHERE o.order_id = ?'
        );
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$order) {
            return false;
        }

        $stmt = $this->conn->prepare(
            'SELECT d.product_id, d.qty, p.product_title, p.product_price, p.product_image
             FROM orderdetails d
             JOIN products p ON p.product_id = d.product_id
             WHERE d.order_id = ?
             ORDER BY p.product_title'
        );
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $order['items'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $order;
    }
}