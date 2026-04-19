<?php
require_once 'config/database.php';

// Ambil kost trending
$stmt = $pdo->prepare("SELECT * FROM kost ORDER BY rating DESC, reviews_count DESC LIMIT 6");
$stmt->execute();
$trending_kosts = $stmt->fetchAll();

// Hitung statistik
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM kost");
$stmt->execute();
$total_kost = $stmt->fetch()['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Pencarian Kost - Temukan Kost Impianmu</title>
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
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><a href="favorit.php">Favorit</a></li>
                    <li><a href="profile.php">Profil</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn-login">Login</a></li>
                    <li><a href="register.php" class="btn-signup">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <section class="hero">
        <div class="container">
            <h1>Temukan Kost Impianmu</h1>
            <p class="hero-subtitle">Ribuan pilihan kost di seluruh Indonesia dengan fasilitas lengkap dan harga terjangkau.</p>
            <div class="hero-stats">
                <div class="stat-item">
                    <h3><?php echo number_format($total_kost); ?>+</h3>
                    <p>Kost Tersedia</p>
                </div>
                <div class="stat-item">
                    <h3>100%</h3>
                    <p>Aman & Terpercaya</p>
                </div>
                <div class="stat-item">
                    <h3>24 Jam</h3>
                    <p>Proses Cepat</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <h2 class="section-title">Kost Trending</h2>
            <p class="section-subtitle" style="text-align: center; color: #718096; margin-bottom: 2rem;">Pilihan favorit pencari kost bulan ini</p>
            
            <div class="kost-grid">
                <?php foreach($trending_kosts as $kost): ?>
                <div class="kost-card">
                    <div class="kost-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">🏠</div>
                    <div class="kost-content">
                        <h3 class="kost-title"><?php echo htmlspecialchars($kost['nama_kost']); ?></h3>
                        <p class="kost-location">📍 <?php echo htmlspecialchars($kost['lokasi']); ?>, <?php echo htmlspecialchars($kost['kota']); ?></p>
                        <div class="kost-rating">
                            <span class="stars"><?php echo str_repeat('★', floor($kost['rating'])) . str_repeat('☆', 5 - floor($kost['rating'])); ?></span>
                            <span class="reviews">(<?php echo $kost['reviews_count']; ?> reviews)</span>
                        </div>
                        <div class="kost-facilities">
                            <?php 
                            $fasilitas = explode(',', $kost['fasilitas']);
                            foreach(array_slice($fasilitas, 0, 4) as $fasilitas_item): 
                            ?>
                                <span class="facility-tag"><?php echo trim($fasilitas_item); ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="kost-price">Rp <?php echo number_format($kost['harga'], 0, ',', '.'); ?> <span>/ bulan</span></div>
                        <div class="kost-availability"><?php echo $kost['kamar_tersedia']; ?> kamar tersedia</div>
                        <a href="detail-kost.php?id=<?php echo $kost['id']; ?>" class="btn-detail">Lihat Detail</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>PK Pencarian Kost</h4>
                    <p>Platform terpercaya untuk menemukan kost impianmu dengan mudah dan cepat.</p>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <p><a href="index.php">Home</a></p>
                    <p><a href="daftar-kost.php">Daftar Kost</a></p>
                    <p><a href="favorit.php">Favorit</a></p>
                    <p><a href="profil.php">Profil</a></p>
                </div>
                <div class="footer-col">
                    <h4>Contact</h4>
                    <p>📧 info@pencarikost.id</p>
                    <p>📞 +62 812-3456-7890</p>
                    <p>📍 Jakarta, Indonesia</p>
                </div>
                <div class="footer-col">
                    <h4>Ikuti Kami</h4>
                    <p>Instagram</p>
                    <p>Facebook</p>
                    <p>Twitter</p>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
                <p>&copy; 2026 Pencarian Kost. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

