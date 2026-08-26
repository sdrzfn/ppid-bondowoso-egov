<?php
session_start();
require 'config/database.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function generateTicketNumber()
{
    return 'PPID-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = $_POST['step'] ?? '1';
    $formType = $_POST['form_type'] ?? 'permohonan';
    
    // Store data in session for multi-step
    if ($step == '1') {
        $_SESSION['ticket_data'] = [
            'form_type' => $formType,
            'kategori_pemohon' => $_POST['kategori_pemohon'] ?? 'perorangan',
            'nik' => $_POST['nik'],
            'nama_lengkap' => $_POST['nama_lengkap'],
            'jenis_identitas' => $_POST['jenis_identitas'] ?? 'ktp',
            'no_identitas' => $_POST['no_identitas'] ?? null,
            'email' => $_POST['email'],
            'no_whatsapp' => $_POST['no_whatsapp'],
            'alamat' => $_POST['alamat'],
        ];
        
        // Handle file upload
        if (!empty($_FILES['lampiran_ktp']['name'])) {
            $uploadDir = 'uploads/tickets/';
            if (!file_exists($uploadDir))
                mkdir($uploadDir, 0755, true);
            $fileName = time() . '_' . basename($_FILES['lampiran_ktp']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['lampiran_ktp']['tmp_name'], $targetPath)) {
                $_SESSION['ticket_data']['lampiran_ktp'] = $targetPath;
            }
        }
        
        echo json_encode(['success' => true, 'next_step' => 2]);
        exit;
    }
    
    if ($step == '2') {
        $_SESSION['ticket_data']['rincian_informasi'] = $_POST['rincian_informasi'];
        $_SESSION['ticket_data']['tujuan_penggunaan'] = $_POST['tujuan_penggunaan'];
        $_SESSION['ticket_data']['format_salinan'] = $_POST['format_salinan'] ?? 'softcopy';
        $_SESSION['ticket_data']['cara_penyampaian'] = $_POST['cara_penyampaian'] ?? 'email';
        
        echo json_encode(['success' => true, 'next_step' => 3]);
        exit;
    }
    
    // Step 3 untuk permohonan, atau step 4 untuk keberatan
    if (($step == '3' && $formType == 'permohonan') || ($step == '4' && $formType == 'keberatan')) {
        $data = $_SESSION['ticket_data'];
        $ticketNumber = generateTicketNumber();
        
        if ($formType == 'permohonan') {
            // Insert permohonan
            $stmt = $conn->prepare("INSERT INTO tickets (
                ticket_number, form_type, kategori_pemohon, nik, nama_lengkap,
                jenis_identitas, no_identitas, email, no_whatsapp, alamat,
                lampiran_ktp, rincian_informasi, tujuan_penggunaan,
                format_salinan, cara_penyampaian, ip_address, user_agent
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            // bind_param butuh variabel murni (by-reference), bukan ekspresi ??
            $lampiranKtp = $data['lampiran_ktp'] ?? null;

            $stmt->bind_param(
                "sssssssssssssssss",
                $ticketNumber,
                $data['form_type'],
                $data['kategori_pemohon'],
                $data['nik'],
                $data['nama_lengkap'],
                $data['jenis_identitas'],
                $data['no_identitas'],
                $data['email'],
                $data['no_whatsapp'],
                $data['alamat'],
                $lampiranKtp,
                $data['rincian_informasi'],
                $data['tujuan_penggunaan'],
                $data['format_salinan'],
                $data['cara_penyampaian'],
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['HTTP_USER_AGENT']
            );
        } else {
            // Insert keberatan
            $alasanKeberatan = isset($_POST['alasan']) ? implode(', ', $_POST['alasan']) : '';
            
            // Handle bukti upload
            $lampiranBukti = null;
            if (!empty($_FILES['lampiran_bukti']['name'])) {
                $uploadDir = 'uploads/tickets/';
                if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);
                $fileName = time() . '_bukti_' . basename($_FILES['lampiran_bukti']['name']);
                $targetPath = $uploadDir . $fileName;
                if (move_uploaded_file($_FILES['lampiran_bukti']['tmp_name'], $targetPath)) {
                    $lampiranBukti = $targetPath;
                }
            }
            
            $stmt = $conn->prepare("INSERT INTO tickets (
                ticket_number, form_type, nama_lengkap, email, no_whatsapp, alamat,
                reference_ticket_number, alasan_keberatan, penjelasan_keberatan, lampiran_bukti,
                ip_address, user_agent
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->bind_param(
                "ssssssssssss",
                $ticketNumber,
                $data['form_type'],
                $data['nama_lengkap'],
                $data['email'],
                $data['no_whatsapp'],
                $data['alamat'],
                $_POST['reference_ticket_number'] ?? null,
                $alasanKeberatan,
                $_POST['penjelasan_keberatan'] ?? null,
                $lampiranBukti,
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['HTTP_USER_AGENT']
            );
        }
        
        if ($stmt->execute()) {
            $ticketId = $conn->insert_id;

            // Catat riwayat status awal tiket (dibuat defensif: kalau gagal, jangan sampai
            // merusak response JSON utama)
            $historyStmt = $conn->prepare("INSERT INTO ticket_history (
                ticket_id, status_lama, status_baru, catatan, updated_by
            ) VALUES (?, ?, ?, ?, ?)");
            if ($historyStmt) {
                $historyStatusLama = null; // tiket baru dibuat, belum ada status sebelumnya
                $historyStatusBaru = 'Menunggu Diproses';
                $historyCatatan = 'Tiket dibuat oleh pemohon.';
                $historyUpdatedBy = 'Sistem';
                $historyStmt->bind_param(
                    "issss",
                    $ticketId,
                    $historyStatusLama,
                    $historyStatusBaru,
                    $historyCatatan,
                    $historyUpdatedBy
                );
                if (!$historyStmt->execute()) {
                    error_log("Insert ticket_history gagal: " . $historyStmt->error);
                }
            } else {
                error_log("Prepare ticket_history gagal: " . $conn->error);
            }


            // Send email notification
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
                $mail->addAddress($data['email'], $data['nama_lengkap']);
                $mail->addAddress('ppidbondowoso@gmail.com', 'Admin PPID');
                
                $mail->isHTML(true);
                if ($formType == 'keberatan') {
                    $mail->Subject = "Konfirmasi Pengajuan Keberatan - $ticketNumber";
                    $mail->Body = "
                        <h3>Konfirmasi Pengajuan Keberatan</h3>
                        <p><b>Nomor Tiket:</b> $ticketNumber</p>
                        <p><b>Nama Pemohon:</b> {$data['nama_lengkap']}</p>
                        <p><b>Nomor Referensi:</b> {$_POST['reference_ticket_number']}</p>
                        <p><b>Status:</b> Menunggu Diproses</p>
                        <p><small>Simpan nomor tiket ini untuk tracking pengajuan keberatan Anda.</small></p>
                    ";
                } else {
                    $mail->Subject = "Konfirmasi Permohonan Informasi - $ticketNumber";
                    $mail->Body = "
                        <h3>Konfirmasi Permohonan Informasi</h3>
                        <p><b>Nomor Tiket:</b> $ticketNumber</p>
                        <p><b>Nama:</b> {$data['nama_lengkap']}</p>
                        <p><b>NIK:</b> {$data['nik']}</p>
                        <p><b>Rincian:</b> {$data['rincian_informasi']}</p>
                        <p><b>Status:</b> Menunggu Diproses</p>
                        <p><small>Simpan nomor tiket ini untuk tracking permohonan Anda.</small></p>
                    ";
                }
                $mail->send();
            } catch (Exception $e) {
                error_log("Email gagal: {$mail->ErrorInfo}");
            }
            
            unset($_SESSION['ticket_data']);
            
            $displayNumber = $ticketId . '-' . $ticketNumber;
            
            echo json_encode([
                'success' => true,
                'ticket_number' => $displayNumber,
                'redirect' => 'ticket.php?number=' . $displayNumber . '&type=' . $data['form_type']
            ]);
            exit;
        } else {
            // Jangan biarkan gagal insert diam-diam - selalu balas JSON
            error_log("Insert tiket gagal: " . $stmt->error);
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data tiket.']);
            exit;
        }
    }
}
?>