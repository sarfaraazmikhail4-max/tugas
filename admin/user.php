<?php
// Proteksi Admin
if ($_SESSION['role'] != 'admin') {
    echo "<script>window.location.href='index.php';</script>";
    exit();
}

$pesan = "";

// 1. PROSES TAMBAH (CREATE)
if (isset($_POST['tambah'])) {
    $nis     = input($_POST['nis']);
    $nama    = input($_POST['nama']);
    $kelas   = input($_POST['kelas']);
    $pasword = input($_POST['pasword']);

    $cek = mysqli_query($koneksi, "SELECT * FROM siswa WHERE nis='$nis'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = "<div class='modern-alert danger shadow-sm animate__animated animate__shakeX'><i class='fas fa-times-circle me-2'></i> Gagal! NIS sudah terdaftar.</div>";
    } else {
        $query = mysqli_query($koneksi, "INSERT INTO siswa (nis, nama, kelas, pasword) VALUES ('$nis', '$nama', '$kelas', '$pasword')");
        if ($query) $pesan = "<div class='modern-alert success shadow-sm animate__animated animate__fadeIn'><i class='fas fa-check-circle me-2'></i> User berhasil ditambahkan!</div>";
    }
}

// 2. PROSES UPDATE (UPDATE)
if (isset($_POST['update'])) {
    $nis_lama = $_POST['nis_lama'];
    $nis_baru = input($_POST['nis']);
    $nama     = input($_POST['nama']);
    $kelas    = input($_POST['kelas']);
    $pasword  = input($_POST['pasword']);

    $query = mysqli_query($koneksi, "UPDATE siswa SET nis='$nis_baru', nama='$nama', kelas='$kelas', pasword='$pasword' WHERE nis='$nis_lama'");
    if ($query) $pesan = "<div class='modern-alert success shadow-sm animate__animated animate__fadeIn'><i class='fas fa-sync me-2'></i> Data user berhasil diupdate!</div>";
}

// 3. PROSES HAPUS (DELETE)
if (isset($_GET['hapus'])) {
    $nis = $_GET['hapus'];
    $query = mysqli_query($koneksi, "DELETE FROM siswa WHERE nis='$nis'");
    if ($query) $pesan = "<div class='modern-alert success shadow-sm animate__animated animate__fadeIn'><i class='fas fa-trash-alt me-2'></i> User berhasil dihapus!</div>";
}

// AMBIL DATA UNTUK EDIT
$edit = null;
if (isset($_GET['edit'])) {
    $nis_edit = $_GET['edit'];
    $sql_edit = mysqli_query($koneksi, "SELECT * FROM siswa WHERE nis='$nis_edit'");
    $edit = mysqli_fetch_assoc($sql_edit);
}
?>

<style>
    :root {
        --main-green: #5b7245;
        --soft-bg: rgba(255, 255, 255, 0.7);
    }

    .user-container { padding: 10px; overflow-x: hidden; }

    /* Bento Box Form */
    .form-glass {
        background: var(--soft-bg);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 25px;
        padding: 25px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        margin-bottom: 30px;
    }

    /* Grid Input yang Pintar */
    .modern-input-group {
        display: grid;
        grid-template-columns: repeat(2, 1fr); /* Default 2 kolom */
        gap: 15px;
        margin-bottom: 20px;
    }

    .form-control-modern {
        background: white;
        border: 1px solid #ddd;
        border-radius: 12px;
        padding: 12px 15px;
        width: 100%;
        transition: 0.3s;
        font-size: 0.9rem;
    }

    .form-control-modern:focus {
        border-color: var(--main-green);
        box-shadow: 0 0 0 4px rgba(91, 114, 69, 0.1);
        outline: none;
    }

    /* Table Styling */
    .table-glass-wrapper {
        background: var(--soft-bg);
        backdrop-filter: blur(10px);
        border-radius: 25px;
        padding: 20px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow-x: auto; /* Untuk scroll tabel di HP */
    }

    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
        min-width: 600px; /* Supaya tabel punya ruang di layar kecil */
    }

    .custom-table thead th {
        color: #777;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 15px;
        border: none;
    }

    .custom-table tbody tr {
        background: white;
        transition: 0.3s;
    }

    .custom-table td {
        padding: 12px 15px;
        vertical-align: middle;
        border: none;
        font-size: 0.9rem;
    }

    .custom-table tr td:first-child { border-radius: 15px 0 0 15px; }
    .custom-table tr td:last-child { border-radius: 0 15px 15px 0; }

    /* Buttons */
    .btn-modern {
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 600;
        transition: 0.3s;
        border: none;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-green { background: var(--main-green); color: white; width: auto; }
    .btn-green:hover { background: #4a5d38; transform: translateY(-2px); }
    
    .btn-edit-sm { background: #fff9db; color: #f08c00; margin-right: 5px; padding: 10px 14px; }
    .btn-delete-sm { background: #fff5f5; color: #fa5252; padding: 10px 14px; }

    /* Responsivitas HP */
    @media (max-width: 768px) {
        .modern-input-group {
            grid-template-columns: 1fr; /* 1 kolom di HP */
        }
        
        .btn-modern {
            width: 100%; /* Tombol jadi lebar penuh */
        }

        .user-container h2 { font-size: 1.5rem; }
        
        .form-glass { padding: 20px; }
    }

    .modern-alert { padding: 15px; border-radius: 15px; margin-bottom: 20px; font-weight: 500; }
    .success { background: #ebfbee; color: #2b8a3e; border-left: 5px solid #40c057; }
    .danger { background: #fff5f5; color: #c92a2a; border-left: 5px solid #fa5252; }
</style>

<div class="user-container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
        <div>
            <h2 class="fw-bold m-0" style="color: #333;"><i class="fas fa-users-cog text-success me-2"></i>Database Siswa</h2>
            <p class="text-muted small m-0">Manajemen kredensial login dan data profil siswa.</p>
        </div>
        <div class="badge bg-white text-dark shadow-sm p-2 px-3 border" style="border-radius: 10px; width: fit-content;">
            <i class="fas fa-user-shield text-success me-1"></i> Mode Admin
        </div>
    </div>

    <?php echo $pesan; ?>

    <div class="form-glass">
        <h5 class="fw-bold mb-4">
            <i class="fas <?php echo $edit ? 'fa-user-edit text-warning' : 'fa-user-plus text-primary'; ?> me-2"></i>
            <?php echo $edit ? "Perbarui Data Siswa" : "Registrasi Siswa Baru"; ?>
        </h5>
        
        <form method="POST">
            <?php if ($edit): ?>
                <input type="hidden" name="nis_lama" value="<?php echo $edit['nis']; ?>">
            <?php endif; ?>

            <div class="modern-input-group">
                <div class="form-item">
                    <label class="small fw-bold text-muted mb-1 d-block"><i class="fas fa-id-card me-1"></i> NIS</label>
                    <input type="number" name="nis" class="form-control-modern" placeholder="Masukkan NIS" value="<?php echo $edit ? $edit['nis'] : ''; ?>" required>
                </div>
                <div class="form-item">
                    <label class="small fw-bold text-muted mb-1 d-block"><i class="fas fa-user me-1"></i> Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control-modern" placeholder="Nama Lengkap" value="<?php echo $edit ? $edit['nama'] : ''; ?>" required>
                </div>
                <div class="form-item">
                    <label class="small fw-bold text-muted mb-1 d-block"><i class="fas fa-graduation-cap me-1"></i> Kelas</label>
                    <input type="text" name="kelas" class="form-control-modern" placeholder="Contoh: XII RPL" value="<?php echo $edit ? $edit['kelas'] : ''; ?>" required>
                </div>
                <div class="form-item">
                    <label class="small fw-bold text-muted mb-1 d-block"><i class="fas fa-lock me-1"></i> Password</label>
                    <input type="text" name="pasword" class="form-control-modern" placeholder="Password" value="<?php echo $edit ? $edit['pasword'] : ''; ?>" required>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button type="submit" name="<?php echo $edit ? 'update' : 'tambah'; ?>" class="btn-modern btn-green shadow-sm">
                    <i class="fas fa-save me-2"></i> <?php echo $edit ? "UPDATE DATA" : "SIMPAN USER"; ?>
                </button>
                <?php if ($edit): ?>
                    <a href="dashboard.php?page=user" class="btn-modern bg-white border text-dark text-decoration-none shadow-sm">BATAL</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="table-glass-wrapper">
        <table class="custom-table">
            <thead>
                <tr>
                    <th width="120">NIS</th>
                    <th>Nama Siswa</th>
                    <th width="150">Kelas</th>
                    <th width="150">Password</th>
                    <th width="120" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = mysqli_query($koneksi, "SELECT * FROM siswa ORDER BY nama ASC");
                if (mysqli_num_rows($sql) == 0) {
                    echo "<tr><td colspan='5' class='text-center text-muted py-5'>Belum ada data siswa.</td></tr>";
                }
                while ($d = mysqli_fetch_assoc($sql)) {
                ?>
                <tr>
                    <td class="fw-bold text-success"><?php echo $d['nis']; ?></td>
                    <td class="fw-bold" style="color:#444;"><?php echo $d['nama']; ?></td>
                    <td>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                             <?php echo $d['kelas']; ?>
                        </span>
                    </td>
                    <td><code class="bg-light p-1 px-2 rounded"><?php echo $d['pasword']; ?></code></td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center">
                            <a href="dashboard.php?page=user&edit=<?php echo $d['nis']; ?>" class="btn-modern btn-edit-sm shadow-sm" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="dashboard.php?page=user&hapus=<?php echo $d['nis']; ?>" class="btn-modern btn-delete-sm shadow-sm" title="Hapus" onclick="return confirm('Hapus siswa ini?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>