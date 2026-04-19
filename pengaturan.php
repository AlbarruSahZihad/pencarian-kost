<?php
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Pengaturan - Pencarian Kost</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<script>
    // Mobile Navbar Toggle
    const navToggle = document.createElement('button');
    navToggle.className = 'nav-toggle';
    navToggle.innerHTML = '☰';
    navToggle.setAttribute('aria-label', 'Menu');
    
    const navbarContainer = document.querySelector('.navbar .container');
    const navLinks = document.querySelector('.nav-links');
    
    if (navbarContainer && navLinks) {
        // Insert toggle button before nav-links
        navbarContainer.insertBefore(navToggle, navLinks);
        
        navToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!navbarContainer.contains(e.target) && navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
            }
        });
    }
</script>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">PK<span> Pencarian Kost</span></div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="daftar-kost.php">Daftar Kost</a></li>
                <li><a href="favorit.php">Favorit</a></li>
                <li><a href="profile.php">Profil</a></li>
                <li><a href="pengaturan.php">Pengaturan</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="settings-container">
        <div class="settings-card">
            <h3>Pengaturan</h3>
            
            <div class="settings-item">
                <div class="settings-info">
                    <h4>Dark mode</h4>
                    <p>Ubah tema aplikasi</p>
                </div>
                <label class="switch">
                    <input type="checkbox" id="darkModeToggle">
                    <span class="slider"></span>
                </label>
            </div>

            <div class="settings-item">
                <div class="settings-info">
                    <h4>Notifikasi</h4>
                    <p>Terima update kost baru</p>
                </div>
                <label class="switch">
                    <input type="checkbox" id="notificationToggle" checked>
                    <span class="slider"></span>
                </label>
            </div>

            <form action="login.php" method="POST">
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </div>
    </div>

    <script>
        // Dark mode toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        darkModeToggle.addEventListener('change', function() {
            if(this.checked) {
                document.body.classList.add('dark-mode');
                localStorage.setItem('darkMode', 'enabled');
            } else {
                document.body.classList.remove('dark-mode');
                localStorage.setItem('darkMode', 'disabled');
            }
        });
        
        // Check saved preference
        if(localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
            darkModeToggle.checked = true;
        }
        
        // Notification toggle
        const notificationToggle = document.getElementById('notificationToggle');
        notificationToggle.addEventListener('change', function() {
            if(this.checked) {
                console.log('Notifikasi diaktifkan');
            } else {
                console.log('Notifikasi dinonaktifkan');
            }
        });
    </script>

    <style>
        body.dark-mode {
            background-color: #1a1a2e;
            color: #e2e8f0;
        }
        body.dark-mode .kost-card,
        body.dark-mode .filter-section,
        body.dark-mode .settings-card,
        body.dark-mode .profile-container {
            background-color: #2d3748;
            color: #e2e8f0;
        }
        body.dark-mode .navbar {
            background-color: #2d3748;
        }
        body.dark-mode .nav-links a {
            color: #e2e8f0;
        }
    </style>

    <?php include 'footer.php'; ?>
</body>
</html>