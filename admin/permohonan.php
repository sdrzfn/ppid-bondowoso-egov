<?php
include '../admin/header.php';
include '../config/database.php';
include '../admin/sidebar.php';

$result = $conn->query("SELECT * FROM permohonan_informasi ORDER BY created_at DESC");
?>

<div class="p-6">
    <h2 class="text-xl font-semibold mb-4">Daftar Permohonan Informasi</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full border border-slate-200 rounded-lg">
            <thead class="bg-slate-100">
                <tr>
                    <th class="px-4 py-2 text-left">Nama</th>
                    <th class="px-4 py-2 text-left">No KTP</th>
                    <th class="px-4 py-2 text-left">Kontak</th>
                    <th class="px-4 py-2 text-left">Tujuan</th>
                    <th class="px-4 py-2 text-left">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border-t hover:bg-slate-50">
                        <td class="px-4 py-2"><?= $row['nama'] ?></td>
                        <td class="px-4 py-2"><?= $row['no_ktp'] ?></td>
                        <td class="px-4 py-2"><?= $row['kontak'] ?></td>
                        <td class="px-4 py-2"><?= $row['tujuan'] ?></td>
                        <td class="px-4 py-2"><?= $row['created_at'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>