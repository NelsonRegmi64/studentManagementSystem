<?php
// Database configuration
// Change these values according to your local setup (XAMPP / WAMP / MAMP)

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // Default XAMPP password is empty
define('DB_NAME', 'sms_db');

function getDB() {
    static $conn = null;
    if ($conn === null) {
        try {
            $conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
            require_once __DIR__ . '/../includes/schema_upgrade.php';
            upgradeSchema($conn);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage() . 
                "<br><br>Please make sure:<br>
                1. MySQL is running<br>
                2. You created the database using sql/schema.sql<br>
                3. Username/password in config/database.php are correct");
        }
    }
    return $conn;
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
