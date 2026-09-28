<?php
require_once __DIR__ . '/../classes/CustomerClass.php';

class CustomerController
{
    private $customer;

    public function __construct()
    {
        $this->customer = new CustomerClass();
    }

    /**
     *register a new customer.
     * @return array ['success' => true, 'customer_id' => N] or ['success' => false, 'error' => '...']
     */
    public function register(array $data): array
    {
        if ($this->customer->emailExists($data['email'])) {
            return ['success' => false, 'error' => 'Email already registered'];
        }

        $hash = password_hash($data['password'], PASSWORD_BCRYPT);

        $newId = $this->customer->addCustomer(
            $data['name'], $data['email'], $hash,
            $data['country'], $data['city'], $data['contact']
        );

        if ($newId === false) {
            return ['success' => false,
                    'error'   => "We couldn't create your account. Please check your details and try again."];
        }

        return ['success' => true, 'customer_id' => $newId];
    }

    /**
     *log a customer in via the Model's login().
     * @return array the customer row on success, or ['error' => '...'] on failure
     */
    public function login(string $email, string $pass): array
    {
        $customer = $this->customer->login($email, $pass);

        if ($customer === false) {
            return ['error' => 'Invalid email or password.'];
        }
        return $customer;
    }
}