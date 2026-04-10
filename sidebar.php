<?php
// Pastikan session sudah dimulai di file induk (dashboard.php)
$role = $_SESSION['role'] ?? 'guest';
$user_display = $_SESSION['username'] ?? 'User';
$nis = $_SESSION['nis'] ?? '';
$page = isset($_GET['page']) ? $_GET['page'] : '';

// LOGIKA FOTO (Disamakan dengan halaman Profil)
// Ambil data terbaru dari database agar foto sinkron saat diupdate
$q_user = mysqli_query($koneksi, "SELECT foto, nama FROM siswa WHERE nis = '$nis'");
$d_user = mysqli_fetch_assoc($q_user);

if ($d_user && $d_user['foto'] != "") {
    $foto_path = "assets/img/" . $d_user['foto'];
} else {
    // Gunakan UI Avatars jika foto kosong (sama seperti halaman profil)
    $foto_path = "https://ui-avatars.com/api/?name=" . urlencode($user_display) . "&background=5b7245&color=fff";
}
?>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root {
    --main-green: #5b7245;
    --soft-green: #84a366;
    --orange: #ffa500;
    --glass: rgba(255, 255, 255, 0.85);
}

body {
    font-family: 'Poppins', sans-serif;
    background-color: #f4f7f0;
    margin: 0;
}

/* --- MOBILE NAVBAR UPDATED (REVERSED) --- */
.mobile-navbar {
    display: none;
    position: sticky;
    top: 0;
    background: var(--glass);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    padding: 10px 15px;
    z-index: 1040;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    align-items: center;
    grid-template-columns: 1fr auto 1fr; /* 3 kolom: Kiri(Toggle), Tengah(Brand), Kanan(Profil) */
}

/* Tombol Hamburger di Kiri sekarang */
.nav-toggle-left {
    display: flex;
    justify-content: flex-start;
}

.mobile-toggle {
    background: var(--main-green);
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: 0.3s;
}

.mobile-toggle:hover { background: var(--soft-green); }

/* Brand di Tengah Tetap */
.mobile-navbar .brand {
    font-weight: 700;
    color: var(--main-green);
    margin: 0;
    font-size: 1.1rem;
    letter-spacing: 1px;
}
.brand-orange { color: var(--orange); }

/* Profil di Kanan sekarang */
.nav-profile-right {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
}

.mobile-avatar {
    width: 35px;
    height: 35px;
    border-radius: 50%; /* Bulat sempurna agar senada dengan halaman profil */
    object-fit: cover;
    border: 2px solid white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* --- SIDEBAR CORE --- */
.sidebar {
    width: 280px;
    height: 95vh;
    position: fixed;
    top: 2.5vh;
    left: 15px;
    display: flex;
    flex-direction: column;
    background: var(--glass);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-radius: 25px;
    border: 1px solid rgba(255,255,255,0.3);
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1050;
}

.sidebar-header { padding: 30px 25px; text-align: center; }
.sidebar-header h4 { font-weight: 700; color: var(--main-green); margin-bottom: 20px; }

.user-profile-box {
    background: #fff;
    padding: 15px;
    border-radius: 15px;
    margin: 0 5px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.03);
}

/* User Image di Sidebar */
.sidebar-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 10px;
    border: 3px solid #f4f7f0;
}

.sidebar-menu {
    flex-grow: 1;
    overflow-y: auto;
    padding: 10px 15px;
}

.menu-label {
    font-size: 0.75rem;
    color: #888;
    padding: 20px 20px 10px;
    font-weight: 700;
    text-transform: uppercase;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    margin-bottom: 5px;
    border-radius: 15px;
    font-size: 0.95rem;
    color: #555 !important;
    text-decoration: none;
    transition: 0.3s;
}

.nav-link i { width: 30px; color: var(--main-green); }

.nav-link:hover {
    background: rgba(91,114,69,0.1);
    color: var(--main-green) !important;
    transform: translateX(8px);
}

.nav-link.active {
    background: var(--main-green);
    color: #fff !important;
}
.nav-link.active i { color: #fff; }

.logout-section { padding: 20px; }
.btn-logout {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    padding: 12px;
    border-radius: 15px;
    background: #fff0f0;
    color: #ff6b6b;
    font-weight: 600;
    text-decoration: none;
}

/* RESPONSIVE */
@media (max-width: 992px) {
    .mobile-navbar { display: grid; }
    .sidebar {
        left: -320px;
        top: 0;
        height: 100vh;
        border-radius: 0;
    }
    .sidebar.active { left: 0; }
}

.sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.3);
    z-index: 1045;
    backdrop-filter: blur(2px);
}
.sidebar-overlay.active { display: block; }
</style>

<div class="mobile-navbar">
    <div class="nav-toggle-left">
        <button class="mobile-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="text-center">
        <h4 class="brand">E-<span class="brand-orange">ASPIRASI</span></h4>
    </div>

    <div class="nav-profile-right">
        <div class="text-end d-none d-sm-block" style="line-height: 1;">
            <span class="fw-bold text-truncate" style="font-size: 0.8rem; max-width: 80px; display: block;"><?= $user_display; ?></span>
            <small class="text-muted" style="font-size: 0.6rem;"><?= strtoupper($role); ?></small>
        </div>
        <img src="<?= $foto_path; ?>" class="mobile-avatar" alt="User">
    </div>
</div>

<div class="sidebar-overlay" onclick="toggleSidebar()"></div>

<div class="sidebar" id="mainSidebar">
    <div class="sidebar-header">
        <h4>E-<span class="brand-orange">ASPIRASI</span></h4>
        <div class="user-profile-box">
            <img src="<?= $foto_path; ?>" class="sidebar-avatar" alt="Avatar">
            <div class="fw-bold text-truncate"><?= $user_display; ?></div>
            <span class="badge mt-2 rounded-pill <?= ($role == 'admin') ? 'bg-danger' : 'bg-warning text-dark'; ?>">
                <i class="fas <?= ($role == 'admin') ? 'fa-user-shield' : 'fa-user-graduate'; ?> me-1"></i>
                <?= strtoupper($role); ?>
            </span>
        </div>
    </div>

    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <li>
                <a href="dashboard.php" class="nav-link <?= ($page == '') ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>

            <?php if ($role == 'admin') : ?>
                <li class="menu-label">Master Data</li>
                <li><a href="dashboard.php?page=kategori" class="nav-link <?= ($page == 'kategori') ? 'active' : ''; ?>"><i class="fas fa-tags"></i> Kategori</a></li>
                <li><a href="dashboard.php?page=user" class="nav-link <?= ($page == 'user') ? 'active' : ''; ?>"><i class="fas fa-user-friends"></i> Data Siswa</a></li>
                <li class="menu-label">Layanan</li>
                <li><a href="dashboard.php?page=tanggapan" class="nav-link <?= ($page == 'tanggapan') ? 'active' : ''; ?>"><i class="fas fa-comments"></i> Tanggapan</a></li>
                <li><a href="admin/cetak_laporan.php" target="_blank" class="nav-link"><i class="fas fa-file-export"></i> Laporan (PDF)</a></li>
            <?php else : ?>
                <li class="menu-label">Aspirasi Saya</li>
                <li><a href="#" data-bs-toggle="modal" data-bs-target="#modalLaporan" class="nav-link"><i class="fas fa-paper-plane"></i> Kirim Aspirasi</a></li>
                <li><a href="dashboard.php?page=profil" class="nav-link <?= ($page == 'profil') ? 'active' : ''; ?>"><i class="fas fa-id-card"></i> Profil</a></li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="logout-section">
        <a href="logout.php" class="btn-logout" onclick="return confirm('Yakin ingin keluar?')">
            <i class="fas fa-power-off"></i> Keluar
        </a>
    </div>
</div>

<script>
function toggleSidebar() {
    document.getElementById('mainSidebar').classList.toggle('active');
    document.querySelector('.sidebar-overlay').classList.toggle('active');
}
</script>