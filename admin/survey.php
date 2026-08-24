<?php
include("../config/database.php");
include("header.php");
include("sidebar.php");

$surveys = $conn->query("SELECT * FROM surveys ORDER BY created_at DESC");

// Hapus survey
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $conn->query("DELETE FROM surveys WHERE id = $id");
    header("Location: survey.php");
    exit;
}

// Update survey
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = (int) $_POST['id'];
    $nama = $conn->real_escape_string($_POST['nama']);
    $email = $conn->real_escape_string($_POST['email']);
    $rating = $conn->real_escape_string($_POST['rating']);
    $saran = $conn->real_escape_string($_POST['saran']);

    $conn->query("UPDATE surveys SET nama='$nama', email='$email', rating='$rating', saran='$saran' WHERE id=$id");
    header("Location: survey.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Survey - Admin PPID</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-6">
    <div class="flex-1 p-2 ml-2">
        <h1 class="text-2xl mb-4 mt-4 font-bold text-black">Kelola Survey</h1>

        <!-- Tabel Survey -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Daftar Survey</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border border-gray-200 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">ID</th>
                            <th class="px-4 py-2 border">Nama</th>
                            <th class="px-4 py-2 border">Email</th>
                            <th class="px-4 py-2 border">Rating</th>
                            <th class="px-4 py-2 border">Tanggal</th>
                            <th class="px-4 py-2 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($surveys->num_rows > 0): ?>
                            <?php while ($s = $surveys->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border"><?= $s['id'] ?></td>
                                    <td class="px-4 py-2 border font-medium"><?= htmlspecialchars($s['nama']) ?></td>
                                    <td class="px-4 py-2 border"><?= htmlspecialchars($s['email']) ?></td>
                                    <td class="px-4 py-2 border">
                                        <?php
                                        $ratingLabels = [
                                            'sangat_baik' => 'Sangat Baik',
                                            'baik' => 'Baik',
                                            'cukup' => 'Cukup',
                                            'kurang' => 'Kurang'
                                        ];
                                        echo htmlspecialchars($ratingLabels[$s['rating']] ?? $s['rating']);
                                        ?>
                                    </td>
                                    <td class="px-4 py-2 border"><?= date("d-m-Y H:i", strtotime($s['created_at'])) ?></td>
                                    <td class="px-4 py-2 border space-x-2">
                                        <button class="text-blue-600 hover:underline" onclick="openModal(<?= $s['id'] ?>, '<?= htmlspecialchars($s['nama'], ENT_QUOTES) ?>', '<?= htmlspecialchars($s['email'], ENT_QUOTES) ?>', '<?= $s['rating'] ?>', '<?= htmlspecialchars($s['saran'] ?? '', ENT_QUOTES) ?>')">Edit</button>
                                        <a href="?hapus=<?= $s['id'] ?>" onclick="return confirm('Yakin ingin menghapus survey ini?')" class="text-red-600 hover:underline">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-gray-500">Belum ada survey</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Edit Survey</h2>
            <form method="POST">
                <input type="hidden" name="id" id="editId">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama</label>
                        <input type="text" name="nama" id="editNama" required class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="email" id="editEmail" required class="w-full border rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Rating</label>
                        <select name="rating" id="editRating" class="w-full border rounded-lg px-3 py-2">
                            <option value="sangat_baik">Sangat Baik</option>
                            <option value="baik">Baik</option>
                            <option value="cukup">Cukup</option>
                            <option value="kurang">Kurang</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Saran</label>
                        <textarea name="saran" id="editSaran" rows="3" class="w-full border rounded-lg px-3 py-2"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">Batal</button>
                        <button type="submit" name="update" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id, nama, email, rating, saran) {
            document.getElementById('editId').value = id;
            document.getElementById('editNama').value = nama;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRating').value = rating;
            document.getElementById('editSaran').value = saran;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }
        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }
    </script>
</body>
</html>