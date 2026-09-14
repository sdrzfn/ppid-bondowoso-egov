<?php
include('config/database.php');
// ambil kategori & opd untuk dropdown
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
$opds = $conn->query("SELECT id, name FROM opd ORDER BY name");

// ambil input filter
$search = $_GET['search'] ?? '';
$filter_cat = $_GET['category'] ?? '';
$filter_opd = $_GET['opd'] ?? '';

// query dokumen
$sql = "SELECT d.id, d.title, d.file_path, d.status, d.created_at, 
               o.name AS opd_name, c.name AS category_name
        FROM documents d
        LEFT JOIN opd o ON d.opd_id = o.id
        LEFT JOIN categories c ON d.category_id = c.id
        WHERE d.status = 'publish'
        AND d.category_id != 4";

if ($search !== '') {
    $search_safe = $conn->real_escape_string($search);
    $sql .= " AND d.title LIKE '%$search_safe%'";
}
if ($filter_cat !== '') {
    $sql .= " AND d.category_id = " . intval($filter_cat);
}
if ($filter_opd !== '') {
    $sql .= " AND d.opd_id = " . intval($filter_opd);
}

$sql .= " ORDER BY d.created_at DESC";
$docs = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Informasi PPID</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="assets/img/bondowoso.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --brand: #0EA5E9;
            --brand-dark: #0284C7;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, 'Apple Color Emoji', 'Segoe UI Emoji';
        }

        .container-wide {
            max-width: 1200px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-section {
            opacity: 0;
            animation: fadeIn 0.8s ease forwards;
        }

        .fade-section {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .fade-section.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<script src="assets/js/speech-consent.js"></script>
<script src="https://cdn.userway.org/widget.js" data-account="d9ZmCPKv7k"></script>

<body class="bg-white text-slate-800">

    <?php include('navbar.php'); ?>

    <!-- Hero -->
    <header class="relative h-60 md:h-72 w-full bg-center bg-cover"
        style="background-image:url('assets/img/cover-informasi.jpg');">
        <div class="absolute inset-0 bg-sky-900/40"></div>
        <div class="absolute inset-0 flex items-center">
            <div class="container-wide mx-auto px-4 fade-section">
                <h1 class="text-white text-3xl md:text-4xl font-extrabold tracking-wide">INFORMASI PPID</h1>
            </div>
        </div>
    </header>

    <!-- Main content -->
    <main class="max-w-6xl mx-auto px-4 py-10 space-y-12">

        <!-- Tabel Informasi + Sidebar Berita -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-8 fade-section">
            <!-- Tabel -->
            <div class="lg:col-span-2">
                <form method="GET" class="mb-6 flex flex-wrap gap-4 items-end bg-white p-4 rounded-lg shadow">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold mb-1">Pencarian</label>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                            placeholder="Cari judul dokumen..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">Kategori</label>
                        <select name="category"
                            class="border border-gray-300 rounded-lg px-3 py-2 w-48 focus:outline-none focus:ring focus:ring-blue-200">
                            <option value="">Semua</option>
                            <?php while ($c = $categories->fetch_assoc()): ?>
                                <option value="<?= $c['id'] ?>" <?= $filter_cat == $c['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold mb-1">OPD</label>
                        <select name="opd"
                            class="border border-gray-300 rounded-lg px-3 py-2 w-48 focus:outline-none focus:ring focus:ring-blue-200">
                            <option value="">Semua</option>
                            <?php while ($o = $opds->fetch_assoc()): ?>
                                <option value="<?= $o['id'] ?>" <?= $filter_opd == $o['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($o['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg shadow">
                            Terapkan
                        </button>
                    </div>
                </form>
                <h2 class="font-semibold mb-3">Tabel Informasi</h2>
                <div class="overflow-hidden rounded-xl border border-slate-300">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-100">
                            <tr class="text-left">
                                <th class="w-16 px-4 py-3 border-r border-slate-300">No</th>
                                <th class="px-4 py-3 border-r border-slate-300">Nama Informasi</th>
                                <th class="px-4 py-3 border-r border-slate-300">Kategori</th>
                                <th class="px-4 py-3 border-r border-slate-300">OPD</th>
                                <th class="w-32 px-4 py-3 border-r border-slate-300">Unduh</th>
                                <th class="w-28 px-4 py-3">Lihat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($docs && $docs->num_rows > 0): ?>
                                <?php $no = 1;
                                while ($d = $docs->fetch_assoc()): ?>
                                    <tr class="<?= $no % 2 === 0 ? 'bg-white' : 'bg-slate-50' ?>">
                                        <td class="px-4 py-3 border-t border-slate-200"><?= $no++ ?></td>
                                        <td class="px-4 py-3 border-t border-slate-200 font-medium text-gray-800">
                                            <?= htmlspecialchars($d['title']) ?>
                                        </td>
                                        <td class="px-4 py-3 border-t border-slate-200">
                                            <?= htmlspecialchars($d['category_name'] ?? '-') ?>
                                        </td>
                                        <td class="px-4 py-3 border-t border-slate-200">
                                            <?= htmlspecialchars($d['opd_name'] ?? '-') ?>
                                        </td>
                                        <td class="px-4 py-3 border-t border-slate-200">
                                            <a href="<?= htmlspecialchars($d['file_path']) ?>" download
                                                class="inline-block px-3 py-1 rounded bg-sky-600 text-white hover:bg-sky-700">
                                                Unduh
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 border-t border-slate-200">
                                            <a href="<?= htmlspecialchars($d['file_path']) ?>" target="_blank"
                                                class="text-sky-600 hover:underline">Lihat</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                        Belum ada dokumen yang tersedia atau <br />Informasi termasuk ke dalam kategori yang
                                        dikecualikan <br /><br />
                                        <a href="ticket-form.php" class="inline-block px-3 py-1 rounded bg-sky-600 text-white hover:bg-sky-700">
                                            Ajukan Permohonan
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sidebar Berita -->
            <aside class="lg:col-span-1">
                <div class="rounded-xl border border-slate-300 p-4 bg-white shadow">
                    <h3 class="font-semibold mb-4">Berita Terbaru</h3>
                    <div class="h-1 w-24 bg-sky-600 mb-4"></div>

                    <ul class="space-y-4">
                        <?php
                        // Ambil 5 berita terbaru
                        $berita = $conn->query("SELECT * FROM berita ORDER BY tanggal DESC LIMIT 5");
                        if ($berita && $berita->num_rows > 0):
                            while ($b = $berita->fetch_assoc()):
                                ?>
                                <li class="flex gap-3 items-start">
                                    <div class="w-20 h-14 overflow-hidden rounded bg-slate-200 flex-shrink-0">
                                        <img src="<?= htmlspecialchars($b['gambar']); ?>"
                                            alt="<?= htmlspecialchars($b['judul']); ?>" class="w-full h-full object-cover">
                                    </div>
                                    <div class="text-sm flex-1">
                                        <a href="detail-berita.php?id=<?= $b['id']; ?>"
                                            class="font-medium leading-snug text-gray-800 hover:text-sky-600 line-clamp-2">
                                            <?= htmlspecialchars($b['judul']); ?>
                                        </a>
                                        <p class="text-slate-500 text-xs mt-1 line-clamp-2">
                                            <?= substr(strip_tags($b['isi']), 0, 60); ?>...
                                            <a href="detail-berita.php?id=<?= $b['id']; ?>" class="underline">more</a>
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            <?= htmlspecialchars($b['penulis']); ?> ·
                                            <?= date('d M Y', strtotime($b['tanggal'])); ?>
                                        </p>
                                    </div>
                                </li>
                            <?php endwhile; else: ?>
                            <li class="text-gray-500 text-sm">Belum ada berita tersedia</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </aside>

        </section>

    </main>

    <?php include('footer.php'); ?>

    <script src="assets/js/scripts.js"></script>
</body>

</html>