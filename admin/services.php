<?php
include '../config/database.php';

// ==================== PROSES DELETE (PALING ATAS) ====================
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    // Hapus file gambar dari folder
    $res = $conn->query("SELECT gambar FROM layanan WHERE id=$id");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if (!empty($row['gambar']) && file_exists("../uploads/layanan/" . $row['gambar'])) {
            unlink("../uploads/layanan/" . $row['gambar']);
        }
    }

    if (!empty($row['dokumen']) && file_exists("../uploads/layanan/" . $row['dokumen'])) {
        unlink("../uploads/layanan/" . $row['dokumen']);
    }

    $conn->query("DELETE FROM layanan WHERE id=$id");
    header("Location: services.php");
    exit;
}

// ==================== PROSES ADD/UPDATE ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $sumber = $_POST['sumber'];
    $id = $_POST['id'];
    $gambarLama = $_POST['gambar_lama'] ?? "";
    $dokumenLama = $_POST['dokumen_lama'] ?? "";

    // Upload gambar jika ada
    if (!empty($_FILES['gambar']['name']) && $_FILES['gambar']['error'] === 0) {
        $targetDir = "../uploads/layanan/";

        // Buat folder jika belum ada
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = basename($_FILES["gambar"]["name"]);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validasi tipe file
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileExt, $allowed)) {
            // Generate nama file unik dengan timestamp
            $newFileName = time() . "_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $fileName);
            $targetFile = $targetDir . $newFileName;

            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $targetFile)) {
                // Hapus gambar lama jika ada dan bukan placeholder
                if (!empty($gambarLama) && file_exists("../uploads/layanan/" . $gambarLama)) {
                    unlink("../uploads/layanan/" . $gambarLama);
                }
                $gambar = $newFileName;
            } else {
                // Simpan error ke session, lalu redirect
                session_start();
                $_SESSION['error'] = "Upload gambar gagal!";
                header("Location: services.php");
                exit;
            }
        } else {
            session_start();
            $_SESSION['error'] = "Tipe file tidak diizinkan. Gunakan jpg, png, gif, atau webp.";
            header("Location: services.php");
            exit;
        }
    } else {
        // Tidak ada gambar baru, pertahankan gambar lama
        $gambar = $gambarLama;
    }

    // Upload PDF jika ada
    $dokumen = '';
    if (!empty($_FILES['dokumen']['name']) && $_FILES['dokumen']['error'] === 0) {
        $targetDir = "../uploads/layanan/";

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = basename($_FILES["dokumen"]["name"]);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        // Validasi PDF
        if ($fileExt === 'pdf') {
            $maxSize = 10 * 1024 * 1024; // 10MB
            if ($_FILES["dokumen"]["size"] <= $maxSize) {
                $newFileName = time() . "_doc_" . preg_replace("/[^a-zA-Z0-9\._-]/", "", $fileName);
                $targetFile = $targetDir . $newFileName;

                if (move_uploaded_file($_FILES["dokumen"]["tmp_name"], $targetFile)) {
                    $dokumen = $newFileName;
                } else {
                    session_start();
                    $_SESSION['error'] = "Upload dokumen PDF gagal!";
                    header("Location: services.php");
                    exit;
                }
            } else {
                session_start();
                $_SESSION['error'] = "Ukuran PDF terlalu besar. Maksimal 10MB.";
                header("Location: services.php");
                exit;
            }
        } else {
            session_start();
            $_SESSION['error'] = "Hanya file PDF yang diizinkan untuk dokumen.";
            header("Location: services.php");
            exit;
        }
    }

    if (!empty($id)) {
        // Update
        $conn->query("UPDATE layanan 
        SET judul='$judul', deskripsi='$deskripsi', gambar='$gambar', 
            dokumen='" . ($dokumen ?? '') . "', sumber='$sumber' 
        WHERE id=$id");
    } else {
        // Insert baru
        $conn->query("INSERT INTO layanan (judul, deskripsi, gambar, dokumen, sumber) 
                  VALUES ('$judul','$deskripsi','$gambar','" . ($dokumen ?? '') . "','$sumber')");
    }

    header("Location: services.php");
    exit;
}

// ==================== AMBIL DATA UNTUK FORM EDIT ====================
$editMode = false;
$id = $judul = $deskripsi = $gambar = $sumber = "";

if (isset($_GET['edit'])) {
    $editMode = true;
    $id = (int) $_GET['edit'];
    $res = $conn->query("SELECT * FROM layanan WHERE id=$id");
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $judul = $row['judul'];
        $deskripsi = $row['deskripsi'];
        $gambar = $row['gambar'];
        $sumber = $row['sumber'];
    }
}

// ==================== AMBIL DATA UNTUK TABEL ====================
$data = $conn->query("SELECT * FROM layanan ORDER BY id ASC");

// ==================== INCLUDE TEMPLATE (SETELAH SEMUA PROSES) ====================
include '../admin/header.php';
include '../admin/sidebar.php';
?>

<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Manajemen Layanan</h1>

    <!-- Form Add/Edit -->
    <div class="bg-white shadow rounded-xl p-6 mb-8">
        <h2 class="text-lg font-semibold mb-4">
            <?= $editMode ? "Edit Layanan" : "Tambah Layanan" ?>
        </h2>
        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($gambar) ?>">
            <input type="hidden" name="dokumen_lama" value="<?= htmlspecialchars($row['dokumen'] ?? '') ?>">

            <div>
                <label class="block text-sm font-medium">Judul</label>
                <input type="text" name="judul" value="<?= htmlspecialchars($judul) ?>"
                    class="w-full mt-1 p-2 border rounded-lg focus:ring-2 focus:ring-sky-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Deskripsi</label>
                <textarea name="deskripsi" rows="4"
                    class="w-full mt-1 p-2 border rounded-lg focus:ring-2 focus:ring-sky-500"
                    required><?= htmlspecialchars($deskripsi) ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium">Gambar</label>
                <?php if ($editMode && $gambar): ?>
                    <img src="../uploads/layanan/<?= htmlspecialchars($gambar) ?>" class="h-16 mb-2 rounded object-cover">
                <?php endif; ?>
                <input type="file" name="gambar" accept="image/*"
                    class="w-full mt-1 p-2 border rounded-lg focus:ring-2 focus:ring-sky-500">
            </div>
            <div>
                <label class="block text-sm font-medium">Dokumen (PDF)</label>
                <?php if ($editMode && $gambar): ?>
                    <?php
                    $pdfAdminPath = "../uploads/layanan/" . $gambar;
                    if (file_exists($pdfAdminPath)): ?>
                        <embed src="<?= htmlspecialchars($pdfAdminPath) ?>" type="application/pdf"
                            class="w-full h-48 rounded border mb-2">
                    <?php endif; ?>
                    <p class="text-xs text-gray-500">Dokumen saat ini:
                        <?= htmlspecialchars($gambar) ?>
                    </p>
                <?php endif; ?>
                <input type="file" name="dokumen" accept="application/pdf"
                    class="w-full mt-1 p-2 border rounded-lg focus:ring-2 focus:ring-sky-500">
                <p class="text-xs text-gray-500 mt-1">Format: PDF. Maksimal 5MB.</p>
            </div>
            <div>
                <label class="block text-sm font-medium">Sumber</label>
                <input type="text" name="sumber" value="<?= htmlspecialchars($sumber) ?>"
                    class="w-full mt-1 p-2 border rounded-lg focus:ring-2 focus:ring-sky-500">
            </div>
            <div class="flex items-center gap-3">
                <button class="px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition">
                    <?= $editMode ? "Update" : "Simpan" ?>
                </button>
                <?php if ($editMode): ?>
                    <a href="services.php" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">Batal</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabel Data -->
    <div class="bg-white shadow rounded-xl p-6">
        <h2 class="text-lg font-semibold mb-4">Daftar Layanan</h2>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-slate-100 text-left text-sm">
                        <th class="p-3 border">#</th>
                        <th class="p-3 border">Judul</th>
                        <th class="p-3 border">Deskripsi</th>
                        <th class="p-3 border">Gambar</th>
                        <th class="p-3 border">Sumber</th>
                        <th class="p-3 border">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $data->fetch_assoc()): ?>
                        <tr class="text-sm">
                            <td class="p-3 border"><?= $row['id'] ?></td>
                            <td class="p-3 border"><?= htmlspecialchars($row['judul']) ?></td>
                            <td class="p-3 border"><?= substr($row['deskripsi'], 0, 50) ?>...</td>
                            <td class="p-3 border">
                                <?php if ($row['gambar']): ?>
                                    <img src="../uploads/layanan/<?= htmlspecialchars($row['gambar']) ?>"
                                        class="h-12 rounded object-cover">
                                <?php else: ?>
                                    <span class="text-slate-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-3 border"><?= htmlspecialchars($row['sumber']) ?></td>
                            <td class="p-3 border flex gap-2">
                                <a href="services.php?edit=<?= $row['id'] ?>"
                                    class="px-3 py-1 bg-yellow-400 text-white rounded-lg hover:bg-yellow-500">Edit</a>
                                <a href="services.php?delete=<?= $row['id'] ?>"
                                    onclick="return confirm('Yakin ingin menghapus layanan ini?')"
                                    class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>