<?php
// Centralized DB configuration (update the values for your environment)
$DB_HOST = "sql101.infinityfree.com";
$DB_PORT = 3306;
$DB_NAME = "if0_42120947_trinkettheory";
$DB_USER = "if0_42120947";
$DB_PASS = "nACD6G6ISEu8";
$DB_CHARSET = "utf8mb4";

function getPDO()
{
	global $DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASS, $DB_CHARSET;

	$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $DB_HOST, $DB_PORT, $DB_NAME, $DB_CHARSET);

	try {
		$pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES => false,
		]);
		return $pdo;
	} catch (PDOException $e) {
		error_log('getPDO() failed: ' . $e->getMessage());
		throw $e;
	}
}
 