<?php
// Database configuration variables <!--Version Control (Database connection for user data)-->
$host     = 'localhost';       // ✅ Host where MySQL server is running (usually localhost)
$db       = 'banana_quiz';     // ✅ Database name where quiz data is stored
$user     = 'root';            // ✅ MySQL username (default is root in local setups)
$pass     = '';                // ✅ MySQL password (empty if none set for root)

// ✅ Enable error reporting for debugging (optional during development)
// These lines make PHP show all errors and warnings directly in the browser.
// Helpful while testing, but should be disabled in production for security.
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create a new MySQLi connection object
$conn = new mysqli($host, $user, $pass, $db);

/* Error Handling */
if ($conn->connect_error) {
    // If connection fails, stop execution and show error
    die("❌ Connection failed: " . $conn->connect_error);
} else {
    // Optional: Uncomment this line for debugging successful connections
    // echo "✅ Connected successfully to database: $db";
}

// Set character encoding to UTF-8
// ✅ Ensures proper handling of special characters (like emojis or accented letters)
$conn->set_charset("utf8");
?>
