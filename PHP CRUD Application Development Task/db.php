<?php
/**
 * Database Configuration File
 * 
 * This file contains the database connection settings.
 * Update these values to match your MySQL server credentials.
 */

// Database host - usually 'localhost' for local development
define('DB_HOST', 'localhost');

// Database name - must match the name used in products.sql
define('DB_NAME', 'crud_products_db');

// Database username - change to your MySQL username
define('DB_USER', 'root');

// Database password - change to your MySQL password (empty for XAMPP default)
define('DB_PASS', '');

// Database character set
define('DB_CHARSET', 'utf8mb4');

/**
 * Create PDO database connection
 * 
 * This function returns a PDO instance connected to the MySQL database.
 * Uses PDO with prepared statements for security against SQL injection.
 * 
 * @return PDO
 * @throws PDOException if connection fails
 */
function getDBConnection() {
    try {
        // DSN (Data Source Name) - specifies the database driver and connection details
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        // PDO connection options for security and error handling
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,    // Throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,          // Return associative arrays
            PDO::ATTR_EMULATE_PREPARES   => false,                     // Use real prepared statements
        ];
        
        // Create and return the PDO connection
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
        
    } catch (PDOException $e) {
        // Log the error (in production, log to file instead of displaying)
        error_log("Database connection error: " . $e->getMessage());
        die("Database connection failed. Please check your configuration.");
    }
}
?>
