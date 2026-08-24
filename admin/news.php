<?php
include("../config/database.php");
include("header.php");
include("sidebar.php");

$berita = $conn->query("SELECT * FROM berita ORDER BY tanggal DESC");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $judul = $conn->real_escape_string($_POST['judul']);
    $isi = $conn->real_escape_string($_POST['isi']);
    $penulis = $conn->real_escape_string($_POST['penulis']);
    $tanggal = $conn->real_escape_string($_POST['tanggal']);

    $targetDir = "../uploads/berita/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($_FILES["gambar"]["name"]);
    $targetFile = $targetDir . time() . "_" . $fileName; // rename biar unik
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["gambar"]["tmp_name"]);
    if ($check === false) {
        die("File bukan gambar!");
    }

    if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFile)) {
        // Buat link relatif untuk database
        $gambarUrl = "../uploads/berita/" . time() . "_" . $fileName;

        // Insert ke database
        $stmt = $conn->prepare("INSERT INTO berita (judul, isi, gambar, penulis, tanggal) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $judul, $isi, $gambarUrl, $penulis, $tanggal);

        if ($stmt->execute()) {
            header("Location: news.php?status=success");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Upload gambar gagal!";
    }
}

// Update berita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = (int) $_POST['id'];
    $judul = $conn->real_escape_string($_POST['judul']);
    $isi = $conn->real_escape_string($_POST['isi']);
    $gambar = $conn->real_escape_string($_POST['gambar']);
    $penulis = $conn->real_escape_string($_POST['penulis']);
    $tanggal = $conn->real_escape_string($_POST['tanggal']);

    $conn->query("UPDATE berita 
                     SET judul='$judul', isi='$isi', gambar='$gambar', penulis='$penulis', tanggal='$tanggal' 
                     WHERE id=$id");
    header("Location: news.php");
    exit;
}

// Hapus berita
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $conn->query("DELETE FROM berita WHERE id = $id");
    header("Location: news.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Berita - Admin PPID</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen p-6">
    <div class="flex-1 p-2 ml-2">
        <h1 class="text-2xl mb-4 mt-4 font-bold text-black">Kelola Berita</h1>

        <!-- Tabel Berita -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Daftar Berita</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border border-gray-200 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">ID</th>
                            <th class="px-4 py-2 border">Judul</th>
                            <th class="px-4 py-2 border">Penulis</th>
                            <th class="px-4 py-2 border">Tanggal</th>
                            <th class="px-4 py-2 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($berita->num_rows > 0): ?>
                            <?php while ($b = $berita->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border"><?= $b['id'] ?></td>
                                    <td class="px-4 py-2 border font-medium"><?= htmlspecialchars($b['judul']) ?></td>
                                    <td class="px-4 py-2 border"><?= htmlspecialchars($b['penulis']) ?></td>
                                    <td class="px-4 py-2 border"><?= date("d-m-Y", strtotime($b['tanggal'])) ?></td>
                                    <td class="px-4 py-2 border space-x-2">
                                        <button class="text-blue-600 hover:underline" onclick="openModal(
                                                <?= $b['id'] ?>,
                                                '<?= htmlspecialchars($b['judul'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($b['isi'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($b['gambar'], ENT_QUOTES) ?>',
                                                '<?= htmlspecialchars($b['penulis'], ENT_QUOTES) ?>',
                                                '<?= $b['tanggal'] ?>'
                                            )">Edit</button>
                                        <a href="news.php?hapus=<?= $b['id'] ?>"
                                            onclick="return confirm('Yakin ingin menghapus berita ini?')"
                                            class="text-red-600 hover:underline">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-gray-500">Belum ada berita</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Tambah -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Tambah Berita Baru</h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Judul</label>
                    <input type="text" name="judul" required
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Gambar</label>
                    <input type="file" name="gambar" accept="image/*" required
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Penulis</label>
                    <input type="text" name="penulis" required
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal</label>
                    <input type="date" name="tanggal" required
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Isi Berita</label>
                    <textarea name="isi" rows="5" required
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"></textarea>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" name="tambah"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6">
            <h2 class="text-xl font-semibold mb-4">Edit Berita</h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="id" id="editId">
                <div>
                    <label class="block text-sm font-medium mb-1">Judul</label>
                    <input type="text" name="judul" id="editJudul" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Gambar (URL)</label>
                    <input type="text" name="gambar" id="editGambar" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Penulis</label>
                    <input type="text" name="penulis" id="editPenulis" required
                        class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal</label>
                    <input type="date" name="tanggal" id="editTanggal" required
                        class="w-full border rounded-lg px-3 py-2">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Isi Berita</label>
                    <textarea name="isi" id="editIsi" rows="5" required
                        class="w-full border rounded-lg px-3 py-2"></textarea>
                </div>
                <div class="md:col-span-2 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                        Batal
                    </button>
                    <button type="submit" name="update"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="/../assets/js/modal-script.js"></script>
</body>

</html>