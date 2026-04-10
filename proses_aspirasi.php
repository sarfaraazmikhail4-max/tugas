<?php
include 'koneksi.php';

if (isset($_POST['kirim'])) {
    $tgl = date('Y-m-d');
    $nis = $_SESSION['nis']; // Mengambil NIS dari session login
    $isi = input($_POST['isi_laporan']);
    $status = '0'; // Default status baru adalah 0 (belum diproses)

    // Pengaturan Foto
    $foto     = $_FILES['foto']['name'];
    $tmp      = $_FILES['foto']['tmp_name'];
    
    if ($foto != "") {
        // Jika ada foto, ganti nama fotonya agar tidak bentrok (pakai timestamp)
        $nama_foto_baru = date('dmYHis') . "_" . $foto;
        $path = "assets/img/" . $nama_foto_baru; // Pastikan folder assets/img sudah ada
        
        // Pindahkan file ke folder
        move_uploaded_file($tmp, $path);
    } else {
        $nama_foto_baru = ""; // Jika tidak upload foto
    }

    // Insert ke Database
    $query = mysqli_query($koneksi, "INSERT INTO pengaduan (tgl_pengaduan, nis, isi_laporan, foto, status) 
                                     VALUES ('$tgl', '$nis', '$isi', '$nama_foto_baru', '$status')");

    if ($query) {
        header("Location: dashboard.php?page=riwayat&status=sukses");
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>