<?php
$page = 'add';
$title = 'Add Product - Trinket Theory';
include __DIR__ . '/includes/header.php';
?>
      <header class="topbar">
        <div>
          <h1>Add Product</h1>
          <p>Enter the product details below to add a new item to the catalog.</p>
        </div>
      </header>

      <div class="container">
        <section class="card">
          <h2>New Product</h2>
          <form id="productForm" enctype="multipart/form-data">
            <div class="form-group">
              <label for="name">Product Name</label>
              <input type="text" id="name" name="name" placeholder="e.g. Gold Pendant" required />
            </div>

            <div class="form-group">
              <label for="price">Price (PKR)</label>
              <input type="number" id="price" name="price" step="0.01" min="0" placeholder="29999" required />
            </div>

            <div class="form-group">
              <label for="image_file">Product Image</label>
              <input type="file" id="image_file" name="image_file" accept="image/*" />
            </div>

            <div class="form-group">
              <label for="description">Description</label>
              <textarea id="description" name="description" rows="5" placeholder="Describe the product..." required></textarea>
            </div>

            <button type="submit" id="submitBtn">Add Product</button>
            <div id="message" class="message"></div>
          </form>
        </section>
      </div>
<?php include __DIR__ . '/includes/footer.php';
