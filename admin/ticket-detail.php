<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include("../config/database.php");
$id = $_GET['id'] ?? 0;

$ticket = $conn->query("SELECT * FROM tickets WHERE id = $id")->fetch_assoc();
if (!$ticket) {
    header("Location: tickets.php");
    exit;
}

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newStatus = $_POST['status'];
    $notes = $_POST['notes'];
    
    // Insert history
    $conn->query("INSERT INTO ticket_history (ticket_id, status_lama, status_baru, catatan, updated_by) 
                  VALUES ({$ticket['id']}, '{$ticket['status']}', '$newStatus', '$notes', '{$_SESSION['name']}')");
    
    // Update ticket
    $conn->query("UPDATE tickets SET status = '$newStatus', notes = '$notes' WHERE id = {$ticket['id']}");
    
    header("Location: ticket-detail.php?id=$id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tiket - Admin PPID</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include("header.php"); ?>
    <?php include("sidebar.php"); ?>
    
    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="tickets.php" class="text-sky-600 hover:text-sky-800 text-sm mb-2 inline-block">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <h2 class="text-2xl font-bold">Detail Tiket <?= htmlspecialchars($ticket['ticket_number']) ?></h2>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-block px-3 py-1 rounded-full text-sm font-medium
                    <?= match($ticket['status']) {
                        'menunggu' => 'bg-amber-100 text-amber-700',
                        'diproses' => 'bg-blue-100 text-blue-700',
                        'selesai' => 'bg-green-100 text-green-700',
                        'ditolak' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-700'
                    } ?>">
                    <?= ucfirst($ticket['status']) ?>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Applicant Data -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-user text-sky-700"></i> Data Pemohon
                    </h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><span class="text-slate-500">Nama:</span> <p class="font-medium"><?= htmlspecialchars($ticket['nama_lengkap']) ?></p></div>
                        <div><span class="text-slate-500">NIK:</span> <p class="font-medium font-mono"><?= htmlspecialchars($ticket['nik']) ?></p></div>
                        <div><span class="text-slate-500">Email:</span> <p class="font-medium"><?= htmlspecialchars($ticket['email']) ?></p></div>
                        <div><span class="text-slate-500">WhatsApp:</span> <p class="font-medium"><?= htmlspecialchars($ticket['no_whatsapp']) ?></p></div>
                        <div class="col-span-2"><span class="text-slate-500">Alamat:</span> <p class="font-medium"><?= nl2br(htmlspecialchars($ticket['alamat'])) ?></p></div>
                    </div>
                    <?php if ($ticket['lampiran_ktp']): ?>
                        <div class="mt-4">
                            <span class="text-slate-500 text-sm">Lampiran KTP:</span>
                            <a href="<?= htmlspecialchars($ticket['lampiran_ktp']) ?>" target="_blank" class="text-sky-600 hover:underline text-sm ml-2">
                                <i class="fa-solid fa-file-pdf"></i> Lihat Berkas
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Request Details -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-file-lines text-sky-700"></i> Rincian Permohonan
                    </h3>
                    <div class="space-y-4 text-sm">
                        <div>
                            <span class="text-slate-500">Informasi yang Diminta:</span>
                            <p class="font-medium mt-1 bg-slate-50 p-3 rounded-lg"><?= nl2br(htmlspecialchars($ticket['rincian_informasi'])) ?></p>
                        </div>
                        <div>
                            <span class="text-slate-500">Tujuan Penggunaan:</span>
                            <p class="font-medium mt-1"><?= nl2br(htmlspecialchars($ticket['tujuan_penggunaan'])) ?></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><span class="text-slate-500">Format:</span> <p class="font-medium capitalize"><?= $ticket['format_salinan'] ?></p></div>
                            <div><span class="text-slate-500">Cara Penyampaian:</span> <p class="font-medium capitalize"><?= str_replace('_', ' ', $ticket['cara_penyampaian']) ?></p></div>
                        </div>
                    </div>
                </div>

                <!-- History -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Riwayat Status</h3>
                    <div class="space-y-3">
                        <?php
                        $history = $conn->query("SELECT * FROM ticket_history WHERE ticket_id = {$ticket['id']} ORDER BY created_at DESC");
                        if ($history && $history->num_rows > 0):
                            while ($h = $history->fetch_assoc()):
                        ?>
                            <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                                <div class="w-8 h-8 bg-sky-100 text-sky-700 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-clock text-xs"></i>
                                </div>
                                <div class="text-sm">
                                    <p class="font-medium"><?= ucfirst($h['status_baru']) ?></p>
                                    <p class="text-slate-500"><?= $h['catatan'] ?></p>
                                    <p class="text-xs text-slate-400 mt-1"><?= date('d/m/Y H:i', strtotime($h['created_at'])) ?> - <?= htmlspecialchars($h['updated_by']) ?></p>
                                </div>
                            </div>
                        <?php endwhile; endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Update Status -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg p-6 sticky top-24">
                    <h3 class="text-lg font-semibold mb-4">Update Status</h3>
                    <form method="POST">
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Status Baru</label>
                            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="menunggu" <?= $ticket['status'] == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                <option value="diproses" <?= $ticket['status'] == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                <option value="selesai" <?= $ticket['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                <option value="ditolak" <?= $ticket['status'] == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-1">Catatan</label>
                            <textarea name="notes" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2"><?= htmlspecialchars($ticket['notes']) ?></textarea>
                        </div>
                        <button type="submit" class="w-full bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700">
                            Update Status
                        </button>
                    </form>

                    <div class="mt-6 pt-6 border-t border-slate-200">
                        <h4 class="text-sm font-semibold mb-2">Informasi Tiket</h4>
                        <div class="text-xs text-slate-500 space-y-1">
                            <p>Dibuat: <?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></p>
                            <p>Diperbarui: <?= date('d/m/Y H:i', strottime($ticket['updated_at'])) ?></p>
                            <p>IP: <?= htmlspecialchars($ticket['ip_address'] ?? '-') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include("footer.php"); ?>
</body>
</html>