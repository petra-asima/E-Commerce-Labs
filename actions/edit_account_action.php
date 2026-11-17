
<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

require_login();


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    redirect(BASE_URL . 'views/account/edit_account.php');
}


$name = trim(strip_tags($_POST['name'] ?? ''));

$email = trim(strip_tags($_POST['email'] ?? ''));

$country = trim(strip_tags($_POST['country'] ?? ''));

$city = trim(strip_tags($_POST['city'] ?? ''));

$contact = trim(strip_tags($_POST['contact'] ?? ''));


// Check required fields
if (
    $name === '' ||
    $email === '' ||
    $country === '' ||
    $city === '' ||
    $contact === ''
) {

    $_SESSION['error'] = 'All fields are required.';

    redirect(BASE_URL . 'views/account/edit_account.php');
}


// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $_SESSION['error'] = 'Please enter a valid email address.';

    redirect(BASE_URL . 'views/account/edit_account.php');
}


// Check email length
if (strlen($email) > 100) {

    $_SESSION['error'] = 'Email address must not exceed 100 characters.';

    redirect(BASE_URL . 'views/account/edit_account.php');
}


$data = [

    'name' => $name,

    'email' => $email,

    'country' => $country,

    'city' => $city,

    'contact' => $contact

];


$controller = new CustomerController();


$updated = $controller->updateAccount(
    $_SESSION['customer_id'],
    $data
);


if ($updated) {

    // Update the session with the new information
    $_SESSION['customer_name'] = $name;

    $_SESSION['customer_email'] = $email;

    $_SESSION['success'] = 'Account has been successfully updated.';

    redirect(BASE_URL . 'views/account/my_account.php');
}


$_SESSION['error'] = 'Unable to update your account.';

redirect(BASE_URL . 'views/account/edit_account.php');

