<?php
// Proteksi: Hanya siswa yang boleh akses
if ($_SESSION['role'] !== 'siswa') {
    echo "<div class='alert alert-danger'>Akses Ditolak!</div>";
    exit();
}
$nis = $_SESSION['nis'];
?>

<style>
    :root {
        --main-green: #5b7245;
        --soft-green: #84a366;
        --light-bg: #f8f9fa;
        --border-color: #f1f3f5;
    }

    .riwayat-container { padding: 20px; background-color: #fff; border-radius: 25px; margin-top: 10px; }

    .section-header h2 { font-weight: 800; color: #333; letter-spacing: -0.5px; }

    /* --- TABLE STYLING --- */
    .table-responsive-custom {
        margin-top: 20px;
        overflow-x: auto;
    }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 15px; 
        table-layout: fixed; /* Mengunci layout agar width kolom dipatuhi */
    }

    /* Pengaturan Lebar Kolom */
    .col-info { width: 140px; } /* Kolom tanggal dibuat lebih lebar */
    .col-kategori { width: 130px; }
    .col-aspirasi { width: auto; }
    .col-lampiran { width: 100px; }
    .col-status { width: 140px; }
    .col-tanggapan { width: 200px; }

    .custom-table thead th {
        color: #adb5bd;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding: 10px 20px;
        border: none;
        text-align: left;
    }

    .custom-table tbody tr {
        background-color: #ffffff;
        box-shadow: 0 5px 20px rgba(0,0,0,0.03);
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    .custom-table tbody tr:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.06);
    }

    .custom-table td {
        padding: 20px;
        vertical-align: middle;
        border: none;
        color: #444;
        word-wrap: break-word; /* Memastikan teks panjang tidak merusak tabel */
    }

    /* Melengkungkan sudut baris */
    .custom-table td:first-child { border-radius: 20px 0 0 20px; border-left: 1px solid var(--border-color); border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); }
    .custom-table td:last-child { border-radius: 0 20px 20px 0; border-right: 1px solid var(--border-color); border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); }

    /* Info Column (Date) - UPDATED */
    .date-wrapper { 
        line-height: 1.1;
        min-width: 100px;
    }
    .day-text { font-size: 1.5rem; font-weight: 800; color: #222; display: block; }
    .month-year { font-size: 0.8rem; font-weight: 700; color: var(--main-green); display: block; text-transform: uppercase; margin-bottom: 5px; }
    .id-text { font-size: 0.65rem; color: #bbb; font-family: 'Monaco', 'Consolas', monospace; display: block; }

    /* Category Badge */
    .tag-category {
        background: #f0f4ed;
        color: var(--main-green);
        padding: 6px 12px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 700;
        display: inline-block;
        white-space: nowrap;
    }

    /* Aspirasi Content */
    .report-text {
        font-size: 0.9rem;
        color: #555;
        line-height: 1.6;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Image Thumbnail */
    .img-thumb-container {
        width: 55px;
        height: 55px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #eee;
    }
    .img-thumb { width: 100%; height: 100%; object-fit: cover; }

    /* Status Pill */
    .status-pill {
        padding: 8px 14px;
        border-radius: 12px;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }
    .pill-menunggu { background: #f8f9fa; color: #6c757d; border: 1px solid #dee2e6; }
    .pill-proses { background: #fff8ec; color: #d98e00; border: 1px solid #ffe8cc; }
    .pill-selesai { background: #edfbf3; color: #28a745; border: 1px solid #c3e6cb; }

    /* Tanggapan Admin Box */
    .admin-feedback {
        background: #fafafa;
        border: 1px dashed #e0e0e0;
        padding: 10px;
        border-radius: 12px;
        font-size: 0.8rem;
        color: #666;
    }

    /* --- RESPONSIVE (Kartu di Mobile) --- */
    @media (max-width: 991px) {
        .custom-table { table-layout: auto; } /* Kembalikan ke auto di mobile */
        .custom-table thead { display: none; }
        .custom-table, .custom-table tbody, .custom-table tr, .custom-table td {
            display: block; width: 100%;
        }
        .custom-table tbody tr { margin-bottom: 25px; border: 1px solid #eee; }
        .custom-table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            text-align: right;
            border: none !important;
            width: 100% !important; /* Reset width di mobile */
        }
        .custom-table td::before {
            content: attr(data-label);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.65rem;
            color: #adb5bd;
            text-align: left;
        }
        .date-wrapper { text-align: right; }
        .day-text { font-size: 1.2rem; display: inline; }
        .month-year { display: inline; margin-left: 5px; }
        .report-text { text-align: right; max-width: 60%; }
        .custom-table td:first-child, .custom-table td:last-child { border-radius: 20px !important; }
    }
</style>

<div class="riwayat-container">
    <div class="section-header d-flex justify-content-between align-items-center">
        <div>
            <h2>Riwayat Aspirasi</h2>
            <p class="text-muted small">Kelola laporan yang telah Anda kirimkan</p>
        </div>
    </div>

    <div class="table-responsive-custom">
        <table class="custom-table">
            <thead>
                <tr>
                    <th class="col-info">Info</th>
                    <th class="col-kategori">Kategori</th>
                    <th class="col-aspirasi">Aspirasi</th>
                    <th class="col-lampiran">Lampiran</th>
                    <th class="col-status">Status</th>
                    <th class="col-tanggapan">Tanggapan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT i.*, k.ket_kategori, a.status as status_akhir, a.feedback 
                        FROM input_aspirasi i
                        LEFT JOIN kategori k ON i.id_kategori = k.id_kategori
                        LEFT JOIN aspirasi a ON i.id_pelaporan = a.id_pelaporan
                        WHERE i.nis = '$nis'
                        ORDER BY i.tgl_lapor DESC";
                
                $query = mysqli_query($koneksi, $sql);
                
                while ($d = mysqli_fetch_assoc($query)) :
                    $status = $d['status_akhir'] ?? 'Menunggu';
                    $pill_class = 'pill-' . strtolower($status);
                ?>
                <tr>
                    <td data-label="Info">
                        <div class="date-wrapper">
                            <span class="month-year"><?php echo date('M Y', strtotime($d['tgl_lapor'])); ?></span>
                            <span class="day-text"><?php echo date('d', strtotime($d['tgl_lapor'])); ?></span>
                            <span class="id-text">#LAP-<?php echo $d['id_pelaporan']; ?></span>
                        </div>
                    </td>
                    <td data-label="Kategori">
                        <span class="tag-category"><?php echo $d['ket_kategori']; ?></span>
                    </td>
                    <td data-label="Aspirasi">
                        <div class="report-text">
                            <?php echo nl2br(htmlspecialchars($d['ket'])); ?>
                        </div>
                    </td>
                    <td data-label="Lampiran">
                        <?php if ($d['foto']) : ?>
                            <div class="img-thumb-container">
                                <a href="assets/img/<?php echo $d['foto']; ?>" target="_blank">
                                    <img src="assets/img/<?php echo $d['foto']; ?>" class="img-thumb">
                                </a>
                            </div>
                        <?php else : ?>
                            <span class="text-muted small">N/A</span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Status">
                        <span class="status-pill <?php echo $pill_class; ?>">
                            <i class="fas fa-circle" style="font-size: 0.3rem;"></i>
                            <?php echo strtoupper($status); ?>
                        </span>
                    </td>
                    <td data-label="Tanggapan">
                        <?php if ($d['feedback']) : ?>
                            <div class="admin-feedback">
                                <?php echo htmlspecialchars($d['feedback']); ?>
                            </div>
                        <?php else : ?>
                            <small class="text-muted italic">No feedback</small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>