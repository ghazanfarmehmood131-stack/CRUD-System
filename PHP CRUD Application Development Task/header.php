<?php
/**
 * Header Include File
 * 
 * Contains the HTML head section, navigation bar, and CSS links.
 * Included at the top of every page for consistent layout.
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_TITLE; ?> - <?php echo isset($pageTitle) ? $pageTitle : 'Home'; ?></title>
    
    <!-- Bootstrap 5 CSS (loaded from CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- Custom application styles -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <!-- Brand / Logo -->
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-boxes-stacked me-2"></i><?php echo SITE_TITLE; ?>
            </a>
            
            <!-- Mobile menu toggle button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-list me-1"></i> All Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'add.php' ? 'active' : ''; ?>" href="add.php">
                            <i class="fas fa-plus me-1"></i> Add Product
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Flash Messages (success/error alerts) -->
    <div class="container mt-3">
        <?php include __DIR__ . '/messages.php'; ?>
    </div>
    
    <!-- Main Content Area -->
    <div class="container mt-4">
