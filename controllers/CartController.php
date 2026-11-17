<?php
// Controller: passes cart requests from Actions and Views to the Model (CartClass).
// No SQL, no HTML, no redirects here.
//
// Brief fix: the cart now belongs to the logged-in customer (not just the IP address).
// The controller adds the logged-in customer's id to every call, so the Actions and
// Views did not need to change: they still pass get_ip() as before.
require_once __DIR__ . '/../classes/CartClass.php';

class CartController
{
    private $cart;

    public function __construct()
    {
        $this->cart = new CartClass();
    }

    /** The logged-in customer's id, or null for a guest. */
    private function customerId(): ?int
    {
        return isset($_SESSION['customer_id']) ? (int) $_SESSION['customer_id'] : null;
    }

    /**
     * Once after each login: move anything added as a guest into the customer's cart.
     * The flag is removed when they log out (the whole session is cleared).
     */
    private function mergeAfterLogin(string $ip): void
    {
        $customerId = $this->customerId();
        if ($customerId && empty($_SESSION['cart_merged'])) {
            $this->cart->mergeGuestCart($ip, $customerId);
            $_SESSION['cart_merged'] = true;
        }
    }

    public function isInCart(int $productId, string $ip): bool
    {
        $this->mergeAfterLogin($ip);
        return $this->cart->isInCart($productId, $ip, $this->customerId());
    }

    // $customerId is kept so the existing add_to_cart_action still works; the session value is used.
    public function addToCart(int $productId, int $qty, string $ip, ?int $customerId = null): bool
    {
        $this->mergeAfterLogin($ip);
        return $this->cart->addToCart($productId, $qty, $ip, $this->customerId());
    }

    public function getCartItems(string $ip): array
    {
        $this->mergeAfterLogin($ip);
        return $this->cart->getCartItems($ip, $this->customerId());
    }

    public function getCartCount(string $ip): int
    {
        $this->mergeAfterLogin($ip);
        return $this->cart->getCartCount($ip, $this->customerId());
    }

    public function getCartTotal(string $ip): float
    {
        $this->mergeAfterLogin($ip);
        return $this->cart->getCartTotal($ip, $this->customerId());
    }

    public function removeFromCart(int $productId, string $ip): bool
    {
        return $this->cart->removeFromCart($productId, $ip, $this->customerId());
    }

    public function updateQty(int $productId, int $qty, string $ip): bool
    {
        return $this->cart->updateQty($productId, $qty, $ip, $this->customerId());
    }

    // Task 13
    public function processPayment(int $customer_id, string $ip, string $reference, float $amount)
    {
        return $this->cart->processPayment($customer_id, $ip, $reference, $amount);
    }

    public function getOrderById(int $order_id)
    {
        return $this->cart->getOrderById($order_id);
    }

    public function getOrdersByCustomerId(int $customer_id): array
    {
        return $this->cart->getOrdersByCustomerId($customer_id);
    }
}