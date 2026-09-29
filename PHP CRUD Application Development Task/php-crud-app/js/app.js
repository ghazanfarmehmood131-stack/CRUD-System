
$(document).ready(function() {

    // =============================================
    // INITIALIZE BOOTSTRAP TOOLTIPS
    // =============================================
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // =============================================
    // DELETE CONFIRMATION DIALOG
    // =============================================
    // Intercepts delete link clicks and shows a confirmation dialog
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        
        var deleteUrl = $(this).attr('href');
        var productName = $(this).data('product-name') || 'this product';
        
        // Show a custom Bootstrap modal-like confirmation
        var confirmMessage = 'Are you sure you want to delete "' + productName + '"?\n\nThis action cannot be undone.';
        
        if (confirm(confirmMessage)) {
            // User confirmed - proceed with deletion
            window.location.href = deleteUrl;
        }
        // User cancelled - do nothing
    });

    // =============================================
    // CLIENT-SIDE FORM VALIDATION
    // =============================================
    

    @param {jQuery} $input 
    @param {string} fieldName 
    @returns {boolean} 
     
    function validateNumericField($input, fieldName) {
        var value = $input.val().trim();
        
        if (value === '') {
            $input.addClass('is-invalid').removeClass('is-valid');
            $input.next('.invalid-feedback, .form-text').first().css('display', '');
            return false;
        }
        
        if (isNaN(parseFloat(value))) {
            $input.addClass('is-invalid').removeClass('is-valid');
            return false;
        }
        
        if (parseFloat(value) < 0) {
            $input.addClass('is-invalid').removeClass('is-valid');
            return false;
        }
        
        $input.removeClass('is-invalid').addClass('is-valid');
        return true;
    }

    
     Validates a text field (non-empty)
     @param {jQuery} $input 
     @returns {boolean} 
    
    function validateTextField($input) {
        var value = $input.val().trim();
        
        if (value === '') {
            $input.addClass('is-invalid').removeClass('is-valid');
            return false;
        }
        
        $input.removeClass('is-invalid').addClass('is-valid');
        return true;
    }

    // =============================================
    // ADD PRODUCT FORM VALIDATION
    // =============================================
    $('#addProductForm').on('submit', function(e) {
        var isValid = true;
        
        var $name = $('#name');
        if (!validateTextField($name)) {
            isValid = false;
        }
        
        var $price = $('#price');
        if (!validateNumericField($price, 'Price')) {
            isValid = false;
        }
        
        var $quantity = $('#quantity');
        if (!validateNumericField($quantity, 'Quantity')) {
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            

            var $alert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                '<i class="fas fa-exclamation-triangle me-2"></i>' +
                'Please fix the errors below before submitting.' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                '</div>');
            
            $(this).before($alert);
            
            $('html, body').animate({
                scrollTop: $alert.offset().top - 20
            }, 300);
        }
    });

    // =============================================
    // EDIT PRODUCT FORM VALIDATION
    // =============================================
    $('#editProductForm').on('submit', function(e) {
        var isValid = true;
        
        var $name = $('#name');
        if (!validateTextField($name)) {
            isValid = false;
        }
        
        var $price = $('#price');
        if (!validateNumericField($price, 'Price')) {
            isValid = false;
        }
        
        var $quantity = $('#quantity');
        if (!validateNumericField($quantity, 'Quantity')) {
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            
            var $alert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                '<i class="fas fa-exclamation-triangle me-2"></i>' +
                'Please fix the errors below before submitting.' +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                '</div>');
            
            $(this).before($alert);
            
            $('html, body').animate({
                scrollTop: $alert.offset().top - 20
            }, 300);
        }
    });

    $('.form-control').on('input', function() {
        $(this).removeClass('is-invalid');
    });

    // =============================================
    // IMPORT FILE VALIDATION (Client-side)
    // =============================================
    $('#import_file').on('change', function() {
        var fileName = $(this).val();
        if (fileName) {

            var extension = fileName.split('.').pop().toLowerCase();
            var allowedExtensions = ['csv', 'xls', 'xlsx'];
            
            if (!allowedExtensions.includes(extension)) {
                alert('Invalid file type. Please select a CSV, XLS, or XLSX file.');
                $(this).val(''); 
                return;
            }
        }
    });

    // =============================================
    // AJAX IMPORT HANDLER (Optional enhancement)
    // =============================================
    
    var $importModal = $('#importModal');
    
    $importModal.on('show.bs.modal', function() {
        var $form = $(this).find('form');
        $form[0].reset();
        $form.find('.form-control').removeClass('is-invalid is-valid');
        $form.find('.alert').remove();
    });

    // =============================================
    // SEARCH FORM ENHANCEMENTS
    // =============================================
    
    $('#search').on('keydown', function(e) {
        if (e.key === 'Escape' && $(this).val().length > 0) {
            $(this).val('');
            window.location.href = 'index.php';
        }
    });

    if ($('#search').length > 0 && window.location.pathname.includes('index.php')) {

        if ($('#search').val() === '') {

            setTimeout(function() {
                $('#search').focus();
            }, 300);
        }
    }

    // =============================================
    // TABLE ROW ANIMATIONS
    // =============================================
    
    $('.table tbody tr').each(function(index) {
        $(this).css({
            'opacity': 0,
            'transform': 'translateY(10px)'
        });
        
        setTimeout(function() {
            $(this).animate({
                'opacity': 1
            }, {
                duration: 300,
                step: function(now) {
                    $(this).css('transform', 'translateY(' + (10 - (10 * now)) + 'px)');
                }
            });
        }.bind(this), index * 50);
    });

    // =============================================
    // QUANTITY COLOR INDICATOR (Visual enhancement)
    // =============================================

    function updateQuantityColors() {
        $('.table tbody tr').each(function() {
            var $qtyBadge = $(this).find('.badge');
            var qtyText = $qtyBadge.text().trim();
            var qty = parseInt(qtyText);
            
            if (!isNaN(qty)) {
                $qtyBadge.removeClass('bg-success bg-warning bg-danger text-dark');
                
                if (qty <= 0) {
                    $qtyBadge.addClass('bg-danger');
                } else if (qty < 10) {
                    $qtyBadge.addClass('bg-warning text-dark');
                } else {
                    $qtyBadge.addClass('bg-success');
                }
            }
        });
    }
    
    updateQuantityColors();

    // =============================================
    // NAVBAR SCROLL EFFECT
    // =============================================
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 10) {
            $('.navbar').addClass('shadow');
        } else {
            $('.navbar').removeClass('shadow');
        }
    });

});
