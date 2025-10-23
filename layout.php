<?php
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$validPages = ['dashboard', 'analytics', 'market', 'forecast', 'about', 'map', 'harvest', 'settings', 'crops', 'tasks'];
if (!in_array($page, $validPages)) {
    $page = 'dashboard';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agrilink Smart System</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet" />

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <!-- ✅ Custom CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9faf7;
        }

        .layout-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex-grow: 1;
            background-color: #fff;
            padding: 1.5rem;
        }

        /* ✅ Active link style */
        .nav-link.active {
            background-color: #10b981 !important;
            color: #fff !important;
            border-radius: 8px;
        }

        /* ✅ Loader style (optional if you want preloader animation) */
        #page-loader {
            position: fixed;
            inset: 0;
            background: rgba(255,255,255,0.85);
            display: none;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            z-index: 9999;
        }
        #page-loader .spinner-border {
            width: 3rem;
            height: 3rem;
        }
        #page-loader p {
            margin-top: 10px;
            color: #10b981;
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="layout-wrapper">
    <?php include 'includes/navbar.php'; ?>

    <div class="main-content">
        <?php include 'includes/header.php'; ?>

        <!-- ✅ Page Loader (optional visual effect) -->
        <div id="page-loader">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p>Loading, please wait...</p>
        </div>

        <!-- ✅ Load correct page -->
        <main>
            <?php include "pages/{$page}.php"; ?>
        </main>
    </div>
</div>

<!-- ✅ Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<!-- ✅ Optional JS for per-page initialization -->
<?php if ($page === 'dashboard'): ?>
<script src="assets/js/dashboard.js"></script>


<script> if (typeof dashboardInit === "function") dashboardInit(); </script>

<?php elseif ($page === 'analytics'): ?>
<script src="assets/js/analytics.js"></script>
<script> if (typeof analyticsInit === "function") analyticsInit(); </script>

<?php elseif ($page === 'tasks'): ?>
<script src="assets/js/tasks.js"></script>
<script> if (typeof tasksInit === "function") tasksInit(); </script>

<?php elseif ($page === 'map'): ?>
<script src="assets/js/map.js"></script>
<script> if (typeof mapInit === "function") mapInit(); </script>

<?php elseif ($page === 'harvest'): ?>
<script src="assets/js/harvest.js"></script>
<script> if (typeof harvestInit === "function") harvestInit(); </script>

<?php elseif ($page === 'settings'): ?>
<script src="assets/js/settings.js"></script>
<script> if (typeof settingsInit === "function") settingsInit(); </script>

<?php elseif ($page === 'crops'): ?>
<script src="assets/js/crops.js"></script>
<script> if (typeof cropsInit === "function") cropsInit(); </script>

<?php endif; ?>

</body>
</html>
