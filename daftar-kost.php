<?php
require_once 'config/database.php';

// Filter parameters
$lokasi = isset($_GET['lokasi']) ? $_GET['lokasi'] : '';
$min_harga = isset($_GET['min_harga']) ? $_GET['min_harga'] : '';
$max_harga = isset($_GET['max_harga']) ? $_GET['max_harga'] : '';
$fasilitas_filter = isset($_GET['fasilitas']) ? $_GET['fasilitas'] : [];
$rating_min = isset($_GET['rating']) ? $_GET['rating'] : 0;

// Build query
$query = "SELECT * FROM kost WHERE 1=1";
$params = [];

if($lokasi) {
    $query .= " AND kota = ?";
    $params[] = $lokasi;
}
if($min_harga) {
    $query .= " AND harga >= ?";
    $params[] = $min_harga;
}
if($max_harga) {
    $query .= " AND harga <= ?";
    $params[] = $max_harga;
}
if($rating_min) {
    $query .= " AND rating >= ?";
    $params[] = $rating_min;
}
if(!empty($fasilitas_filter)) {
    foreach($fasilitas_filter as $fasilitas) {
        $query .= " AND fasilitas LIKE ?";
        $params[] = "%$fasilitas%";
    }
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$kosts = $stmt->fetchAll();

// Hitung total
$total_kost = count($kosts);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Daftar Kost - Pencarian Kost</title>
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

    <div class="container" style="display: flex; gap: 2rem; margin-top: 2rem;">
        <!-- Sidebar Filter -->
        <aside style="width: 280px; flex-shrink: 0;">
            <div class="filter-section">
                <h3 class="filter-title">Filter</h3>
                <form method="GET" action="">
                    <div class="filter-group">
                        <label>Lokasi</label>
                        <select name="lokasi" class="filter-select">
                            <option value="">Semua Lokasi</option>
                            <option value="Yogyakarta" <?php echo $lokasi == 'Yogyakarta' ? 'selected' : ''; ?>>Yogyakarta</option>
                            <option value="Jakarta Pusat" <?php echo $lokasi == 'Jakarta Pusat' ? 'selected' : ''; ?>>Jakarta Pusat</option>
                            <option value="Jakarta Selatan" <?php echo $lokasi == 'Jakarta Selatan' ? 'selected' : ''; ?>>Jakarta Selatan</option>
                            <option value="Bandung" <?php echo $lokasi == 'Bandung' ? 'selected' : ''; ?>>Bandung</option>
                            <option value="Bali" <?php echo $lokasi == 'Bali' ? 'selected' : ''; ?>>Bali</option>
                            <option value="Surabaya" <?php echo $lokasi == 'Surabaya' ? 'selected' : ''; ?>>Surabaya</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Harga per bulan</label>
                        <div class="price-range">
                            <input type="number" name="min_harga" placeholder="Min" class="filter-input" value="<?php echo $min_harga; ?>">
                            <input type="number" name="max_harga" placeholder="Max" class="filter-input" value="<?php echo $max_harga; ?>">
                        </div>
                    </div>

                    <div class="filter-group">
                        <label>Fasilitas</label>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="fasilitas[]" value="WiFi" <?php echo in_array('WiFi', $fasilitas_filter) ? 'checked' : ''; ?>> WiFi</label>
                            <label><input type="checkbox" name="fasilitas[]" value="AC" <?php echo in_array('AC', $fasilitas_filter) ? 'checked' : ''; ?>> AC</label>
                            <label><input type="checkbox" name="fasilitas[]" value="Kamar Mandi Dalam" <?php echo in_array('Kamar Mandi Dalam', $fasilitas_filter) ? 'checked' : ''; ?>> Kamar Mandi Dalam</label>
                            <label><input type="checkbox" name="fasilitas[]" value="Parkir" <?php echo in_array('Parkir', $fasilitas_filter) ? 'checked' : ''; ?>> Parkir</label>
                            <label><input type="checkbox" name="fasilitas[]" value="Dapur Bersama" <?php echo in_array('Dapur Bersama', $fasilitas_filter) ? 'checked' : ''; ?>> Dapur Bersama</label>
                        </div>
                    </div>

                    <div class="filter-group">
                        <label>Rating minimum</label>
                        <div class="rating-stars" id="ratingStars">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <span class="<?php echo $rating_min >= $i ? 'active' : ''; ?>" data-rating="<?php echo $i; ?>">★</span>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="<?php echo $rating_min; ?>">
                    </div>

                    <button type="submit" class="btn-detail" style="margin-top: 1rem;">Terapkan Filter</button>
                    <a href="daftar-kost.php" style="display: block; text-align: center; margin-top: 0.5rem; color: #718096; text-decoration: none;">Reset Filter</a>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2>Daftar kost</h2>
                <p style="color: #718096;">Menampilkan <?php echo $total_kost; ?> dari <?php echo $total_kost; ?> kost</p>
            </div>

            <div class="kost-grid">
                <?php foreach($kosts as $kost): ?>
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

            <?php if(empty($kosts)): ?>
                <p style="text-align: center; padding: 3rem;">Tidak ada kost yang ditemukan.</p>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // Rating stars interaction
        const stars = document.querySelectorAll('#ratingStars span');
        const ratingInput = document.getElementById('ratingInput');
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                ratingInput.value = rating;
                
                stars.forEach(s => {
                    if(parseInt(s.dataset.rating) <= rating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>