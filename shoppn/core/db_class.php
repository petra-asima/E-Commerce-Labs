<?php
class Database
{
    protected $conn;
    private static $sharedConn = null;

    public function __construct()
    {
        if (self::$sharedConn === null) {
            require_once __DIR__ . '/db_cred.php';

            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            try {
                self::$sharedConn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                self::$sharedConn->set_charset('utf8mb4');
            } catch (mysqli_sql_exception $ex) {
                error_log('DB connection failed: ' . $ex->getMessage());
                http_response_code(500);
                die('Connection failed.');
            }
        }
        $this->conn = self::$sharedConn;
    }
}