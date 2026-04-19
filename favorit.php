<?php
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle hapus favorit
if(isset($_GET['hapus'])) {
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['hapus'], $_SESSION['user_id']]);
    header("Location: favorit.php");
    exit();
}

// Ambil daftar favorit
$stmt = $pdo->prepare("
    SELECT f.*, k.nama_kost, k.lokasi, k.kota, k.harga, k.fasilitas, k.rating, k.reviews_count, k.kamar_tersedia 
    FROM favorites f 
    JOIN kost k ON f.kost_id = k.id 
    WHERE f.user_id = ? 
    ORDER BY f.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$favorites = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Favorit Saya - Pencarian Kost</title>
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

    <div class="container" style="margin-top: 2rem;">
        <h2>Kost Favorit Saya</h2>
        <p style="color: #718096; margin-bottom: 2rem;">Kost yang Anda tandai sebagai favorit</p>

        <?php if(empty($favorites)): ?>
            <div style="text-align: center; padding: 4rem; background: white; border-radius: 16px;">
                <p style="font-size: 1.2rem; margin-bottom: 1rem;">Belum ada kost favorit</p>
                <p style="color: #718096; margin-bottom: 1.5rem;">Mulai jelajahi kost dan tandai favoritmu</p>
                <a href="daftar-kost.php" class="btn-detail" style="width: auto; display: inline-block;">Jelajahi Kost</a>
            </div>
        <?php else: ?>
            <div class="kost-grid">
                <?php foreach($favorites as $fav): ?>
                <div class="kost-card">
                    <div class="kost-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; position: relative;">
                        🏠
                        <a href="favorit.php?hapus=<?php echo $fav['id']; ?>" style="position: absolute; top: 10px; right: 10px; background: rgba(0,0,0,0.5); color: white; padding: 5px 10px; border-radius: 20px; text-decoration: none; font-size: 0.8rem;" onclick="return confirm('Hapus dari favorit?')">❤️ Hapus</a>
                    </div>
                    <div class="kost-content">
                        <h3 class="kost-title"><?php echo htmlspecialchars($fav['nama_kost']); ?></h3>
                        <p class="kost-location">📍 <?php echo htmlspecialchars($fav['lokasi']); ?>, <?php echo htmlspecialchars($fav['kota']); ?></p>
                        <div class="kost-rating">
                            <span class="stars"><?php echo str_repeat('★', floor($fav['rating'])) . str_repeat('☆', 5 - floor($fav['rating'])); ?></span>
                            <span class="reviews">(<?php echo $fav['reviews_count']; ?> reviews)</span>
                        </div>
                        <div class="kost-facilities">
                            <?php 
                            $fasilitas = explode(',', $fav['fasilitas']);
                            foreach(array_slice($fasilitas, 0, 3) as $fasilitas_item): 
                            ?>
                                <span class="facility-tag"><?php echo trim($fasilitas_item); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="kost-price">Rp <?php echo number_format($fav['harga'], 0, ',', '.'); ?> <span>/ bulan</span></div>
                        <div class="kost-availability"><?php echo $fav['kamar_tersedia']; ?> kamar tersedia</div>
                        <a href="detail-kost.php?id=<?php echo $fav['kost_id']; ?>" class="btn-detail">Lihat Detail</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>