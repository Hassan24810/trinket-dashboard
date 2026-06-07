# Admin Panel (PHP + MySQL)

A simple product dashboard with add-product form, product listing, and summary cards.

## Files
- `admin.html` — dashboard UI
- `style.css` — dashboard styling
- `script.js` — client-side logic for loading and adding products
- `add_product.php` — REST endpoint for adding and listing products

## Setup
1. Copy all files into your PHP server webroot, for example `htdocs/trinket-dashboard`.
2. Edit DB credentials in `add_product.php` if needed.
   ```php
   $DB_HOST = "localhost";
   $DB_NAME = "shop";
   $DB_USER = "root";
   $DB_PASS = "";
   ```
3. The script will create the `shop` database and the `products` table automatically if they do not exist.
4. Open `http://localhost/trinket-dashboard/admin.html` in your browser.

## API
- `POST add_product.php` — JSON body: `{ "name": "...", "price": 9.99, "image_url": "https://...", "description": "..." }`
- `GET  add_product.php` — returns `{ success, products: [...] }`
- `DELETE add_product.php?id={id}` — deletes a product by ID

Requirements:
- PHP 7.4+ with PDO MySQL extension
- MySQL server running and accessible with the configured credentials
# trinket-dashboard
