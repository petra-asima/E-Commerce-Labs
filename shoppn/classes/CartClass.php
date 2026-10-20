<?php
// Data access for the shopping cart.
// Cart rows are tied to the visitor's IP address, so guests can shop without an account.
require_once __DIR__ . '/../core/db_class.php';

class CartClass extends Database
{
    /**
     * SELECT from cart WHERE p_id = ? AND ip_add = ?
     * @return bool true if this product is already in this visitor's cart
     */
    public function isInCart(int $productId, string $ip): bool
    {
        $stmt = $this->conn->prepare('SELECT 1 FROM cart WHERE p_id = ? AND ip_add = ? LIMIT 1');
        $stmt->bind_param('is', $productId, $ip);
        $stmt->execute();
        $found = $stmt->get_result()->num_rows > 0;
        $stmt->close();
        return $found;
    }

    /**
     * INSERT INTO cart (p_id, ip_add, c_id, qty) VALUES (?, ?, ?, ?)
     * $customerId is saved when the shopper is logged in, and left NULL for guests.
     * @return bool true if the item was added
     */
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

    /**
     * Cart rows joined with each product's title, price and image.
     * @return array list of cart rows
     */
    public function getCartItems(string $ip): array
    {
        $stmt = $this->conn->prepare(
            'SELECT cart.*, products.product_title, products.product_price, products.product_image
             FROM cart
             JOIN products ON cart.p_id = products.product_id
             WHERE cart.ip_add = ?
             ORDER BY products.product_title ASC'
        );
        $stmt->bind_param('s', $ip);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /**
     * SELECT COUNT(*) FROM cart WHERE ip_add = ?
     * @return int number of different products in the cart
     */
    public function getCartCount(string $ip): int
    {
        $stmt = $this->conn->prepare('SELECT COUNT(*) FROM cart WHERE ip_add = ?');
        $stmt->bind_param('s', $ip);
        $stmt->execute();
        $count = (int) $stmt->get_result()->fetch_row()[0];
        $stmt->close();
        return $count;
    }

    /**
     * Go through the cart items and add up qty × price.
     * @return float cart total
     */
    public function getCartTotal(string $ip): float
    {
        $total = 0.0;
        foreach ($this->getCartItems($ip) as $item) {
            $total += (int) $item['qty'] * (float) $item['product_price'];
        }
        return $total;
    }

    /**
     * DELETE FROM cart WHERE p_id = ? AND ip_add = ?
     * @return bool true if the query ran
     */
    public function removeFromCart(int $productId, string $ip): bool
    {
        try {
            $stmt = $this->conn->prepare('DELETE FROM cart WHERE p_id = ? AND ip_add = ?');
            $stmt->bind_param('is', $productId, $ip);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $ex) {
            error_log('removeFromCart failed: ' . $ex->getMessage());
            return false;
        }
    }

    /**
     * UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ?
     * Refuses a quantity below 1 (removing an item is a separate action).
     * @return bool true if the query ran
     */
    public function updateQty(int $productId, int $newQty, string $ip): bool
    {
        if ($newQty < 1) {
            return false;
        }
        try {
            $stmt = $this->conn->prepare('UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ?');
            $stmt->bind_param('iis', $newQty, $productId, $ip);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $ex) {
            error_log('updateQty failed: ' . $ex->getMessage());
            return false;
        }
    }
}