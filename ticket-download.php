<?php
require 'config/database.php';

$number = $_GET['number'] ?? '';

if (empty($number)) {
    http_response_code(400);
    die('Parameter nomor tiket tidak valid.');
}

$number = $conn->real_escape_string($number);
$ticket = $conn->query("SELECT * FROM tickets WHERE ticket_number = '$number' OR id = '$number' LIMIT 1")->fetch_assoc();

if (!$ticket || empty($ticket['lampiran_balasan'])) {
    http_response_code(404);
    die('Berkas tidak ditemukan. Pastikan tiket sudah diproses.');
}

// Validasi status: hanya 'selesai' yang boleh download
if ($ticket['status'] !== 'selesai') {
    http_response_code(403);
    die('Berkas belum tersedia. Menunggu status tiket menjadi "Selesai".');
}

$filePath = __DIR__ . '/' . $ticket['lampiran_balasan'];

if (!file_exists($filePath)) {
    http_response_code(404);
    die('File tidak ditemukan di server.');
}

// Determine MIME type
$ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
$mimeTypes = [
    'pdf' => 'application/pdf',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png'
];
$mime = $mimeTypes[$ext] ?? 'application/octet-stream';

$fileName = basename($filePath);

// Send headers
header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $fileName . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: private, must-revalidate');

// Stream file
readfile($filePath);
exit;