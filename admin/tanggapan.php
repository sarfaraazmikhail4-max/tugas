<?php
// Tidak perlu include koneksi lagi karena sudah ada di dashboard.php
$pesan = "";

// PROSES UPDATE STATUS & FEEDBACK
if (isset($_POST['tanggapi'])) {
    $id_pelaporan = $_POST['id_pelaporan'];
    $status       = $_POST['status'];
    $feedback     = input($_POST['feedback']);

    $cek = mysqli_query($koneksi, "SELECT * FROM aspirasi WHERE id_pelaporan = '$id_pelaporan'");
    
    if (mysqli_num_rows($cek) > 0) {
        $query = mysqli_query($koneksi, "UPDATE aspirasi SET status='$status', feedback='$feedback' WHERE id_pelaporan='$id_pelaporan'");
    } else {
        $query = mysqli_query($koneksi, "INSERT INTO aspirasi (id_pelaporan, status, feedback) VALUES ('$id_pelaporan', '$status', '$feedback')");
    }

    if ($query) {
        $pesan = "<div class='modern-alert success shadow-sm animate__animated animate__fadeIn'><i class='fas fa-check-circle me-2'></i> Tanggapan berhasil diperbarui!</div>";
    }
}
?>

<style>
    :root {
        --main-green: #5b7245;
        --soft-bg: rgba(255, 255, 255, 0.7);
    }

    .manage-container { padding: 10px; overflow-x: hidden; }

    /* Glassmorphism Table Wrapper */
    .table-glass-wrapper {
        background: var(--soft-bg);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 25px;
        padding: 20px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    /* Container untuk scroll tabel di mobile */
    .table-responsive-custom {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .admin-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 15px;
        min-width: 900px; /* Memaksa scroll horizontal jika layar terlalu kecil */
    }

    .admin-table thead th {
        color: #777;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 10px 15px;
        border: none;
    }

    .admin-table tbody tr {
        background: white;
        border-radius: 15px;
        transition: 0.3s;
        box-shadow: 0 4px 10px rgba(0,0,0,0.02);
    }

    .admin-table tbody tr:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }

    .admin-table td {
        padding: 15px;
        vertical-align: middle;
        border: none;
    }

    /* Info Styling */
    .student-info strong { color: var(--main-green); font-size: 0.95rem; display: block; }
    .loc-tag { font-size: 0.7rem; color: #888; display: block; margin-top: 3px; }
    
    .cat-badge {
        background: #f0f3ed;
        color: var(--main-green);
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 700;
        display: inline-block;
        margin-bottom: 5px;
    }

    /* Form inside Table */
    .form-tanggapan {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 15px;
        min-width: 220px;
    }

    .status-select, .feedback-area {
        border-radius: 10px;
        border: 1px solid #ddd;
        padding: 8px;
        font-size: 0.8rem;
        width: 100%;
        margin-bottom: 8px;
        background: white;
    }

    .feedback-area {
        height: 70px;
        resize: none;
    }

    .btn-save-modern {
        background: var(--main-green);
        color: white;
        border: none;
        padding: 10px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 700;
        width: 100%;
        transition: 0.3s;
    }

    .btn-save-modern:hover { background: #4a5d38; }

    .img-admin-preview {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    /* Media Queries */
    @media (max-width: 768px) {
        .manage-container h2 { font-size: 1.5rem; }
        .table-glass-wrapper { padding: 15px; border-radius: 15px; }
        .admin-table td { padding: 10px; }
    }

    .modern-alert {
        padding: 15px;
        border-radius: 15px;
        margin-bottom: 20px;
    }
    .success { background: #d4edda; color: #155724; border-left: 5px solid #28a745; }
</style>

<div class="manage-container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold m-0" style="color: #333;"><i class="fas fa-inbox text-success me-2"></i>Aspirasi Masuk</h2>
            <p class="text-muted small">Kelola dan berikan tanggapan resmi pada laporan siswa.</p>
        </div>
        <div>
            <span class="badge bg-white text-dark shadow-sm p-2 px-3 border" style="border-radius: 10px;">
                <i class="fas fa-sync-alt me-1 text-success"></i> Real-time Data
            </span>
        </div>
    </div>

    <?php echo $pesan; ?>

    <div class="table-glass-wrapper">
        <div class="table-responsive-custom">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th width="180">Siswa & Waktu</th>
                        <th width="180">Kategori & Lokasi</th>
                        <th>Isi Laporan</th>
                        <th width="100" class="text-center">Foto</th>
                        <th width="260">Tanggapan Admin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $sql = "SELECT i.*, s.nama, k.ket_kategori, a.status as status_skrg, a.feedback 
                            FROM input_aspirasi i
                            LEFT JOIN siswa s ON i.nis = s.nis
                            LEFT JOIN kategori k ON i.id_kategori = k.id_kategori
                            LEFT JOIN aspirasi a ON i.id_pelaporan = a.id_pelaporan
                            ORDER BY i.tgl_lapor DESC";
                    
                    $query = mysqli_query($koneksi, $sql);
                    if(mysqli_num_rows($query) > 0) {
                        while ($d = mysqli_fetch_assoc($query)) {
                            $status_skrg = $d['status_skrg'] ?? 'Menunggu';
                    ?>
                    <tr>
                        <td class="text-center fw-bold text-muted"><?php echo $no++; ?></td>
                        <td class="student-info">
                            <strong><?php echo $d['nama']; ?></strong>
                            <span class="loc-tag"><i class="far fa-clock me-1"></i> <?php echo date('d M, H:i', strtotime($d['tgl_lapor'])); ?></span>
                        </td>
                        <td>
                            <span class="cat-badge"><?php echo $d['ket_kategori']; ?></span>
                            <span class="loc-tag"><i class="fas fa-map-marker-alt me-1 text-danger"></i> <?php echo $d['lokasi']; ?></span>
                        </td>
                        <td>
                            <p class="small mb-0 text-dark" style="line-height: 1.4; max-width: 300px;">
                                <?php echo (strlen($d['ket']) > 120) ? substr($d['ket'], 0, 120) . '...' : $d['ket']; ?>
                            </p>
                        </td>
                        <td class="text-center">
                            <?php if ($d['foto']) : ?>
                                <a href="assets/img/<?php echo $d['foto']; ?>" target="_blank">
                                    <img src="assets/img/<?php echo $d['foto']; ?>" class="img-admin-preview shadow-sm">
                                </a>
                            <?php else : ?>
                                <div class="text-muted small"><i class="fas fa-image-slash fa-2x opacity-25"></i></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" class="form-tanggapan shadow-sm border">
                                <input type="hidden" name="id_pelaporan" value="<?php echo $d['id_pelaporan']; ?>">
                                
                                <select name="status" class="status-select shadow-sm">
                                    <option value="Menunggu" <?php if($status_skrg == 'Menunggu') echo 'selected'; ?>>🟡 Menunggu</option>
                                    <option value="Proses" <?php if($status_skrg == 'Proses') echo 'selected'; ?>>🔵 Proses</option>
                                    <option value="Selesai" <?php if($status_skrg == 'Selesai') echo 'selected'; ?>>🟢 Selesai</option>
                                    <option value="Ditolak" <?php if($status_skrg == 'Ditolak') echo 'selected'; ?>>🔴 Ditolak</option>
                                </select>
                                
                                <textarea name="feedback" class="feedback-area" placeholder="Tulis tanggapan..."><?php echo $d['feedback']; ?></textarea>
                                
                                <button type="submit" name="tanggapi" class="btn-save-modern">
                                    <i class="fas fa-check me-1"></i> SIMPAN
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center py-5 text-muted'>Belum ada laporan masuk.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>