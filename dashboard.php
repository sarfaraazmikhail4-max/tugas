<?php
ob_start(); // Menghindari error "headers already sent"
session_start();
include 'koneksi.php'; // Pastikan koneksi dipanggil di awal

// Cek apakah user sudah login
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: index.php");
    exit();
}

$role = $_SESSION['role'];
$page = isset($_GET['page']) ? $_GET['page'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Aspirasi | Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --main-green: #5b7245;
            --soft-green: #84a366;
            --orange-accent: #ffa500;
        }

        body { 
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e9f2 100%);
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }

        /* Layout Utama */
        .main-content { 
            margin-left: 310px;
            padding: 40px; 
            transition: all 0.3s ease; 
        }

        /* Welcome Card */
        .welcome-card {
            background: linear-gradient(45deg, var(--main-green), var(--soft-green));
            color: white;
            border-radius: 25px;
            padding: 40px;
            border: none;
            box-shadow: 0 10px 25px rgba(91, 114, 69, 0.2);
            position: relative;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .welcome-card::after {
            content: "\f0a1";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            right: -20px;
            bottom: -20px;
            font-size: 150px;
            opacity: 0.1;
            transform: rotate(-15deg);
        }

        /* Card Konten Glassmorphism */
        .content-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

        /* --- SOLUSI TABEL RESPONSIF --- */
       /* --- SOLUSI TABEL FULL VIEW --- */
.table-responsive-wrapper {
    width: 100%;
    overflow-x: auto; /* Mengaktifkan scroll horizontal */
    -webkit-overflow-scrolling: touch;
    border-radius: 15px;
    background: #fff;
    border: 1px solid rgba(0,0,0,0.05);
}

/* Paksa Tabel memiliki lebar yang cukup untuk SEMUA kolom */
.table-responsive-wrapper table {
    min-width: 1100px; /* Ditambah lebarnya agar kolom Tanggapan pasti muat */
    width: 100%;
    margin-bottom: 0;
    table-layout: fixed; /* Mengunci lebar kolom agar konsisten */
}

/* Pengaturan Lebar Kolom secara spesifik agar tidak berantakan */
.table-responsive-wrapper th:nth-child(1) { width: 50px; }  /* No */
.table-responsive-wrapper th:nth-child(2) { width: 130px; } /* Tanggal */
.table-responsive-wrapper th:nth-child(3) { width: 150px; } /* Kategori */
.table-responsive-wrapper th:nth-child(4) { width: 300px; } /* Isi Laporan */
.table-responsive-wrapper th:nth-child(5) { width: 120px; } /* Status */
.table-responsive-wrapper th:nth-child(6) { width: 250px; } /* Tanggapan (FULL) */
.table-responsive-wrapper th:nth-child(7) { width: 100px; } /* Aksi */

/* Agar teks laporan/tanggapan yang panjang turun ke bawah (wrap), bukan hilang */
.table-responsive-wrapper td {
    word-wrap: break-word;
    white-space: normal !important; /* Membolehkan teks baris baru */
    vertical-align: top;
    font-size: 0.9rem;
    padding: 12px 8px;
}

/* Gaya Scrollbar agar terlihat jelas di HP */
.table-responsive-wrapper::-webkit-scrollbar {
    height: 10px;
}
.table-responsive-wrapper::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}
        /* Badge Role */
        .badge-role {
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 50px;
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* Scrollbar Styling untuk Tabel */
        .table-responsive-wrapper::-webkit-scrollbar {
            height: 6px;
        }
        .table-responsive-wrapper::-webkit-scrollbar-thumb {
            background: var(--soft-green);
            border-radius: 10px;
        }

        /* --- MEDIA QUERIES (HP & TABLET) --- */
        @media (max-width: 992px) {
            .main-content { 
                margin-left: 0; 
                padding: 20px;
                padding-top: 80px; 
            }
            .welcome-card { padding: 30px 20px; text-align: center; }
            .welcome-card h1 { font-size: 1.6rem; }
            .stat-card h2 { font-size: 1.4rem; }
        }

        @media (max-width: 576px) {
            .section-title { font-size: 1.2rem; }
            .btn-group-sm > .btn { font-size: 0.75rem; }
        }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <?php
            if ($role == 'admin') {
                switch ($page) {
                    case 'kategori': include 'admin/kategori.php'; break;
                    case 'user':     include 'admin/user.php'; break;
                    case 'tanggapan': include 'admin/tanggapan.php'; break;
                    case 'laporan':   
                        ?>
                        <div class="content-card text-center py-5 shadow-sm">
                            <i class="fas fa-file-pdf fa-4x text-danger mb-3"></i>
                            <h2 class="fw-bold">Laporan Aspirasi</h2>
                            <p class="text-muted">Klik tombol untuk mencetak laporan ke format PDF.</p>
                            <a href="admin/cetak_laporan.php" target="_blank" class="btn btn-danger btn-lg rounded-pill px-5">
                                <i class="fas fa-print me-2"></i> Cetak Sekarang
                            </a>
                        </div>
                        <?php
                        break;
                    
                    default:
                        // Logika Data Dashboard Admin
                        $total_all = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM input_aspirasi"))['total'] ?? 0;
                        $val_menunggu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM input_aspirasi i LEFT JOIN aspirasi a ON i.id_pelaporan = a.id_pelaporan WHERE a.status = 'Menunggu' OR a.status IS NULL"))['total'] ?? 0;
                        $val_proses = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM aspirasi WHERE status = 'Proses'"))['total'] ?? 0;
                        $val_selesai = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM aspirasi WHERE status = 'Selesai'"))['total'] ?? 0;

                        $kat_labels = []; $kat_values = [];
                        $q_kat = mysqli_query($koneksi, "SELECT k.ket_kategori, COUNT(i.id_pelaporan) as jumlah FROM kategori k LEFT JOIN input_aspirasi i ON k.id_kategori = i.id_kategori GROUP BY k.id_kategori");
                        while($rk = mysqli_fetch_assoc($q_kat)) {
                            $kat_labels[] = $rk['ket_kategori'];
                            $kat_values[] = $rk['jumlah'];
                        }
                        ?>

                        <div class="welcome-card shadow">
                            <span class="badge-role mb-2 d-inline-block">System Administrator</span>
                            <h1 class="fw-bold">Halo, <?= $_SESSION['username']; ?>!</h1>
                            <p class="opacity-75">Berikut rangkuman laporan aspirasi sekolah hari ini.</p>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-4">
                                <div class="content-card text-center border-start border-5 border-warning py-3 mb-0">
                                    <h6 class="text-muted small fw-bold">MENUNGGU</h6>
                                    <h2 class="fw-bold text-dark mb-0"><?= $val_menunggu; ?></h2>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="content-card text-center border-start border-5 border-primary py-3 mb-0">
                                    <h6 class="text-muted small fw-bold">PROSES</h6>
                                    <h2 class="fw-bold text-dark mb-0"><?= $val_proses; ?></h2>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="content-card text-center border-start border-5 border-success py-3 mb-0">
                                    <h6 class="text-muted small fw-bold">SELESAI</h6>
                                    <h2 class="fw-bold text-dark mb-0"><?= $val_selesai; ?></h2>
                                </div>
                            </div>
                        </div>

                        <div class="content-card">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                                <h4 class="fw-bold m-0">Visualisasi Data</h4>
                                <div class="btn-group btn-group-sm shadow-sm">
                                    <button class="btn btn-outline-success active" onclick="changeChart('doughnut', this)">Status</button>
                                    <button class="btn btn-outline-success" onclick="changeChart('bar', this)">Kategori</button>
                                </div>
                            </div>
                            <div style="position: relative; height: 320px;">
                                <canvas id="dynamicChart"></canvas>
                            </div>
                        </div>

                        <script>
                            let myChart;
                            const ctx = document.getElementById('dynamicChart').getContext('2d');
                            function renderChart(type) {
                                if (myChart) myChart.destroy();
                                const isBar = (type === 'bar');
                                myChart = new Chart(ctx, {
                                    type: type,
                                    data: isBar ? {
                                        labels: <?= json_encode($kat_labels) ?>,
                                        datasets: [{ label: 'Jumlah', data: <?= json_encode($kat_values) ?>, backgroundColor: '#84a366', borderRadius: 8 }]
                                    } : {
                                        labels: ['Menunggu', 'Proses', 'Selesai'],
                                        datasets: [{ data: [<?= $val_menunggu ?>, <?= $val_proses ?>, <?= $val_selesai ?>], backgroundColor: ['#ffc107', '#0d6efd', '#198754'] }]
                                    },
                                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', display: !isBar } } }
                                });
                            }
                            function changeChart(type, btn) {
                                document.querySelectorAll('.btn-group .btn').forEach(b => b.classList.remove('active'));
                                btn.classList.add('active');
                                renderChart(type);
                            }
                            renderChart('doughnut');
                        </script>
                        <?php
                        break;
                }
            } else {
                // DASHBOARD SISWA
                switch ($page) {
                    case 'profil':  include 'siswa/profil.php'; break;
                    case 'riwayat':
                        ?>
                        <div class="content-card">
                            <h4 class="fw-bold mb-4">Riwayat Lengkap</h4>
                            <div class="table-responsive-wrapper">
                                <?php include 'siswa/riwayat.php'; ?>
                            </div>
                        </div>
                        <?php
                        break;
                    default:
                        ?>
                        <div class="welcome-card shadow">
                            <span class="badge-role mb-2 d-inline-block">Portal Siswa</span>
                            <h1 class="fw-bold">Halo, <?= $_SESSION['username']; ?>!</h1>
                            <p class="opacity-75">Suaramu membawa perubahan. Laporkan aspirasimu untuk sekolah yang lebih baik.</p>
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-success mt-2 shadow" data-bs-toggle="modal" data-bs-target="#modalLaporan">
                                <i class="fas fa-paper-plane me-2"></i> Buat Laporan Baru
                            </button>
                        </div>
                        
                        <div class="content-card">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="fw-bold m-0"><i class="fas fa-history text-success me-2"></i>Riwayat Terakhir</h4>
                                <span class="badge bg-soft-success text-success d-md-none" style="background: #e8f5e9;">Geser Tabel &rarr;</span>
                            </div>
                            <hr class="opacity-25 mb-4">
                            
                            <div class="table-responsive-wrapper">
                                <?php include 'siswa/riwayat.php'; ?>
                            </div>
                        </div>
                        <?php
                        break;
                }
            }
            ?>
        </div>
    </div>

    <?php if ($role == 'siswa') include 'siswa/kirim_aspirasi.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>