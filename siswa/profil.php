<?php
// 1. PROTEKSI: Cek apakah yang masuk benar-benar siswa
if ($_SESSION['role'] !== 'siswa') {
    echo "<div class='alert alert-danger'>Akses Ditolak!</div>";
    exit();
}

$nis = $_SESSION['nis'];
$pesan = "";

// 2. PROSES UPDATE DATA (Nama & Foto)
if (isset($_POST['update_profil'])) {
    $nama_baru = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $foto_nama = $_FILES['foto']['name'];
    
    $old_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT foto FROM siswa WHERE nis = '$nis'"));

    if ($foto_nama != "") {
        $ext = pathinfo($foto_nama, PATHINFO_EXTENSION);
        $nama_file_baru = "user_" . $nis . "_" . time() . "." . $ext;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], "assets/img/" . $nama_file_baru)) {
            if ($old_data['foto'] != "" && file_exists("assets/img/" . $old_data['foto'])) {
                unlink("assets/img/" . $old_data['foto']);
            }
            $query_update = "UPDATE siswa SET nama = '$nama_baru', foto = '$nama_file_baru' WHERE nis = '$nis'";
        }
    } else {
        $query_update = "UPDATE siswa SET nama = '$nama_baru' WHERE nis = '$nis'";
    }

    if (mysqli_query($koneksi, $query_update)) {
        $pesan = "<div class='modern-alert success animate__animated animate__fadeInDown'><i class='fas fa-check-circle me-2'></i> Profil berhasil diperbarui!</div>";
    } else {
        $pesan = "<div class='modern-alert danger animate__animated animate__shakeX'><i class='fas fa-exclamation-triangle me-2'></i> Gagal memperbarui profil.</div>";
    }
}

// 3. AMBIL DATA TERBARU
$query = mysqli_query($koneksi, "SELECT * FROM siswa WHERE nis = '$nis'");
$data = mysqli_fetch_assoc($query);
$foto_profil = ($data['foto'] != "") ? "assets/img/" . $data['foto'] : "https://ui-avatars.com/api/?name=" . urlencode($data['nama']) . "&background=5b7245&color=fff";
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<style>
    :root {
        --main-green: #5b7245;
        --soft-green: #84a366;
        --bg-light: #f4f7f2;
        --text-dark: #2d3436;
    }

    .profile-wrapper { 
        max-width: 1000px; 
        margin: 40px auto; 
        padding: 0 20px;
        font-family: 'Inter', sans-serif;
    }
    
    .profile-grid {
        display: grid;
        grid-template-columns: 1fr 1.8fr;
        gap: 25px;
        align-items: start;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        padding: 30px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.06);
        border: 1px solid rgba(255,255,255,0.7);
        transition: transform 0.3s ease;
    }

    /* Foto Profil */
    .avatar-section {
        text-align: center;
        position: relative;
    }

    .image-upload-wrapper {
        position: relative;
        width: 160px;
        height: 160px;
        margin: 0 auto 20px;
    }

    .profile-img-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 6px solid #fff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .upload-btn {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: linear-gradient(135deg, var(--main-green), var(--soft-green));
        color: white;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 4px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        transition: 0.3s;
    }

    .upload-btn:hover { transform: scale(1.1) rotate(15deg); }

    /* Form Styles */
    .form-group { margin-bottom: 20px; }
    
    .label-custom {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--main-green);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .modern-input {
        width: 100%;
        padding: 14px 18px;
        border-radius: 14px;
        border: 2px solid #edf2f7;
        background: #f8fafc;
        color: var(--text-dark);
        font-weight: 500;
        transition: 0.3s;
    }

    .modern-input:focus {
        background: #fff;
        border-color: var(--main-green);
        box-shadow: 0 0 0 4px rgba(91, 114, 69, 0.1);
        outline: none;
    }

    .modern-input[readonly] {
        background: #edf2f7;
        color: #718096;
        cursor: not-allowed;
    }

    /* Badge */
    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 16px;
        background: #e6fffa;
        color: #2d3748;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid #b2f5ea;
    }

    .dot {
        height: 8px;
        width: 8px;
        background-color: #38a169;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }

    /* Button */
    .btn-save {
        background: linear-gradient(135deg, var(--main-green), #4a5d38);
        color: white;
        border: none;
        padding: 16px;
        border-radius: 16px;
        font-weight: 700;
        width: 100%;
        font-size: 1rem;
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(91, 114, 69, 0.3);
    }

    /* Alert */
    .modern-alert {
        padding: 16px;
        border-radius: 16px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .danger { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

    /* Responsive */
    @media (max-width: 992px) {
        .profile-grid { grid-template-columns: 1fr; }
        .profile-wrapper { margin: 20px auto; }
    }

    @media (max-width: 480px) {
        .glass-card { padding: 20px; }
        .image-upload-wrapper { width: 130px; height: 130px; }
    }
</style>

<div class="profile-wrapper">
    <div class="d-flex align-items-center mb-4">
        <div style="width: 50px; height: 5px; background: var(--main-green); border-radius: 10px; margin-right: 15px;"></div>
        <h2 class="fw-bold m-0" style="color: var(--text-dark);">Edit Profil</h2>
    </div>
    
    <?php echo $pesan; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="profile-grid">
            <div class="glass-card animate__animated animate__fadeInLeft">
                <div class="avatar-section">
                    <div class="image-upload-wrapper">
                        <img src="<?php echo $foto_profil; ?>" id="previewImg" class="profile-img-preview">
                        <label for="fotoInput" class="upload-btn" title="Ganti Foto">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" name="foto" id="fotoInput" hidden accept="image/*">
                    </div>
                    <h4 class="fw-bold mb-1" style="color: var(--text-dark);"><?php echo $data['nama']; ?></h4>
                    <p class="text-muted small mb-4">NIS: <?php echo $data['nis']; ?></p>
                    
                    <div class="status-pill mb-2">
                        <span class="dot"></span> Akun Terverifikasi
                    </div>
                </div>
            </div>

            <div class="glass-card animate__animated animate__fadeInRight">
                <h5 class="fw-bold mb-4" style="color: var(--text-dark);">Informasi Akun</h5>
                
                <div class="form-group">
                    <label class="label-custom">Nomor Induk Siswa</label>
                    <input type="text" class="modern-input" value="<?php echo $data['nis']; ?>" readonly>
                </div>

                <div class="form-group">
                    <label class="label-custom">Nama Lengkap</label>
                    <input type="text" name="nama" class="modern-input" value="<?php echo $data['nama']; ?>" placeholder="Masukkan nama lengkap..." required>
                </div>

                <div class="form-group">
                    <label class="label-custom">Kelas / Jurusan</label>
                    <input type="text" class="modern-input" value="<?php echo $data['kelas']; ?>" readonly>
                </div>

                <button type="submit" name="update_profil" class="btn-save">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    // Preview Foto dengan animasi kecil
    document.getElementById('fotoInput').onchange = evt => {
        const [file] = document.getElementById('fotoInput').files;
        if (file) {
            const preview = document.getElementById('previewImg');
            preview.style.opacity = '0';
            setTimeout(() => {
                preview.src = URL.createObjectURL(file);
                preview.style.opacity = '1';
            }, 300);
        }
    }

    // Auto-hide alert setelah 5 detik
    setTimeout(() => {
        const alert = document.querySelector('.modern-alert');
        if(alert) {
            alert.classList.replace('animate__fadeInDown', 'animate__fadeOutUp');
            setTimeout(() => alert.remove(), 1000);
        }
    }, 5000);
</script>