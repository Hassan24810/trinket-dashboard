<?php
require_once __DIR__ . '/db_config.php';

try {
    $pdo = getPDO();
    echo "OK: Connected to database successfully.\n";
    $stmt = $pdo->query('SELECT VERSION() as ver');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row && isset($row['ver'])) {
        echo "MySQL version: " . $row['ver'] . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    // include stack for debugging
    echo $e->getTraceAsString();
}
