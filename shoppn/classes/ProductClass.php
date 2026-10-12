<?php
require_once __DIR__ . '/../core/db_class.php';

class ProductClass extends Database
{
    /**
     * INSERT INTO brands (brand_name) VALUES (?)
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
     *  SELECT * FROM brands ORDER BY brand_name ASC
     * @return array list of brand rows
     */
    public function getAllBrands(): array
    {
        $result = $this->conn->query('SELECT * FROM brands ORDER BY brand_name ASC');
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     *  SELECT * FROM brands WHERE brand_id = ?
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
     *  UPDATE brands SET brand_name = ? WHERE brand_id = ?
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
     *  INSERT INTO categories (cat_name) VALUES (?)
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
     *  SELECT * FROM categories ORDER BY cat_name ASC
     * @return array list of category rows
     */
    public function getAllCategories(): array
    {
        $result = $this->conn->query('SELECT * FROM categories ORDER BY cat_name ASC');
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}