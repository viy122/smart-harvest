<!-- Plant Disease Detection Section -->


    <style>
        /* Custom Styles for Clean Design */
        .analytics-tab .nav-link { 
            border-radius: 12px 12px 0 0; 
            font-weight: 500; 
            border: 1px solid #dee2e6;
        }
        .analytics-tab .nav-link.active { 
            background: #10b981; /* Green theme */
            color: white; 
            border-color: #10b981;
        }
        .symptom-checkbox { 
            margin-right: 1rem; 
            margin-bottom: 0.5rem; 
            display: inline-block;
        }
        .recommendation-item { 
            border-left: 4px solid #f59e0b; /* Orange accent */
            padding: 1rem; 
            margin-bottom: 1rem; 
            background: rgba(255, 255, 255, 0.8); 
            border-radius: 8px; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .activity-modal .modal-header { 
            background: #10b981; 
            color: white; 
            border-radius: 12px 12px 0 0; 
        }
        #preview, #resultPreview { 
            max-height: 300px; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
        }
        .confidence-bar { 
            height: 8px; 
            background: #e2e8f0; 
            border-radius: 4px; 
            overflow: hidden; 
        }
        .confidence-fill { 
            height: 100%; 
            background: #10b981; 
            transition: width 0.3s ease; 
        }
        .harvest-card {
            border: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-radius: 12px;
        }
        .harvest-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        @media (max-width: 768px) { 
            .symptom-group { flex-direction: column; } 
            .symptom-checkbox { margin-right: 0; width: 100%; } 
            .nav-tabs { flex-wrap: wrap; }
            .recommendation-item { margin-bottom: 1rem; }
        }



    </style>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-3 text-success"><i class="fas fa-microscope me-2"></i> Plant Disease Analytics</h2>
                <p class="text-secondary">Detect diseases using AI image analysis or manual symptoms. Get tailored recommendations and schedule treatments in your activity log.</p>
            </div>
        </div>

        <!-- Tabs for Sections -->
        <ul class="nav nav-tabs analytics-tab mb-4" id="analyticsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="ai-tab" data-bs-toggle="tab" data-bs-target="#ai-section" type="button" role="tab">
                    <i class="fas fa-camera me-1"></i> AI Image Detection
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual-section" type="button" role="tab">
                    <i class="fas fa-stethoscope me-1"></i> Manual Diagnosis
                </button>
            </li>
        </ul>

        <div class="tab-content" id="analyticsTabContent">
            <!-- Section 1: AI Image Upload (Your Original UI Enhanced) -->


            <div class="tab-pane fade show active" id="ai-section" role="tabpanel">
                <div class="card shadow-sm harvest-card">
                    <div class="card-body">
                        <h5 class="text-success mb-3"><i class="fas fa-camera me-2"></i>Upload Plant Image</h5>
                        <p class="text-secondary mb-3">Upload a clear photo of the affected plant/leaf for AI-powered disease detection.</p>

                            <form id="diseaseForm" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Upload Plant Image:</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                </div>

                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-search me-2"></i>Analyze
                                </button>
                            </form>
                        <div id="aiResultAlert" class="alert mt-3" style="display:none;"></div>
                        <img id="preview" src="" alt="Preview" class="img-fluid mt-3" style="display:none; border-radius:8px;">
                    </div>
                </div>
            </div>

            <!-- Section 2: Manual Diagnosis -->
            <div class="tab-pane fade" id="manual-section" role="tabpanel">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow-sm harvest-card">
                            <div class="card-body">
                                <h5><i class="fas fa-seedling me-2 text-success"></i>Select Crop</h5>
                                <select id="cropSelect" class="form-select mb-3" required onchange="updateSymptoms()">

                                    <!-- Expand with your crop data -->
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm harvest-card">
                            <div class="card-body">
                                <h5><i class="fas fa-notes-medical me-2 text-warning"></i>Select Visible Symptoms</h5>
                                <div id="symptomsContainer" class="symptom-group d-flex flex-wrap">
                                    <!-- Symptoms populated dynamically by JS -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button id="manualDiagnoseBtn" class="btn btn-primary w-100" disabled>
                            <i class="fas fa-stethoscope me-2"></i> Diagnose Manually
                        </button>
                    </div>
                </div>
                <div id="manualResultAlert" class="alert mt-3" style="display:none;"></div>
            </div>





        </div>
    </div>

    <!-- Activity Scheduling Modal -->
    <div class="modal fade" id="activityModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content activity-modal">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Add Recommendation to Activity Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="activityForm">
                        <input type="hidden" id="activityCrop" name="crop">
                        <input type="hidden" id="activityTask" name="task">
                        <div class="mb-3">
                            <label for="activityDate" class="form-label">Scheduled Date</label>
                            <input type="date" class="form-control" id="activityDate" name="date" value="" required>
                        </div>
                        <div class="mb-3">
                            <label for="activityNotes" class="form-label">Additional Notes</label>
                            <input type="text" class="form-control" id="activityNotes" name="notes" placeholder="e.g., Apply to all tomato plants in row 3">
                        </div>
                        <div class="alert alert-info">
                            <small><strong>Pre-filled Task:</strong> <span id="previewTask"></span></small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="saveActivityBtn">
                        <i class="fas fa-save me-1"></i> Save to Activities
                    </button>
                </div>
            </div>
        </div>
    </div>



<script>
function analyticsInit() {
  console.log("✅ analyticsInit() initialized properly from analytics.js");

  const diseaseForm = document.getElementById("diseaseForm");
  if (!diseaseForm) {
    console.warn("⚠️ diseaseForm not found — page not ready yet");
    return;
  }

  diseaseForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const fileInput = document.getElementById("image");
    const file = fileInput.files[0];
    const resultDiv = document.getElementById("aiResultAlert");
    const preview = document.getElementById("preview");

    if (!file) {
      alert("Please upload an image first.");
      return;
    }

    // Show loading state
    resultDiv.style.display = "block";
    resultDiv.className = "alert alert-info";
    resultDiv.innerHTML = "🧠 Analyzing image... please wait...";
    preview.style.display = "none";

    const formData = new FormData();
    formData.append("file", file);

    try {
      const response = await fetch("backend/detectDisease.php", {
        method: "POST",
        body: formData
      });

      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

      const data = await response.json();
      console.log("✅ AI Detection response:", data);

      // Show image preview
      preview.src = URL.createObjectURL(file);
      preview.style.display = "block";

      // Display results
      if (data.predictions && data.predictions.length > 0) {
        const disease = data.predictions[0].class;
        const confidence = (data.predictions[0].confidence * 100).toFixed(2);

        // Update disease result section
        document.getElementById("diseaseTitle").innerHTML = `<i class="fas fa-bug me-2"></i>${disease}`;
        document.getElementById("confidenceSection").style.display = "block";
        document.getElementById("confidencePct").textContent = confidence;
        document.getElementById("confidenceFill").style.width = `${confidence}%`;

        resultDiv.className = "alert alert-success";
        resultDiv.innerHTML = `
          <strong>Disease:</strong> ${disease}<br>
          <strong>Confidence:</strong> ${confidence}%`;

        // ✅ Fetch Recommendations
        fetch(`/Agrilink/backend/api/diseaseReco.php?disease=${encodeURIComponent(disease)}`)
          .then(res => res.json())
          .then(info => {
            const recommendationsDiv = document.getElementById("recommendationsList");
            if (info.success) {
              let html = `
                <div class="mb-3">
                  <h6 class="text-danger"><i class="fas fa-syringe me-2"></i>Treatments</h6>
                  <ul>${info.treatments.map(t => `<li>${t}</li>`).join("")}</ul>
                </div>
                <div>
                  <h6 class="text-success"><i class="fas fa-shield-alt me-2"></i>Prevention</h6>
                  <ul>${info.prevention.map(p => `<li>${p}</li>`).join("")}</ul>
                </div>
              `;
              recommendationsDiv.innerHTML = html;
            } else {
              recommendationsDiv.innerHTML = `<p class="text-muted">No specific recommendations found for this disease.</p>`;
            }
          })
          .catch(err => {
            console.error("⚠️ Error loading recommendations:", err);
            document.getElementById("recommendationsList").innerHTML = `<p class="text-danger">Error loading recommendations.</p>`;
          });

      } else {
        resultDiv.className = "alert alert-warning";
        resultDiv.innerHTML = "⚠️ No disease detected or unclear image.";
      }

    } catch (err) {
      console.error("🔥 Error:", err);
      resultDiv.className = "alert alert-danger";
      resultDiv.innerHTML = "❌ Error analyzing image. Please try again.";
    }
  });
}
</script>

