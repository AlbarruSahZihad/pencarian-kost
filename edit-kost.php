<?php
require_once 'config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'pemilik') {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : 0;

// Ambil data kost
$stmt = $pdo->prepare("SELECT * FROM kost WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user_id']]);
$kost = $stmt->fetch();

if(!$kost) {
    header("Location: dashboard-pemilik.php");
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
    
    $stmt = $pdo->prepare("UPDATE kost SET nama_kost = ?, lokasi = ?, kota = ?, harga = ?, deskripsi = ?, fasilitas = ?, kamar_tersedia = ? WHERE id = ? AND user_id = ?");
    if($stmt->execute([$nama_kost, $lokasi, $kota, $harga, $deskripsi, $fasilitas, $kamar_tersedia, $id, $_SESSION['user_id']])) {
        header("Location: dashboard-pemilik.php?success=Kost berhasil diupdate");
        exit();
    } else {
        $error = "Gagal mengupdate kost!";
    }
}

$fasilitas_kost = explode(',', $kost['fasilitas']);
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
    <title>Edit Kost - Pencarian Kost</title>
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
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
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
        <h2 style="margin-bottom: 1.5rem;">Edit Kost: <?php echo htmlspecialchars($kost['nama_kost']); ?></h2>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Nama Kost *</label>
                <input type="text" name="nama_kost" required value="<?php echo htmlspecialchars($kost['nama_kost']); ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Lokasi/Jalan *</label>
                    <input type="text" name="lokasi" required value="<?php echo htmlspecialchars($kost['lokasi']); ?>">
                </div>
                <div class="form-group">
                    <label>Kota *</label>
                    <select name="kota" required>
                        <option value="">Pilih Kota</option>
                        <option value="Yogyakarta" <?php echo $kost['kota'] == 'Yogyakarta' ? 'selected' : ''; ?>>Yogyakarta</option>
                        <option value="Jakarta Pusat" <?php echo $kost['kota'] == 'Jakarta Pusat' ? 'selected' : ''; ?>>Jakarta Pusat</option>
                        <option value="Jakarta Selatan" <?php echo $kost['kota'] == 'Jakarta Selatan' ? 'selected' : ''; ?>>Jakarta Selatan</option>
                        <option value="Bandung" <?php echo $kost['kota'] == 'Bandung' ? 'selected' : ''; ?>>Bandung</option>
                        <option value="Surabaya" <?php echo $kost['kota'] == 'Surabaya' ? 'selected' : ''; ?>>Surabaya</option>
                        <option value="Bali" <?php echo $kost['kota'] == 'Bali' ? 'selected' : ''; ?>>Bali</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Harga per Bulan *</label>
                    <input type="number" name="harga" required value="<?php echo $kost['harga']; ?>">
                </div>
                <div class="form-group">
                    <label>Jumlah Kamar Tersedia *</label>
                    <input type="number" name="kamar_tersedia" required value="<?php echo $kost['kamar_tersedia']; ?>" min="0">
                </div>
            </div>

            <div class="form-group">
                <label>Deskripsi Kost</label>
                <textarea name="deskripsi" rows="4"><?php echo htmlspecialchars($kost['deskripsi']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Fasilitas</label>
                <div class="checkbox-group">
                    <?php foreach($fasilitas_options as $fasilitas): ?>
                        <label>
                            <input type="checkbox" name="fasilitas[]" value="<?php echo $fasilitas; ?>"
                                <?php echo in_array($fasilitas, $fasilitas_kost) ? 'checked' : ''; ?>>
                            <?php echo $fasilitas; ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn-submit">Update Kost</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>