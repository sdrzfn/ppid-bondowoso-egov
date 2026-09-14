<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include("../config/database.php");

// Get filter
$status = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query
$sql = "SELECT * FROM tickets WHERE 1=1";
if ($status !== 'all') {
    $sql .= " AND status = '" . $conn->real_escape_string($status) . "'";
}
if ($search !== '') {
    $sql .= " AND (ticket_number LIKE '%$search%' OR nama_lengkap LIKE '%$search%' OR nik LIKE '%$search%')";
}
$sql .= " ORDER BY created_at DESC";

$tickets = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Tiket - Admin PPID</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <?php include("header.php"); ?>
    <?php include("sidebar.php"); ?>

    <div class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold">Manajemen Tiket Permohonan</h2>
            <a href="ticket-stats.php" class="px-4 py-2 bg-sky-600 text-white rounded-lg text-sm hover:bg-sky-700">
                <i class="fa-solid fa-chart-line mr-1"></i> Statistik
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow rounded-lg p-4 mb-6">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2">
                        <option value="all" <?= $status == 'all' ? 'selected' : '' ?>>Semua</option>
                        <option value="menunggu" <?= $status == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                        <option value="diproses" <?= $status == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                        <option value="selesai" <?= $status == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                        <option value="ditolak" <?= $status == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Cari</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                        placeholder="No. Tiket / Nama / NIK" class="border border-gray-300 rounded-lg px-3 py-2">
                </div>
                <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded-lg hover:bg-sky-700">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>
            </form>
        </div>

        <!-- Tickets Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-left">No. Tiket</th>
                        <th class="px-4 py-3 text-left">Nama Pemohon</th>
                        <th class="px-4 py-3 text-left">NIK</th>
                        <th class="px-4 py-3 text-left">Tipe</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($tickets && $tickets->num_rows > 0): ?>
                        <?php while ($t = $tickets->fetch_assoc()): ?>
                            <tr class="border-t border-slate-200 hover:bg-slate-50">
                                <td class="px-4 py-3 font-mono font-medium text-sky-700">
                                    <?= htmlspecialchars($t['ticket_number']) ?></td>
                                <td class="px-4 py-3"><?= htmlspecialchars($t['nama_lengkap']) ?></td>
                                <td class="px-4 py-3 font-mono"><?= htmlspecialchars($t['nik']) ?></td>
                                <td class="px-4 py-3 capitalize"><?= $t['form_type'] ?></td>
                                <td class="px-4 py-3">
                                    <?php
                                    $statusColors = [
                                        'menunggu' => 'bg-amber-100 text-amber-700',
                                        'diproses' => 'bg-blue-100 text-blue-700',
                                        'selesai' => 'bg-green-100 text-green-700',
                                        'ditolak' => 'bg-red-100 text-red-700'
                                    ];
                                    $statusLabels = [
                                        'menunggu' => 'Menunggu',
                                        'diproses' => 'Diproses',
                                        'selesai' => 'Selesai',
                                        'ditolak' => 'Ditolak'
                                    ];
                                    ?>
                                    <span
                                        class="inline-block px-2 py-1 rounded-full text-xs font-medium <?= $statusColors[$t['status']] ?>">
                                        <?= $statusLabels[$t['status']] ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-slate-500"><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
                                <td class="px-4 py-3 text-center">
                                    <a href="ticket-detail.php?id=<?= $t['id'] ?>"
                                        class="text-sky-600 hover:text-sky-800 text-xs font-semibold">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">Belum ada tiket</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include("footer.php"); ?>
</body>

</html>