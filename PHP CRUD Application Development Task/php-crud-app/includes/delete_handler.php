<?php
/**
 * Delete Product Handler (AJAX)
 * 
 * Handles AJAX delete requests with JSON responses.
 * Provides an alternative to the inline delete in index.php.
 */

// Start session
session_start();

// Load dependencies
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/functions.php';

// Set JSON response header
header('Content-Type: application/json; charset=utf-8');

// Get database connection
$pdo = getDBConnection();

// Validate the request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get the product ID from POST data
$productId = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
    exit;
}

// Fetch the product to get its name for the response
$product = getProductById($pdo, $productId);

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit;
}

// Delete the product
if (deleteProduct($pdo, $productId)) {
    echo json_encode([
        'success' => true,
        'message' => "Product '{$product['name']}' has been deleted successfully."
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete the product.']);
}
?>
