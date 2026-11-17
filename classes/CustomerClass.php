<?php
require_once __DIR__ . '/../core/db_class.php';

class CustomerClass extends Database
{
    /**
     * Is this email already registered?
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        $stmt = $this->conn->prepare('SELECT customer_email FROM customer WHERE customer_email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    /**
     * Insert a new customer. $hash must already be hashed with password_hash().
     * @return int|false  the new customer_id, or false if the insert failed
     */
    public function addCustomer(string $name, string $email, string $hash,
                                string $country, string $city, string $contact)
    {
        try {
            $stmt = $this->conn->prepare(
                'INSERT INTO customer
                    (customer_name, customer_email, customer_pass,
                     customer_country, customer_city, customer_contact)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->bind_param('ssssss', $name, $email, $hash, $country, $city, $contact);
            $stmt->execute();
            $newId = $stmt->insert_id;
            $stmt->close();
            return $newId;
        } catch (mysqli_sql_exception $ex) {
            error_log('addCustomer failed: ' . $ex->getMessage());
            return false;
        }
    }

    /**
     * SELECT * FROM customer WHERE customer_email = ?
     * @return array|false  one customer row, or false if not found
     */
    public function getCustomerByEmail(string $email)
    {
        $stmt = $this->conn->prepare('SELECT * FROM customer WHERE customer_email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: false;
    }

    /**
     * Find the customer, then check the password with password_verify().
     * @return array|false  the customer row on success, false on failure
     */
    public function login(string $email, string $pass)
    {
        $row = $this->getCustomerByEmail($email);

        if ($row && password_verify($pass, $row['customer_pass'])) {
            return $row;
        }
        return false;
    }

    /**
     * My account page — one customer's details by id.
     * The password hash is deliberately NOT selected.
     * @return array|false  the customer's details, or false if not found
     */
    public function getCustomerById(int $id)
    {
        $stmt = $this->conn->prepare(
            'SELECT customer_id, customer_name, customer_email, customer_country,
                    customer_city, customer_contact, user_role
             FROM customer WHERE customer_id = ?'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: false;
    }
}
