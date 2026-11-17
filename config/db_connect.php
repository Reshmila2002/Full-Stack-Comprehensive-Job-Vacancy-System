<?php
// config/db_connect.php
$host = 'localhost';
$dbname = 'career_link_db'; // Ensure you create this database in phpMyAdmin
$username = 'root'; // Default XAMPP/WAMP username
$password = ''; // Default XAMPP/WAMP password

// connect between php web and mysql database 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set PDO error mode to exception for easier debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>