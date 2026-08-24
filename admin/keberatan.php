<?php
include '../admin/header.php';
include '../config/database.php';
include '../admin/sidebar.php';

$result = $conn->query("SELECT * FROM pengajuan_keberatan ORDER BY created_at DESC");
?>

<div class="p-6">
    <h2 class="text-xl font-semibold mb-4">Daftar Pengajuan Keberatan</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full border border-slate-200 rounded-lg">
            <thead class="bg-slate-100">
                <tr>
                    <th class="px-4 py-2 text-left">Nama</th>
                    <th class="px-4 py-2 text-left">No Registrasi</th>
                    <th class="px-4 py-2 text-left">Alasan</th>
                    <th class="px-4 py-2 text-left">Kontak</th>
                    <th class="px-4 py-2 text-left">Lampiran</th>
                    <th class="px-4 py-2 text-left">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border-t hover:bg-slate-50">
                        <td class="px-4 py-2"><?= $row['nama'] ?></td>
                        <td class="px-4 py-2"><?= $row['no_registrasi'] ?></td>
                        <td class="px-4 py-2"><?= $row['alasan'] ?></td>
                        <td class="px-4 py-2"><?= $row['kontak'] ?></td>
                        <td class="px-4 py-2"><?= $row['lampiran'] ?></td>
                        <td class="px-4 py-2"><?= $row['created_at'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>