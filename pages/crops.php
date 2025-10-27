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

<!-- 🟢 Add Crop Modal -->
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
              <input type="hidden" id="selectedCropDuration">

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
                <input type="date" id="expected_harvest" class="form-control" readonly>
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
let allFields = [];
let allCrops = [];
let map;
const BASE = window.location.origin + "/Agrilink"; // use single absolute base

// 🔹 Initialization
document.addEventListener("DOMContentLoaded", cropManagementInit);

async function cropManagementInit() {
  try {
    const cropRes = await fetch(`${BASE}/backend/api/crops/getCrops.php`);
    if (!cropRes.ok) throw new Error('Failed to load crops: ' + cropRes.status);
    const cropData = await cropRes.json();

    allCrops = [...(cropData.onFarm || []), ...(cropData.notOnFarm || [])]; // combine both lists

    renderCrops(cropData.onFarm || [], "onFarmCrops", true);
    renderCrops(cropData.notOnFarm || [], "notOnFarmCrops", false);

    await loadFields();

    // Search feature
    const searchEl = document.getElementById("searchCrop");
    if (searchEl) {
      searchEl.addEventListener("input", (e) => {
        const term = e.target.value.toLowerCase();
        document.querySelectorAll(".crop-card").forEach(card => {
          const name = (card.dataset.name || '').toLowerCase();
          card.style.display = name.includes(term) ? "" : "none";
        });
      });
    }

    // 🟢 Planting Date -> Expected Harvest auto calculation
    const plantingEl = document.getElementById("planting_date");
    if (plantingEl) plantingEl.addEventListener("change", handlePlantingDateChange);

  } catch (err) {
    console.error("Error loading data:", err);
  }
}

// 🔹 Render Crops
function renderCrops(list, containerId, onFarm) {
  const container = document.getElementById(containerId);
  if (!container) return;
  container.innerHTML = "";

  if (!list || list.length === 0) {
    container.innerHTML = "<p class='text-muted'>No crops found.</p>";
    return;
  }

  list.forEach(crop => {
    const col = document.createElement("div");
    col.className = "col crop-card";
    col.dataset.name = crop.crop_name || '';
    col.dataset.id = crop.crop_id || '';

    // resolve image path
    let imgSrc = crop.image_path || 'assets/images/placeholder.jpg';
    if (!imgSrc.startsWith('http') && !imgSrc.startsWith('/')) imgSrc = BASE + '/' + imgSrc;
    if (imgSrc.startsWith('//')) imgSrc = window.location.protocol + imgSrc;

    col.innerHTML = `
      <div class="card shadow-sm h-100">
        <img src="${imgSrc}"
             class="card-img-top"
             alt="${(crop.crop_name || '')}">
        <div class="card-body text-center">
          <h6>${crop.crop_name || ''}</h6>
          ${
            onFarm
              ? `<span class="badge bg-${getStatusColor(crop.status)}">${crop.status || ''}</span>`
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

// 🔹 Fetch and populate fields
async function loadFields() {
  try {
    const res = await fetch(`${BASE}/backend/api/map/get_fields.php`);
    if (!res.ok) throw new Error('Failed to load fields: ' + res.status);
    allFields = await res.json();
    populateFieldDropdown(allFields);
  } catch (err) {
    console.error("Error loading fields:", err);
  }
}

function populateFieldDropdown(fields) {
  const select = document.getElementById("field_id");
  if (!select) return;

  if (!fields || fields.length === 0) {
    select.innerHTML = `<option disabled selected>No fields available</option>`;
    return;
  }

  select.innerHTML = fields.map(f =>
    `<option value="${f.field_id}">
        ${f.name || '(Unnamed Field)'}
     </option>`
  ).join("");
}

// 🔹 Add Crop button handler
document.addEventListener("click", (e) => {
  if (e.target.matches(".btn-outline-success")) {
    const card = e.target.closest(".crop-card");
    const cropId = card.dataset.id;

    const selectedCrop = allCrops.find(c => String(c.crop_id) === String(cropId));
    const selCropIdEl = document.getElementById("selectedCropId");
    const selCropDurEl = document.getElementById("selectedCropDuration");
    if (selCropIdEl) selCropIdEl.value = cropId;
    if (selCropDurEl) selCropDurEl.value = selectedCrop?.duration || 0;

    // Reset date fields
    const plantingEl = document.getElementById("planting_date");
    const expectedEl = document.getElementById("expected_harvest");
    if (plantingEl) plantingEl.value = "";
    if (expectedEl) expectedEl.value = "";

    const modalEl = document.getElementById("addCropModal");
    if (modalEl) {
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
    }

    loadMap();
  }
});

// 🔹 Auto-calculate Expected Harvest Date
function handlePlantingDateChange() {
  const plantingEl = document.getElementById("planting_date");
  const durationEl = document.getElementById("selectedCropDuration");
  const expectedEl = document.getElementById("expected_harvest");
  const plantingDate = plantingEl ? plantingEl.value : '';
  const duration = parseInt(durationEl?.value || 0, 10);

  if (!plantingDate || duration <= 0) return;

  const plant = new Date(plantingDate);
  plant.setDate(plant.getDate() + duration);

  const yyyy = plant.getFullYear();
  const mm = String(plant.getMonth() + 1).padStart(2, "0");
  const dd = String(plant.getDate()).padStart(2, "0");

  if (expectedEl) expectedEl.value = `${yyyy}-${mm}-${dd}`;
}

// 🔹 Map loader
function loadMap() {
  if (map) {
    map.eachLayer(layer => {
      if (layer instanceof L.TileLayer) return;
      map.removeLayer(layer);
    });
  } else {
    map = L.map("map").setView([13.75, 121.05], 12);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png").addTo(map);
  }

  allFields.forEach(f => {
    if (f.geometry) {
      let coords = f.geometry;
      if (typeof coords === 'string') {
        try { coords = JSON.parse(coords); } catch(e){ coords = null; }
      }
      if (!coords) return;
      const layer = L.geoJSON(coords).addTo(map);
      layer.on("click", () => {
        const fieldSel = document.getElementById("field_id");
        if (fieldSel) fieldSel.value = f.field_id;
      });
    }
  });
}

// 🟢 Handle Add Crop Form Submission
const addCropForm = document.getElementById("addCropForm");
if (addCropForm) {
  addCropForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const field_id = document.getElementById("field_id")?.value;
    const crop_id = document.getElementById("selectedCropId")?.value;
    const planting_date = document.getElementById("planting_date")?.value;
    const expected_harvest = document.getElementById("expected_harvest")?.value;

    if (!field_id || !crop_id || !planting_date || !expected_harvest) {
      alert("⚠️ Please complete all fields before saving.");
      return;
    }

    try {
      const res = await fetch(`${BASE}/backend/api/crops/addCrop.php`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          field_id,
          crop_id,
          planting_date,
          expected_harvest
        }),
      });

      const text = await res.text();
      let data;
      try { data = JSON.parse(text); } catch (e) { throw new Error('Invalid JSON response: ' + text.slice(0,200)); }

      if (data.success) {
        alert("Crop added successfully!");
        const modal = bootstrap.Modal.getInstance(document.getElementById("addCropModal"));
        if (modal) modal.hide();

        // Optionally reload crop list
        cropManagementInit();
      } else {
        alert("Failed to add crop: " + (data.message || "Unknown error"));
      }
    } catch (err) {
      console.error("Error saving crop:", err);
      alert("Error saving crop. Check console for details.");
    }
  });
}
</script>
