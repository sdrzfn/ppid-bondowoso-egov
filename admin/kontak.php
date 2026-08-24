<?php
include '../admin/header.php';
include '../config/database.php';
include '../admin/sidebar.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM kontak_admin WHERE id = $id");
    echo "<script>alert('Pesan berhasil dihapus!'); window.location.href='../admin/kontak.php';</script>";
}
?>

<div class="p-6">
    <h2 class="text-xl font-semibold mb-4">Manajemen Pesan Kontak</h2>
    <div class="overflow-x-auto bg-white shadow rounded-lg">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-slate-100">
                <tr>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Subjek</th>
                    <th class="px-4 py-2">Pesan</th>
                    <th class="px-4 py-2">Tanggal</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include '../config/database.php';
                $result = $conn->query("SELECT * FROM kontak_admin ORDER BY created_at DESC");
                while ($row = $result->fetch_assoc()):
                    ?>
                    <tr class="border-b">
                        <td class="px-4 py-2"><?= htmlspecialchars($row['nama']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($row['email']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($row['subjek']) ?></td>
                        <td class="px-4 py-2"><?= nl2br(htmlspecialchars($row['pesan'])) ?></td>
                        <td class="px-4 py-2"><?= $row['created_at'] ?></td>
                        <td class="px-4 py-2">
                            <a href="../admin/kontak.php?delete=<?= $row['id'] ?>"
                                onclick="return confirm('Yakin hapus pesan ini?')"
                                class="text-red-600 hover:underline">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>