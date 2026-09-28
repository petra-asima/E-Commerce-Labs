<?php
//handles the registration form submit.
//Reads $_POST, validates, calls the controller, sets the session, redirects. 
require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';

//Only accept form submissions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('views/register.php');
}

//Collect and sanitise 
$name    = trim(strip_tags($_POST['customer_name'] ?? ''));
$email   = trim($_POST['customer_email'] ?? '');
$pass    = $_POST['customer_pass'] ?? '';          
$country = trim($_POST['customer_country'] ?? '');
$city    = trim(strip_tags($_POST['customer_city'] ?? ''));
$dial    = $_POST['dial_code'] ?? '';
$contact = trim($_POST['customer_contact'] ?? '');  

//Validate (limits match the customer table) 
$errors = [];

if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
    $errors[] = 'Full name must be 2-100 characters.';
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 50) {
    $errors[] = 'Enter a valid email address (max 50 characters).';
}
if (!preg_match('/^(?=.*\d).{8,72}$/', $pass)) {
    $errors[] = 'Password must be 8-72 characters and include at least one number.';
}
if (!in_array($country, COUNTRIES, true)) {
    $errors[] = 'Choose a country from the list.';
}
if (mb_strlen($city) < 2 || mb_strlen($city) > 30) {
    $errors[] = 'City must be 2-30 characters.';
}

//Phone: build the full number, e.g. GH + "024 123 4567" "+233241234567"
$fullContact = '';
if (!isset(DIAL_CODES[$dial])) {
    $errors[] = 'Choose a country code.';
} else {
    $digits = preg_replace('/[\s\-]/', '', $contact);  
    $digits = ltrim($digits, '0');                      
    $fullContact = DIAL_CODES[$dial][0] . $digits;

    //the number we will store (max 15 = DB column)
    if (!preg_match('/^[0-9]{6,12}$/', $digits) || !preg_match('/^[0-9+\-\s]{7,15}$/', $fullContact)) {
        $errors[] = 'Enter a valid phone number (digits only, without the country code).';
    }
}

//Send the user back with the errors, keeping what they typed (except the password)
if ($errors) {
    $_SESSION['error'] = implode(' ', $errors);
    $_SESSION['old']   = compact('name', 'email', 'country', 'city', 'contact', 'dial');
    redirect('views/register.php');
}

//Register via the controller 
$ctrl   = new CustomerController();
$result = $ctrl->register([
    'name' => $name, 'email' => $email, 'password' => $pass,
    'country' => $country, 'city' => $city, 'contact' => $fullContact,
]);

if (!$result['success']) {
    $_SESSION['error'] = $result['error'];
    $_SESSION['old']   = compact('name', 'email', 'country', 'city', 'contact', 'dial');
    redirect('views/register.php');
}

//Logs the new customer in 
session_regenerate_id(true);
$_SESSION['customer_id']    = $result['customer_id'];
$_SESSION['customer_name']  = $name;
$_SESSION['customer_email'] = $email;
$_SESSION['user_role']      = 2;   

$_SESSION['success'] = 'Your account has been created. Welcome to shoppn!';
redirect('views/account/my_account.php');