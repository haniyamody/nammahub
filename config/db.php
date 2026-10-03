<?php
$host     = "localhost";
$user     = "root";
$password = "";           // XAMPP default — leave empty unless you set one
$database = "namma_blr";   // ← change this to your actual DB name in phpMyAdmin

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "DB Connection failed: " . mysqli_connect_error()]);
    exit();
}
