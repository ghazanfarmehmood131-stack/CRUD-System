<?php
session_start();

require_once 'config/constants.php';
require_once 'includes/functions.php';

$pdo = getDBConnection();


$pageTitle = 'Add Product';

// =============================================
// HANDLE FORM SUBMISSION (POST request)
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name'] ?? '');
    $price    = trim($_POST['price'] ?? '');
    $quantity = trim($_POST['quantity'] ?? '');
    

    $validation = validateProduct($name, $price, $quantity);
    
    if ($validation['valid']) {

        if (addProduct($pdo, $name, floatval($price), intval($quantity))) {
            $_SESSION['success'] = "Product '{$name}' has been added successfully.";
            header('Location: index.php');
            exit;
        } else {
            $_SESSION['error'] = "Failed to add the product. Please try again.";
            header('Location: add.php');
            exit;
        }
    } else {

        $_SESSION['error'] = implode('<br>', $validation['errors']);
    }
}
?>
<?php include 'includes/header.php'; ?>


<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
        <i class="fas fa-plus-circle me-2"></i>Add New Product
    </h1>
    <a href="index.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Products
    </a>
</div>


<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-box me-2"></i>Product Details
                </h5>
            </div>
            <div class="card-body">

                <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $_SESSION['error']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); endif; ?>
                
                <form method="POST" action="add.php" id="addProductForm" novalidate>

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">
                            Product Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control <?php echo isset($_SESSION['error']) && isset($name) && empty($name) ? 'is-invalid' : ''; ?>" 
                               id="name" 
                               name="name" 
                               value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" 
                               placeholder="Enter product name"
                               maxlength="255"
                               required>
                        <div class="invalid-feedback">Product name is required.</div>
                        <div class="form-text">Maximum 255 characters.</div>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label fw-semibold">
                            Price ($) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" 
                                   class="form-control <?php echo isset($_SESSION['error']) && isset($price) && empty($price) ? 'is-invalid' : ''; ?>" 
                                   id="price" 
                                   name="price" 
                                   value="<?php echo isset($price) ? htmlspecialchars($price) : ''; ?>" 
                                   placeholder="0.00" 
                                   step="0.01" 
                                   min="0"
                                   required>
                            <div class="invalid-feedback">Please enter a valid price.</div>
                        </div>
                        <div class="form-text">Enter the product price (e.g., 29.99).</div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="quantity" class="form-label fw-semibold">
                            Quantity <span class="text-danger">*</span>
                        </label>
                        <input type="number" 
                               class="form-control <?php echo isset($_SESSION['error']) && isset($quantity) && empty($quantity) ? 'is-invalid' : ''; ?>" 
                               id="quantity" 
                               name="quantity" 
                               value="<?php echo isset($quantity) ? htmlspecialchars($quantity) : ''; ?>" 
                               placeholder="0" 
                               min="0"
                               required>
                        <div class="invalid-feedback">Please enter a valid quantity.</div>
                        <div class="form-text">Enter the number of items in stock.</div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Save Product
                        </button>
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
