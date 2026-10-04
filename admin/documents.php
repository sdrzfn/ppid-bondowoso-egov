<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../admin/login.php");
    exit;
}

include("../config/database.php");

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int) $_GET['delete'];

    // Get file path before delete
    $doc = $conn->query("SELECT file_path FROM documents WHERE id = $id")->fetch_assoc();
    if ($doc && file_exists($doc['file_path'])) {
        @unlink($doc['file_path']);
    }

    $conn->query("DELETE FROM documents WHERE id = $id");
    header("Location: documents.php?status=deleted");
    exit;
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = (int) $_POST['id'];
    $title = $conn->real_escape_string($_POST['title']);
    $desc = $conn->real_escape_string($_POST['description']);
    $cat_id = (int) $_POST['category_id'];
    $status = $conn->real_escape_string($_POST['status']);
    $opd_id = ($_SESSION['role'] == 'super_admin') ? (int) $_POST['opd_id'] : (int) ($_SESSION['opd_id'] ?? 0);
    $buatBerita = isset($_POST['buat_berita']) && $_POST['buat_berita'] == '1';

    if (!empty($_FILES['file']['name']) && $_FILES['file']['error'] === 0) {
        $file_name = time() . '_' . basename($_FILES['file']['name']);
        $target = "../public/uploads/" . $file_name;
        move_uploaded_file($_FILES['file']['tmp_name'], $target);

        $oldDoc = $conn->query("SELECT file_path FROM documents WHERE id = $id")->fetch_assoc();
        if ($oldDoc && file_exists($oldDoc['file_path'])) {
            @unlink($oldDoc['file_path']);
        }

        $conn->query("UPDATE documents SET 
            title='$title', 
            description='$desc', 
            file_path='$target',
            category_id=$cat_id, 
            opd_id=$opd_id,
            status='$status' 
            WHERE id=$id");
    } else {
        $conn->query("UPDATE documents SET 
            title='$title', 
            description='$desc', 
            category_id=$cat_id, 
            opd_id=$opd_id,
            status='$status' 
            WHERE id=$id");
    }

    if ($buatBerita) {
        $berita_judul = $conn->real_escape_string($_POST['berita_judul'] ?: $title);
        $berita_isi = $conn->real_escape_string($_POST['berita_isi'] ?? '');
        $berita_penulis = $conn->real_escape_string($_POST['berita_penulis'] ?: ($_SESSION['name'] ?? 'Admin'));
        $berita_tanggal = $conn->real_escape_string($_POST['berita_tanggal'] ?: date('Y-m-d'));

        $current = $conn->query("SELECT berita_link FROM documents WHERE id=$id")->fetch_assoc();
        $existingBeritaId = 0;
        if ($current && $current['berita_link'] && strpos($current['berita_link'], 'detail-berita.php?id=') === 0) {
            $existingBeritaId = (int) str_replace('detail-berita.php?id=', '', $current['berita_link']);
        }

        $berita_gambar_url = '';
        if (!empty($_FILES['berita_gambar']['name']) && $_FILES['berita_gambar']['error'] === 0) {
            $targetDir = "../uploads/berita/";
            if (!is_dir($targetDir))
                mkdir($targetDir, 0777, true);
            $fileName = basename($_FILES['berita_gambar']['name']);
            $targetFile = $targetDir . time() . "_" . $fileName;
            if (move_uploaded_file($_FILES['berita_gambar']['tmp_name'], $targetFile)) {
                $berita_gambar_url = "../uploads/berita/" . time() . "_" . $fileName;
            }
        }

        if ($existingBeritaId > 0) {
            $gambar_clause = $berita_gambar_url ? ", gambar='$berita_gambar_url'" : "";
            $conn->query("UPDATE berita SET judul='$berita_judul', isi='$berita_isi', penulis='$berita_penulis', tanggal='$berita_tanggal' $gambar_clause WHERE id=$existingBeritaId");
        } else {
            $stmt = $conn->prepare("INSERT INTO berita (judul, isi, gambar, penulis, tanggal) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $berita_judul, $berita_isi, $berita_gambar_url, $berita_penulis, $berita_tanggal);
            $stmt->execute();
            $berita_id = $conn->insert_id;
            $berita_link = 'detail-berita.php?id=' . $berita_id;
            $conn->query("UPDATE documents SET berita_link='$berita_link' WHERE id=$id");
        }
    } else {
        $conn->query("UPDATE documents SET berita_link=NULL WHERE id=$id");
    }

    header("Location: documents.php?status=updated");
    exit;
}

// Handle Create
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $desc = $conn->real_escape_string($_POST['description']);
    $cat_id = (int) $_POST['category_id'];
    $status = $conn->real_escape_string($_POST['status']);
    $opd_id = ($_SESSION['role'] == 'super_admin') ? (int) $_POST['opd_id'] : (int) ($_SESSION['opd_id'] ?? 0);
    $created = (int) $_SESSION['user_id'];
    $buatBerita = isset($_POST['buat_berita']) && $_POST['buat_berita'] == '1';

    if (empty($_FILES['file']['name']) || $_FILES['file']['error'] !== 0) {
        $_SESSION['error'] = 'File dokumen wajib diupload!';
        header("Location: documents.php");
        exit;
    }

    $file_name = time() . '_' . basename($_FILES['file']['name']);
    $target = "../public/uploads/" . $file_name;
    move_uploaded_file($_FILES['file']['tmp_name'], $target);

    $berita_link = '';
    if ($buatBerita) {
        $berita_judul = $conn->real_escape_string($_POST['berita_judul'] ?: $title);
        $berita_isi = $conn->real_escape_string($_POST['berita_isi'] ?? '');
        $berita_penulis = $conn->real_escape_string($_POST['berita_penulis'] ?: ($_SESSION['name'] ?? 'Admin'));
        $berita_tanggal = $conn->real_escape_string($_POST['berita_tanggal'] ?: date('Y-m-d'));

        $berita_gambar_url = '';
        if (!empty($_FILES['berita_gambar']['name']) && $_FILES['berita_gambar']['error'] === 0) {
            $targetDir = "../uploads/berita/";
            if (!is_dir($targetDir))
                mkdir($targetDir, 0777, true);
            $fileName = basename($_FILES['berita_gambar']['name']);
            $targetFile = $targetDir . time() . "_" . $fileName;
            if (move_uploaded_file($_FILES['berita_gambar']['tmp_name'], $targetFile)) {
                $berita_gambar_url = "../uploads/berita/" . time() . "_" . $fileName;
            }
        }

        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO berita (judul, isi, gambar, penulis, tanggal) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $berita_judul, $berita_isi, $berita_gambar_url, $berita_penulis, $berita_tanggal);
            $stmt->execute();
            $berita_id = $conn->insert_id;

            $berita_link = 'detail-berita.php?id=' . $berita_id;

            $stmt2 = $conn->prepare("INSERT INTO documents (title, description, file_path, berita_link, category_id, opd_id, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("ssssssss", $title, $desc, $target, $berita_link, $cat_id, $opd_id, $status, $created);
            $stmt2->execute();

            $conn->commit();
            header("Location: documents.php?status=created");
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error'] = 'Gagal menyimpan: ' . $e->getMessage();
            header("Location: documents.php");
            exit;
        }
    } else {
        $conn->query("INSERT INTO documents (title, description, file_path, category_id, opd_id, status, created_by) 
                VALUES ('$title','$desc','$target','$cat_id','$opd_id','$status','$created')");

        header("Location: documents.php?status=created");
        exit;
    }
}

// Query dokumen
if ($_SESSION['role'] == 'super_admin') {
    $docs = $conn->query("SELECT d.*, c.name as category_name, o.name as opd_name 
                          FROM documents d 
                          LEFT JOIN categories c ON d.category_id = c.id 
                          LEFT JOIN opd o ON d.opd_id = o.id 
                          ORDER BY d.created_at DESC");
} else {
    $opd_id = (int) ($_SESSION['opd_id'] ?? 0);
    if ($opd_id > 0) {
        $docs = $conn->query("SELECT d.*, c.name as category_name, o.name as opd_name 
                              FROM documents d 
                              LEFT JOIN categories c ON d.category_id = c.id 
                              LEFT JOIN opd o ON d.opd_id = o.id 
                              WHERE d.opd_id = $opd_id 
                              ORDER BY d.created_at DESC");
    } else {
        $docs = false;
    }
}

// Get categories and OPDs for dropdowns
$cats = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$opds = $conn->query("SELECT * FROM opd");

include("../admin/header.php");
include("../admin/sidebar.php");
?>

<h2 class="text-2xl font-bold mb-6 ml-6 mt-6">Katalog Informasi</h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="ml-6 mr-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
        <?= $_SESSION['success'];
        unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="ml-6 mr-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
        <?= $_SESSION['error'];
        unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<!-- Form Tambah Dokumen -->
<div class="ml-6 mr-6 bg-white shadow-md rounded-lg p-6 mb-8">
    <h3 class="text-lg font-semibold mb-4">Tambah Informasi Baru</h3>
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-1">Judul</label>
                <input type="text" name="title" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
            </div>
            <div>
                <label class="block font-semibold mb-1">Kategori</label>
                <select name="category_id" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
                    <?php while ($c = $cats->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <div>
            <label class="block font-semibold mb-1">Deskripsi</label>
            <textarea name="description" rows="3" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200"></textarea>
        </div>
        <?php if ($_SESSION['role'] == 'super_admin'): ?>
            <div>
                <label class="block font-semibold mb-1">Pilih OPD</label>
                <select name="opd_id" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
                    <?php while ($o = $opds->fetch_assoc()): ?>
                        <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        <?php endif; ?>
        <div>
            <label class="block font-semibold mb-1">File</label>
            <input type="file" name="file" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 cursor-pointer focus:outline-none focus:ring focus:ring-blue-200">
        </div>
        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="buat_berita" id="buatBeritaCheck" value="1"
                    class="h-4 w-4 text-sky-600 rounded border-gray-300 focus:ring-sky-500">
                <span class="text-sm font-semibold text-slate-700">Buat juga sebagai Berita / Info Serta-Merta</span>
            </label>
            <p class="text-xs text-slate-500 mt-1">Jika dicentang, dokumen ini akan otomatis terpublish sebagai berita.
            </p>
        </div>

        <div id="beritaFields" class="hidden space-y-4 border border-slate-200 rounded-xl p-4 bg-slate-50/50">
            <p class="text-xs font-bold text-sky-700 uppercase tracking-wider mb-3">Data Berita (Opsional)</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Judul Berita</label>
                    <input type="text" name="berita_judul" id="beritaJudul" placeholder="Sama dengan judul dokumen"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Penulis</label>
                    <input type="text" name="berita_penulis"
                        placeholder="<?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?>"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Isi Berita</label>
                <textarea name="berita_isi" rows="4" placeholder="Isi ringkas berita..."
                    class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"></textarea>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Gambar Berita</label>
                    <input type="file" name="berita_gambar" accept="image/*"
                        class="w-full border rounded-lg px-3 py-2 bg-gray-50 cursor-pointer focus:outline-none focus:ring focus:ring-blue-200">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal</label>
                    <input type="date" name="berita_tanggal" value="<?= date('Y-m-d') ?>"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                </div>
            </div>
        </div>
        <div>
            <label class="block font-semibold mb-1">Status</label>
            <select name="status"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
                <option value="publish">Publish</option>
                <option value="draft">Draft</option>
            </select>
        </div>
        <button type="submit" name="create"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition">
            <i class="fa-solid fa-upload mr-1"></i> Upload Dokumen
        </button>
    </form>
</div>

<!-- Tabel Dokumen -->
<div class="ml-6 mr-6 bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full border border-gray-200">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th class="px-4 py-3 text-left">Judul</th>
                <th class="px-4 py-3 text-left">Kategori</th>
                <th class="px-4 py-3 text-left">OPD</th>
                <th class="px-4 py-3 text-left">File</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Tanggal</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if ($docs && $docs->num_rows > 0): ?>
                <?php while ($d = $docs->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800"><?= htmlspecialchars($d['title']) ?></td>
                        <td class="px-4 py-3 text-sm"><?= htmlspecialchars($d['category_name'] ?? '-') ?></td>
                        <td class="px-4 py-3 text-sm"><?= htmlspecialchars($d['opd_name'] ?? '-') ?></td>
                        <td class="px-4 py-3">
                            <?php if ($d['berita_link']): ?>
                                <a href="/<?= htmlspecialchars($d['berita_link']) ?>" target="_blank"
                                    class="text-sky-600 hover:underline text-sm">
                                    <i class="fa-solid fa-link"></i> Lihat Link
                                </a>
                            <?php elseif ($d['file_path']): ?>
                                <a href="<?= htmlspecialchars($d['file_path']) ?>" target="_blank"
                                    class="text-blue-600 hover:underline text-sm">
                                    <i class="fa-solid fa-file"></i> Lihat
                                </a>
                            <?php else: ?>
                                <span class="text-gray-400 text-sm">Tidak ada</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-1 rounded-full text-sm font-semibold 
                                <?= $d['status'] === 'publish' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                <?= htmlspecialchars($d['status']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            <?= date('d M Y', strtotime($d['created_at'])) ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button
                                onclick="openEditModal(<?= $d['id'] ?>, '<?= htmlspecialchars($d['title'], ENT_QUOTES) ?>', '<?= htmlspecialchars($d['description'], ENT_QUOTES) ?>', '<?= htmlspecialchars($d['file_path'] ?? '', ENT_QUOTES) ?>', <?= $d['category_id'] ?>, <?= $d['opd_id'] ?>, '<?= $d['status'] ?>', '<?= htmlspecialchars($d['berita_link'] ?? '', ENT_QUOTES) ?>')"
                                class="text-blue-600 hover:text-blue-800 mr-2 text-xs font-semibold" title="Edit">
                                <i class="fa-solid fa-edit"></i> Edit
                            </button>
                            <a href="?delete=<?= $d['id'] ?>" onclick="return confirm('Yakin ingin menghapus dokumen ini?')"
                                class="text-red-600 hover:text-red-800 text-xs font-semibold" title="Hapus">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">Tidak ada dokumen</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Edit Dokumen -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Edit Dokumen</h2>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Form Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <form method="POST" enctype="multipart/form-data" id="editForm">
                <input type="hidden" name="id" id="editId">
                <div class="space-y-4">
                    <div>
                        <label class="block font-semibold mb-1">Judul</label>
                        <input type="text" name="title" id="editTitle" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Deskripsi</label>
                        <textarea name="description" id="editDescription" rows="3" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold mb-1">Kategori</label>
                            <select name="category_id" id="editCategory" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <?php
                                $cats->data_seek(0); // Reset pointer
                                while ($c = $cats->fetch_assoc()): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">OPD</label>
                            <select name="opd_id" id="editOpd"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <?php
                                $opds->data_seek(0); // Reset pointer
                                while ($o = $opds->fetch_assoc()): ?>
                                    <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">File Baru (Opsional)</label>
                        <input type="file" name="file" id="editFile"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 cursor-pointer">
                        <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengganti file. File saat ini:
                            <a href="<?= htmlspecialchars($d['file_path'] ?? '') ?>" target="_blank"
                                class="text-blue-600 hover:underline">Lihat</a>
                        </p>
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">Status</label>
                        <select name="status" id="editStatus"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="publish">Publish</option>
                            <option value="draft">Draft</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="buat_berita" id="editBuatBerita" value="1"
                            class="h-4 w-4 text-sky-600 rounded border-gray-300 focus:ring-sky-500">
                        <span class="text-sm font-semibold text-slate-700">Tautkan ke Berita / Info Serta-Merta</span>
                    </label>
                </div>

                <div id="editBeritaFields"
                    class="hidden space-y-4 border border-slate-200 rounded-xl p-4 bg-slate-50/50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Judul Berita</label>
                            <input type="text" name="berita_judul" id="editBeritaJudul"
                                placeholder="Sama dengan judul dokumen"
                                class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Penulis</label>
                            <input type="text" name="berita_penulis" id="editBeritaPenulis" placeholder="Nama penulis"
                                class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Isi Berita</label>
                        <textarea name="berita_isi" id="editBeritaIsi" rows="4" placeholder="Isi ringkas berita..."
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Gambar Berita</label>
                            <input type="file" name="berita_gambar" id="editBeritaGambar" accept="image/*"
                                class="w-full border rounded-lg px-3 py-2 bg-gray-50 cursor-pointer">
                            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengganti gambar.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Tanggal</label>
                            <input type="date" name="berita_tanggal" id="editBeritaTanggal"
                                class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeModal()"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" name="update"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fa-solid fa-save mr-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Modal functions
    function openEditModal(id, title, description, filePath, categoryId, opdId, status, beritaLink) {
        document.getElementById('editId').value = id;
        document.getElementById('editTitle').value = title;
        document.getElementById('editDescription').value = description;
        document.getElementById('editCategory').value = categoryId;
        document.getElementById('editOpd').value = opdId;
        document.getElementById('editStatus').value = status;

        const buatBeritaCheck = document.getElementById('editBuatBerita');
        const beritaFields = document.getElementById('editBeritaFields');

        if (beritaLink && beritaLink.trim() !== '' && beritaLink.includes('detail-berita.php?id=')) {
            buatBeritaCheck.checked = true;
            beritaFields.classList.remove('hidden');
        } else {
            buatBeritaCheck.checked = false;
            beritaFields.classList.add('hidden');
        }

        document.getElementById('editFile').value = '';

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    document.getElementById('editBuatBerita')?.addEventListener('change', function () {
        const fields = document.getElementById('editBeritaFields');
        if (this.checked) {
            fields.classList.remove('hidden');
        } else {
            fields.classList.add('hidden');
        }
    });

    function closeModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close modal when clicking outside
    document.getElementById('editModal')?.addEventListener('click', function (e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('editModal');
            if (!modal.classList.contains('hidden')) {
                closeModal();
            }
        }
    });

    document.getElementById('buatBeritaCheck')?.addEventListener('change', function () {
        const fields = document.getElementById('beritaFields');
        if (this.checked) {
            fields.classList.remove('hidden');
        } else {
            fields.classList.add('hidden');
        }
    });
</script>

<?php include("../admin/footer.php"); ?>