<?php
// TEMPORARY diagnostic — delete after use
ini_set('display_errors', '1');
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_OFF);

require __DIR__ . '/core/db_cred.php';

echo 'Branch used: ' . (strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') === 0 ? 'LOCAL' : 'LIVE') . '<br>';
echo 'Host: ' . DB_HOST . '<br>User: ' . DB_USER . '<br>Database: ' . DB_NAME . '<br>';
echo 'Password filled in: ' . (DB_PASS !== '' && DB_PASS !== 'PUT_SERVER_DB_PASSWORD_HERE' ? 'yes' : 'NO') . '<br><br>';

$start = microtime(true);
$conn = mysqli_init();
$conn->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5);   // give up after 5 seconds instead of hanging
$ok = @$conn->real_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
printf('Connection attempt took %.2f seconds<br>', microtime(true) - $start);

echo $ok
    ? 'CONNECTED. Tables found: ' . $conn->query('SHOW TABLES')->num_rows
    : 'FAILED: ' . mysqli_connect_error();