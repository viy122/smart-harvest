<div class="container py-4">
  <h2 class="mb-3">Crop Catalogue</h2>

  <!-- Search -->
  <input type="text" id="searchCrop" class="form-control mb-3" placeholder="Search crops...">

  <!-- Farm Stats -->
  <div class="mb-3">
    <span class="badge bg-success">Active</span>
    <span class="badge bg-warning text-dark">Planned</span>
    <span class="badge bg-secondary">Past</span>
    <span class="badge bg-danger">Needs plan</span>
  </div>

  <!-- On your farm -->
  <h5>On your farm</h5>
  <div id="onFarmCrops" class="row row-cols-2 row-cols-md-4 g-3 mb-4"></div>

  <!-- Add to your farm -->
  <h5>Add to your farm</h5>
  <div id="notOnFarmCrops" class="row row-cols-2 row-cols-md-4 g-3"></div>
</div>

<!-- 🟢 Add Crop Modal (dito mo ilalagay sa ilalim ng container) -->
<div class="modal fade" id="addCropModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Crop to Field</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <!-- Map Panel -->
          <div class="col-md-8">
            <div id="map" style="height: 400px;"></div>
          </div>
          <!-- Form Panel -->
          <div class="col-md-4">
            <form id="addCropForm">
              <input type="hidden" id="selectedCropId">
              <div class="mb-3">
                <label for="field_id" class="form-label">Select Field</label>
                <select id="field_id" class="form-select"></select>
              </div>
              <div class="mb-3">
                <label for="planting_date" class="form-label">Planting Date</label>
                <input type="date" id="planting_date" class="form-control">
              </div>
              <div class="mb-3">
                <label for="expected_harvest" class="form-label">Expected Harvest</label>
                <input type="date" id="expected_harvest" class="form-control">
              </div>
              <button type="submit" class="btn btn-success w-100">Save</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>



<script>
async function cropManagementInit() {
  try {
    const res = await fetch('/Agrilink/backend/api/crops/getCrops.php');
    const data = await res.json();

    renderCrops(data.onFarm, "onFarmCrops", true);
    renderCrops(data.notOnFarm, "notOnFarmCrops", false);

    // Search feature
    document.getElementById("searchCrop").addEventListener("input", (e) => {
      const term = e.target.value.toLowerCase();
      document.querySelectorAll(".crop-card").forEach(card => {
        const name = card.dataset.name.toLowerCase();
        card.style.display = name.includes(term) ? "" : "none";
      });
    });
  } catch (err) {
    console.error("Error loading crops:", err);
  }
}

function renderCrops(list, containerId, onFarm) {
  const container = document.getElementById(containerId);
  container.innerHTML = "";

  if (!list || list.length === 0) {
    container.innerHTML = "<p class='text-muted'>No crops found.</p>";
    return;
  }

  list.forEach(crop => {
    const col = document.createElement("div");
    col.className = "col crop-card";
    col.dataset.name = crop.crop_name;
    col.dataset.id = crop.crop_id; // ✅ add this line


    col.innerHTML = `
      <div class="card shadow-sm h-100">
        <img src="${crop.image_path || '/Agrilink/assets/images/placeholder.jpg'}"
             class="card-img-top"
             alt="${crop.crop_name}">
        <div class="card-body text-center">
          <h6>${crop.crop_name}</h6>
          ${
            onFarm
              ? `<span class="badge bg-${getStatusColor(crop.status)}">${crop.status}</span>`
              : `<button class="btn btn-sm btn-outline-success mt-2">Add</button>`
          }
        </div>
      </div>`;
    
    container.appendChild(col);
  });
}

function getStatusColor(status) {
  switch (status) {
    case "Active": return "success";
    case "Planned": return "warning text-dark";
    case "Past": return "secondary";
    case "Needs plan": return "danger";
    default: return "light";
  }
}


// 🔹 Open modal when clicking "Add"
document.addEventListener("click", (e) => {
  if (e.target.matches(".btn-outline-success")) {
    const cropName = e.target.closest(".crop-card").dataset.name;
    const crop = e.target.closest(".crop-card").dataset;
    const cropId = e.target.closest(".crop-card").dataset.id;
    
    document.getElementById("selectedCropId").value = cropId;
    const modal = new bootstrap.Modal(document.getElementById("addCropModal"));
    modal.show();

    loadMapAndFields();
  }
});

// 🔹 Load map and fields
async function loadMapAndFields() {
  const res = await fetch('/Agrilink/backend/api/map/getFields.php');
  const fields = await res.json();

  // Fill dropdown
  const select = document.getElementById("field_id");
  select.innerHTML = fields.map(f => `<option value="${f.field_id}">${f.name}</option>`).join("");

  // Render Leaflet map
  const map = L.map("map").setView([13.75, 121.05], 12);
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);

  fields.forEach(f => {
    if (f.geometry) {
      const coords = JSON.parse(f.geometry);
      const layer = L.geoJSON(coords).addTo(map);
      layer.on("click", () => select.value = f.field_id);
    }
  });
}


// Initialize on page load
document.addEventListener("DOMContentLoaded", cropManagementInit);
</script>
