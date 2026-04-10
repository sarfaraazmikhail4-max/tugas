<?php
include '../koneksi.php'; 

$sql = "SELECT i.*, s.nama, k.ket_kategori, a.status, a.feedback 
        FROM input_aspirasi i
        LEFT JOIN siswa s ON i.nis = s.nis
        LEFT JOIN kategori k ON i.id_kategori = k.id_kategori
        LEFT JOIN aspirasi a ON i.id_pelaporan = a.id_pelaporan
        ORDER BY i.tgl_lapor DESC";
$query = mysqli_query($koneksi, $sql);

// Kita akan menghitung total keseluruhan di akhir, 
// tapi variabel ini untuk menghitung kumulatif per halaman
$total = 0; $ditolak = 0; $proses = 0; $diterima = 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Aspirasi</title>
    <style>
        body { font-family: sans-serif; padding: 20px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px double #000; padding-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 11px; }
        th { background-color: #f2f2f2 !important; -webkit-print-color-adjust: exact; }
        
        .page-break { display: block; page-break-before: always; }
        
        .rekap-box { 
            width: 250px; 
            margin-left: auto; /* Biar rekap ke arah kanan bawah tabel */
            margin-bottom: 30px;
        }
        .rekap-box table { font-size: 10px; }
        .rekap-box h5 { margin: 5px 0; font-size: 11px; }

        .footer { text-align: right; font-size: 9px; margin-top: 10px; }

        @media print {
            @page { margin: 1cm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <?php 
    $no = 1;
    $counter = 0; 
    $limit = 10;  
    $first_page = true;
    
    // Ambil semua data dulu ke array supaya gampang deteksi baris terakhir
    $all_data = [];
    while($row = mysqli_fetch_assoc($query)) {
        $all_data[] = $row;
    }
    $total_rows = count($all_data);

    foreach($all_data as $index => $d) : 
        $status_sekarang = $d['status'] ?? 'Menunggu';
        
        // Hitung Rekap (Kumulatif)
        $total++;
        if($status_sekarang == 'Ditolak') $ditolak++;
        elseif($status_sekarang == 'Proses') $proses++;
        elseif($status_sekarang == 'Selesai' || $status_sekarang == 'Diterima') $diterima++;

        // JIKA AWAL HALAMAN
        if ($counter % $limit == 0) {
            if (!$first_page) {
                echo "<div class='page-break'></div>";
            }
            ?>
            <div class="header">
                <h2 style="margin:0; font-size: 18px;">LAPORAN ASPIRASI SISWA</h2>
                <p style="margin:5px; font-size: 12px;">Halaman: <?php echo floor($counter/$limit) + 1; ?> | Cetak: <?php echo date('d/m/Y'); ?></p>
            </div>
            <table>
                <thead>
                    <tr>
                        <th width="30">No</th>
                        <th width="70">Tanggal</th>
                        <th>Nama Siswa</th>
                        <th width="100">Kategori</th>
                        <th width="100">Lokasi</th>
                        <th>Isi Laporan</th>
                        <th width="70">Status</th>
                    </tr>
                </thead>
                <tbody>
            <?php 
            $first_page = false;
        } 
        ?>

            <tr>
                <td style="text-align:center;"><?php echo $no++; ?></td>
                <td><?php echo date('d/m/Y', strtotime($d['tgl_lapor'])); ?></td>
                <td><?php echo $d['nama']; ?></td>
                <td><?php echo $d['ket_kategori']; ?></td>
                <td><?php echo $d['lokasi']; ?></td>
                <td><?php echo $d['ket']; ?></td>
                <td><?php echo $status_sekarang; ?></td>
            </tr>

        <?php 
        $counter++;

        // JIKA AKHIR HALAMAN (Sudah 10 baris) ATAU DATA TERAKHIR
        if ($counter % $limit == 0 || $counter == $total_rows) {
            ?>
                </tbody>
            </table>

            <div class="rekap-box">
                <h5>Rekapitulasi s/d Halaman Ini:</h5>
                <table>
                    <tr><td>Total Aspirasi</td><td align="center"><?php echo $total; ?></td></tr>
                    <tr><td>Ditolak</td><td align="center"><?php echo $ditolak; ?></td></tr>
                    <tr><td>Proses</td><td align="center"><?php echo $proses; ?></td></tr>
                    <tr><td>Selesai</td><td align="center"><?php echo $diterima; ?></td></tr>
                </table>
            </div>

            <div class="footer">
                <p><i>Sistem E-Aspirasi - Dicetak pada <?php echo date('d/m/Y H:i'); ?></i></p>
            </div>
            <?php
        }
    endforeach; 
    ?>

    <script>
        window.onload = function() {
            setTimeout(function() { window.print(); }, 700);
        };
        window.onafterprint = function() { window.close(); };
    </script>
</body>
</html>