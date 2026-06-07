<?php
// product_api.php - REST endpoint for products
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") { exit(0); }

require_once __DIR__ . "/db_config.php";

// Prefer direct DB connection to the configured database (works on hosted environments).
try {
    $pdo = getPDO();
} catch (PDOException $e) {
    // If direct DB connection fails, attempt server-level connection and try to create/use DB (local dev fallback).
    try {
        $tempDsn = sprintf('mysql:host=%s;port=%d;charset=%s', $DB_HOST, $DB_PORT ?? 3306, $DB_CHARSET ?? 'utf8mb4');
        $pdo = new PDO($tempDsn, $DB_USER, $DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        // Try to create database if permissions allow (keeps previous behavior for local setups)
        try {
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . addslashes($DB_NAME) . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `" . addslashes($DB_NAME) . "`");
        } catch (PDOException $eCreate) {
            // If creation is not permitted (shared hosting), attempt to select the database and report if it fails
            try {
                $pdo->exec("USE `" . addslashes($DB_NAME) . "`");
            } catch (PDOException $eUse) {
                http_response_code(500);
                echo json_encode(["success" => false, "error" => "Database selection failed: " . $eUse->getMessage()]);
                exit;
            }
        }
    } catch (PDOException $e2) {
        http_response_code(500);
        echo json_encode(["success" => false, "error" => "Database connection failed: " . $e2->getMessage()]);
        exit;
    }
}

// Auto-create table on first run
$pdo->exec("CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(1024) DEFAULT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Add image_url column if needed on existing table
try {
    $pdo->exec("ALTER TABLE products ADD COLUMN IF NOT EXISTS image_url VARCHAR(1024) DEFAULT NULL");
} catch (PDOException $e) {
    // ignore if ALTER TABLE not supported or already exists
}

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "POST") {
    $input = json_decode(file_get_contents("php://input"), true);
    if (!$input) { $input = $_POST; }

    $name = trim($input["name"] ?? "");
    $price = isset($input["price"]) ? floatval($input["price"]) : -1;
    $description = trim($input["description"] ?? "");
    $imagePath = null;

    if (isset($_FILES["image_file"]) && $_FILES["image_file"]["error"] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . "/images/uploads";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $tmpName = $_FILES["image_file"]["tmp_name"];
        $originalName = basename($_FILES["image_file"]["name"]);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExtensions = ["jpg", "jpeg", "png", "gif", "webp"];

        if (!in_array($extension, $allowedExtensions, true)) {
            http_response_code(400);
            echo json_encode(["success" => false, "error" => "Invalid image file type. Use JPG, PNG, GIF, or WEBP."]);
            exit;
        }

        $newName = uniqid("product_", true) . '.' . $extension;
        $destination = $uploadDir . '/' . $newName;

        if (!move_uploaded_file($tmpName, $destination)) {
            http_response_code(500);
            echo json_encode(["success" => false, "error" => "Failed to save uploaded image."]);
            exit;
        }

        $imagePath = "images/uploads/" . $newName;
    }

    if ($name === "" || $description === "" || $price < 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Invalid input. name, price, description are required."]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO products (name, price, image_url, description) VALUES (:name, :price, :image_url, :description)");
    $stmt->execute([
        ":name" => $name,
        ":price" => $price,
        ":image_url" => $imagePath,
        ":description" => $description,
    ]);

    echo json_encode([
        "success" => true,
        "id" => $pdo->lastInsertId(),
        "message" => "Product added successfully",
    ]);
    exit;
}

if ($method === "GET") {
    $stmt = $pdo->query("SELECT id, name, price, image_url, description, created_at FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["success" => true, "products" => $products]);
    exit;
}

if ($method === "DELETE") {
    $productId = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
    if ($productId <= 0) {
        http_response_code(400);
        echo json_encode(["success" => false, "error" => "Product ID is required for delete."]);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([":id" => $productId]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(["success" => false, "error" => "Product not found."]);
        exit;
    }

    echo json_encode(["success" => true, "message" => "Product deleted successfully."]);
    exit;
}

http_response_code(405);
echo json_encode(["success" => false, "error" => "Method not allowed"]);
