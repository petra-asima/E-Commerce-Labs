<?php
// Model: all SQL for brands, categories and products lives here.
// No HTML, no echo, no $_POST, no $_SESSION.
require_once __DIR__ . '/../core/db_class.php';

class ProductClass extends Database
{
    /**
     * Task 5 — INSERT INTO brands (brand_name) VALUES (?)
     * @return bool true if the brand was added
     */
    public function addBrand(string $name): bool
    {
        try {
            $stmt = $this->conn->prepare('INSERT INTO brands (brand_name) VALUES (?)');
            $stmt->bind_param('s', $name);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $ex) {
            error_log('addBrand failed: ' . $ex->getMessage());
            return false;
        }
    }

    /**
     * Task 5 — SELECT * FROM brands ORDER BY brand_name ASC
     * @return array list of brand rows
     */
    public function getAllBrands(): array
    {
        $result = $this->conn->query('SELECT * FROM brands ORDER BY brand_name ASC');
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Task 6 — SELECT * FROM brands WHERE brand_id = ?
     * @return array|false one brand row, or false if not found
     */
    public function getBrandById(int $id)
    {
        $stmt = $this->conn->prepare('SELECT * FROM brands WHERE brand_id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: false;
    }

    /**
     * Task 6 — UPDATE brands SET brand_name = ? WHERE brand_id = ?
     * @return bool true if the update ran
     */
    public function updateBrand(int $id, string $name): bool
    {
        try {
            $stmt = $this->conn->prepare('UPDATE brands SET brand_name = ? WHERE brand_id = ?');
            $stmt->bind_param('si', $name, $id);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $ex) {
            error_log('updateBrand failed: ' . $ex->getMessage());
            return false;
        }
    }

    /**
     * Task 7 — INSERT INTO categories (cat_name) VALUES (?)
     * @return bool true if the category was added
     */
    public function addCategory(string $name): bool
    {
        try {
            $stmt = $this->conn->prepare('INSERT INTO categories (cat_name) VALUES (?)');
            $stmt->bind_param('s', $name);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $ex) {
            error_log('addCategory failed: ' . $ex->getMessage());
            return false;
        }
    }

    /**
     * Task 7 — SELECT * FROM categories ORDER BY cat_name ASC
     * @return array list of category rows
     */
    public function getAllCategories(): array
    {
        $result = $this->conn->query('SELECT * FROM categories ORDER BY cat_name ASC');
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Task 8 — SELECT * FROM categories WHERE cat_id = ?
     * @return array|false one category row, or false if not found
     */
    public function getCategoryById(int $id)
    {
        $stmt = $this->conn->prepare('SELECT * FROM categories WHERE cat_id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: false;
    }

    /**
     * Task 8 — UPDATE categories SET cat_name = ? WHERE cat_id = ?
     * @return bool true if the update ran
     */
    public function updateCategory(int $id, string $name): bool
    {
        try {
            $stmt = $this->conn->prepare('UPDATE categories SET cat_name = ? WHERE cat_id = ?');
            $stmt->bind_param('si', $name, $id);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $ex) {
            error_log('updateCategory failed: ' . $ex->getMessage());
            return false;
        }
    }

    /**
     * Task 9 — INSERT a new product (prepared statement).
     * @return bool true if the product was added
     */
    public function addProduct(int $cat, int $brand, string $title, float $price,
                               string $desc, string $image_filename, string $keywords): bool
    {
        try {
            $stmt = $this->conn->prepare(
                'INSERT INTO products
                    (product_cat, product_brand, product_title, product_price,
                     product_desc, product_image, product_keywords)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->bind_param('iisdsss', $cat, $brand, $title, $price, $desc, $image_filename, $keywords);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $ex) {
            error_log('addProduct failed: ' . $ex->getMessage());
            return false;
        }
    }

    /**
     * Task 9 — UPDATE an existing product (prepared statement).
     * @return bool true if the update ran
     */
    public function updateProduct(int $id, int $cat, int $brand, string $title, float $price,
                                  string $desc, string $image_filename, string $keywords): bool
    {
        try {
            $stmt = $this->conn->prepare(
                'UPDATE products
                 SET product_cat = ?, product_brand = ?, product_title = ?, product_price = ?,
                     product_desc = ?, product_image = ?, product_keywords = ?
                 WHERE product_id = ?'
            );
            $stmt->bind_param('iisdsssi', $cat, $brand, $title, $price, $desc, $image_filename, $keywords, $id);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        } catch (mysqli_sql_exception $ex) {
            error_log('updateProduct failed: ' . $ex->getMessage());
            return false;
        }
    }

    /**
     * Task 9 — one product, with its category name and brand name (JOIN).
     * @return array|false the product row, or false if not found
     */
    public function getProductById(int $id)
    {
        $stmt = $this->conn->prepare(
            'SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             JOIN categories c ON p.product_cat = c.cat_id
             JOIN brands b     ON p.product_brand = b.brand_id
             WHERE p.product_id = ?'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: false;
    }

    /**
     * Task 9 (admin list) — every product with its category and brand names, newest first.
     * @return array list of product rows (empty array if none)
     */
    public function getAllProducts(): array
    {
        $result = $this->conn->query(
            'SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             JOIN categories c ON p.product_cat = c.cat_id
             JOIN brands b     ON p.product_brand = b.brand_id
             ORDER BY p.product_id DESC'
        );
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ── Task 10: customer-facing product lists ─────────────────
    // Every list also returns the brand and category names, for the product cards.
    // All of them return an array of rows: an empty array when nothing matches, never false.

    /**
     * Task 10 — random selection for the home page (ORDER BY RAND() LIMIT ?).
     */
    public function getFeaturedProducts(int $limit = 6): array
    {
        $stmt = $this->conn->prepare(
            'SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             JOIN categories c ON p.product_cat = c.cat_id
             JOIN brands b     ON p.product_brand = b.brand_id
             ORDER BY RAND()
             LIMIT ?'
        );
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /**
     * Task 10 — products in one category (WHERE product_cat = ?).
     */
    public function getProductsByCategory(int $cat_id): array
    {
        $stmt = $this->conn->prepare(
            'SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             JOIN categories c ON p.product_cat = c.cat_id
             JOIN brands b     ON p.product_brand = b.brand_id
             WHERE p.product_cat = ?
             ORDER BY p.product_title ASC'
        );
        $stmt->bind_param('i', $cat_id);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /**
     * Task 10 — products from one brand (WHERE product_brand = ?).
     */
    public function getProductsByBrand(int $brand_id): array
    {
        $stmt = $this->conn->prepare(
            'SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             JOIN categories c ON p.product_cat = c.cat_id
             JOIN brands b     ON p.product_brand = b.brand_id
             WHERE p.product_brand = ?
             ORDER BY p.product_title ASC'
        );
        $stmt->bind_param('i', $brand_id);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }

    /**
     * Task 10 — search titles and keywords (LIKE ? with '%' . $query . '%').
     */
    public function searchProducts(string $query): array
    {
        // Escape % and _ so they are searched for literally, not used as wildcards
        $like = '%' . addcslashes($query, '%_\\') . '%';

        $stmt = $this->conn->prepare(
            'SELECT p.*, c.cat_name, b.brand_name
             FROM products p
             JOIN categories c ON p.product_cat = c.cat_id
             JOIN brands b     ON p.product_brand = b.brand_id
             WHERE p.product_title LIKE ? OR p.product_keywords LIKE ?
             ORDER BY p.product_title ASC'
        );
        $stmt->bind_param('ss', $like, $like);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }
}