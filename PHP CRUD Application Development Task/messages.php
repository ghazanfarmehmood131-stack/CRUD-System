<?php
/**
 * Flash Messages Display
 * 
 * Displays success and error messages stored in the session.
 * Messages are set in individual pages and displayed here.
 * After display, session messages are cleared.
 */

// Display success message if set in session
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    echo '<i class="fas fa-check-circle me-2"></i>';
    echo htmlspecialchars($_SESSION['success']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['success']); // Clear the message after display
}

// Display error message if set in session
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
    echo '<i class="fas fa-exclamation-circle me-2"></i>';
    echo htmlspecialchars($_SESSION['error']);
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    echo '</div>';
    unset($_SESSION['error']); // Clear the message after display
}
?>
