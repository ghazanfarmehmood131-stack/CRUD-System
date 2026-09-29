<?php
/**
 * Core Functions for Products CRUD Application
 * 
 * Contains all database operations (CRUD), search, pagination,
 * import/export, and validation functions.
 */

// Require database configuration
require_once __DIR__ . '/../config/db.php';

// =============================================
// PRODUCT FUNCTIONS
// =============================================

/**
 * Get all products with pagination
 * 
 * Fetches products from the database with optional search,
 * sorted by ID in descending order (newest first).
 * 
 * @param PDO $pdo       Database connection
 * @param int $offset    Starting record for the current page
 * @param int $limit     Number of records per page
 * @param string $search Optional search term for filtering by name
 * @return array          Array of product records
 */
function getProducts($pdo, $offset, $limit, $search = '') {
    try {
        if (!empty($search)) {
            // Search products by name using prepared statement with LIKE
            $sql = "SELECT * FROM products 
                    WHERE name LIKE :search 
                    ORDER BY id DESC 
                    LIMIT :offset, :limit";
            $stmt = $pdo->prepare($sql);
            $searchParam = '%' . $search . '%';
            $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // Get all products without search filter
            $sql = "SELECT * FROM products 
                    ORDER BY id DESC 
                    LIMIT :offset, :limit";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
        }
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching products: " . $e->getMessage());
        return [];
    }
}

/**
 * Count total products matching search criteria
 * 
 * Used for pagination calculation.
 * 
 * @param PDO $pdo    Database connection
 * @param string $search Optional search term
 * @return int         Total number of matching records
 */
function countProducts($pdo, $search = '') {
    try {
        if (!empty($search)) {
            $sql = "SELECT COUNT(*) FROM products WHERE name LIKE :search";
            $stmt = $pdo->prepare($sql);
            $searchParam = '%' . $search . '%';
            $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
            $stmt->execute();
        } else {
            $sql = "SELECT COUNT(*) FROM products";
            $stmt = $pdo->query($sql);
        }
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Error counting products: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get a single product by ID
 * 
 * @param PDO $pdo  Database connection
 * @param int $id   Product ID
 * @return array|null Product data or null if not found
 */
function getProductById($pdo, $id) {
    try {
        $sql = "SELECT * FROM products WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching product: " . $e->getMessage());
        return null;
    }
}

/**
 * Add a new product to the database
 * 
 * @param PDO $pdo       Database connection
 * @param string $name   Product name
 * @param float $price   Product price
 * @param int $quantity  Product quantity
 * @return bool          True on success, false on failure
 */
function addProduct($pdo, $name, $price, $quantity) {
    try {
        $sql = "INSERT INTO products (name, price, quantity) 
                VALUES (:name, :price, :quantity)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error adding product: " . $e->getMessage());
        return false;
    }
}

/**
 * Update an existing product
 * 
 * @param PDO $pdo       Database connection
 * @param int $id        Product ID to update
 * @param string $name   New product name
 * @param float $price   New product price
 * @param int $quantity  New product quantity
 * @return bool          True on success, false on failure
 */
function updateProduct($pdo, $id, $name, $price, $quantity) {
    try {
        $sql = "UPDATE products 
                SET name = :name, price = :price, quantity = :quantity 
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_STR);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error updating product: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a product by ID
 * 
 * @param PDO $pdo  Database connection
 * @param int $id   Product ID to delete
 * @return bool     True on success, false on failure
 */
function deleteProduct($pdo, $id) {
    try {
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Error deleting product: " . $e->getMessage());
        return false;
    }
}

// =============================================
// VALIDATION FUNCTIONS
// =============================================

/**
 * Validate product form data
 * 
 * Checks all required fields and data types.
 * 
 * @param string $name   Product name
 * @param string $price  Product price
 * @param string $quantity Product quantity
 * @return array         Array with 'valid' (bool) and 'errors' (array)
 */
function validateProduct($name, $price, $quantity) {
    $errors = [];
    
    // Validate name is not empty and not just whitespace
    if (empty(trim($name))) {
        $errors[] = "Product name is required.";
    } elseif (strlen(trim($name)) > 255) {
        $errors[] = "Product name must not exceed 255 characters.";
    }
    
    // Validate price is present and numeric
    if (empty(trim($price))) {
        $errors[] = "Price is required.";
    } elseif (!is_numeric($price)) {
        $errors[] = "Price must be a valid number.";
    } elseif (floatval($price) < 0) {
        $errors[] = "Price cannot be negative.";
    }
    
    // Validate quantity is present, numeric, and non-negative
    if (empty(trim($quantity))) {
        $errors[] = "Quantity is required.";
    } elseif (!is_numeric($quantity)) {
        $errors[] = "Quantity must be a valid number.";
    } elseif (intval($quantity) < 0) {
        $errors[] = "Quantity cannot be negative.";
    }
    
    return [
        'valid'  => empty($errors),
        'errors' => $errors
    ];
}

// =============================================
// IMPORT / EXPORT FUNCTIONS
// =============================================

/**
 * Export all products to a CSV file for download
 * 
 * Generates a CSV file with headers and streams it to the browser.
 * 
 * @param PDO $pdo  Database connection
 */
function exportToCSV($pdo) {
    try {
        // Fetch all products from the database
        $sql = "SELECT * FROM products ORDER BY id DESC";
        $stmt = $pdo->query($sql);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="products_export_' . date('Y-m-d_His') . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Open output stream
        $output = fopen('php://output', 'w');
        
        // Add BOM for Excel UTF-8 compatibility
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        
        // Write CSV header row
        fputcsv($output, ['ID', 'Name', 'Price', 'Quantity', 'Created At', 'Updated At']);
        
        // Write each product as a CSV row
        foreach ($products as $product) {
            fputcsv($output, $product);
        }
        
        fclose($output);
        exit; // Stop further output
        
    } catch (PDOException $e) {
        error_log("Error exporting products: " . $e->getMessage());
        return false;
    }
}

/**
 * Import products from a CSV file
 * 
 * Reads an uploaded CSV file and inserts records into the database.
 * 
 * @param PDO $pdo       Database connection
 * @param string $filePath Path to the uploaded file
 * @return array          Array with 'success' count and 'errors' array
 */
function importFromCSV($pdo, $filePath) {
    $imported = 0;
    $errors = [];
    $rowNumber = 0;
    
    try {
        // Open the uploaded file for reading
        $handle = fopen($filePath, 'r');
        
        if ($handle === false) {
            return ['success' => 0, 'errors' => ['Could not open the file.']];
        }
        
        // Read and skip the header row
        $header = fgetcsv($handle);
        
        // Prepare the INSERT statement (reused for each row)
        $sql = "INSERT INTO products (name, price, quantity) 
                VALUES (:name, :price, :quantity)";
        $stmt = $pdo->prepare($sql);
        
        // Process each row
        while (($data = fgetcsv($handle)) !== false) {
            $rowNumber++;
            
            // Ensure row has enough columns
            if (count($data) < 3) {
                $errors[] = "Row {$rowNumber}: Insufficient data columns.";
                continue;
            }
            
            $name = trim($data[0]);
            $price = trim($data[1]);
            $quantity = trim($data[2]);
            
            // Skip empty rows
            if (empty($name) && empty($price) && empty($quantity)) {
                continue;
            }
            
            // Validate the row data
            $validation = validateProduct($name, $price, $quantity);
            
            if (!$validation['valid']) {
                $errors[] = "Row {$rowNumber}: " . implode(', ', $validation['errors']);
                continue;
            }
            
            // Insert the product using prepared statement
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':price', $price, PDO::PARAM_STR);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->execute();
            $imported++;
        }
        
        fclose($handle);
        return ['success' => $imported, 'errors' => $errors];
        
    } catch (PDOException $e) {
        error_log("Error importing products: " . $e->getMessage());
        return ['success' => $imported, 'errors' => ['Database error: ' . $e->getMessage()]];
    }
}

/**
 * Handle Excel file conversion to CSV for import
 * 
 * Uses PhpSpreadsheet if available, otherwise attempts basic CSV reading.
 * 
 * @param PDO $pdo       Database connection
 * @param string $filePath Path to the uploaded file
 * @param string $extension File extension (csv, xls, xlsx)
 * @return array          Import result
 */
function importFromFile($pdo, $filePath, $extension) {
    // If it's already a CSV file, import directly
    if ($extension === 'csv') {
        return importFromCSV($pdo, $filePath);
    }
    
    // For Excel files (xls/xlsx), check if PhpSpreadsheet is available
    if (class_exists('PhpOffice\PhpSpreadsheet\Reader\Xlsx')) {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            
            // Convert spreadsheet data to a temporary CSV file
            $tempFile = tempnam(sys_get_temp_dir(), 'import_') . '.csv';
            $tempHandle = fopen($tempFile, 'w');
            
            foreach ($sheet->getRowIterator() as $row) {
                $rowIndex = $row->getRowIndex();
                if ($rowIndex == 1) continue; // Skip header row
                
                $data = [];
                foreach ($row->getCellIterator() as $cell) {
                    $data[] = $cell->getValue();
                }
                fputcsv($tempHandle, $data);
            }
            
            fclose($tempHandle);
            
            $result = importFromCSV($pdo, $tempFile);
            
            // Clean up temporary file
            unlink($tempFile);
            return $result;
            
        } catch (Exception $e) {
            error_log("Error reading Excel file: " . $e->getMessage());
            return ['success' => 0, 'errors' => ['Error reading Excel file: ' . $e->getMessage()]];
        }
    } else {
        // If PhpSpreadsheet is not available, give instructions
        return ['success' => 0, 'errors' => [
            'Excel file support requires PhpSpreadsheet library. Please install it using: composer require phpoffice/phpspreadsheet',
            'Alternatively, please convert your Excel file to CSV format and try again.'
        ]];
    }
}

/**
 * Generate pagination links
 * 
 * Creates HTML pagination navigation based on total records and current page.
 * 
 * @param int $totalRecords Total number of records
 * @param int $currentPage  Current page number
 * @param int $recordsPerPage Records per page
 * @param string $search   Current search term (preserved in links)
 * @return string          HTML string of pagination links
 */
function generatePagination($totalRecords, $currentPage, $recordsPerPage, $search = '') {
    // Calculate total number of pages
    $totalPages = ceil($totalRecords / $recordsPerPage);
    
    // If only one page, no pagination needed
    if ($totalPages <= 1) {
        return '';
    }
    
    $searchParam = !empty($search) ? '&search=' . urlencode($search) : '';
    $html = '<nav aria-label="Product pagination"><ul class="pagination justify-content-center">';
    
    // Previous page button
    if ($currentPage > 1) {
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="?page=' . ($currentPage - 1) . $searchParam . '">&laquo; Previous</a>';
        $html .= '</li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">&laquo; Previous</span></li>';
    }
    
    // Page number buttons (show max 5 pages around current page)
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    // Show first page and ellipsis if needed
    if ($startPage > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="?page=1' . $searchParam . '">1</a></li>';
        if ($startPage > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    // Numbered page buttons
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $currentPage) {
            $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $html .= '<li class="page-item"><a class="page-link" href="?page=' . $i . $searchParam . '">' . $i . '</a></li>';
        }
    }
    
    // Show last page and ellipsis if needed
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . $searchParam . '">' . $totalPages . '</a></li>';
    }
    
    // Next page button
    if ($currentPage < $totalPages) {
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="?page=' . ($currentPage + 1) . $searchParam . '">Next &raquo;</a>';
        $html .= '</li>';
    } else {
        $html .= '<li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}
?>
