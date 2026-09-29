# PHP Products CRUD Application

A complete, responsive CRUD (Create, Read, Update, Delete) application built with **Core PHP**, **PDO**, **MySQL**, **Bootstrap 5**, and **jQuery**. It includes advanced features like search, pagination, and import/export functionality.

## Features

*   **Add Product**: Create new products with server-side and client-side validation.
*   **View Products**: List all products in a clean, responsive table format.
*   **Edit Product**: Update existing product details.
*   **Delete Product**: Remove products with confirmation.
*   **Search**: Search products by name with dynamic filtering.
*   **Pagination**: Navigate through records (10 records per page).
*   **Import/Export**: 
    *   Export all products to a CSV file.
    *   Import products from a CSV, XLS, or XLSX file.
*   **Security**: Uses PDO prepared statements to prevent SQL injection.
*   **Responsive Design**: Fully responsive UI built with Bootstrap 5.

## Requirements

*   PHP 7.4 or higher
*   MySQL 5.7 or higher / MariaDB
*   Apache or Nginx web server (e.g., XAMPP, WAMP, MAMP)
*   Modern web browser

## Project Structure

```text
php-crud-app/
├── config/
│   ├── db.php          # Database connection settings
│   └── constants.php   # Application constants
├── css/
│   └── style.css       # Custom CSS styles
├── includes/
│   ├── functions.php   # Core PHP functions (CRUD, search, import/export)
│   ├── header.php      # HTML header and navigation
│   ├── footer.php      # HTML footer and scripts
│   ├── messages.php    # Flash message display
│   ├── handle_import.php # AJAX import handler
│   ├── export.php      # Export handler
│   └── delete_handler.php # AJAX delete handler
├── js/
│   └── app.js          # jQuery interactions and client-side validation
├── uploads/            # Directory for temporary uploaded files
├── index.php           # Main products listing page
├── add.php             # Add new product page
├── edit.php            # Edit product page
├── products.sql        # Database creation script
└── README.md           # Installation instructions
```

## Installation Instructions

Follow these steps to set up and run the application on your local machine:

### 1. Start Your Web Server and Database
Ensure your local server environment (like XAMPP or WAMP) is running, and both Apache and MySQL services are active.

### 2. Copy the Project Files
Copy the entire `php-crud-app` folder into your web server's root directory.
*   For XAMPP on Windows: `C:\xampp\htdocs\php-crud-app`
*   For MAMP on Mac: `/Applications/MAMP/htdocs/php-crud-app`
*   For Ubuntu/Linux: `/var/www/html/php-crud-app`

### 3. Create the Database
Open your database management tool (like phpMyAdmin or MySQL Workbench):
1.  Create a new database named `crud_products_db`.
2.  Import or run the `products.sql` file located in the project root to create the `products` table.

### 4. Configure Database Credentials
Open the `config/db.php` file in a text editor and update the database credentials to match your local environment:

```php
// Update these values based on your MySQL setup
define('DB_HOST', 'localhost');
define('DB_NAME', 'crud_products_db');
define('DB_USER', 'root');       // Change if your username is different
define('DB_PASS', '');           // Change if you have a database password
```

### 5. Set Directory Permissions (Linux/Mac)
If you are using Linux or macOS, ensure the web server has write permissions for the `uploads` directory:

```bash
chmod 755 /var/www/html/php-crud-app/uploads
```

### 6. Access the Application
Open your web browser and navigate to:

`http://localhost/php-crud-app/`

## Usage Guide

1.  **Add Product**: Click the "Add Product" button on the navigation bar. Fill in the Name, Price, and Quantity fields. Click "Save Product".
2.  **View Products**: The homepage displays a paginated list of all products.
3.  **Search**: Use the search bar at the top of the products list to filter products by name.
4.  **Edit Product**: Click the blue edit icon next to a product to update its details.
5.  **Delete Product**: Click the red trash icon next to a product and confirm the deletion.
6.  **Export CSV**: Click the "Export CSV" button to download all products in CSV format.
7.  **Import CSV**: Click the "Import CSV" button, select a CSV file, and click "Import". Ensure the CSV file has columns for Name, Price, and Quantity.
