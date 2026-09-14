<?php
include("../config/database.php");

$cards = $conn->query("SELECT * FROM homepage_cards ORDER BY urutan ASC, id DESC");
$banner = $conn->query("SELECT * FROM site_banner WHERE is_active = 1 LIMIT 1")->fetch_assoc();
$bannerJudul = $banner ? $banner['judul'] : 'Peringatan Dini Cuaca & Potensi Bencana:';
$bannerDeskripsi = $banner ? $banner['deskripsi'] : 'Informasi tanggap darurat dan nomor kontak bantuan darurat 24 Jam.';
$bannerLink = $banner ? $banner['link_url'] : '';

// Handle banner update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_banner'])) {
    $judul = $conn->real_escape_string($_POST['banner_judul']);
    $deskripsi = $conn->real_escape_string($_POST['banner_deskripsi']);
    $linkUrl = $conn->real_escape_string($_POST['banner_link_url']);

    // Validate URL if not empty
    if (!empty($linkUrl) && !filter_var($linkUrl, FILTER_VALIDATE_URL)) {
        $_SESSION['error'] = 'URL tidak valid. Masukkan URL yang benar (contoh: https://example.com)';
        header("Location: homepage_cards.php");
        exit;
    }

    // Check if banner exists
    $checkBanner = $conn->query("SELECT id FROM site_banner WHERE is_active = 1 LIMIT 1");
    if ($checkBanner && $checkBanner->num_rows > 0) {
        $bannerId = $checkBanner->fetch_assoc()['id'];
        $conn->query("UPDATE site_banner SET judul='$judul', deskripsi='$deskripsi', link_url='" . ($linkUrl ?: NULL) . "' WHERE id=$bannerId");
    } else {
        $conn->query("INSERT INTO site_banner (judul, deskripsi, link_url, is_active) VALUES ('$judul', '$deskripsi', '" . ($linkUrl ?: NULL) . "', 1)");
    }

    $_SESSION['success'] = 'Banner berhasil diperbarui!';
    header("Location: homepage_cards.php");
    exit;
}

// Handle cards
// Tambah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah'])) {
    $judul = $conn->real_escape_string($_POST['judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $kontak = $conn->real_escape_string($_POST['kontak']);
    $urutan = (int) $_POST['urutan'];
    $status = $conn->real_escape_string($_POST['status']);

    $gambarUrl = '';
    if (!empty($_FILES["gambar"]["name"])) {
        $targetDir = "../uploads/homepage/";
        if (!is_dir($targetDir))
            mkdir($targetDir, 0777, true);
        $fileName = basename($_FILES["gambar"]["name"]);
        $targetFile = $targetDir . time() . "_" . $fileName;
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFile)) {
            $gambarUrl = "../uploads/homepage/" . time() . "_" . $fileName;
        }
    }

    $stmt = $conn->prepare("INSERT INTO homepage_cards (judul, deskripsi, gambar, kontak, urutan, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $judul, $deskripsi, $gambarUrl, $kontak, $urutan, $status);

    if ($stmt->execute()) {
        header("Location: homepage_cards.php?status=success");
        exit;
    }
}

// Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = (int) $_POST['id'];
    $judul = $conn->real_escape_string($_POST['judul']);
    $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
    $kontak = $conn->real_escape_string($_POST['kontak']);
    $urutan = (int) $_POST['urutan'];
    $status = $conn->real_escape_string($_POST['status']);

    // Cek apakah ada gambar baru yang diupload
    if (!empty($_FILES["gambar"]["name"]) && $_FILES["gambar"]["error"] === 0) {
        $targetDir = "../uploads/homepage/";
        if (!is_dir($targetDir))
            mkdir($targetDir, 0777, true);
        $fileName = basename($_FILES["gambar"]["name"]);
        $targetFile = $targetDir . time() . "_" . $fileName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Validasi tipe file
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($imageFileType, $allowed)) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFile)) {
                $gambarUrl = "../uploads/homepage/" . time() . "_" . $fileName;
                $conn->query("UPDATE homepage_cards SET gambar='$gambarUrl' WHERE id=$id");
            }
        }
    }

    $conn->query("UPDATE homepage_cards SET judul='$judul', deskripsi='$deskripsi', kontak='$kontak', urutan=$urutan, status='$status' WHERE id=$id");
    exit;
}

// Hapus
if (isset($_GET['hapus'])) {
    $id = (int) $_GET['hapus'];
    $conn->query("DELETE FROM homepage_cards WHERE id=$id");
    header("Location: homepage_cards.php");
    exit;
}
include("header.php");
include("sidebar.php");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kelola Homepage Cards - Admin PPID</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen p-6">
    <div class="flex-1 p-2 ml-2">
        <h1 class="text-2xl mb-4 mt-4 font-bold text-black">Kelola Homepage Cards</h1>

        <!-- Tabel -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i class="fa-solid fa-bullhorn text-amber-600"></i>
                Kelola Banner Serta-Merta (Homepage)
            </h2>
            <p class="text-xs text-slate-500 mb-4">Teks ini akan muncul di homepage pada bagian banner Serta-Merta
                dengan animasi marquee. Klik banner akan mengarah ke link yang ditentukan.</p>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
                    <?= $_SESSION['success'];
                    unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
                    <?= $_SESSION['error'];
                    unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Judul Banner</label>
                        <input type="text" name="banner_judul" value="<?= htmlspecialchars($bannerJudul) ?>"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                            placeholder="Contoh: Peringatan Dini Cuaca & Potensi Bencana:">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Deskripsi/Isi Banner</label>
                        <input type="text" name="banner_deskripsi" value="<?= htmlspecialchars($bannerDeskripsi) ?>"
                            class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                            placeholder="Contoh: Informasi tanggap darurat dan nomor kontak bantuan darurat 24 Jam.">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Link URL (Opsional)</label>
                    <input type="url" name="banner_link_url" value="<?= htmlspecialchars($bannerLink) ?>"
                        placeholder="https://example.com"
                        class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                    <p class="text-xs text-slate-500 mt-1">Biarkan kosong jika banner tidak perlu diklik. Jika diisi,
                        banner akan bisa diklik dan mengarah ke URL tersebut.</p>
                </div>
                <div class="flex justify-end">
                    <button type="submit" name="update_banner"
                        class="bg-amber-600 hover:bg-amber-700 text-white font-semibold px-5 py-2 rounded-lg transition">
                        <i class="fa-solid fa-save mr-1"></i> Simpan Banner
                    </button>
                </div>
            </form>

            <!-- <h2 class="text-lg font-semibold mb-4">Daftar Cards</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm border border-gray-200 rounded-lg">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">ID</th>
                            <th class="px-4 py-2 border">Judul</th>
                            <th class="px-4 py-2 border">Gambar</th>
                            <th class="px-4 py-2 border">Kontak</th>
                            <th class="px-4 py-2 border">Urutan</th>
                            <th class="px-4 py-2 border">Status</th>
                            <th class="px-4 py-2 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($cards->num_rows > 0): ?>
                            <?php while ($c = $cards->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border"><?= $c['id'] ?></td>
                                    <td class="px-4 py-2 border font-medium"><?= htmlspecialchars($c['judul']) ?></td>
                                    <td class="px-4 py-2 border">
                                        <?php if ($c['gambar']): ?>
                                            <img src="<?= $c['gambar'] ?>" class="h-10 w-16 object-cover rounded">
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-2 border"><?= htmlspecialchars($c['kontak']) ?></td>
                                    <td class="px-4 py-2 border"><?= $c['urutan'] ?></td>
                                    <td class="px-4 py-2 border">
                                        <span
                                            class="px-2 py-1 rounded text-xs <?= $c['status'] == 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                            <?= ucfirst($c['status']) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 border space-x-2">
                                        <button class="text-blue-600 hover:underline"
                                            onclick="openModal(<?= $c['id'] ?>, '<?= htmlspecialchars($c['judul'], ENT_QUOTES) ?>', '<?= htmlspecialchars($c['deskripsi'], ENT_QUOTES) ?>', '<?= htmlspecialchars($c['gambar'] ?? '', ENT_QUOTES) ?>', '<?= htmlspecialchars($c['kontak'], ENT_QUOTES) ?>', <?= $c['urutan'] ?>, '<?= $c['status'] ?>')">Edit</button>
                                        <a href="?hapus=<?= $c['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')"
                                            class="text-red-600 hover:underline">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-4 py-3 text-center text-gray-500">Belum ada cards</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div> -->
        </div>

        <!-- Form Tambah -->
        <!-- <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-4">Tambah Card Baru</h2>
            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Judul*</label>
                    <input type="text" name="judul" required class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Gambar</label>
                    <input type="file" name="gambar" accept="image/*" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Deskripsi*</label>
                    <textarea name="deskripsi" rows="3" required class="w-full border rounded-lg px-3 py-2"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Kontak</label>
                    <input type="text" name="kontak" value="0859-xxxx-xxxx" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Urutan</label>
                    <input type="number" name="urutan" value="0" class="w-full border rounded-lg px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" class="w-full border rounded-lg px-3 py-2">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" name="tambah"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg">Simpan</button>
                </div>
            </form>
        </div> -->
    </div>

    <!-- Modal Edit -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">
            <!-- Header -->
            <!-- <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Edit Card</h2>
                <button type="button" onclick="closeModal()"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div> -->

            <!-- Form Content (Scrollable) -->
            <!-- <div class="flex-1 overflow-y-auto p-6"> -->
                <!-- <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="editId">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700">Judul</label>
                            <input type="text" name="judul" id="editJudul" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700">Gambar</label>
                            <input type="file" name="gambar" id="editGambar" accept="image/*"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <img id="editGambarPreview" src="" alt="Preview"
                                class="mt-2 h-24 w-full object-cover rounded hidden">
                            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengganti gambar.</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1 text-gray-700">Deskripsi</label>
                            <textarea name="deskripsi" id="editDeskripsi" rows="3" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700">Kontak</label>
                            <input type="text" name="kontak" id="editKontak"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700">Urutan</label>
                            <input type="number" name="urutan" id="editUrutan"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1 text-gray-700">Status</label>
                            <select name="status" id="editStatus"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div> -->

                    <!-- Footer Actions -->
                    <!-- <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeModal()"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition-colors">Batal</button>
                        <button type="submit" name="update"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">Update</button>
                    </div> -->
                <!-- </form> -->
            <!-- </div> -->
        </div>
    </div>

    <script src="/../assets/js/modal-script.js"></script>
    <script>
        function openModal(id, judul, deskripsi, gambar, kontak, urutan, status) {
            document.getElementById('editId').value = id;
            document.getElementById('editJudul').value = judul;
            document.getElementById('editDeskripsi').value = deskripsi;
            document.getElementById('editKontak').value = kontak;
            document.getElementById('editUrutan').value = urutan;
            document.getElementById('editStatus').value = status;

            // Reset file input
            document.getElementById('editGambar').value = '';

            // Preview gambar yang sudah ada
            const preview = document.getElementById('editGambarPreview');
            if (gambar && gambar.trim() !== '') {
                preview.src = gambar;
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }

            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }

        document.getElementById('editModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('editModal');
                if (!modal.classList.contains('hidden')) {
                    closeModal();
                }
            }
        });
    </script>
</body>

</html>