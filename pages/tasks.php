<?php
// add_task.php
// expects a working backend/db_connect.php that sets $conn (MySQLi)
include 'backend/db_connect.php';

// fetch tasks from DB
$tasks = [];
$sql = "SELECT task_id, task_name, description, icon, category FROM tasks ORDER BY task_name";
$res = $conn->query($sql);
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $tasks[] = $r;
    }
}
?>

<!-- FullCalendar (global build) -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>


<style>
  body { background:#f7faf7; }
  .step { display:none; }
  .step.active { display:block; }
  .task-card {
    background:white;
    border-radius:10px;
    border:1px solid #e9ecef;
    padding:18px;
    cursor:pointer;
    transition: transform .12s ease, box-shadow .12s ease;
    text-align:center;
    height:120px;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
  }
  .task-card:hover { transform: translateY(-6px); box-shadow:0 6px 18px rgba(20,20,20,0.06); }
  .task-icon { font-size:26px; margin-bottom:8px; }
  .task-name { font-weight:600; font-size:14px; color:#1f2937; }
  .topbar { display:flex; align-items:center; gap:12px; }
  .fc .fc-daygrid-day:hover { background:#eef6f2; cursor:pointer; }
  .selected-date { font-weight:600; color:#0f5132; }
</style>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="topbar">
      <a href="javascript:history.back()" class="text-decoration-none">&larr;</a>
      <h4 class="m-0">Add Task</h4>
    </div>
    <div>
      <!-- top right can hold user/profile or breadcrumb -->
    </div>
  </div>

  <!-- Stepper header -->
  <div class="mb-4">
    <div class="progress" style="height:8px;">
      <div id="progressBar" class="progress-bar bg-success" role="progressbar" style="width:50%"></div>
    </div>
    <div class="d-flex justify-content-between mt-2">
      <small class="text-muted">1) Choose Task</small>
      <small class="text-muted">2) Pick Date</small>
      <small class="text-muted">3) Select Field</small>
    </div>
  </div>

  <!-- STEP 1: Task Cards -->
  <div id="step1" class="step active">
    <h5 class="mb-3">Select task type</h5>
    <div class="row g-3" id="taskGrid">
      <?php if (empty($tasks)): ?>
        <div class="col-12">
          <div class="alert alert-warning">No tasks found in the database.</div>
        </div>
      <?php else: ?>
        <?php foreach ($tasks as $t):
          $icon = $t['icon'] ?: '🌾';
          $desc = htmlspecialchars($t['description']);
          $name = htmlspecialchars($t['task_name']);
          $tid = (int)$t['task_id'];
        ?>
          <div class="col-6 col-md-4 col-lg-3">
            <div class="task-card" data-taskid="<?= $tid ?>" data-taskname="<?= $name ?>" onclick="chooseTask(this)">
              <div class="task-icon"><?= htmlspecialchars($icon) ?></div>
              <div class="task-name"><?= $name ?></div>
              <?php if (!empty($desc)): ?>
                <small class="text-muted mt-1"><?= strlen($desc) > 60 ? substr($desc,0,57).'...' : $desc ?></small>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- STEP 2: Calendar -->
  <div id="step2" class="step">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <button class="btn btn-outline-secondary btn-sm" onclick="goBackToStep(1)">&larr; Back</button>
      </div>
      <div>
        <span class="text-muted">Selected task:</span>
        <span id="selectedTaskName" class="fw-semibold ms-2"></span>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-8 mb-3">
        <div id="calendar"></div>
      </div>

      <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h6 class="card-title">Selected Date</h6>
            <p id="selectedDateDisplay" class="selected-date">— none —</p>

            <div class="mb-3">
              <label class="form-label">Optional time</label>
              <input type="time" id="selectedTime" class="form-control">
            </div>

            <div class="mb-3">
              <label class="form-label">Notes (optional)</label>
              <textarea id="taskNotes" class="form-control" rows="3"></textarea>
            </div>

            <div class="d-grid gap-2">
              <button id="continueBtn" class="btn btn-success" disabled onclick="continueToNext()">Continue</button>
              <button class="btn btn-outline-secondary" onclick="goBackToStep(1)">Choose different task</button>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<script>
  // ===== STATE =====
  let selectedTask = null;
  let selectedDate = null;
  let fcCalendar = null;
  let fcRendered = false;

  // ===== STEP FUNCTIONS =====
  function chooseTask(el) {
    const taskId = el.getAttribute('data-taskid');
    const taskName = el.getAttribute('data-taskname');

    selectedTask = { id: taskId, name: taskName };
    document.getElementById('selectedTaskName').textContent = taskName;
    goToStep(2);
  }

  function goToStep(n) {
    document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
    document.getElementById('step' + n).classList.add('active');

    // Progress bar update
    const progress = {1: 33, 2: 66, 3: 100}[n] || 0;
    document.getElementById('progressBar').style.width = progress + '%';

    // Initialize calendar when step 2 is active
    if (n === 2) {
      ensureCalendarInitialized();
      if (!fcRendered) {
        try {
          fcCalendar.render();
          fcRendered = true;
        } catch (err) {
          console.error('Error rendering calendar:', err);
        }
      } else {
        try { fcCalendar.updateSize(); } catch (e) { /* ignore */ }
      }
    }
  }

  function goBackToStep(n) {
    if (n === 1) {
      selectedDate = null;
      document.getElementById('selectedDateDisplay').textContent = '— none —';
      document.getElementById('continueBtn').disabled = true;
    }
    goToStep(n);
  }

  // ===== CALENDAR SETUP =====
  function ensureCalendarInitialized() {
    if (fcCalendar) return;
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) {
      console.error('Calendar element not found (#calendar)');
      return;
    }
    if (typeof FullCalendar === 'undefined' || !FullCalendar.Calendar) {
      console.error('FullCalendar library not loaded');
      calendarEl.innerHTML = '<div class="alert alert-danger">Calendar library failed to load.</div>';
      return;
    }

    fcCalendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      dayMaxEvents: true,
      selectable: true,
      dateClick: function(info) {
        selectedDate = info.dateStr;
        document.getElementById('selectedDateDisplay').textContent = selectedDate;
        document.getElementById('continueBtn').disabled = false;
      },
      validRange: function(nowDate) {
        return { start: nowDate }; // today + future only
      }
    });
  }

  // ===== INITIALIZATION =====
  document.addEventListener('DOMContentLoaded', function() {
    ensureCalendarInitialized();
  });

  // ===== CONTINUE BUTTON =====
  function continueToNext() {
    const time = document.getElementById('selectedTime').value;
    const notes = encodeURIComponent(document.getElementById('taskNotes').value || '');
    const tid = selectedTask ? selectedTask.id : '';
    const date = selectedDate || '';

    if (!tid || !date) {
      alert('Please select a date and task.');
      return;
    }

    // use absolute path to avoid relative resolution issues
    const base = window.location.origin + '/Agrilink';
   const url = `pages/task_steps/select_field.php?task_id=${encodeURIComponent(tid)}&date=${encodeURIComponent(date)}&time=${encodeURIComponent(time)}&notes=${notes}`;
    window.location.href = url;

    console.log('Navigating to:', url);
    window.location.href = url;
  }
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

