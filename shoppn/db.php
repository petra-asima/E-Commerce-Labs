<?php
$host = "localhost";
$db_user = "petra.asima";
$db_pass = "petLuv@22";
$db_name = "ecommerce_2026A_petra_asima";
$conn = new mysqli($host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
die("Connection failed: " . $conn->connect_error);
}
