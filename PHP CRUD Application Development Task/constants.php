<?php
/**
 * Application Constants and Global Settings
 * 
 * Defines site-wide constants used throughout the application.
 */

// Site title displayed in the browser tab and header
define('SITE_TITLE', 'Products CRUD Manager');

// Base URL of the application (change if deployed in a subdirectory)
define('BASE_URL', '/');

// Records displayed per page in the products listing
define('RECORDS_PER_PAGE', 10);

// Maximum file upload size (10MB) for import functionality
define('MAX_UPLOAD_SIZE', 10 * 1024 * 1024);

// Allowed file extensions for import
define('ALLOWED_EXTENSIONS', ['csv', 'xls', 'xlsx']);

// Upload directory path (relative to application root)
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// Session timeout in seconds (30 minutes)
define('SESSION_TIMEOUT', 1800);
?>
