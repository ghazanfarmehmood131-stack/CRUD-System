<?php
/**
 * Edit Product Page
 * 
 * Displays a form pre-filled with the selected product's data.
 * Handles POST requests to update the product in the database.
 * Includes server-side and client-side validation.
 */

// Start session for flash messages
session_start();

// Load application constants and functions
require_once 'config/constants.php';
require_once 'includes/functions.php';

// Get database connection
$pdo = getDBConnection();

// Set page title
$pageTitle = 'Edit Product';

// =============================================
// VALIDATE PRODUCT ID
// =============================================
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = 'Invalid product ID.';
    header('Location: index.php');
    exit;
}

$productId = (int) $_GET['id'];

// =============================================
// HANDLE FORM SUBMISSION (POST request)
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize
    $name     = trim($_POST['name'] ?? '');
    $price    = trim($_POST['price'] ?? '');
    $quantity = trim($_POST['quantity'] ?? '');
    
    // Validate the submitted data
    $validation = validateProduct($name, $price, $quantity);
    
    if ($validation['valid']) {
        // Verify the product still exists before updating
        $existingProduct = getProductById($pdo, $productId);
        
        if ($existingProduct) {
            // Attempt to update the product
            if (updateProduct($pdo, $productId, $name, floatval($price), intval($quantity))) {
                $_SESSION['success'] = "Product '{$name}' has been updated successfully.";
                header('Location: index.php');
                exit;
            } else {
                $_SESSION['error'] = "Failed to update the product. Please try again.";
            }
        } else {
            $_SESSION['error'] = 'Product not found. It may have been deleted.';
            header('Location: index.php');
            exit;
        }
    } else {
        // Store validation errors in session for display
        $_SESSION['error'] = implode('<br>', $validation['errors']);
    }
}

// =============================================
// LOAD EXISTING PRODUCT DATA (for GET request / initial display)
// =============================================
$product = getProductById($pdo, $productId);

if (!$product) {
    $_SESSION['error'] = 'Product not found.';
    header('Location: index.php');
    exit;
}
?>
<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-edit me-2"></i>Edit Product
    </h1>
    <a href="index.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Products
    </a>
</div>

<!-- Edit Product Form Card -->
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-box-open me-2"></i>Update Product Details
                    <span class="float-end small">Product ID: #<?php echo $productId; ?></span>
                </h5>
            </div>
            <div class="card-body">
                <!-- Display validation errors if any -->
                <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); endif; ?>
                
                <!-- Product Form -->
                <form method="POST" action="edit.php?id=<?php echo $productId; ?>" id="editProductForm" novalidate>
                    
                    <!-- Product Name Field -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">
                            Product Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name" 
                               value="<?php echo htmlspecialchars($product['name']); ?>" 
                               placeholder="Enter product name"
                               maxlength="255"
                               required>
                        <div class="invalid-feedback">Product name is required.</div>
                        <div class="form-text">Maximum 255 characters.</div>
                    </div>
                    
                    <!-- Price Field -->
                    <div class="mb-3">
                        <label for="price" class="form-label fw-semibold">
                            Price ($) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" 
                                   class="form-control" 
                                   id="price" 
                                   name="price" 
                                   value="<?php echo htmlspecialchars($product['price']); ?>" 
                                   placeholder="0.00" 
                                   step="0.01" 
                                   min="0"
                                   required>
                            <div class="invalid-feedback">Please enter a valid price.</div>
                        </div>
                        <div class="form-text">Enter the product price (e.g., 29.99).</div>
                    </div>
                    
                    <!-- Quantity Field -->
                    <div class="mb-4">
                        <label for="quantity" class="form-label fw-semibold">
                            Quantity <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               class="form-control" 
                               id="quantity" 
                               name="quantity" 
                               value="<?php echo htmlspecialchars($product['quantity']); ?>" 
                               placeholder="0" 
                               min="0"
                               required>
                        <div class="invalid-feedback">Please enter a valid quantity.</div>
                        <div class="form-text">Enter the number of items in stock.</div>
                    </div>
                    
                    <!-- Form Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Product
                        </button>
                    </div>
                    
                </form>
            </div>
        </div>
        
        <!-- Product Metadata Info Card -->
        <div class="card mt-3 shadow-sm">
            <div class="card-body">
                <h6 class="card-title text-muted">
                    <i class="fas fa-info-circle me-1"></i> Product Information
                </h6>
                <div class="row text-muted small">
                    <div class="col-6">
                        <strong>Created:</strong><br>
                        <?php echo date('M j, Y g:i A', strtotime($product['created_at'])); ?>
                    </div>
                    <div class="col-6">
                        <strong>Last Updated:</strong><br>
                        <?php echo date('M j, Y g:i A', strtotime($product['updated_at'])); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
