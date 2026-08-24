<?php
include 'config/database.php'; // koneksi database
require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/phpmailer/src/SMTP.php';
require 'vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $subjek = $_POST['subjek'];
    $pesan = $_POST['pesan'];

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO kontak_admin (nama, email, subjek, pesan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $email, $subjek, $pesan);
    $stmt->execute();

    // Kirim email ke admin
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "email_admin@gmail.com"; // ganti dengan email admin
        $mail->Password = "password_email"; // password aplikasi
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;

        $mail->setFrom($email, $nama);
        $mail->addAddress("email_admin@gmail.com"); // email tujuan admin
        $mail->Subject = $subjek;
        $mail->Body = "Pesan dari: $nama\nEmail: $email\n\n$pesan";

        $mail->send();
        echo "<script>alert('Pesan berhasil dikirim!'); window.location.href='index.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Pesan gagal dikirim!'); window.location.href='index.php';</script>";
    }
}
?>