<?php
// Controller: sits between the actions/views and CartClass.
// No SQL, no echo, no HTML, no redirects.
require_once __DIR__ . '/../classes/CartClass.php';

class CartController
{
    private $cart;

    public function __construct()
    {
        $this->cart = new CartClass();
    }

    /** Task 11 — is this product already in this visitor's cart? */
    public function isInCart(int $productId, string $ip): bool
    {
        return $this->cart->isInCart($productId, $ip);
    }

    /** Task 11 — add a product to the cart. */
    public function addToCart(int $productId, int $qty, string $ip, ?int $customerId = null): bool
    {
        return $this->cart->addToCart($productId, $qty, $ip, $customerId);
    }

    /** Task 11 — everything in this visitor's cart, with product details. */
    public function getCartItems(string $ip): array
    {
        return $this->cart->getCartItems($ip);
    }

    /** Task 11 — how many different products are in the cart. */
    public function getCartCount(string $ip): int
    {
        return $this->cart->getCartCount($ip);
    }

    /** Task 11 — total price of the cart. */
    public function getCartTotal(string $ip): float
    {
        return $this->cart->getCartTotal($ip);
    }

    /** Task 12 — remove one product from the cart. */
    public function removeFromCart(int $productId, string $ip): bool
    {
        return $this->cart->removeFromCart($productId, $ip);
    }

    /** Task 12 — change the quantity of one product (must be 1 or more). */
    public function updateQty(int $productId, int $newQty, string $ip): bool
    {
        return $this->cart->updateQty($productId, $newQty, $ip);
    }
}