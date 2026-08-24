<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'];

    if ($formType === 'permohonan') {
        $nama = $_POST['nama'];
        $no_ktp = $_POST['no_ktp'];
        $jenis_pemohon = $_POST['jenis_pemohon'];
        $kontak = $_POST['kontak'];
        $alamat = $_POST['alamat'];
        $rincian = $_POST['rincian'];
        $tujuan = $_POST['tujuan'];
        $salinan = $_POST['salinan'];

        // Simpan ke database
        $stmt = $conn->prepare("INSERT INTO permohonan_informasi 
            (nama, no_ktp, jenis_pemohon, kontak, alamat, rincian, tujuan, cara_salinan) 
            VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssss", $nama, $no_ktp, $jenis_pemohon, $kontak, $alamat, $rincian, $tujuan, $salinan);
        $stmt->execute();

        // Kirim email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'emailkamu@gmail.com';
            $mail->Password = 'passwordapp';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('emailkamu@gmail.com', 'PPID Bondowoso');
            $mail->addAddress('ppidbondowoso@gmail.com', 'Admin PPID');

            $mail->isHTML(true);
            $mail->Subject = "Permohonan Informasi Baru dari $nama";
            $mail->Body = "
                <h3>Permohonan Informasi Baru</h3>
                <p><b>Nama:</b> $nama</p>
                <p><b>No KTP:</b> $no_ktp</p>
                <p><b>Jenis Pemohon:</b> $jenis_pemohon</p>
                <p><b>Kontak:</b> $kontak</p>
                <p><b>Alamat:</b> $alamat</p>
                <p><b>Rincian:</b> $rincian</p>
                <p><b>Tujuan:</b> $tujuan</p>
                <p><b>Cara Salinan:</b> $salinan</p>
            ";
            $mail->send();
        } catch (Exception $e) {
            error_log("Email gagal: {$mail->ErrorInfo}");
        }

    } elseif ($formType === 'keberatan') {
        $nama_pemohon = $_POST['nama_pemohon'];
        $no_reg = $_POST['no_registrasi'];
        $alasan = $_POST['alasan'];
        $kontak = $_POST['kontak'];

        // Upload lampiran
        $lampiranPath = null;
        if (!empty($_FILES['lampiran']['name'])) {
            $lampiranPath = 'uploads/' . basename($_FILES['lampiran']['name']);
            move_uploaded_file($_FILES['lampiran']['tmp_name'], $lampiranPath);
        }

        // Simpan ke database
        $stmt = $conn->prepare("INSERT INTO pengajuan_keberatan 
            (nama_pemohon, no_registrasi, alasan, kontak, lampiran) 
            VALUES (?,?,?,?,?)");
        $stmt->bind_param("sssss", $nama_pemohon, $no_reg, $alasan, $kontak, $lampiranPath);
        $stmt->execute();

        // Kirim email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'emailkamu@gmail.com';
            $mail->Password = 'passwordapp';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('emailkamu@gmail.com', 'PPID Bondowoso');
            $mail->addAddress('ppidbondowoso@gmail.com', 'Admin PPID');

            $mail->isHTML(true);
            $mail->Subject = "Pengajuan Keberatan Baru dari $nama_pemohon";
            $mail->Body = "
                <h3>Pengajuan Keberatan Baru</h3>
                <p><b>Nama:</b> $nama_pemohon</p>
                <p><b>No Registrasi:</b> $no_reg</p>
                <p><b>Alasan:</b> $alasan</p>
                <p><b>Kontak:</b> $kontak</p>
            ";
            if ($lampiranPath) {
                $mail->addAttachment($lampiranPath);
            }
            $mail->send();
        } catch (Exception $e) {
            error_log("Email gagal: {$mail->ErrorInfo}");
        }
    }

    header("Location: sukses.php");
    exit;
}
