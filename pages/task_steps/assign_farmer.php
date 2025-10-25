<?php
include_once 'backend/db_connect.php'; // adjust path if needed

// Fetch all farmers from the database
$query = "SELECT * FROM farmers";
$result = $conn->query($query);
?>

<div class="container py-4">
  <h2 class="text-success mb-4 text-center">👨‍🌾 Assign Farmer</h2>
  <p class="text-muted text-center mb-4">Select the farmers you want to assign to this task.</p>

  <form id="assignFarmerForm">
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle text-center">
        <thead class="table-success">
          <tr>
            <th>Select</th>
            <th>Farmer Name</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td>
                <input type="checkbox" name="farmer_ids[]" value="<?php echo $row['farmer_id']; ?>">
              </td>
              <td><?php echo htmlspecialchars($row['farmer_name']); ?></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-4">
      <button type="submit" class="btn btn-success px-4 py-2">Save</button>
    </div>
  </form>
</div>

<script>
// Save selected farmers
document.getElementById('assignFarmerForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const selected = [...document.querySelectorAll('input[name="farmer_ids[]"]:checked')]
    .map(cb => cb.value);

  if (selected.length === 0) {
    alert('Please select at least one farmer.');
    return;
  }

  // Retrieve previously stored task or field data if needed
  const selectedFields = JSON.parse(localStorage.getItem('selectedFields') || '[]');
  const selectedTask = localStorage.getItem('selectedTask') || '';

  try {
    const res = await fetch('../../backend/api/save_assignment.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({
        farmer_ids: selected,
        fields: selectedFields,
        task: selectedTask
      })
    });

    const data = await res.json();
    if (data.success) {
      alert('✅ Farmers successfully assigned!');
      window.location.href = '../../layout.php?page=tasks'; // go back or next step
    } else {
      alert('❌ Failed to save assignment.');
    }
  } catch (err) {
    console.error('Error:', err);
    alert('Error saving assignment.');
  }
});
</script>
