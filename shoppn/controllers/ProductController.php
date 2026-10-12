<?php


require_once __DIR__ . '/../classes/ProductClass.php';

class ProductController
{
    private $product;

    public function __construct()
    {
        $this->product = new ProductClass();
    }

    /** Task 5 — add a brand. Returns true on success, false on failure. */
    public function addBrand(string $name): bool
    {
        return $this->product->addBrand($name);
    }

    /** list all brands, A to Z. */
    public function getAllBrands(): array
    {
        return $this->product->getAllBrands();
    }

    /** one brand by id, or false if it doesn't exist. */
    public function getBrandById(int $id)
    {
        return $this->product->getBrandById($id);
    }

    /**  rename a brand. Returns true on success, false on failure. */
    public function updateBrand(int $id, string $name): bool
    {
        return $this->product->updateBrand($id, $name);
    }

    /** add a category. Returns true on success, false on failure. */
    public function addCategory(string $name): bool
    {
        return $this->product->addCategory($name);
    }

    /** list all categories, A to Z. */
    public function getAllCategories(): array
    {
        return $this->product->getAllCategories();
    }
}