<?php
// Controller: sits between the actions/views and ProductClass.
// No SQL, no echo, no HTML, no redirects.
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

    /** Task 5 — list all brands, A to Z. */
    public function getAllBrands(): array
    {
        return $this->product->getAllBrands();
    }

    /** Task 6 — one brand by id, or false if it doesn't exist. */
    public function getBrandById(int $id)
    {
        return $this->product->getBrandById($id);
    }

    /** Task 6 — rename a brand. Returns true on success, false on failure. */
    public function updateBrand(int $id, string $name): bool
    {
        return $this->product->updateBrand($id, $name);
    }

    /** Task 7 — add a category. Returns true on success, false on failure. */
    public function addCategory(string $name): bool
    {
        return $this->product->addCategory($name);
    }

    /** Task 7 — list all categories, A to Z. */
    public function getAllCategories(): array
    {
        return $this->product->getAllCategories();
    }

    /** Task 8 — one category by id, or false if it doesn't exist. */
    public function getCategoryById(int $id)
    {
        return $this->product->getCategoryById($id);
    }

    /** Task 8 — rename a category. Returns true on success, false on failure. */
    public function updateCategory(int $id, string $name): bool
    {
        return $this->product->updateCategory($id, $name);
    }

    /** Task 9 — add a product. The image file is already saved; this receives its filename. */
    public function addProduct(int $cat, int $brand, string $title, float $price,
                               string $desc, string $image_filename, string $keywords): bool
    {
        return $this->product->addProduct($cat, $brand, $title, $price, $desc, $image_filename, $keywords);
    }

    /** Task 9 — update a product. $image_filename is the new file, or the existing one if unchanged. */
    public function updateProduct(int $id, int $cat, int $brand, string $title, float $price,
                                  string $desc, string $image_filename, string $keywords): bool
    {
        return $this->product->updateProduct($id, $cat, $brand, $title, $price, $desc, $image_filename, $keywords);
    }

    /** Task 9 — one product with category and brand names, or false. */
    public function getProductById(int $id)
    {
        return $this->product->getProductById($id);
    }

    /** Task 9 (admin list) — all products with category and brand names. */
    public function getAllProducts(): array
    {
        return $this->product->getAllProducts();
    }

    /** Task 10 — random products for the home page. */
    public function getFeaturedProducts(int $limit = 6): array
    {
        return $this->product->getFeaturedProducts($limit);
    }

    /** Task 10 — products in one category. */
    public function getProductsByCategory(int $cat_id): array
    {
        return $this->product->getProductsByCategory($cat_id);
    }

    /** Task 10 — products from one brand. */
    public function getProductsByBrand(int $brand_id): array
    {
        return $this->product->getProductsByBrand($brand_id);
    }

    /** Task 10 — search product titles and keywords. */
    public function searchProducts(string $query): array
    {
        return $this->product->searchProducts($query);
    }
}