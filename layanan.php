<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('config/database.php');

// Ambil data layanan dari database
$layanan = $conn->query("SELECT * FROM layanan ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Layanan Informasi PPID</title>
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
            transform: translateY(40px);
            transition: opacity 0.7s ease, transform 0.7s ease;
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
    <!-- Navbar -->
    <?php include('navbar.php'); ?>

    <!-- Hero -->
    <header class="relative h-60 md:h-72 w-full bg-center bg-cover"
        style="background-image:url('assets/img/cover-layanan.jpg');">
        <div class="absolute inset-0 bg-sky-900/40"></div>
        <div class="absolute inset-0 flex items-center">
            <div class="container-wide mx-auto px-4 fade-section">
                <h1 class="text-white text-3xl md:text-4xl font-extrabold tracking-wide">LAYANAN INFORMASI PPID</h1>
            </div>
        </div>
    </header>

    <!-- Main content -->
    <main class="max-w-6xl mx-auto px-4 py-10 space-y-12">

        <!-- Search & Filter -->
        <?php
        $sb_target_id = 'layanan-container';
        $sb_search_endpoint = 'search-layanan.php';
        $sb_filter_endpoint = 'filter-layanan.php';
        $sb_filter_fields = [
            'sumber' => [
                'label' => 'Sumber',
                'type' => 'select',
                'options' => [
                    '' => 'Semua',
                    'Dinas Kominfo' => 'Dinas Kominfo',
                    'PPID Utama' => 'PPID Utama',
                    'PPID Pembantu' => 'PPID Pembantu'
                ]
            ],
            'sort' => [
                'label' => 'Urutkan',
                'type' => 'select',
                'options' => [
                    'desc' => 'Terbaru',
                    'asc' => 'Terlama'
                ]
            ]
        ];
        include('searchbar.php');
        ?>

        <!-- Cards Layanan -->
        <section class="container-wide mx-auto px-4 pb-12 fade-section">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="layanan-container">
                <?php if ($layanan->num_rows > 0): ?>
                    <?php while ($row = $layanan->fetch_assoc()): ?>
                        <article class="rounded-xl border border-slate-200 overflow-hidden bg-white hover:shadow-md transition">
                            <div class="aspect-[4/3] bg-slate-200 grid place-content-center text-slate-500">
                            <img src="uploads/layanan/<?= $row['gambar'] ?>" alt="<?= $row['judul'] ?>"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="p-5 space-y-3">
                            <h3 class="font-semibold text-lg"><?= $row['judul'] ?></h3>
                            <p class="text-sm text-slate-600"><?= substr($row['deskripsi'], 0, 100) ?>...</p>
                            <div class="flex items-center justify-between pt-2 text-xs">
                                <div class="flex items-center gap-2">
                                    <!-- <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Lambang_Kabupaten_Bondowoso.png/40px-Lambang_Kabupaten_Bondowoso.png"
                                        class="w-5 h-5" alt="PPID" /> -->
                                    <span class="font-medium"><?= $row['sumber'] ?></span>
                                </div>
                                <a href="detail-layanan.php?id=<?= $row['id'] ?>"
                                    class="text-sky-600 hover:underline">Selengkapnya</a>
                            </div>
                        </div>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="col-span-3 text-center text-slate-500 py-10">Belum ada layanan tersedia.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Form Section -->
        <section id="layanan" class="container-wide mx-auto px-4 pb-20 fade-section">
            <div class="flex items-center justify-center mb-6">
                <div class="relative inline-block">
                    <button id="formSwitcher"
                        class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 rounded-lg bg-white hover:bg-slate-50 text-sm">
                        <!-- <span id="formTitle">Form Permohonan Informasi</span> -->
                        <!-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                        <path fill-rule="evenodd"
                            d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 10.94l3.71-3.71a.75.75 0 1 1 1.06 1.06l-4.24 4.25a.75.75 0 0 1-1.06 0L5.21 8.29a.75.75 0 0 1 .02-1.08Z"
                            clip-rule="evenodd" />
                    </svg> -->
                    </button>
                    <div id="formMenu">
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php include('footer.php'); ?>

    <script src="assets/js/scripts.js"></script>
    <script src="assets/js/searchbar.js"></script>
</body>

</html>