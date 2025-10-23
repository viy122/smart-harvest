// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    // Navigation Active State (Update on click)
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', (e) => {
            // Optional: Prevent default and handle section switch here
            // e.preventDefault();
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            link.classList.add('active');
            // Close sidebar on mobile after click
            if (window.innerWidth <= 992) {
                toggleSidebar();
            }
        });
    });
    // Load Dark Mode Preference
    if (localStorage.getItem('darkMode') === 'true') {
        document.body.classList.add('dark-mode');
        // Update toggle icon if needed
    }
});
// Toggle Sidebar Function
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    sidebar.classList.toggle('collapsed');
    sidebar.classList.toggle('active'); // For mobile overlay
    if (window.innerWidth <= 992) {
        overlay.classList.toggle('active');
        toggleBtn.innerHTML = sidebar.classList.contains('active') ? '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
    } else {
        // On desktop, toggle slim mode
        if (sidebar.classList.contains('collapsed')) {
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
        } else {
            toggleBtn.innerHTML = '<i class="fas fa-times"></i>';
        }
    }
}
// Dark Mode Toggle
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    localStorage.setItem('darkMode', isDark);
    // Update button text/icon
    const btn = document.querySelector('.btn-dark-mode');
    btn.innerHTML = isDark ? '<i class="fas fa-sun"></i><span>Light</span>' : '<i class="fas fa-moon"></i><span>Dark</span>';
}

// Handle window resize (reposition for mobile/desktop)
window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (window.innerWidth > 992) {
        sidebar.classList.remove('active');
        sidebar.classList.add('collapsed'); // Default slim on desktop
        overlay.classList.remove('active');
    } else {
        sidebar.classList.remove('collapsed');
        if (!sidebar.classList.contains('active')) {
            sidebar.style.transform = 'translateX(-100%)';
        }
    }
});



//Header
document.addEventListener('DOMContentLoaded', function() {
    // ... your existing sidebar init code ...
    // User Dropdown Handling
    const userBtn = document.querySelector('.user-btn');
    const userMenu = document.getElementById('userMenu');
    let dropdownOpen = false;
    if (userBtn && userMenu) {
        userBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent bubbling
            dropdownOpen = !dropdownOpen;
            userMenu.classList.toggle('show', dropdownOpen);
            userBtn.setAttribute('aria-expanded', dropdownOpen);
            userBtn.querySelector('i').classList.toggle('fa-chevron-up', dropdownOpen); // Rotate arrow
            console.log('User  menu toggled:', dropdownOpen);
        });
        // Close dropdown on outside click
        document.addEventListener('click', (e) => {
            if (!userMenu.contains(e.target) && !userBtn.contains(e.target)) {
                dropdownOpen = false;
                userMenu.classList.remove('show');
                userBtn.setAttribute('aria-expanded', 'false');
                userBtn.querySelector('i').classList.remove('fa-chevron-up');
            }
        });
        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && dropdownOpen) {
                dropdownOpen = false;
                userMenu.classList.remove('show');
                userBtn.setAttribute('aria-expanded', 'false');
                userBtn.focus();
            }
        });
    }

     // Search Functionality (Optional: Basic clear on focus)
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('focus', () => {
            searchInput.parentElement.classList.add('focused');
        });
        searchInput.addEventListener('blur', () => {
            searchInput.parentElement.classList.remove('focused');
        });
        // Add real search logic here, e.g., fetch('/search?q=' + value)
    }
    // Notification Click (Optional: Open modal or redirect)
    const bellBtn = document.querySelector('.action-btn:has(.fa-bell)');
    if (bellBtn) {
        bellBtn.addEventListener('click', () => {
            console.log('Notifications clicked');
            // e.g., window.location.href = 'notifications.php';
        });
    }
    // Sync Dark Mode Button in Header with Sidebar (If both exist)
    const headerDarkBtn = document.querySelector('.modern-header .action-btn[onclick="toggleDarkMode()"]');
    if (headerDarkBtn) {
        // Update icon on toggle (mirror sidebar)
        const originalToggle = window.toggleDarkMode;
        window.toggleDarkMode = function() {
            originalToggle();
            const icon = headerDarkBtn.querySelector('i');
            icon.className = document.body.classList.contains('dark-mode') ? 'fas fa-sun' : 'fas fa-moon';
        };
    }
});


// Global function for user menu toggle (for onclick)
function toggleUserMenu() {
    // This is already handled in event listener; onclick is fallback
    const userBtn = document.querySelector('.user-btn');
    if (userBtn) userBtn.click();
}
// Update main-content padding on load/resize (for header overlap)
window.addEventListener('load', () => {
    document.querySelectorAll('.main-content, .container').forEach(el => {
        el.style.paddingTop = '80px';
    });
});
window.addEventListener('resize', () => {
    // ... your existing resize code ...
    // Re-apply padding if needed
});





