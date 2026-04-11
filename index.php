<?php
// 1. Session wajib di baris paling pertama
session_start();

// 2. Aktifkan pelaporan error agar tidak muncul halaman kosong/500
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'koneksi.php';

// 3. Buat fungsi input di sini agar tidak "Undefined function"
if (!function_exists('input')) {
    function input($data) {
        global $koneksi;
        return mysqli_real_escape_string($koneksi, htmlspecialchars(trim($data)));
    }
}

// 4. Proteksi: Jika sudah login, lempar ke dashboard
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

// --- LOGIKA LOGIN ---
if (isset($_POST['login'])) {
    $user = input($_POST['username']);
    $pass = input($_POST['password']); 

    // PENTING: Gunakan nama tabel sesuai database (biasanya kecil semua)
    // Cek Admin
    $query_admin = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$user' AND password='$pass'");
    
    if ($query_admin && mysqli_num_rows($query_admin) > 0) {
        $data = mysqli_fetch_assoc($query_admin);
        $_SESSION['username'] = $data['nama_ad']; 
        $_SESSION['role']     = 'admin';
        echo "<script>alert('Login Admin Berhasil!'); window.location.href='dashboard.php';</script>";
        exit();
    } else {
        // Cek Siswa
        $query_siswa = mysqli_query($koneksi, "SELECT * FROM siswa WHERE nis='$user' AND pasword='$pass'");
        
        if ($query_siswa && mysqli_num_rows($query_siswa) > 0) {
            $data = mysqli_fetch_assoc($query_siswa);
            $_SESSION['username'] = $data['nama']; 
            $_SESSION['nis']      = $data['nis'];
            $_SESSION['role']     = 'siswa';
            echo "<script>alert('Login Siswa Berhasil!'); window.location.href='dashboard.php';</script>";
            exit();
        } else {
            // Jika query gagal (misal tabel tidak ada), tampilkan errornya
            if (!$query_siswa) {
                $error = "Kesalahan Database: " . mysqli_error($koneksi);
            } else {
                $error = "Username/NIS atau Password salah!";
            }
        }
    }
}


$error = "";
$pesan_daftar = "";

// --- LOGIKA LOGIN (Tetap seperti sebelumnya) ---
if (isset($_POST['login'])) {
    $user = input($_POST['username']);
    $pass = input($_POST['password']); 

    // Cek Admin
    $query_admin = mysqli_query($koneksi, "SELECT * FROM admin WHERE Username='$user' AND password='$pass'");
    if (mysqli_num_rows($query_admin) > 0) {
        $data = mysqli_fetch_assoc($query_admin);
        $_SESSION['username'] = $data['nama_ad']; 
        $_SESSION['role']     = 'admin';
        echo "<script>alert('Login Admin Berhasil!'); window.location.href='dashboard.php';</script>";
        exit();
    } else {
        // Cek Siswa
        $query_siswa = mysqli_query($koneksi, "SELECT * FROM siswa WHERE nis='$user' AND pasword='$pass'");
        if (mysqli_num_rows($query_siswa) > 0) {
            $data = mysqli_fetch_assoc($query_siswa);
            $_SESSION['username'] = $data['nama']; 
            $_SESSION['nis']      = $data['nis'];
            $_SESSION['role']     = 'siswa';
            echo "<script>alert('Login Siswa Berhasil!'); window.location.href='dashboard.php';</script>";
            exit();
        } else {
            $error = "Username/NIS atau Password salah!";
        }
    }
}

// --- LOGIKA DAFTAR (Tetap seperti sebelumnya) ---
if (isset($_POST['daftar'])) {
    $nis     = input($_POST['nis']);
    $nama    = input($_POST['nama']);
    $kelas   = input($_POST['kelas']);
    $pasword = input($_POST['pasword']);

    $cek_nis = mysqli_query($koneksi, "SELECT * FROM siswa WHERE nis = '$nis'");
    if (mysqli_num_rows($cek_nis) > 0) {
        $pesan_daftar = "NIS sudah terdaftar!";
    } else {
        $query = mysqli_query($koneksi, "INSERT INTO siswa (nis, nama, pasword, kelas) VALUES ('$nis', '$nama', '$pasword', '$kelas')");
        if ($query) {
            echo "<script>alert('Pendaftaran Berhasil! Silakan Login.'); window.location.href='login.php';</script>";
        } else {
            $pesan_daftar = "Gagal mendaftar!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Aspirasi | Selamat Datang</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --main-green: #5b7245;
            --soft-green: #84a366;
            --orange-accent: #ffa500;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        /* --- ANIMASI --- */
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        /* --- NAVBAR --- */
        .navbar {
            padding: 20px 0;
            transition: 0.3s;
            z-index: 100;
        }
        .navbar-brand { font-weight: 700; font-size: 1.6rem; color: white !important; }
        .btn-outline-white {
            border: 2px solid rgba(255,255,255,0.7);
            color: white;
            border-radius: 50px;
            padding: 8px 25px;
            font-weight: 600;
            transition: 0.3s;
            background: transparent;
        }
        .btn-outline-white:hover { background: white; color: var(--main-green); }

        /* --- HERO SECTION MODERN --- */
        .hero-section {
            /* PERUBAHAN: Warna hijau kini GRADASI */
            background: linear-gradient(135deg, 
            var(--main-green) 10%, 
            var(--soft-green) 40%, 
            #7a9461 60%, 
            #a3b88e 90%);
            min-height: 85vh;
            padding: 120px 0 150px 0;
            color: white;
            position: relative;
            clip-path: ellipse(150% 100% at 50% 0%); /* Membuat lengkungan di bawah */
            z-index: 1;
        }

        .hero-text {
            text-align: left; /* Justify Left */
            z-index: 2;
        }

        .hero-text h1 {
            font-weight: 800;
            font-size: 3.5rem;
            line-height: 1.2;
            margin-bottom: 25px;
        }

        .hero-text p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 35px;
            max-width: 550px;
        }

        .hero-image-container {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .animated-hero-icon {
            /* PERUBAHAN: Ukuran ikon diperbesar */
            font-size: 250px;
            color: rgba(255, 255, 255, 0.2); /* Transparan lembut */
            animation: float 5s ease-in-out infinite; /* Efek mengambang */
            filter: drop-shadow(0 20px 30px rgba(0,0,0,0.1));
        }

        /* --- TOMBOL GET STARTED --- */
        .btn-get-started {
            background: var(--orange-accent);
            color: white;
            font-weight: 700;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: none;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(255, 165, 0, 0.4);
        }

        .btn-get-started:hover {
            transform: translateY(-5px);
            background: #ffb733;
            color: white;
            box-shadow: 0 15px 30px rgba(255, 165, 0, 0.5);
        }

        /* --- BENTO GRID LAMA (Digabung dengan gaya tumpuk modern) --- */
        .grid-container {
            max-width: 1100px;
            margin: 100px auto 50px; /* Overlap/menumpuk ke hero section */
            position: relative;
            z-index: -10;
            display: grid;
            grid-template-columns: 1fr 1fr 1.5fr;
            grid-auto-rows: minmax(150px, auto);
            gap: 20px;
            padding: 0 20px;
        }
        
        .box {
            background: white;
            border-radius: 20px;
            padding: 30px;
            color: #333; 
            transition: 0.4s ease;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08); 
            border: 1px solid rgba(0,0,0,0.02);
        }
        .box:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 20px 45px rgba(0,0,0,0.12);
        }

        .box h5 { font-weight: 700; color: var(--main-green); margin-bottom: 10px; }
        .box p { font-weight: 400; font-size: 0.9rem; color: #555; line-height: 1.6; margin-bottom: 0; }
        .box i { font-size: 2.5rem; color: var(--orange-accent); margin-bottom: 15px; display: block; }
        
        .box-1 { grid-row: span 2; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; border: 2px solid var(--orange-accent);} 
        .box-1 h4 { font-weight: 700; color: var(--orange-accent); }

        /* --- MODAL STYLE --- */
        .modal-content.modern-modal {
            border: none;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .modal-header .btn-close { background-color: #f8f9fa; border-radius: 50%; padding: 10px; opacity: 1; }
        .input-group-text { background-color: #f8f9fa; border-right: none; color: var(--main-green); border-radius: 12px 0 0 12px; }
        .form-control-modern { border-left: none; border-radius: 0 12px 12px 0; padding: 12px; background-color: #f8f9fa; }
        .form-control-modern:focus { background-color: #fff; box-shadow: none; border-color: #dee2e6; }
        .btn-modern { background: linear-gradient(45deg, var(--main-green), var(--soft-green)); border: none; border-radius: 12px; padding: 12px; font-weight: 600; letter-spacing: 1px; transition: 0.3s; }
        .btn-modern:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(91, 114, 69, 0.3); color: white; }
        .modal-title { font-weight: 800; color: #333; letter-spacing: -0.5px; }
        .text-switch { font-size: 0.85rem; color: #777; }
        .text-switch a { color: var(--orange-accent); text-decoration: none; font-weight: 600; }

        /* --- FOOTER STYLE --- */
        footer {
            background: #ffffff;
            padding: 80px 0 30px; 
            border-top: 1px solid #f0f0f0;
            color: var(--main-green);
            margin-top: 80px;
        }
        .footer-content h5 {
            font-weight: 700;
            margin-bottom: 25px;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1.5px;
            color: #333;
            position: relative;
        }
        .footer-content h5::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -8px;
            width: 30px;
            height: 2px;
            background: var(--orange-accent);
        }
        .footer-link { color: var(--main-green); text-decoration: none; display: block; margin-bottom: 12px; font-size: 0.95rem; opacity: 0.7; transition: 0.3s all ease; }
        .footer-link:hover { opacity: 1; color: var(--orange-accent); transform: translateX(5px); }
        .social-icons i { font-size: 1.2rem; margin-right: 20px; color: var(--main-green); transition: 0.3s; cursor: pointer; }
        .social-icons i:hover { color: var(--orange-accent); transform: translateY(-5px); }
        .copyright { border-top: 1px solid #eee; margin-top: 50px; padding-top: 25px; font-size: 0.85rem; color: #999; }

                                /* Styling agar teks statistik mengecil di HP */
   .stat-number { font-size: 2.5rem; }
.stat-label { font-size: 1rem; }
.step-icon { 
    width: 80px; 
    height: 80px; 
    display: inline-flex; 
    align-items: center; 
    justify-content: center; 
}
.step-icon i { font-size: 2rem; }
.step-text { font-size: 1rem; font-weight: 600; }

/* 2. TABLET (max-width: 991px) */
@media (max-width: 991px) {
    .hero-text { text-align: center; }
    .hero-text h1 { font-size: 2.8rem; }
    .hero-text p { margin: 0 auto 30px; }
    .grid-container {
        grid-template-columns: 1fr 1fr; 
        margin-top: 10px;
    }
    .box-1 {
        grid-row: auto; 
        grid-column: span 2; 
        padding: 40px;
    }
}

/* 3. HP / ANDROID (max-width: 767px) */
@media (max-width: 767px) {
    .hero-section { clip-path: none; padding-bottom: 60px; } /* Hilangkan clip-path di HP agar aman */
    .hero-text h1 { font-size: 2.2rem; }
    
    .grid-container {
        grid-template-columns: 1fr; 
        margin-top: 20px;
    }

    /* KUNCI: Kecilkan font & icon di sini supaya tetap sejajar */
    .stat-number { font-size: 1.2rem !important; }
    .stat-label { font-size: 0.7rem !important; }
    
    .step-icon { 
        width: 50px !important; 
        height: 50px !important; 
    }
    .step-icon i { font-size: 1rem !important; }
    .step-text { font-size: 0.7rem !important; }

    .box-1 { grid-column: span 1; padding: 30px; }
    .footer-content { text-align: center; }
    .footer-content h5::after { left: 50%; transform: translateX(-50%); }
    .contact-item { justify-content: center; }
}
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark position-absolute w-100">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-bullhorn me-2"></i>E-ASPIRASI</a>
            <div class="ms-auto d-flex gap-2">
                <button class="btn btn-link text-white text-decoration-none d-none d-sm-block fw-bold" data-bs-toggle="modal" data-bs-target="#modalDaftar">Daftar</button>
                <button class="btn btn-outline-white" data-bs-toggle="modal" data-bs-target="#modalLogin">Login</button>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 hero-text">
                    <span class="badge bg-light text-success mb-3 px-3 py-2 rounded-pill fw-bold" style="color: var(--main-green) !important;">PORTAL ASPIRASI SISWA</span>
                    <h1>Sampaikan Suaramu,<br>Ciptakan Perubahan!</h1>
                    <p>Wadah resmi bagi siswa untuk menyampaikan keluhan, saran, dan ide kreatif demi kemajuan lingkungan sekolah kita yang lebih transparan dan nyaman.</p>
                    <div class="d-flex flex-wrap gap-3 justify-content-lg-start justify-content-center">
                        <button class="btn btn-get-started" data-bs-toggle="modal" data-bs-target="#modalLogin">
                            Mulai Lapor <i class="fas fa-rocket ms-2"></i>
                        </button>
                        <a href="#fitur" class="btn btn-link text-white text-decoration-none fw-bold p-3">Pelajari Selengkapnya</a>
                    </div>
                </div>
                <div class="col-lg-5 hero-image-container d-none d-lg-flex">
                    <i class="fas fa-comments animated-hero-icon"></i>
                </div>
            </div>
        </div>
    </section>

   <section class="py-4 bg-white">
    <div class="container">
        <div class="row text-center">
            <div class="col-4">
                <h4 class="fw-bold stat-number" style="color: var(--main-green);">150+</h4>
                <p class="text-muted stat-label">Aspirasi</p>
            </div>
            <div class="col-4">
                <h4 class="fw-bold stat-number" style="color: var(--orange-accent);">45</h4>
                <p class="text-muted stat-label">Proses</p>
            </div>
            <div class="col-4">
                <h4 class="fw-bold stat-number" style="color: var(--main-green);">102</h4>
                <p class="text-muted stat-label">Selesai</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="background: #f1f3f0;">
    <div class="container text-center mb-4">
        <h2 class="fw-bold h4">Bagaimana Cara Kerjanya?</h2>
    </div>
    <div class="container">
        <div class="row g-2 justify-content-center">
            <div class="col-3 text-center">
                <div class="step-icon shadow-sm bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2">
                    <i class="fas fa-user-edit text-success"></i>
                </div>
                <h6 class="step-text">Tulis</h6>
            </div>
            <div class="col-3 text-center">
                <div class="step-icon shadow-sm bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2">
                    <i class="fas fa-check-double text-success"></i>
                </div>
                <h6 class="step-text">Verif</h6>
            </div>
            <div class="col-3 text-center">
                <div class="step-icon shadow-sm bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2">
                    <i class="fas fa-reply text-success"></i>
                </div>
                <h6 class="step-text">Respon</h6>
            </div>
            <div class="col-3 text-center">
                <div class="step-icon shadow-sm bg-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
                <h6 class="step-text">Selesai</h6>
            </div>
        </div>
    </div>
</section>
            
    <div class="grid-container" id="fitur">
        <div class="box box-1">
            <i class="fas fa-pen-nib"></i>
            <h4>Buat Aspirasi</h4>
            <p>Sampaikan keluhan, saran, atau ide kreatif Anda untuk kemajuan sekolah kita tercinta dengan mudah dan cepat.</p>
        </div>

        <div class="box box-2">
            <i class="fas fa-history"></i>
            <h5>Pantau Status</h5>
            <p>Cek riwayat pengaduan Anda dan pantau sejauh mana laporan Anda diproses.</p>
        </div>

        <div class="box box-3">
            <i class="fas fa-comments"></i>
            <h5>Tanggapan Cepat</h5>
            <p>Admin dan staf sekolah akan memberikan respon resmi secara transparan.</p>
        </div>

        <div class="box box-4">
            <i class="fas fa-shield-alt"></i>
            <h5>Privasi Terjamin</h5>
            <p>Identitas Anda terlindungi. Lapor tanpa ragu demi kenyamanan bersama.</p>
        </div>

        <div class="box box-5">
            <i class="fas fa-chart-line"></i>
            <h5>Hasil Nyata</h5>
            <p>Setiap aspirasi Anda berkontribusi pada perubahan positif di lingkungan sekolah.</p>
        </div>
    </div>

    <div class="modal fade" id="modalLogin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modern-modal p-4">
                <div class="modal-header border-0">
                    <div>
                        <h3 class="modal-title m-0">Welcome Back!</h3>
                        <p class="text-muted small">Silakan masuk untuk akses dashboard.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <?php if($error): ?> <div class="alert alert-danger py-2 border-0 small"><?= $error ?></div> <?php endif; ?>
                    
                    <form method="POST">
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" name="username" class="form-control form-control-modern" placeholder="Username / NIS" required autocomplete="off">
                        </div>
                        
                        <div class="input-group mb-4">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" class="form-control form-control-modern" placeholder="Password" required>
                        </div>
                        
                        <button type="submit" name="login" class="btn btn-modern w-100 text-white shadow-sm">MASUK SEKARANG</button>
                    </form>
                    
                    <div class="text-center mt-4 text-switch">
                        Belum punya akun? <a href="#" data-bs-toggle="modal" data-bs-target="#modalDaftar" data-bs-dismiss="modal">Daftar di sini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDaftar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modern-modal p-4">
                <div class="modal-header border-0">
                    <div>
                        <h3 class="modal-title m-0">Join Us</h3>
                        <p class="text-muted small">Lengkapi data untuk mulai melapor.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <?php if($pesan_daftar): ?> <div class="alert alert-danger py-2 border-0 small"><?= $pesan_daftar ?></div> <?php endif; ?>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1">NIS</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <input type="number" name="nis" class="form-control form-control-modern" placeholder="12345" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small fw-bold mb-1">Kelas</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                                    <input type="text" name="kelas" class="form-control form-control-modern" placeholder="XII RPL" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="small fw-bold mb-1">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                <input type="text" name="nama" class="form-control form-control-modern" placeholder="Masukkan nama" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="small fw-bold mb-1">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                                <input type="password" name="pasword" class="form-control form-control-modern" placeholder="Minimal 6 karakter" required>
                            </div>
                        </div>
                        
                        <button type="submit" name="daftar" class="btn btn-modern w-100 text-white">BUAT AKUN</button>
                    </form>
                    
                    <div class="text-center mt-4 text-switch">
                        Sudah punya akun? <a href="#" data-bs-toggle="modal" data-bs-target="#modalLogin" data-bs-dismiss="modal">Login kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 footer-content">
                    <h5>E-Aspirasi</h5>
                    <p class="small" style="line-height: 1.8; opacity: 0.8;">
                        Wadah resmi penyampaian aspirasi dan pengaduan siswa untuk menciptakan lingkungan sekolah yang lebih transparan dan inovatif.
                    </p>
                    <div class="social-icons mt-3">
                        <i class="fab fa-instagram"></i>
                        <i class="fab fa-twitter"></i>
                        <i class="fab fa-facebook"></i>
                    </div>
                </div>

                <div class="col-md-4 mb-4 footer-content text-md-center">
                    <h5>Tautan Cepat</h5>
                    <div class="d-inline-block text-start">
                        <a href="#" class="footer-link">Beranda</a>
                        <a href="#" class="footer-link" data-bs-toggle="modal" data-bs-target="#modalLogin">Masuk Sistem</a>
                        <a href="#" class="footer-link" data-bs-toggle="modal" data-bs-target="#modalDaftar">Daftar Akun</a>
                    </div>
                </div>

                <div class="col-md-4 mb-4 footer-content">
                    <h5>Kontak Kami</h5>
                    <div class="contact-item"><i class="fas fa-map-marker-alt me-2 text-warning"></i> Jl. Pendidikan No. 123, Kota Anda</div>
                    <div class="contact-item"><i class="fas fa-envelope me-2 text-warning"></i> admin@sekolah.sch.id</div>
                    <div class="contact-item"><i class="fas fa-phone me-2 text-warning"></i> (021) 12345678</div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 text-center copyright">
                    <p>&copy; <?= date('Y') ?> <strong>UKK Pengaduan Masyarakat</strong>. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php if($error): ?>
    <script>new bootstrap.Modal(document.getElementById('modalLogin')).show();</script>
    <?php endif; ?>
    
    <?php if($pesan_daftar): ?>
    <script>new bootstrap.Modal(document.getElementById('modalDaftar')).show();</script>
    <?php endif; ?>

</body>
</html>
