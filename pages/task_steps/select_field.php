<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Select Fields</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: #f8fafc;
      font-family: "Poppins", sans-serif;
    }
    .field-card {
      border: 2px solid #ddd;
      border-radius: 12px;
      padding: 15px;
      text-align: center;
      transition: all 0.2s ease;
      cursor: pointer;
      background-color: white;
    }
    .field-card:hover {
      transform: scale(1.02);
      border-color: #28a745;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .field-card.selected {
      background-color: #e8f9ee;
      border-color: #28a745;
    }
    .btn-continue {
      margin-top: 25px;
    }
  </style>
</head>

<body>
  <div class="container py-5">
    <h2 class="text-center mb-4 text-success"><i class="bi bi-geo-alt"></i> Select Fields</h2>
    <p class="text-muted text-center mb-4">Choose one or more fields where this task will be applied.</p>

    <div class="row g-3" id="fieldList">
      <!-- Fields will be dynamically generated here -->
    </div>

    <div class="text-center">
      <button id="continueBtn" class="btn btn-success btn-continue px-4 py-2">Continue</button>
    </div>
  </div>

<script>
  // Fetch fields from PHP (database)
  fetch('http://localhost/Agrilink/backend/api/map/get_fields.php')
    .then(response => response.json())
    .then(fields => {
      const fieldList = document.getElementById('fieldList');

      fields.forEach(field => {
        const col = document.createElement('div');
        col.classList.add('col-md-3');

        col.innerHTML = `
          <div class="field-card" data-id="${field.field_id}">
            <h5>${field.name}</h5>
            <p class="text-muted mb-1">${field.type || 'Unknown Type'}</p>
            <small class="text-secondary">${field.area || 'N/A'} ha</small>
          </div>
        `;

        fieldList.appendChild(col);
      });

      // Enable selection toggle
      document.querySelectorAll('.field-card').forEach(card => {
        card.addEventListener('click', () => {
          card.classList.toggle('selected');
        });
      });

      // Handle continue button
      document.getElementById('continueBtn').addEventListener('click', () => {
        const selectedFields = [];
        document.querySelectorAll('.field-card.selected').forEach(card => {
          selectedFields.push(card.dataset.id);
        });

        if (selectedFields.length === 0) {
          alert('Please select at least one field.');
          return;
        }

        // Store temporarily in localStorage
        localStorage.setItem('selectedFields', JSON.stringify(selectedFields));

        // Go to next step
        window.location.href = 'assign_farmer.php';
      });
    })
    .catch(error => {
      console.error('Error fetching fields:', error);
      alert('Failed to load fields from database.');
    });
</script>

</body>
</html>
