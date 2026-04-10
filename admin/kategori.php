<?php
// Proteksi Admin (Pastikan session_start() sudah ada di dashboard.php)
if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

$pesan = "";

// 1. PROSES TAMBAH (CREATE)
if (isset($_POST['tambah'])) {
    $ket = isset($_POST['ket_kategori']) ? input($_POST['ket_kategori']) : "";
    if (!empty($ket)) {
        $query = mysqli_query($koneksi, "INSERT INTO kategori (ket_kategori) VALUES ('$ket')");
        if ($query) {
            $pesan = "<div class='modern-alert success shadow-sm animate__animated animate__fadeIn'><i class='fas fa-check-circle me-2'></i> Kategori <b>$ket</b> berhasil ditambahkan!</div>";
        }
    }
}

// 2. PROSES UPDATE (UPDATE)
if (isset($_POST['update'])) {
    $id  = isset($_POST['id_kategori']) ? input($_POST['id_kategori']) : "";
    $ket = isset($_POST['ket_kategori']) ? input($_POST['ket_kategori']) : "";
    if (!empty($id) && !empty($ket)) {
        $query = mysqli_query($koneksi, "UPDATE kategori SET ket_kategori='$ket' WHERE id_kategori='$id'");
        if ($query) {
            $pesan = "<div class='modern-alert success shadow-sm animate__animated animate__fadeIn'><i class='fas fa-sync me-2'></i> Kategori berhasil diperbarui!</div>";
        }
    }
}

// 3. PROSES HAPUS (DELETE)
if (isset($_GET['hapus'])) {
    $id = input($_GET['hapus']);
    $query = mysqli_query($koneksi, "DELETE FROM kategori WHERE id_kategori='$id'");
    if ($query) {
        $pesan = "<div class='modern-alert success shadow-sm animate__animated animate__fadeIn'><i class='fas fa-trash-alt me-2'></i> Kategori berhasil dihapus!</div>";
    }
}

// AMBIL DATA UNTUK FORM EDIT
$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = input($_GET['edit']);
    $res = mysqli_query($koneksi, "SELECT * FROM kategori WHERE id_kategori='$id_edit'");
    $edit_data = mysqli_fetch_assoc($res);
}
?>

<style>
    :root {
        --main-green: #5b7245;
        --soft-bg: rgba(255, 255, 255, 0.7);
    }

    .kategori-wrapper { padding: 10px; overflow-x: hidden; }

    /* Glassmorphism Card for Form */
    .form-glass {
        background: var(--soft-bg);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 25px;
        padding: 20px; /* Diperkecil untuk mobile */
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }

    .modern-input {
        background: white;
        border: 1px solid #ddd;
        border-radius: 12px;
        padding: 12px 15px;
        flex: 1; /* Biar input memanjang */
        min-width: 250px; /* Mencegah terlalu sempit */
        transition: 0.3s;
    }

    /* Table Design Responsif */
    .table-glass-container {
        background: var(--soft-bg);
        backdrop-filter: blur(10px);
        border-radius: 25px;
        padding: 20px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow-x: auto; /* Penting untuk mobile */
    }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }

    .custom-table thead th {
        color: #777;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 12px;
        border: none;
    }

    .custom-table tbody tr {
        background: white;
        transition: 0.3s;
    }

    .custom-table td {
        padding: 12px;
        vertical-align: middle;
        border: none;
    }

    .custom-table tr td:first-child { border-radius: 15px 0 0 15px; }
    .custom-table tr td:last-child { border-radius: 0 15px 15px 0; }

    /* Action Buttons */
    .btn-modern {
        padding: 10px 18px;
        border-radius: 12px;
        font-weight: 600;
        transition: 0.3s;
        border: none;
        font-size: 0.85rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .btn-green { background: var(--main-green); color: white; }
    .btn-edit-tool { background: #fff9db; color: #f08c00; }
    .btn-delete-tool { background: #fff5f5; color: #fa5252; }
    
    .btn-green:hover { background: #4a5d38; transform: translateY(-2px); }
    .btn-edit-tool:hover { background: #f08c00; color: white; }
    .btn-delete-tool:hover { background: #fa5252; color: white; }

    /* MEDIA QUERIES UNTUK RESPONSIVE */
    @media (max-width: 768px) {
        .kategori-wrapper { padding: 5px; }
        
        /* Judul diperkecil di mobile */
        h2 { font-size: 1.5rem; }
        
        /* Form Stack Vertical di mobile */
        .flex-wrap {
            flex-direction: column;
        }
        
        .modern-input {
            width: 100%;
            min-width: 100%;
        }
        
        .btn-modern {
            width: 100%;
            padding: 12px;
        }

        /* Ukuran font tabel diperkecil agar tidak meluber */
        .custom-table td, .custom-table thead th {
            font-size: 0.85rem;
            padding: 10px;
        }
    }

    /* Alert Styling */
    .modern-alert {
        padding: 15px;
        border-radius: 15px;
        margin-bottom: 20px;
        font-weight: 500;
    }
    .success { background: #ebfbee; color: #2b8a3e; border-left: 5px solid #40c057; }
</style>

<div class="kategori-wrapper">
    <div class="mb-4">
        <h2 class="fw-bold" style="color: #333;"><i class="fas fa-tags text-success me-2"></i>Kelola Kategori</h2>
        <p class="text-muted small">Atur kategori aspirasi agar laporan siswa lebih terorganisir.</p>
    </div>

    <?php echo $pesan; ?>

    <div class="form-glass">
        <h5 class="fw-bold mb-3">
            <i class="fas <?php echo $edit_data ? 'fa-edit text-warning' : 'fa-plus-circle text-primary'; ?> me-2"></i>
            <?php echo $edit_data ? "Edit Kategori" : "Tambah Kategori"; ?>
        </h5>
        
        <form method="POST" action="dashboard.php?page=kategori">
            <?php if ($edit_data) : ?>
                <input type="hidden" name="id_kategori" value="<?php echo $edit_data['id_kategori']; ?>">
            <?php endif; ?>
            
            <div class="d-flex flex-wrap gap-3">
                <input type="text" name="ket_kategori" 
                       class="modern-input shadow-sm"
                       placeholder="Contoh: Sarana Prasarana, Guru, dll..." 
                       value="<?php echo $edit_data ? $edit_data['ket_kategori'] : ''; ?>" 
                       required>
                
                <button type="submit" name="<?php echo $edit_data ? 'update' : 'tambah'; ?>" class="btn-modern btn-green shadow-sm">
                    <i class="fas fa-save me-2"></i> <?php echo $edit_data ? "UPDATE" : "SIMPAN"; ?>
                </button>

                <?php if ($edit_data) : ?>
                    <a href="dashboard.php?page=kategori" class="btn-modern bg-white text-dark shadow-sm border">BATAL</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="table-glass-container">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th width="60" class="text-center">No</th>
                        <th>Kategori</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $sql = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY id_kategori DESC");
                    if (mysqli_num_rows($sql) > 0) {
                        while ($d = mysqli_fetch_assoc($sql)) {
                    ?>
                        <tr>
                            <td class="text-center fw-bold text-muted"><?php echo $no++; ?></td>
                            <td class="fw-bold" style="color: #444;"><?php echo $d['ket_kategori']; ?></td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="dashboard.php?page=kategori&edit=<?php echo $d['id_kategori']; ?>" class="btn-modern btn-edit-tool shadow-sm" title="Edit">
                                        <i class="fas fa-pencil-alt"></i>
                                    </a>
                                    <a href="dashboard.php?page=kategori&hapus=<?php echo $d['id_kategori']; ?>" 
                                       class="btn-modern btn-delete-tool shadow-sm" title="Hapus"
                                       onclick="return confirm('Hapus kategori ini? Laporan dengan kategori ini mungkin akan terpengaruh.')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center py-5 text-muted'>Belum ada kategori yang dibuat.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>