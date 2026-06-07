<?php
if (!isset($page) || !is_string($page)) {
    $page = '';
}

define('BASE_CSS', 'style.css');

define('BASE_JS', 'script.js');

function nav_link($href, $label, $pageName) {
    global $page;
    $active = $page === $pageName ? 'active' : '';
    return sprintf('<a href="%s" class="%s">%s</a>', htmlspecialchars($href, ENT_QUOTES, 'UTF-8'), $active, htmlspecialchars($label, ENT_QUOTES, 'UTF-8'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($title ?? 'Trinket Theory Dashboard', ENT_QUOTES, 'UTF-8') ?></title>
  <link rel="stylesheet" href="<?= BASE_CSS ?>" />
</head>
<body data-page="<?= htmlspecialchars($page, ENT_QUOTES, 'UTF-8') ?>">
  <div class="dashboard-shell">
    <aside class="sidebar">
      <div class="brand">
        <img class="brand-logo" src="images/logo.png" alt="Trinket Theory logo" />
        <div>
          <h1>Trinket Theory</h1>
          <p>Admin Dashboard</p>
        </div>
      </div>

      <nav>
        <?= nav_link('admin.php', 'Overview', 'overview') ?>
        <?= nav_link('products.php', 'Products', 'products') ?>
        <?= nav_link('add_product.php', 'Add Product', 'add') ?>
      </nav>

      <div class="sidebar-footer">
        <p>Powered by</p>
        <div class="powered-by">
          <img src="images/mylogo.png" alt="Powered by logo" />
          <span>RJ Tech</span>
        </div>
      </div>
    </aside>

    <main class="main-panel">
