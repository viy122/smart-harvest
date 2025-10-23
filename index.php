<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Smart Harvest</title>

  <!-- Google Fonts: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet" />

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">

</head>
<body>


  <!-- Navbar -->
<nav class="navbar navbar-expand-lg fixed-top custom-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">🌱 Smart Harvest</a>
    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarNav"
      aria-controls="navbarNav"
      aria-expanded="false"
      aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav fs-6">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#home">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#features">Features</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#about">About</a>
        </li>
        <li class="nav-item ms-3">
          <a class="btn btn-success px-3" href="#login">Login</a>
        </li>
      </ul>
    </div>
  </div>
</nav>



  <!-- Hero Section -->
  <header id="home" class="hero-section d-flex align-items-center text-center text-white">
    <div class="container">
      <h1 class="display-4 fw-bold mb-3 hero-title">Smart Harvest</h1>
      <p class="lead mb-4 hero-subtitle">
        Empowering Farmers & Admins with Smart Crop Monitoring and Forecasting
      </p>
      <a href="#login" class="btn btn-lg btn-success fw-semibold shadow hero-btn">Login to Your Account</a>
    </div>
  </header>

  <!-- Features Section -->
  <section id="features" class="py-5">
    <div class="container">
      <h2 class="text-center fw-bold mb-5 text-success">Core Features</h2>
      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="feature-card p-4 h-100 shadow-sm rounded bg-white text-center">
            <i class="bi bi-bar-chart-line-fill feature-icon text-success mb-3"></i>
            <h5 class="fw-semibold mb-2">Crop Monitoring</h5>
            <p class="text-muted small">
              Real-time tracking of crop health and growth stages to maximize yield.
            </p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="feature-card p-4 h-100 shadow-sm rounded bg-white text-center">
            <i class="bi bi-bug-fill feature-icon text-success mb-3"></i>
            <h5 class="fw-semibold mb-2">Pest Alerts</h5>
            <p class="text-muted small">
              Early warnings and identification of pest threats to protect your crops.
            </p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="feature-card p-4 h-100 shadow-sm rounded bg-white text-center">
            <i class="bi bi-droplet-half feature-icon text-success mb-3"></i>
            <h5 class="fw-semibold mb-2">Soil & Water Logs</h5>
            <p class="text-muted small">
              Detailed records of soil quality and water usage for sustainable farming.
            </p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="feature-card p-4 h-100 shadow-sm rounded bg-white text-center">
            <i class="bi bi-calendar-event-fill feature-icon text-success mb-3"></i>
            <h5 class="fw-semibold mb-2">Harvest Forecasting</h5>
            <p class="text-muted small">
              Predictive analytics to plan harvests and optimize resource allocation.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="bg-light py-5">
    <div class="container">
      <h2 class="text-center fw-bold mb-4 text-success">About Smart Harvest</h2>
      <p class="lead text-center mx-auto mb-5" style="max-width: 700px;">
        Smart Harvest is a comprehensive system designed to empower farmers and administrators with advanced tools for crop monitoring, pest management, soil and water tracking, and harvest forecasting. Our platform leverages smart technology to help you make informed decisions, increase productivity, and promote sustainable agriculture.
      </p>
      <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
          <ul class="list-group list-group-flush fs-6">
            <li class="list-group-item">
              <i class="bi bi-check-circle-fill text-success me-2"></i>
              User-friendly interface tailored for farmers and admins
            </li>
            <li class="list-group-item">
              <i class="bi bi-check-circle-fill text-success me-2"></i>
              Real-time data and alerts for proactive crop management
            </li>
            <li class="list-group-item">
              <i class="bi bi-check-circle-fill text-success me-2"></i>
              Secure login and role-based access control
            </li>
            <li class="list-group-item">
              <i class="bi bi-check-circle-fill text-success me-2"></i>
              Supports sustainable and efficient farming practices
            </li>
          </ul>
        </div>
      </div>
    </div>
  </section>




  <!-- Login Section -->
<section id="login" class="py-5 text-center">
  <div class="container">
    <h2 class="fw-bold mb-4 text-success">Access Your Account</h2>
    <p class="mb-4 fs-5">
      Secure login portal for farmers and administrators to manage their data and settings.
    </p>
    <!-- Trigger Modal -->
    <button class="btn btn-lg btn-success px-5 shadow" data-bs-toggle="modal" data-bs-target="#loginModal">Go to Login</button>
  </div>
</section>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content login-modal shadow-lg rounded-4">
      <div class="modal-header bg-success text-white border-0">
        <h5 class="modal-title" id="loginModalLabel">Login to Your Account</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <form action="backend/auth.php" method="POST" class="login-form">
          <div class="mb-3">
            <label for="username" class="form-label">Email</label>
            <input type="text" class="form-control rounded-3" id="username" name="username" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control rounded-3" id="password" name="password" required>
          </div>
          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="showPassword">
            <label class="form-check-label" for="showPassword">Show Password</label>
          </div>
          <button type="submit" class="btn btn-success w-100 py-2">Login</button>
          <div class="text-center mt-3">
            <a href="#" class="text-decoration-none">Forgot Password?</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-success text-white text-center py-3">
    <div class="container">
      <small>&copy; 2024 Smart Harvest. All rights reserved.</small>
    </div>
  </footer>

  <!-- Bootstrap 5 JS Bundle (Popper + Bootstrap JS) -->
   <script src="assets/js/bootstrap.bundle.min.js"></script>
   <script>
      document.getElementById('showPassword').addEventListener('change', function() {
      const passwordField = document.getElementById('password');
      passwordField.type = this.checked ? 'text' : 'password';
    });
</script>

  <!-- Custom JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
