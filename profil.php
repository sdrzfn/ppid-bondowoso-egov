<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('config/database.php');
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil PPID</title>
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

        .navbar {
            display: flex;
            justify-content: space-between;
            background: #f5b7a1;
            padding: 15px 30px;
        }

        .navbar ul {
            display: flex;
            list-style: none;
            gap: 20px;
        }

        .hero {
            height: 250px;
            background: url('') center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 32px;
            font-weight: bold;
        }

        .cover {
            text-align: center;
            padding: 40px 0;
        }

        .cover img,
        .struktur img {
            width: 80%;
            max-width: 600px;
            height: 300px;
            background: #ddd;
            display: block;
            margin: auto;
        }

        .content {
            display: flex;
            justify-content: space-between;
            padding: 50px;
            gap: 30px;
        }

        .content .main-text {
            flex: 2;
        }

        .content .sidebar {
            flex: 1;
            background: #fff;
            border: 1px solid #ccc;
            padding: 15px;
        }

        .struktur {
            text-align: center;
            padding: 50px;
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
    <!-- Top Bar -->
    <header class="w-full border-b border-slate-200 bg-rose-100/70">
        <?php include('navbar.php'); ?>
        <button id="navToggle"
            class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg hover:bg-white/50">
            <span class="sr-only">Menu</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                <path d="M3.75 5.25h16.5v1.5H3.75zM3.75 11.25h16.5v1.5H3.75zM3.75 17.25h16.5v1.5H3.75z" />
            </svg>
        </button>
        <div id="mobileNav" class="md:hidden hidden border-t border-slate-200 bg-white">
            <div class="container-wide mx-auto px-4 py-3 grid gap-2">
                <a href="#profil" class="py-2">Profil</a>
                <a href="#layanan" class="py-2">Layanan</a>
                <a href="#informasi" class="py-2">Informasi</a>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <section class="relative">
        <img src="assets/img/cover-profil.jpg" alt="Hero" class="w-full h-[280px] md:h-[360px] object-cover" />
        <div class="absolute inset-0 bg-sky-900/40"></div>
        <div class="absolute inset-0 flex items-center">
            <div class="container-wide mx-auto px-4 fade-section">
                <h1 class="text-white text-2xl md:text-4xl font-extrabold drop-shadow-sm leading-snug uppercase">PROFIL
                    PPID Kabupaten Bondowoso
                </h1>
            </div>
        </div>
    </section>

    <!-- Cards -->
    <section class="container mx-auto px-4 py-12 fade-section">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Card Kiri: Picture -->
            <div class="lg:col-span-2">
                <div class="bg-slate-100 rounded-lg shadow-md overflow-hidden h-80 flex items-center justify-center">
                    <img src="assets/img/cover-profil.jpg" alt="Profil PPID" class="w-full h-full object-cover" />
                </div>
            </div>

            <!-- Sidebar Berita -->
            <aside>
                <div class="rounded-xl border border-slate-300 p-5 bg-white shadow">
                    <h3 class="font-semibold mb-1 text-lg">Berita</h3>
                    <div class="h-1 w-16 bg-sky-600 mb-4"></div>

                    <ul class="space-y-4">
                        <?php
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
                                        <p class="text-slate-500 text-xs mt-1">
                                            <?= substr(strip_tags($b['isi']), 0, 60); ?>...
                                            <a href="detail-berita.php?id=<?= $b['id']; ?>"
                                                class="underline text-sky-600">more</a>
                                        </p>
                                    </div>
                                </li>
                            <?php endwhile; else: ?>
                            <li class="text-gray-500 text-sm">Belum ada berita tersedia</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </section>

    <!-- Sejarah PPID -->
    <section class="py-12 fade-section">
        <div class="container mx-auto px-4 max-w-5xl">
            <h2 class="text-2xl font-bold mb-6 pb-2 border-b-2 border-sky-600 inline-block">Sejarah Kabupaten Bondowoso
            </h2>
            <p class="text-gray-700 leading-relaxed text-justify">
                Sejarah Kabupaten Bondowoso bermula dari pembukaan hutan (wanawasa) oleh Raden Bagus Asra (Mas Ngabehi
                Astro Truno) pada awal abad ke-19. Wilayah ini resmi berdiri dan lepas dari Besuki pada 17 Agustus 1819,
                yang saat ini diperingati sebagai hari jadi kabupaten. Asal-usul Bondowoso berakar dari cucu penguasa
                Besuki, Raden Bagus Asra. Ia membuka kawasan hutan belukar yang kemudian berkembang menjadi pusat
                pemerintahan. Nama "Bondowoso" sendiri diyakini berasal dari kata wana yang berarti hutan dan wasa yang
                berarti tempat atau kekuasaan. Daerah yang terletak di kawasan Tapal Kuda ini juga dikenal sebagai
                wilayah multikultural yang didominasi oleh suku Jawa dan Madura.
            </p>
        </div>
    </section>

    <!-- Seputar PPID -->
    <section class="py-12 fade-section">
        <div class="container mx-auto px-4 max-w-5xl">
            <h2 class="text-2xl font-bold mb-6 pb-2 border-b-2 border-sky-600 inline-block">Seputar PPID</h2>
            <p class="text-gray-700 leading-relaxed text-justify">
                Ditetapkannya UU No. 14 tahun 2008 tentang Keterbukaan Informasi Publik yang bertujuan untuk mewujudkan
                tata kelola pemerintahan yang baik dan bertanggungjawab (good governance) melalui penerapan
                prinsip-prinsip akuntabilitas, transparansi dan supremasi hukum serta melibatkan partisipasi masyarakat
                dalam setiap proses kebijakan publik. <br><br>

                Dalam proses keterlibatan masyarakat perlu diakomodasikan dengan cara mempermudah jaminan akses
                informasi publik berdasarkan pedoman pengelolaan informasi dan dokumentasi. Dalam kaitan ini,
                pengelolaan informasi dan dokumentasi publik diharapkan tidak sampai mengganggu prinsip kehati-hatian
                dalam menjaga kelangsungan kehidupan berbangsa dan bernegara untuk kepentingan yang lebih luas. <br><br>

                Undang Undang No. 14 tahun 2008 tentang Keterbukaan Informasi Publik (KIP) mengamanatkan, setiap Badan
                Publik Pemerintah maupun Badan Publik Non Pemerintah mempunyai kewajiban untuk menyediakan Informasi
                Publik yang berada di bawah kewenangannya kepada masyarakat dengan cepat, aktual, tepat waktu, biaya
                ringan dan cara sederhana. <br><br>

                Untuk tujuan inilah setiap Badan Publik wajib menunjuk Pejabat Pengelola Informasi dan Dokumentasi
                (PPID), yang tugas pokok dan fungsinya adalah bertanggungjawab di bidang penyimpanan, pendokumentasian,
                penyediaan dan pelayanan informasi. PPID Kabupaten Bondowoso dibentuk dan ditetapkan dengan Surat
                Keputusan Bupati Bondowoso Nomor 188.45/285/430.4.2/2017, sedangkan Badan Publik / OPD di lingkungan
                Pemerintah Kabupaten Bondowoso sebagai PPID Pembantu di OPD ditetapkan dengan Surat Keputusan Kepala
                Badan Publik / OPD. <br><br>
            </p>
        </div>
    </section>

    <!-- Struktur Organisasi -->
    <section class="py-12 fade-section">
        <div class="container mx-auto px-4 max-w-5xl">
            <h2 class="text-2xl font-bold mb-6 pb-2 border-b-2 border-sky-600 inline-block">Struktur Organisasi PPID
            </h2>
            <div class="bg-slate-100 rounded-lg mb-6 overflow-hidden flex items-center justify-center"
                style="min-height: 300px; max-height: 500px;">
                <img src="assets/img/struktur-organisasi.jpg" alt="Struktur Organisasi PPID"
                    class="max-w-full max-h-[500px] object-contain" />
            </div>
            <p class="text-gray-700 leading-relaxed text-justify">
                Posisi tertinggi ditempati oleh Atasan PPID Pengarah. Tugas utamanya memberikan arahan kebijakan
                strategis pelayanan.Tepat di bawahnya berada posisi Ketua PPID. Ketua bertanggung jawab atas seluruh
                operasional pelayanan informasi. Selanjutnya, Sekretaris PPID mengatur administrasi dan koordinasi. Di
                bawah sekretaris, terdapat Anggota PPID sebagai pelaksana. Mereka membantu menjalankan seluruh program
                kerja dokumentasi.Alur kemudian berlanjut ke Operator PPID Utama. Operator utama bertugas mengelola
                sistem teknis informasi pusat. Struktur paling bawah adalah Anggota Operator Pembantu. Mereka bekerja di
                seluruh Organisasi Perangkat Daerah. Tugasnya menghimpun data dari setiap instansi kabupaten. Seluruh
                tingkatan ini saling terhubung secara terintegrasi.
            </p>
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

    <?php include('footer.php'); ?>

    <script src="assets/js/scripts.js"></script>
</body>

</html>