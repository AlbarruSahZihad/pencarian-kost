<?php
require_once 'config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pemilik') {
    header("Location: login.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kost = $_POST['nama_kost'];
    $lokasi = $_POST['lokasi'];
    $kota = $_POST['kota'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $fasilitas = implode(',', $_POST['fasilitas']);
    $kamar_tersedia = $_POST['kamar_tersedia'];
    
    // Upload foto (opsional)
    $foto = null;
    if(isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "assets/uploads/";
        if(!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . '_' . basename($_FILES['foto']['name']);
        $target_file = $target_dir . $file_name;
        if(move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $foto = $file_name;
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO kost (user_id, nama_kost, lokasi, kota, harga, deskripsi, fasilitas, kamar_tersedia, foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if($stmt->execute([$_SESSION['user_id'], $nama_kost, $lokasi, $kota, $harga, $deskripsi, $fasilitas, $kamar_tersedia, $foto])) {
        header("Location: dashboard-pemilik.php?success=Kost berhasil ditambahkan");
        exit();
    } else {
        $error = "Gagal menambahkan kost!";
    }
}

// Daftar fasilitas yang tersedia
$fasilitas_options = [
    'WiFi', 'AC', 'Kamar Mandi Dalam', 'Parkir', 'Dapur Bersama', 
    'Lemari', 'Meja Belajar', 'TV', 'Kulkas', 'Air Panas', 
    'Laundry', 'Security 24 Jam', 'Area Parkir Motor', 'Mushola'
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Tambah Kost - Pencarian Kost</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #1a1a2e;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        .checkbox-group label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: normal;
            cursor: pointer;
        }
        .btn-submit {
            background: #4f46e5;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
        }
        .btn-submit:hover {
            background: #4338ca;
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
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
                <li><a href="profile.php">Profil</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="form-container">
        <h2 style="margin-bottom: 1.5rem;">Tambah Kost Baru</h2>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Kost *</label>
                <input type="text" name="nama_kost" required placeholder="Contoh: Kost Mahkota Residence">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Lokasi/Jalan *</label>
                    <input type="text" name="lokasi" required placeholder="Nama jalan/area">
                </div>
                <div class="form-group">
                    <label>Kota *</label>
                    <select name="kota" required>
                        <option value="">Pilih Kota</option>
                        <option value="Yogyakarta">Yogyakarta</option>
                        <option value="Jakarta Pusat">Jakarta Pusat</option>
                        <option value="Jakarta Selatan">Jakarta Selatan</option>
                        <option value="Jakarta Barat">Jakarta Barat</option>
                        <option value="Jakarta Timur">Jakarta Timur</option>
                        <option value="Jakarta Utara">Jakarta Utara</option>
                        <option value="Bandung">Bandung</option>
                        <option value="Surabaya">Surabaya</option>
                        <option value="Semarang">Semarang</option>
                        <option value="Medan">Medan</option>
                        <option value="Bali">Bali</option>
                        <option value="Makassar">Makassar</option>
                        <option value="Palembang">Palembang</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Harga per Bulan *</label>
                    <input type="number" name="harga" required placeholder="Rp">
                </div>
                <div class="form-group">
                    <label>Jumlah Kamar Tersedia *</label>
                    <input type="number" name="kamar_tersedia" required placeholder="Jumlah kamar" min="0">
                </div>
            </div>

            <div class="form-group">
                <label>Deskripsi Kost</label>
                <textarea name="deskripsi" rows="4" placeholder="Jelaskan tentang kost Anda, keunggulan, lingkungan sekitar, dll."></textarea>
            </div>

            <div class="form-group">
                <label>Fasilitas</label>
                <div class="checkbox-group">
                    <?php foreach($fasilitas_options as $fasilitas): ?>
                        <label>
                            <input type="checkbox" name="fasilitas[]" value="<?php echo $fasilitas; ?>">
                            <?php echo $fasilitas; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Foto Kost</label>
                <input type="file" name="foto" accept="image/*">
                <small style="color: #718096;">Upload foto terbaik kost Anda (opsional)</small>
            </div>

            <button type="submit" class="btn-submit">Simpan Kost</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>