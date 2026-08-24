<?php
include("config/database.php");
include("config/helper.php");
$sb_target_id = 'cardsContainer';
$sb_search_endpoint = 'search-index.php';
$sb_filter_endpoint = 'filter-index.php';
$sb_filter_fields = [
    'status' => [
        'label' => 'Status',
        'type' => 'select',
        'options' => [
            '' => 'Semua',
            'aktif' => 'Aktif',
            'nonaktif' => 'Nonaktif'
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
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PPID Kabupaten Bondowoso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="icon" href="assets/img/bondowoso.ico">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --brand: #0EA5E9;
            --brand-dark: #0284C7;
            --font-size-base: 16px;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, 'Apple Color Emoji', 'Segoe UI Emoji';
            font-size: var(--font-size-base);
        }

        .container-wide {
            max-width: 1200px;
        }

        body.high-contrast {
            --brand: #0000FF;
            --brand-dark: #0000FF;
        }

        body.high-contrast .bg-slate-50 {
            background-color: #000 !important;
            color: #FFF !important;
        }

        body.high-contrast .bg-white {
            background-color: #000 !important;
            color: #FFF !important;
            border-color: #FFF !important;
        }

        body.high-contrast .text-slate-800 {
            color: #FFF !important;
        }

        body.high-contrast .text-slate-600 {
            color: #FFF !important;
        }

        body.high-contrast .border-slate-200 {
            border-color: #FFF !important;
        }

        body.high-contrast .bg-slate-900 {
            background-color: #000 !important;
        }

        body.font-size-medium {
            --font-size-base: 18px;
        }

        body.font-size-large {
            --font-size-base: 20px;
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
            opacity: 1;
            transform: none;
            transition: none;
        }

        .fade-section.animate-ready {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .fade-section.animate-ready.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Marquee / Scrolling Text Banner */
        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .marquee-container {
            overflow: hidden;
            white-space: nowrap;
        }

        .marquee-content {
            display: inline-block;
            animation: marquee 20s linear infinite;
        }

        .marquee-content:hover {
            animation-play-state: paused;
        }
    </style>
</head>

<body class="bg-white text-slate-800">

    <?php include('navbar.php'); ?>

    <!-- ==================== ALERT BANNER: SERTA-MERTA ==================== -->
    <div id="sertaMertaBanner" class="bg-amber-50 border-y border-amber-200/80 overflow-hidden">
        <div class="marquee-container py-3">
            <div class="marquee-content">
                <span class="inline-flex items-center gap-2 mx-4">
                    <span
                        class="flex-shrink-0 bg-amber-600 text-white px-2 py-0.5 rounded font-bold uppercase text-[10px] tracking-wider">
                        Serta-Merta
                    </span>
                    <span class="font-medium text-amber-900 text-xs sm:text-sm">
                        <span class="font-semibold">Peringatan Dini Cuaca & Potensi Bencana:</span> Informasi tanggap
                        darurat dan nomor kontak bantuan darurat 24 Jam.
                    </span>
                </span>
                <!-- Duplicate for seamless loop -->
                <span class="inline-flex items-center gap-2 mx-4">
                    <span
                        class="flex-shrink-0 bg-amber-600 text-white px-2 py-0.5 rounded font-bold uppercase text-[10px] tracking-wider">
                        Serta-Merta
                    </span>
                    <span class="font-medium text-amber-900 text-xs sm:text-sm">
                        <span class="font-semibold">Peringatan Dini Cuaca & Potensi Bencana:</span> Informasi tanggap
                        darurat dan nomor kontak bantuan darurat 24 Jam.
                    </span>
                </span>
            </div>
        </div>
    </div>

    <!-- ==================== HERO SECTION ==================== -->
    <section
        class="relative bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 text-white py-20 px-4 sm:px-6 lg:px-8 overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center opacity-20"
            style="background-image: url('assets/img/cover-index.jpg');"></div>
        <div class="max-w-4xl mx-auto text-center relative z-10">
            <!-- Badges Kepatuhan Hukum -->
            <div
                class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/10 border border-blue-400/20 text-blue-300 text-xs font-semibold mb-6">
                <i class="fa-solid fa-shield-halved text-blue-400"></i>
                <span>Implementasi UU No. 14 Tahun 2008 Keterbukaan Informasi Publik</span>
            </div>

            <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight mb-4">
                Layanan Keterbukaan & Hak Informasi Publik
            </h1>
            <p class="text-base sm:text-lg text-slate-300 font-normal max-w-2xl mx-auto mb-10 leading-relaxed">
                Akses dokumen resmi, laporan kinerja, dan ajukan permohonan informasi publik secara transparan,
                akuntabel, dan terukur.
            </p>

            <!-- GLOBAL SEARCH BAR (Hero Overlay) -->
            <div
                class="bg-white/95 backdrop-blur-sm p-2 sm:p-2.5 rounded-2xl shadow-2xl max-w-3xl mx-auto text-slate-800 flex flex-col sm:flex-row items-center gap-2 border border-slate-100">
                <div class="relative w-full flex items-center pl-3">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-lg mr-3"></i>
                    <input type="text" id="heroSearch"
                        placeholder="Cari Dokumen, Laporan Keuangan, RKA, DIPA, atau SOP..."
                        class="w-full bg-transparent py-2.5 text-sm sm:text-base text-slate-800 placeholder-slate-400 focus:outline-none font-medium">
                </div>
                <button onclick="heroSearch()"
                    class="w-full sm:w-auto px-6 py-3.5 bg-sky-700 hover:bg-sky-800 text-white font-semibold text-sm rounded-xl shadow-md transition flex items-center justify-center gap-2 whitespace-nowrap">
                    <span>Cari Informasi</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>

            <!-- QUICK ACTION CARDS / POPULAR TAGS -->
            <div class="mt-8 flex flex-wrap justify-center items-center gap-2 text-xs text-slate-400">
                <span class="font-semibold text-slate-300">Pencarian Populer:</span>
                <a href="informasi.php?search=Laporan+Keuangan+2025"
                    class="px-3 py-1 bg-slate-800/80 hover:bg-slate-700 border border-slate-700 rounded-lg text-slate-300 transition">Laporan
                    Keuangan 2025</a>
                <a href="informasi.php?search=DIPA"
                    class="px-3 py-1 bg-slate-800/80 hover:bg-slate-700 border border-slate-700 rounded-lg text-slate-300 transition">DIPA
                    / RKA</a>
                <a href="informasi.php?search=Pengadaan"
                    class="px-3 py-1 bg-slate-800/80 hover:bg-slate-700 border border-slate-700 rounded-lg text-slate-300 transition">Pengadaan
                    Barang & Jasa</a>
            </div>
        </div>
    </section>

    <!-- ==================== KATALOG INFORMASI PUBLIK SECTION ==================== -->
    <section id="katalog-informasi" class="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto fade-section">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10">
            <div>
                <div class="text-xs font-bold uppercase tracking-wider text-sky-700 mb-1">Klasifikasi Legal</div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Katalog Informasi Publik
                    (DIP)</h2>
                <p class="text-slate-600 text-sm mt-1">Daftar Informasi Publik yang dikategorikan berdasarkan ketentuan
                    UU No. 14 Tahun 2008.</p>
            </div>
        </div>

        <!-- 4 CLASSIFICATION CARDS GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- CARD 1: INFORMASI BERKALA (PASAL 9) -->
            <a href="informasi.php?category=berkala"
                class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                <div>
                    <div
                        class="w-12 h-12 bg-sky-50 text-sky-700 rounded-xl flex items-center justify-center font-bold text-xl mb-5 group-hover:bg-sky-700 group-hover:text-white transition">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <span
                            class="text-[11px] font-extrabold uppercase tracking-wider text-sky-700 bg-sky-50 px-2 py-0.5 rounded">Pasal
                            9 UU KIP</span>
                        <span class="text-xs font-semibold text-slate-400">
                            <?php
                            $count_berkala = $conn->query("SELECT COUNT(*) AS jml FROM documents d LEFT JOIN categories c ON d.category_id = c.id WHERE c.name LIKE '%berkala%' OR d.status = 'publish'")->fetch_assoc()['jml'];
                            echo $count_berkala . ' Dokumen';
                            ?>
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Informasi Berkala</h3>
                    <p class="text-slate-600 text-xs leading-relaxed mb-4">
                        Informasi yang diperbarui dan diumumkan secara rutin minimal 6 bulan sekali (Profil, Laporan
                        Keuangan, LAKIP).
                    </p>
                </div>
                <div
                    class="inline-flex items-center justify-between text-xs font-bold text-slate-700 border-t border-slate-100 pt-4 group-hover:text-sky-700 transition">
                    <span>Jelajahi Dokumen</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            <!-- CARD 2: INFORMASI SERTA-MERTA (PASAL 10) -->
            <a href="informasi.php?category=serta-merta"
                class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                <div>
                    <div
                        class="w-12 h-12 bg-amber-50 text-amber-700 rounded-xl flex items-center justify-center font-bold text-xl mb-5 group-hover:bg-amber-600 group-hover:text-white transition">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <span
                            class="text-[11px] font-extrabold uppercase tracking-wider text-amber-700 bg-amber-50 px-2 py-0.5 rounded">Pasal
                            10 UU KIP</span>
                        <span class="text-xs font-semibold text-slate-400">
                            <?php
                            $count_serta = $conn->query("SELECT COUNT(*) AS jml FROM documents d LEFT JOIN categories c ON d.category_id = c.id WHERE c.name LIKE '%serta%' OR c.name LIKE '%darurat%'")->fetch_assoc()['jml'];
                            echo $count_serta . ' Dokumen';
                            ?>
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Informasi Serta-Merta</h3>
                    <p class="text-slate-600 text-xs leading-relaxed mb-4">
                        Informasi yang dapat mengancam hajat hidup orang banyak dan ketertiban umum yang wajib diumumkan
                        seketika.
                    </p>
                </div>
                <div
                    class="inline-flex items-center justify-between text-xs font-bold text-slate-700 border-t border-slate-100 pt-4 group-hover:text-amber-700 transition">
                    <span>Jelajahi Dokumen</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            <!-- CARD 3: INFORMASI SETIAP SAAT (PASAL 11) -->
            <a href="informasi.php?category=setiap-saat"
                class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                <div>
                    <div
                        class="w-12 h-12 bg-emerald-50 text-emerald-700 rounded-xl flex items-center justify-center font-bold text-xl mb-5 group-hover:bg-emerald-600 group-hover:text-white transition">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <span
                            class="text-[11px] font-extrabold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">Pasal
                            11 UU KIP</span>
                        <span class="text-xs font-semibold text-slate-400">
                            <?php
                            $count_setiap = $conn->query("SELECT COUNT(*) AS jml FROM documents d LEFT JOIN categories c ON d.category_id = c.id WHERE c.name LIKE '%setiap%' OR c.name LIKE '%sop%' OR c.name LIKE '%rencana%'")->fetch_assoc()['jml'];
                            echo $count_setiap . ' Dokumen';
                            ?>
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Informasi Setiap Saat</h3>
                    <p class="text-slate-600 text-xs leading-relaxed mb-4">
                        Informasi yang telah dikuasai dan siap diberikan kapan saja saat dimohonkan oleh publik (SOP,
                        Rencana Kerja).
                    </p>
                </div>
                <div
                    class="inline-flex items-center justify-between text-xs font-bold text-slate-700 border-t border-slate-100 pt-4 group-hover:text-emerald-700 transition">
                    <span>Jelajahi Dokumen</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

            <!-- CARD 4: INFORMASI DIKECUALIKAN (PASAL 17) -->
            <a href="informasi.php?category=dikecualikan"
                class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col justify-between group">
                <div>
                    <div
                        class="w-12 h-12 bg-rose-50 text-rose-700 rounded-xl flex items-center justify-center font-bold text-xl mb-5 group-hover:bg-rose-700 group-hover:text-white transition">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <span
                            class="text-[11px] font-extrabold uppercase tracking-wider text-rose-700 bg-rose-50 px-2 py-0.5 rounded">Pasal
                            17 UU KIP</span>
                        <span class="text-xs font-semibold text-slate-400">Uji Konsekuensi</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">Informasi Dikecualikan</h3>
                    <p class="text-slate-600 text-xs leading-relaxed mb-4">
                        Informasi rahasia negara/pribadi yang dibatasi berdasarkan Uji Konsekuensi dan ketetapan Hukum.
                    </p>
                </div>
                <div
                    class="inline-flex items-center justify-between text-xs font-bold text-slate-700 border-t border-slate-100 pt-4 group-hover:text-rose-700 transition">
                    <span>Lihat Hasil Uji</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </div>
            </a>

        </div>
    </section>

    <!-- ==================== LIVE STATISTIK METRICS ==================== -->
    <section id="statistik" class="bg-slate-100 border-y border-slate-200 py-12 px-4 sm:px-6 lg:px-8 fade-section">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-8">
                <h3 class="text-xl font-bold text-slate-900">Statistik Transparansi & Kecepatan Layanan</h3>
                <p class="text-xs text-slate-500 mt-1">Data real-time pemrosesan permohonan informasi sesuai SLA UU KIP
                    (10 + 7 Hari Kerja)</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="bg-white p-5 rounded-xl border border-slate-200/60 shadow-sm">
                    <div class="text-2xl sm:text-3xl font-extrabold text-sky-700 mb-1">
                        <?php
                        $total_permohonan = $conn->query("SELECT COUNT(*) AS jml FROM tickets")->fetch_assoc()['jml'];
                        echo number_format($total_permohonan, 0, ',', '.');
                        ?>
                    </div>
                    <div class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Total Permohonan</div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-slate-200/60 shadow-sm">
                    <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mb-1">
                        <?php
                        $total_selesai = $conn->query("SELECT COUNT(*) AS jml FROM tickets WHERE status = 'selesai'")->fetch_assoc()['jml'];
                        $persentase = $total_permohonan > 0 ? round(($total_selesai / $total_permohonan) * 100, 1) : 0;
                        echo $persentase . '%';
                        ?>
                    </div>
                    <div class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Dikabulkan</div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-slate-200/60 shadow-sm">
                    <div class="text-2xl sm:text-3xl font-extrabold text-amber-600 mb-1">
                        <?php
                        // Hitung rata-rata hari proses
                        $rata2 = $conn->query("SELECT AVG(DATEDIFF(updated_at, created_at)) as avg_days FROM tickets WHERE status = 'selesai'")->fetch_assoc()['avg_days'];
                        echo $rata2 ? round($rata2, 1) . ' Hari' : '0 Hari';
                        ?>
                    </div>
                    <div class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Rata-Rata Respons</div>
                </div>
                <div class="bg-white p-5 rounded-xl border border-slate-200/60 shadow-sm">
                    <div class="text-2xl sm:text-3xl font-extrabold text-indigo-600 mb-1">
                        <?php
                        $sengketa = $conn->query("SELECT COUNT(*) AS jml FROM tickets WHERE form_type = 'keberatan'")->fetch_assoc()['jml'];
                        echo $sengketa;
                        ?>
                    </div>
                    <div class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Keberatan</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Hubungi Kami Button -->
    <div class="flex justify-center py-6">
        <a href="https://wa.me/6285784283713"
            class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-full flex items-center gap-2">
            <i class="fab fa-whatsapp"></i> Hubungi Kami
        </a>
    </div>

    <?php include('footer.php'); ?>

    <script src="assets/js/scripts.js"></script>
    <script src="assets/js/searchbar.js"></script>
    <script src="assets/js/speech-consent.js"></script>
    <script src="https://cdn.userway.org/widget.js" data-account="d9ZmCPKv7k"></script>

    <!-- Accessibility & Font Size Scripts -->
    <script>
        function toggleHighContrast() {
            document.body.classList.toggle('high-contrast');
        }

        function changeFontSize(size) {
            document.body.classList.remove('font-size-medium', 'font-size-large');
            if (size === 'medium') {
                document.body.classList.add('font-size-medium');
            } else if (size === 'large') {
                document.body.classList.add('font-size-large');
            }
        }

        function heroSearch() {
            const query = document.getElementById('heroSearch').value.trim();
            if (query) {
                window.location.href = 'informasi.php?search=' + encodeURIComponent(query);
            }
        }

        document.getElementById('heroSearch')?.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                heroSearch();
            }
        });

        // Navbar Scroll Behavior
        let lastScroll = 0;
        const topBar = document.getElementById('topBar');
        const mainNavbar = document.getElementById('mainNavbar');

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;

            if (currentScroll > 50) {
                topBar.style.transform = 'translateY(-100%)';
                topBar.style.opacity = '0';
                mainNavbar.classList.add('top-0');
                mainNavbar.classList.remove('top-[32px]', 'sm:top-[33px]');
            } else {
                topBar.style.transform = 'translateY(0)';
                topBar.style.opacity = '1';
                mainNavbar.classList.remove('top-0');
                mainNavbar.classList.add('top-[32px]', 'sm:top-[33px]');
            }

            lastScroll = currentScroll;
        });
    </script>

</body>

</html>