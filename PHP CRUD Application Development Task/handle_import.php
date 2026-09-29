<?php
/**
 * Import/Export AJAX Handler
 * 
 * Handles import requests via AJAX calls from the front-end.
 * Returns JSON responses for client-side processing.
 * 
 * This file can also be used as a standalone import handler.
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

// =============================================
// HANDLE IMPORT VIA AJAX
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajax_import') {
    
    $response = ['success' => false, 'message' => '', 'imported' => 0];
    
    // Check if file was uploaded
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $response['message'] = 'No file was uploaded or an upload error occurred.';
        echo json_encode($response);
        exit;
    }
    
    $file = $_FILES['file'];
    
    // Validate file size
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        $response['message'] = 'File size exceeds the maximum allowed limit.';
        echo json_encode($response);
        exit;
    }
    
    // Validate file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        $response['message'] = 'Invalid file type. Only CSV, XLS, and XLSX files are allowed.';
        echo json_encode($response);
        exit;
    }
    
    // Save file to uploads directory
    $uploadPath = UPLOAD_DIR . time() . '_' . basename($file['name']);
    
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $response['message'] = 'Failed to save the uploaded file.';
        echo json_encode($response);
        exit;
    }
    
    // Process the import
    $result = importFromFile($pdo, $uploadPath, $extension);
    
    // Clean up the uploaded file
    if (file_exists($uploadPath)) {
        unlink($uploadPath);
    }
    
    // Build response
    $response['imported'] = $result['success'];
    
    if ($result['success'] > 0 && empty($result['errors'])) {
        $response['success'] = true;
        $response['message'] = "Successfully imported {$result['success']} product(s).";
    } elseif ($result['success'] > 0 && !empty($result['errors'])) {
        $response['success'] = true; // Partial success
        $response['message'] = "Imported {$result['success']} product(s) with some errors.";
        $response['errors'] = $result['errors'];
    } else {
        $response['message'] = !empty($result['errors']) ? implode('<br>', $result['errors']) : 'No products were imported.';
    }
    
    echo json_encode($response);
    exit;
}

// If reached here without valid action
echo json_encode(['success' => false, 'message' => 'Invalid request.']);
?>
