# Admin Panel (PHP + MySQL)

A simple product dashboard with add-product form, product listing, and summary cards.

## Files
- `admin.php` — dashboard UI
- `products.php` — product listing UI
- `add_product.php` — add product form UI
- `product_api.php` — REST endpoint for adding, listing, and deleting products
- `style.css` — dashboard styling
- `script.js` — client-side logic for loading and adding products
- `db_config.php` — database connection settings

## Setup
1. Copy all files into your PHP server webroot, for example `htdocs/trinket-dashboard`.
2. Edit DB credentials in `db_config.php` if needed.
   ```php
   $DB_HOST = "localhost";
   $DB_NAME = "shop";
   $DB_USER = "root";
   $DB_PASS = "";
   ```
3. The script will create the `shop` database and the `products` table automatically if they do not exist.
4. Open `http://localhost/trinket-dashboard/admin.php` in your browser.

## API
-- `POST product_api.php` — JSON body: `{ "name": "...", "price": 9.99, "image_url": "https://...", "description": "..." }`
-- `GET  product_api.php` — returns `{ success, products: [...] }`
-- `DELETE product_api.php?id={id}` — deletes a product by ID

Requirements:
- PHP 7.4+ with PDO MySQL extension
- MySQL server running and accessible with the configured credentials
# trinket-dashboard
