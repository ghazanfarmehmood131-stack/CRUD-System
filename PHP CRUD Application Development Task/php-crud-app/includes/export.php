<?php
/**
 * Export Handler
 * 
 * Provides multiple export format options (CSV).
 * Can be called directly or via AJAX.
 */

// Start session
session_start();

// Load dependencies
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/functions.php';

// Get database connection
$pdo = getDBConnection();

// Determine export format from GET parameter
$format = isset($_GET['format']) ? strtolower($_GET['format']) : 'csv';

switch ($format) {
    case 'csv':
    default:
        // Use the CSV export function from functions.php
        exportToCSV($pdo);
        break;
}
?>
