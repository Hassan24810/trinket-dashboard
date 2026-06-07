<?php
$page = 'products';
$title = 'Products - Trinket Theory';
include __DIR__ . '/includes/header.php';
?>
      <header class="topbar">
        <div>
          <h1>Product Catalog</h1>
          <p>All of your products are listed here with the latest additions first.</p>
        </div>
        <button id="refreshBtn" class="secondary-btn">Refresh</button>
      </header>

      <div class="container">
        <section class="card">
          <div class="list-header">
            <div>
              <h2>Products</h2>
              <p>Latest products are shown first.</p>
            </div>
            <div class="product-count">Showing <span id="summaryTotalSmall">0</span> products</div>
          </div>
          <div id="productList" class="product-list">Loading...</div>
        </section>
      </div>
<?php include __DIR__ . '/includes/footer.php';
