<?php
// Pastikan session sudah dimulai di file utama (index.php atau dashboard.php)
if (isset($_POST['kirim'])) {
    session_start(); // Pastikan session aktif
    $nis = $_SESSION['nis'] ?? ''; // Ambil NIS dari session

    // JIKA NIS KOSONG, JANGAN LANJUT!
    if (empty($nis)) {
        echo "<script>alert('Gagal: Sesi Login Hilang! Silakan Logout lalu Login kembali.'); window.location='index.php';</script>";
        exit;
    }

    $id_kat = $_POST['id_kategori'];
    $lokasi = mysqli_real_escape_string($koneksi, $_POST['lokasi']);
    $ket    = mysqli_real_escape_string($koneksi, $_POST['ket']);
    
    // Logika Foto (Tetap sama)
    $foto = $_FILES['foto']['name'];
    $foto_nama = "";
    if ($foto != "") {
        $ext = pathinfo($foto, PATHINFO_EXTENSION);
        $foto_nama = date('dmYHis').".$ext";
        move_uploaded_file($_FILES['foto']['tmp_name'], "assets/img/".$foto_nama);
    }

    // Eksekusi Query
    $q = mysqli_query($koneksi, "INSERT INTO input_aspirasi (nis, id_kategori, tgl_lapor, lokasi, ket, foto) 
         VALUES ('$nis', '$id_kat', CURDATE(), '$lokasi', '$ket', '$foto_nama')");
    
    if($q) {
        echo "<script>alert('Berhasil terkirim!'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('DB Error: " . mysqli_error($koneksi) . "');</script>";
    }
}
?>

<div class="modal fade" id="modalLaporan" tabindex="-1" aria-labelledby="modalLaporanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 25px; border: none; overflow: hidden; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);">
            
            <div class="modal-header" style="background: linear-gradient(45deg, #5b7245, #84a366); color: white; border: none; padding: 25px;">
                <h5 class="modal-title fw-bold" id="modalLaporanLabel">
                    <i class="fas fa-edit me-2"></i> Sampaikan Aspirasi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-600">Kategori Masalah</label>
                        <select name="id_kategori" class="form-select border-0 bg-light shadow-none" style="border-radius: 12px; padding: 12px;" required>
                            <option value="" disabled selected>Pilih Kategori...</option>
                            <?php
                            $kat = mysqli_query($koneksi, "SELECT * FROM kategori");
                            while($k = mysqli_fetch_assoc($kat)) {
                                echo "<option value='".$k['id_kategori']."'>".$k['ket_kategori']."</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Lokasi Kejadian</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light" style="border-radius: 12px 0 0 12px;"><i class="fas fa-map-marker-alt text-danger"></i></span>
                            <input type="text" name="lokasi" class="form-control border-0 bg-light shadow-none" style="border-radius: 0 12px 12px 0; padding: 12px;" placeholder="Contoh: Kantin, Kelas 10, Toilet" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-600">Deskripsi Laporan</label>
                        <textarea name="ket" class="form-control border-0 bg-light shadow-none" style="border-radius: 12px; padding: 12px;" rows="4" placeholder="Ceritakan detail masalah atau saran Anda..." required></textarea>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-600">Upload Bukti Foto <small class="text-muted">(Opsional)</small></label>
                        <input type="file" name="foto" id="fotoInput" class="form-control border-0 bg-light shadow-none" style="border-radius: 12px; padding: 12px;" accept="image/*">
                        
                        <div id="previewArea" class="mt-3 text-center d-none">
                            <p class="small text-muted mb-2 text-start fw-600">Preview Foto:</p>
                            <div class="position-relative d-inline-block">
                                <img id="imagePreview" src="#" alt="Preview" style="max-height: 200px; border-radius: 15px; border: 2px solid #5b7245; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                <button type="button" id="btnHapusFoto" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle shadow">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <div class="form-text mt-2"><i class="fas fa-info-circle me-1"></i> Format: JPG, PNG (Max 2MB)</div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="kirim" class="btn text-white rounded-pill px-4 fw-bold shadow-sm" style="background: #5b7245;">
                        <i class="fas fa-paper-plane me-2"></i>Kirim Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .fw-600 { font-weight: 600; color: #444; margin-bottom: 8px; font-size: 0.9rem; }
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 4px rgba(91, 114, 69, 0.1) !important;
        border: 1px solid #5b7245 !important;
    }
</style>

<script>
document.getElementById('fotoInput').addEventListener('change', function() {
    const file = this.files[0];
    const previewArea = document.getElementById('previewArea');
    const imagePreview = document.getElementById('imagePreview');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.src = e.target.result;
            previewArea.classList.remove('d-none');
        }
        reader.readAsDataURL(file);
    } else {
        previewArea.classList.add('d-none');
    }
});

document.getElementById('btnHapusFoto').addEventListener('click', function() {
    document.getElementById('fotoInput').value = "";
    document.getElementById('previewArea').classList.add('d-none');
    document.getElementById('imagePreview').src = "#";
});
</script>