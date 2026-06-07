const pageType = document.body.dataset.page || "";
// Use the Apache/XAMPP server as the API backend so PHP endpoints are reachable.
// If you open pages with Live Server (127.0.0.1:5500) PHP won't run — use
// http://localhost/trinket-dashboard/ in that case.
const API_BASE = "http://localhost/trinket-dashboard/";
const form = document.getElementById("productForm");
const message = document.getElementById("message");
const submitBtn = document.getElementById("submitBtn");
const refreshBtn = document.getElementById("refreshBtn");
const productList = document.getElementById("productList");
const summaryTotal = document.getElementById("summaryTotal");
const summaryAverage = document.getElementById("summaryAverage");
const summaryLatest = document.getElementById("summaryLatest");
const summaryTotalSmall = document.getElementById("summaryTotalSmall");

function showMessage(text, type) {
  if (!message) return;
  message.textContent = text;
  message.className = "message " + type;
  setTimeout(() => { message.className = "message"; }, 4000);
}

function formatCurrency(value) {
  const amount = Number(value || 0);
  return `PKR ${amount.toLocaleString("en-PK", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  })}`;
}

function updateDashboardSummary(products) {
  if (!summaryTotal && !summaryAverage && !summaryLatest && !summaryTotalSmall) return;

  const total = products.length;
  const average = total
    ? products.reduce((sum, p) => sum + Number(p.price || 0), 0) / total
    : 0;
  const latest = total
    ? `${products[0].name} — ${new Date(products[0].created_at).toLocaleDateString()}`
    : "No products yet";

  if (summaryTotal) summaryTotal.textContent = total;
  if (summaryTotalSmall) summaryTotalSmall.textContent = total;
  if (summaryAverage) summaryAverage.textContent = formatCurrency(average);
  if (summaryLatest) summaryLatest.textContent = latest;
}

function escapeHtml(str) {
  const div = document.createElement("div");
  div.textContent = str;
  return div.innerHTML;
}

function renderProductList(products) {
  if (!productList) return;

  if (!products.length) {
    productList.innerHTML = '<p class="empty-state">No products added yet.</p>';
    return;
  }

  productList.innerHTML = products.map(p => {
    const imageHtml = p.image_url
      ? `<img src="${escapeHtml(p.image_url)}" alt="${escapeHtml(p.name)}" class="product-image" />`
      : `<div class="product-image-placeholder">No image</div>`;

    return `
      <div class="product-item">
        <div class="product-image-wrapper">${imageHtml}</div>
        <div>
          <h3>${escapeHtml(p.name)}</h3>
          <p>${escapeHtml(p.description)}</p>
          <p class="meta">Added: ${new Date(p.created_at).toLocaleString()}</p>
        </div>
        <div class="product-actions">
          <span class="price">${formatCurrency(p.price)}</span>
          <button class="delete-btn" data-id="${p.id}">Delete</button>
        </div>
      </div>
    `;
  }).join("");
}

async function fetchProducts() {
  try {
    const res = await fetch(API_BASE + "add_product.php");
    const data = await res.json();
    if (!data.success || !Array.isArray(data.products)) {
      return [];
    }
    return data.products;
  } catch (err) {
    return [];
  }
}

async function loadProducts() {
  const products = await fetchProducts();
  updateDashboardSummary(products);
  if (pageType === "overview" || pageType === "products") {
    renderProductList(products);
  }
}

async function deleteProduct(productId) {
  if (!confirm("Delete this product permanently?")) {
    return;
  }

  try {
    const res = await fetch(API_BASE + `add_product.php?id=${encodeURIComponent(productId)}`, {
      method: "DELETE",
    });
    const data = await res.json();
    if (data.success) {
      showMessage("Product deleted successfully.", "success");
      loadProducts();
    } else {
      showMessage(data.error || "Failed to delete product.", "error");
    }
  } catch (err) {
    showMessage("Network error while deleting. Please try again.", "error");
  }
}

if (form && pageType === "add") {
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (!submitBtn) return;

    submitBtn.disabled = true;
    submitBtn.textContent = "Adding...";

    const formData = new FormData();
    formData.append("name", document.getElementById("name").value.trim());
    formData.append("price", document.getElementById("price").value);
    formData.append("description", document.getElementById("description").value.trim());

    const imageFile = document.getElementById("image_file").files[0];
    if (imageFile) {
      formData.append("image_file", imageFile);
    }

    const name = document.getElementById("name").value.trim();
    const price = parseFloat(document.getElementById("price").value);
    const description = document.getElementById("description").value.trim();

    if (!name || isNaN(price) || price < 0 || !description) {
      showMessage("Please complete all fields with valid values.", "error");
      submitBtn.disabled = false;
      submitBtn.textContent = "Add Product";
      return;
    }

    try {
      const res = await fetch(API_BASE + "add_product.php", {
        method: "POST",
        body: formData,
      });
      const data = await res.json();
      if (data.success) {
        showMessage("Product added successfully!", "success");
        form.reset();
      } else {
        showMessage(data.error || "Failed to add product.", "error");
      }
    } catch (err) {
      showMessage("Network error. Please try again.", "error");
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = "Add Product";
    }
  });
}

if (productList) {
  productList.addEventListener("click", (event) => {
    const button = event.target.closest(".delete-btn");
    if (!button) return;
    deleteProduct(button.dataset.id);
  });
}

const sidebarLinks = document.querySelectorAll('.sidebar nav a');
sidebarLinks.forEach(link => {
  link.addEventListener('click', () => {
    sidebarLinks.forEach(item => item.classList.remove('active'));
    link.classList.add('active');
  });
});

if (refreshBtn) {
  refreshBtn.addEventListener("click", () => {
    loadProducts();
    showMessage("Dashboard refreshed.", "success");
  });
}

if (pageType === "overview" || pageType === "products") {
  loadProducts();
}
