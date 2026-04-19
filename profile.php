<?php
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Update profil
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ? WHERE id = ?");
    $stmt->execute([$full_name, $phone, $_SESSION['user_id']]);
    $success = "Profil berhasil diperbarui!";
    
    // Refresh data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}

// Ambil riwayat pencarian
$stmt = $pdo->prepare("SELECT * FROM search_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$histories = $stmt->fetchAll();

// Tentukan badge role
$role_badge = $user['role'] == 'pemilik' ? 'Pemilik Kost' : 'Pencari Kost';
$role_color = $user['role'] == 'pemilik' ? '#10b981' : '#4f46e5';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Profil - Pencarian Kost</title>
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
                <?php if($user['role'] == 'pemilik'): ?>
                    <li><a href="dashboard-pemilik.php">Dashboard</a></li>
                    <li><a href="tambah-kost.php">Tambah Kost</a></li>
                <?php endif; ?>
                <li><a href="profil.php">Profil</a></li>
                <li><a href="pengaturan.php">Pengaturan</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
            </div>
            <h2><?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?></h2>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
            <span style="display: inline-block; background: <?php echo $role_color; ?>; color: white; padding: 0.25rem 1rem; border-radius: 20px; font-size: 0.85rem; margin-top: 0.5rem;">
                <?php echo $role_badge; ?>
            </span>
        </div>

        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" class="profile-form">
            <div class="form-group">
                <label>Nama lengkap</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>">
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Nomor telepon</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" placeholder="+62 812-3456-7890">
            </div>
            <button type="submit" class="btn-save">Simpan perubahan</button>
        </form>

        <?php if($user['role'] == 'pencari'): ?>
        <div class="search-history">
            <h3>Riwayat pencarian</h3>
            <div class="history-items">
                <?php foreach($histories as $history): ?>
                    <span class="history-tag"><?php echo htmlspecialchars($history['keyword']); ?></span>
                <?php endforeach; ?>
                <?php if(empty($histories)): ?>
                    <p style="color: #718096;">Belum ada riwayat pencarian</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>