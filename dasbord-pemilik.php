<?php
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Cek role user
if($_SESSION['role'] != 'pemilik') {
    header("Location: index.php");
    exit();
}

// Ambil data kost milik pemilik
$stmt = $pdo->prepare("SELECT * FROM kost WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$my_kosts = $stmt->fetchAll();

// Statistik
$total_kost = count($my_kosts);
$total_kamar = 0;
$total_terisi = 0;

foreach($my_kosts as $kost) {
    $total_kamar += $kost['kamar_tersedia'];
    // Hitung kamar terisi (asumsi: total kamar awal - tersedia)
    // Untuk demo, kita anggap kamar awal 10
    $total_terisi += (10 - $kost['kamar_tersedia']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Dashboard Pemilik Kost - Pencarian Kost</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            text-align: center;
        }
        .stat-card h3 {
            font-size: 2rem;
            color: #4f46e5;
            margin-bottom: 0.5rem;
        }
        .stat-card p {
            color: #718096;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .btn-action {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
        }
        .btn-add {
            background: #4f46e5;
            color: white;
        }
        .btn-manage {
            background: #10b981;
            color: white;
        }
        .table-container {
            background: white;
            border-radius: 16px;
            overflow-x: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th {
            background: #f9fafb;
            font-weight: 600;
            color: #1a1a2e;
        }
        .status-tersedia {
            color: #10b981;
            font-weight: 500;
        }
        .status-terisi {
            color: #ef4444;
            font-weight: 500;
        }
        .action-icons a {
            margin-right: 0.5rem;
            text-decoration: none;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.85rem;
        }
        .btn-edit {
            background: #f59e0b;
            color: white;
        }
        .btn-delete {
            background: #ef4444;
            color: white;
        }
        .btn-view {
            background: #3b82f6;
            color: white;
        }
    </style>
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
                <li><a href="dashboard-pemilik.php">Dashboard</a></li>
                <li><a href="tambah-kost.php">Tambah Kost</a></li>
                <li><a href="profil.php">Profil</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="container" style="margin-top: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1>Dashboard Pemilik Kost</h1>
                <p style="color: #718096;">Selamat datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
            </div>
            <a href="tambah-kost.php" class="btn-add btn-action">+ Tambah Kost Baru</a>
        </div>

        <!-- Statistik -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo $total_kost; ?></h3>
                <p>Total Kost</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $total_kamar; ?></h3>
                <p>Total Kamar</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $total_terisi; ?></h3>
                <p>Kamar Terisi</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $total_kamar - $total_terisi; ?></h3>
                <p>Kamar Tersedia</p>
            </div>
        </div>

        <!-- Daftar Kost Saya -->
        <div style="margin-top: 2rem;">
            <h2>Daftar Kost Saya</h2>
            <p style="color: #718096; margin-bottom: 1rem;">Kelola kost yang Anda miliki</p>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kost</th>
                            <th>Lokasi</th>
                            <th>Harga</th>
                            <th>Kamar Tersedia</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($my_kosts)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3rem;">
                                    <p>Anda belum memiliki kost. <a href="tambah-kost.php">Tambah kost sekarang</a></p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($my_kosts as $index => $kost): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><strong><?php echo htmlspecialchars($kost['nama_kost']); ?></strong></td>
                                <td><?php echo htmlspecialchars($kost['lokasi']); ?>, <?php echo htmlspecialchars($kost['kota']); ?></td>
                                <td>Rp <?php echo number_format($kost['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo $kost['kamar_tersedia']; ?> kamar</td>
                                <td>
                                    <?php if($kost['kamar_tersedia'] > 0): ?>
                                        <span class="status-tersedia">✓ Tersedia</span>
                                    <?php else: ?>
                                        <span class="status-terisi">✗ Penuh</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-icons">
                                    <a href="detail-kost.php?id=<?php echo $kost['id']; ?>" class="btn-view">Lihat</a>
                                    <a href="edit-kost.php?id=<?php echo $kost['id']; ?>" class="btn-edit">Edit</a>
                                    <a href="hapus-kost.php?id=<?php echo $kost['id']; ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus kost ini?')">Hapus</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tips untuk Pemilik Kost -->
        <div style="margin-top: 3rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 2rem; color: white;">
            <h3 style="margin-bottom: 1rem;">💡 Tips untuk Pemilik Kost</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                <div>
                    <h4>📸 Foto Berkualitas</h4>
                    <p style="font-size: 0.9rem; opacity: 0.9;">Upload foto kost dengan pencahayaan baik untuk menarik lebih banyak pencari kost.</p>
                </div>
                <div>
                    <h4>💰 Harga Kompetitif</h4>
                    <p style="font-size: 0.9rem; opacity: 0.9;">Sesuaikan harga dengan fasilitas dan lokasi untuk bersaing di pasar.</p>
                </div>
                <div>
                    <h4>⭐ Jaga Rating</h4>
                    <p style="font-size: 0.9rem; opacity: 0.9;">Berikan pelayanan terbaik untuk mendapatkan ulasan positif dari penghuni.</p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>