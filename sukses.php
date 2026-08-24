<?php
// proses simpan database & kirim email
if ($mail->send()) {
    echo "<script>alert('Data berhasil dikirim!'); window.location.href='informasi.php';</script>";
} else {
    echo "<script>alert('Terjadi kesalahan, data gagal dikirim!'); window.location.href='informasi.php';</script>";
}
?>