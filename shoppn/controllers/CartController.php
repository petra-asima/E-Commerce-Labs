<?php
// Business logic between the actions/views and CartClass.
require_once __DIR__ . '/../classes/CartClass.php';

class CartController
{
    private $cart;

    public function __construct()
    {
        $this->cart = new CartClass();
    }

    /** Is this product already in this visitor's cart? */
    public function isInCart(int $productId, string $ip): bool
    {
        return $this->cart->isInCart($productId, $ip);
    }

    /** Add a product to the cart. */
    public function addToCart(int $productId, int $qty, string $ip, ?int $customerId = null): bool
    {
        return $this->cart->addToCart($productId, $qty, $ip, $customerId);
    }

    /** Everything in this visitor's cart, with product details. */
    public function getCartItems(string $ip): array
    {
        return $this->cart->getCartItems($ip);
    }

    /** How many different products are in the cart. */
    public function getCartCount(string $ip): int
    {
        return $this->cart->getCartCount($ip);
    }

    /** Total price of the cart. */
    public function getCartTotal(string $ip): float
    {
        return $this->cart->getCartTotal($ip);
    }

    /** Remove one product from the cart. */
    public function removeFromCart(int $productId, string $ip): bool
    {
        return $this->cart->removeFromCart($productId, $ip);
    }

    /** Change the quantity of one product (must be 1 or more). */
    public function updateQty(int $productId, int $newQty, string $ip): bool
    {
        return $this->cart->updateQty($productId, $newQty, $ip);
    }
}