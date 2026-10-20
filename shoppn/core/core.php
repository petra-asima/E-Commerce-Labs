<?php
// Display errors during development and always log them
error_reporting(E_ALL);
ini_set('display_errors', '1');   
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../error/error.log');

// Session
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

date_default_timezone_set('Africa/Accra');

// Web path to the project root.
if (strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') === 0) {
    define('BASE_URL', '/shoppn/');
} else {
    define('BASE_URL', '/~petra.asima/E-Commerce/shoppn/');
}

// Countries offered in the registration dropdown (each max 30 chars, per the DB)
const COUNTRIES = ['Ghana', 'Nigeria', 'Togo', 'Burkina Faso', "Côte d'Ivoire",
                   'Kenya', 'South Africa', 'United Kingdom', 'United States', 'Other'];
                   
// Phone country codes for the registration form: ISO code => [dial code, country]
const DIAL_CODES = [
    'GH' => ['+233', 'Ghana'],
    'NG' => ['+234', 'Nigeria'],
    'TG' => ['+228', 'Togo'],
    'BF' => ['+226', 'Burkina Faso'],
    'CI' => ['+225', "Côte d'Ivoire"],
    'KE' => ['+254', 'Kenya'],
    'ZA' => ['+27',  'South Africa'],
    'GB' => ['+44',  'United Kingdom'],
    'US' => ['+1',   'United States'],
];

require_once __DIR__ . '/db_class.php';

// Helpers

// Full link from the project root: url('views/login.php') → /shoppn/views/login.php
function url(string $path = ''): string
{
    return BASE_URL . ltrim($path, '/');
}

// Send the browser to another page, then stop this script
function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

// Make text safe to print inside HTML
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Write a timestamped entry to error/error.log
function log_error(string $message): void
{
    error_log('[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, 3, __DIR__ . '/../error/error.log');
}

// Visitor's IP address — used to identify guest carts
function get_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// Is someone logged in?
function is_logged_in(): bool
{
    return isset($_SESSION['customer_id']);
}

// Is the logged-in user an admin? (user_role 1 = admin, 2 = customer)
function is_admin(): bool
{
    return is_logged_in() && (int) ($_SESSION['user_role'] ?? 0) === 1;
}


// Send visitors who aren't logged in to the login page
function require_login(): void
{
    if (!is_logged_in()) {
        $_SESSION['error'] = 'Please log in to continue.';
        redirect('views/login.php');
    }
}

// Only admins may continue; everyone else goes home
function require_admin(): void
{
    if (!is_admin()) {
        $_SESSION['error'] = 'You do not have permission to view that page.';
        redirect('index.php');
    }
}