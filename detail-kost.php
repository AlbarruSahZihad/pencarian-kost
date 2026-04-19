<?php
require_once 'config/database.php';

$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Ambil detail kost
$stmt = $pdo->prepare("SELECT * FROM kost WHERE id = ?");
$stmt->execute([$id]);
$kost = $stmt->fetch();

if(!$kost) {
    header("Location: daftar-kost.php");
    exit();
}

// Cek apakah sudah difavoritkan
$is_favorited = false;
if(isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND kost_id = ?");
    $stmt->execute([$_SESSION['user_id'], $id]);
    $is_favorited = $stmt->fetch();
}

// Handle tambah favorit
if(isset($_GET['favorit']) && isset($_SESSION['user_id'])) {
    if(!$is_favorited) {
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, kost_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $id]);
    }
    header("Location: detail-kost.php?id=$id");
    exit();
}

// Handle hapus favorit
if(isset($_GET['unfavorit']) && isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND kost_id = ?");
    $stmt->execute([$_SESSION['user_id'], $id]);
    header("Location: detail-kost.php?id=$id");
    exit();
}

// Simpan ke riwayat pencarian jika user login
if(isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("INSERT INTO search_history (user_id, keyword) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $kost['nama_kost']]);
}

$fasilitas_list = explode(',', $kost['fasilitas']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title><?php echo htmlspecialchars($kost['nama_kost']); ?> - Pencarian Kost</title>
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
                    <li><a href="profil.php">Profil</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn-login">Login</a></li>
                    <li><a href="register.php" class="btn-signup">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 2rem;">
        <div style="display: flex; gap: 3rem; flex-wrap: wrap;">
            <!-- Gambar Kost -->
            <div style="flex: 1; min-width: 300px;">
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; height: 400px; display: flex; align-items: center; justify-content: center; color: white; font-size: 8rem;">
                    🏠
                </div>
            </div>

            <!-- Info Kost -->
            <div style="flex: 1.5; min-width: 300px;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <h1 style="font-size: 2rem; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($kost['nama_kost']); ?></h1>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php if($is_favorited): ?>
                            <a href="detail-kost.php?id=<?php echo $id; ?>&unfavorit=1" style="background: #ef4444; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none;">❤️ Hapus Favorit</a>
                        <?php else: ?>
                            <a href="detail-kost.php?id=<?php echo $id; ?>&favorit=1" style="background: #4f46e5; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none;">🤍 Tambah Favorit</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="login.php" style="background: #4f46e5; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none;">Login untuk Favorit</a>
                    <?php endif; ?>
                </div>

                <p style="color: #718096; margin-bottom: 1rem;">📍 <?php echo htmlspecialchars($kost['lokasi']); ?>, <?php echo htmlspecialchars($kost['kota']); ?></p>

                <div class="kost-rating" style="margin-bottom: 1rem;">
                    <span class="stars" style="font-size: 1.2rem;"><?php echo str_repeat('★', floor($kost['rating'])) . str_repeat('☆', 5 - floor($kost['rating'])); ?></span>
                    <span class="reviews">(<?php echo $kost['reviews_count']; ?> reviews)</span>
                </div>

                <div class="kost-price" style="font-size: 2rem; margin-bottom: 1rem;">
                    Rp <?php echo number_format($kost['harga'], 0, ',', '.'); ?> 
                    <span style="font-size: 1rem;">/ bulan</span>
                </div>

                <div class="kost-availability" style="margin-bottom: 1.5rem;">
                    🟢 <?php echo $kost['kamar_tersedia']; ?> kamar tersedia
                </div>

                <div style="margin-bottom: 2rem;">
                    <h3>Deskripsi</h3>
                    <p style="color: #4a5568; line-height: 1.8;">
                        <?php echo htmlspecialchars($kost['deskripsi'] ?: 'Kost nyaman dengan fasilitas lengkap, lokasi strategis dekat dengan kampus dan pusat perkantoran.'); ?>
                    </p>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h3>Fasilitas</h3>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 1rem;">
                        <?php foreach($fasilitas_list as $fasilitas): ?>
                            <span style="background: #f3f4f6; padding: 0.5rem 1rem; border-radius: 8px;">✅ <?php echo trim($fasilitas); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <a href="#" class="btn-detail" style="width: auto; display: inline-block;">Hubungi Pemilik</a>
            </div>
        </div>

        <!-- Kost Rekomendasi -->
        <div style="margin-top: 4rem;">
            <h3>Kost Rekomendasi Lainnya</h3>
            <div class="kost-grid" style="margin-top: 1.5rem;">
                <?php
                $stmt = $pdo->prepare("SELECT * FROM kost WHERE id != ? ORDER BY rating DESC LIMIT 3");
                $stmt->execute([$id]);
                $rekomendasi = $stmt->fetchAll();
                foreach($rekomendasi as $rek):
                ?>
                <div class="kost-card">
                    <div class="kost-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; height: 150px;">🏠</div>
                    <div class="kost-content">
                        <h4><?php echo htmlspecialchars($rek['nama_kost']); ?></h4>
                        <p class="kost-location">📍 <?php echo htmlspecialchars($rek['lokasi']); ?></p>
                        <div class="kost-price" style="font-size: 1.2rem;">Rp <?php echo number_format($rek['harga'], 0, ',', '.'); ?></div>
                        <a href="detail-kost.php?id=<?php echo $rek['id']; ?>" class="btn-detail" style="margin-top: 0.5rem;">Lihat Detail</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>