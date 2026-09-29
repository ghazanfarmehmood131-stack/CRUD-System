<?php

// Start session for flash messages
session_start();

// Load application constants and functions
require_once 'config/constants.php';
require_once 'includes/functions.php';

// Get database connection
$pdo = getDBConnection();

// =============================================
// HANDLE DELETE REQUEST (if submitted)
// =============================================
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    
    // Verify the product exists before deleting
    $product = getProductById($pdo, $deleteId);
    
    if ($product) {
        if (deleteProduct($pdo, $deleteId)) {
            $_SESSION['success'] = "Product '{$product['name']}' has been deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete the product. Please try again.";
        }
    } else {
        $_SESSION['error'] = "Product not found.";
    }
    
    // Redirect to prevent re-delete on page refresh
    header('Location: index.php');
    exit;
}

// =============================================
// HANDLE IMPORT REQUEST (if file uploaded)
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'import') {
    
    // Check if a file was uploaded
    if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['import_file'];
        
        // Validate file size
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            $_SESSION['error'] = 'File size exceeds the maximum allowed size of ' . (MAX_UPLOAD_SIZE / 1024 / 1024) . 'MB.';
        } else {
            // Get file extension
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // Validate file extension
            if (in_array($extension, ALLOWED_EXTENSIONS)) {
                // Move uploaded file to uploads directory
                $uploadPath = UPLOAD_DIR . time() . '_' . basename($file['name']);
                
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    // Process the import
                    $result = importFromFile($pdo, $uploadPath, $extension);
                    
                    if ($result['success'] > 0) {
                        $_SESSION['success'] = "Successfully imported {$result['success']} product(s).";
                    }
                    
                    if (!empty($result['errors'])) {
                        $_SESSION['error'] = "Import completed with errors: " . implode('; ', $result['errors']);
                    }
                    
                    // Clean up uploaded file
                    unlink($uploadPath);
                } else {
                    $_SESSION['error'] = 'Failed to upload the file. Please check directory permissions.';
                }
            } else {
                $_SESSION['error'] = 'Invalid file type. Only CSV, XLS, and XLSX files are allowed.';
            }
        }
    } else {
        $_SESSION['error'] = 'Please select a file to import.';
    }
    
    header('Location: index.php');
    exit;
}

// =============================================
// HANDLE EXPORT REQUEST (if requested)
// =============================================
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    exportToCSV($pdo);
}

// =============================================
// PAGINATION & SEARCH SETUP
// =============================================

// Get the current page number from URL (default to 1)
$currentPage = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;

// Get the search term from URL (if any)
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Calculate the offset for the database query
$offset = ($currentPage - 1) * RECORDS_PER_PAGE;

// =============================================
// FETCH PRODUCTS
// =============================================

// Get total count for pagination calculation
$totalRecords = countProducts($pdo, $search);

// Fetch products for the current page
$products = getProducts($pdo, $offset, RECORDS_PER_PAGE, $search);

// Generate pagination HTML
$pagination = generatePagination($totalRecords, $currentPage, RECORDS_PER_PAGE, $search);

// Set page title
$pageTitle = 'All Products';
?>
<?php include 'includes/header.php'; ?>

<!-- Page Header with Action Buttons -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h1 class="h3 mb-0">
        <i class="fas fa-boxes-stacked me-2"></i>All Products
        <span class="badge bg-secondary ms-2"><?php echo $totalRecords; ?></span>
    </h1>
    
    <div class="d-flex flex-wrap gap-2">
        <!-- Add Product Button -->
        <a href="add.php" class="btn btn-success">
            <i class="fas fa-plus me-1"></i> Add Product
        </a>
        
        <!-- Export to CSV Button -->
        <a href="?export=csv" class="btn btn-outline-primary">
            <i class="fas fa-file-export me-1"></i> Export CSV
        </a>
        
        <!-- Import from CSV Button (opens modal) -->
        <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-import me-1"></i> Import CSV
        </button>
    </div>
</div>

<!-- Search Form -->
<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3 align-items-end">
            <div class="col-md-8">
                <label for="search" class="form-label fw-semibold">
                    <i class="fas fa-search me-1"></i> Search Products
                </label>
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Enter product name to search...">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i> Search
                </button>
                <?php if (!empty($search)): ?>
                <a href="index.php" class="btn btn-outline-secondary mt-2 mt-md-0 w-100">
                    <i class="fas fa-times me-1"></i> Clear Search
                </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<?php if (!empty($products)): ?>
<div class="table-responsive card shadow-sm">
    <table class="table table-hover table-striped mb-0">
        <thead class="table-dark">
            <tr>
                <th width="5%">ID</th>
                <th width="35%">Name</th>
                <th width="20%">Price</th>
                <th width="15%">Quantity</th>
                <th width="15%">Updated</th>
                <th width="10%" class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
            <tr>
                <td><span class="fw-bold text-muted">#<?php echo (int)$product['id']; ?></span></td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td>
                    <span class="badge bg-info text-dark">
                        $<?php echo number_format((float)$product['price'], 2); ?>
                    </span>
                </td>
                <td>
                    <?php 
                    $qty = (int)$product['quantity'];
                    $badgeClass = $qty <= 0 ? 'bg-danger' : ($qty < 10 ? 'bg-warning text-dark' : 'bg-success');
                    ?>
                    <span class="badge <?php echo $badgeClass; ?>"><?php echo $qty; ?> in stock</span>
                </td>
                <td class="text-muted small">
                    <?php 
                    echo date('M j, Y g:i A', strtotime($product['updated_at']));
                    ?>
                </td>
                <td class="text-center">
                    <!-- Edit Button -->
                    <a href="edit.php?id=<?php echo (int)$product['id']; ?>" 
                       class="btn btn-sm btn-primary" 
                       title="Edit Product"
                       data-bs-toggle="tooltip">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    <!-- Delete Button (with confirmation) -->
                    <a href="?delete=<?php echo (int)$product['id']; ?>" 
                       class="btn btn-sm btn-danger delete-btn" 
                       title="Delete Product"
                       data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                       data-bs-toggle="tooltip">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Pagination Links -->
<?php if (!empty($pagination)): ?>
<div class="mt-4">
    <?php echo $pagination; ?>
</div>
<?php endif; ?>

<?php else: ?>
<!-- No Products Found Message -->
<div class="text-center py-5">
    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
    <h4 class="text-muted">
        <?php echo !empty($search) ? 'No products found matching your search.' : 'No products found.'; ?>
    </h4>
    <p class="text-muted">
        <?php echo !empty($search) ? 'Try a different search term or add a new product.' : 'Get started by adding your first product.'; ?>
    </p>
    <a href="add.php" class="btn btn-success mt-2">
        <i class="fas fa-plus me-1"></i> Add Product
    </a>
</div>
<?php endif; ?>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="fas fa-file-import me-2"></i>Import Products from CSV
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="index.php" enctype="multipart/form-data">
                    <!-- Hidden field to identify import action -->
                    <input type="hidden" name="action" value="import">
                    
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Select CSV File</label>
                        <input type="file" 
                               class="form-control" 
                               id="import_file" 
                               name="import_file" 
                               accept=".csv,.xls,.xlsx" 
                               required>
                        <div class="form-text">
                            Supported formats: CSV, XLS, XLSX (max <?php echo MAX_UPLOAD_SIZE / 1024 / 1024; ?>MB).
                            The first row should contain headers: Name, Price, Quantity.
                        </div>
                    </div>
                    
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>CSV Format:</strong> Each row should have 3 columns: 
                        <code>Name, Price, Quantity</code>. The first row (header) will be skipped.
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-upload me-1"></i> Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
