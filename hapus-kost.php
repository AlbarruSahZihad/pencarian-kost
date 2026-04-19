<?php
require_once 'config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pemilik') {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Hapus kost
$stmt = $pdo->prepare("DELETE FROM kost WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);

header("Location: dashboard-pemilik.php?success=Kost berhasil dihapus");
exit();
?>
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