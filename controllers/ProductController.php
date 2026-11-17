<?php
// Business logic between the actions/views and ProductClass.
require_once __DIR__ . '/../classes/ProductClass.php';

class ProductController
{
    private $product;

    public function __construct()
    {
        $this->product = new ProductClass();
    }

    /** Add a brand. Returns true on success, false on failure. */
    public function addBrand(string $name): bool
    {
        return $this->product->addBrand($name);
    }

    /** List all brands, A to Z. */
    public function getAllBrands(): array
    {
        return $this->product->getAllBrands();
    }

    /** One brand by id, or false if it doesn't exist. */
    public function getBrandById(int $id)
    {
        return $this->product->getBrandById($id);
    }

    /** Rename a brand. Returns true on success, false on failure. */
    public function updateBrand(int $id, string $name): bool
    {
        return $this->product->updateBrand($id, $name);
    }

    /** Add a category. Returns true on success, false on failure. */
    public function addCategory(string $name): bool
    {
        return $this->product->addCategory($name);
    }

    /** List all categories, A to Z. */
    public function getAllCategories(): array
    {
        return $this->product->getAllCategories();
    }

    /** One category by id, or false if it doesn't exist. */
    public function getCategoryById(int $id)
    {
        return $this->product->getCategoryById($id);
    }

    /** Rename a category. Returns true on success, false on failure. */
    public function updateCategory(int $id, string $name): bool
    {
        return $this->product->updateCategory($id, $name);
    }

    /** Add a product. The image file is already saved; this receives its filename. */
    public function addProduct(int $cat, int $brand, string $title, float $price,
                               string $desc, string $image_filename, string $keywords): bool
    {
        return $this->product->addProduct($cat, $brand, $title, $price, $desc, $image_filename, $keywords);
    }

    /** Update a product. $image_filename is the new file, or the existing one if unchanged. */
    public function updateProduct(int $id, int $cat, int $brand, string $title, float $price,
                                  string $desc, string $image_filename, string $keywords): bool
    {
        return $this->product->updateProduct($id, $cat, $brand, $title, $price, $desc, $image_filename, $keywords);
    }

    /** One product with category and brand names, or false. */
    public function getProductById(int $id)
    {
        return $this->product->getProductById($id);
    }

    /** All products with category and brand names. */
    public function getAllProducts(): array
    {
        return $this->product->getAllProducts();
    }

    /** Random products for the home page. */
    public function getFeaturedProducts(int $limit = 6): array
    {
        return $this->product->getFeaturedProducts($limit);
    }

    /** Products in one category. */
    public function getProductsByCategory(int $cat_id): array
    {
        return $this->product->getProductsByCategory($cat_id);
    }

    /** Products from one brand. */
    public function getProductsByBrand(int $brand_id): array
    {
        return $this->product->getProductsByBrand($brand_id);
    }

    /** Search product titles and keywords. */
    public function searchProducts(string $query): array
    {
        return $this->product->searchProducts($query);
    }

    /** Categories that have at least one product (for the shop menu, sidebar and tiles). */
    public function getCategoriesWithProducts(): array
    {
        return $this->product->getCategoriesWithProducts();
    }
}