<?php
include 'config/database.php';
$id = intval($_GET['id']);
$query = $conn->query("SELECT * FROM layanan WHERE id=$id");
$data = $query->fetch_assoc();
?>

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
</head>

<script src="assets/js/speech-consent.js"></script>
<script src="https://cdn.userway.org/widget.js" data-account="d9ZmCPKv7k"></script>

<section class="container mx-auto px-4 py-10">
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Gambar -->
        <!-- <div class="w-full">
            <img src="uploads/<?= $data['gambar'] ?>" alt="<?= htmlspecialchars($data['judul']) ?>"
                class="w-full h-72 object-cover">
        </div> -->
        <div>
            <?php
            $dokumen = $data['dokumen'] ?? '';
            $pdfPath = "uploads/layanan/" . $dokumen;

            if (!empty($dokumen) && file_exists($pdfPath) && is_file($pdfPath)): ?>
                <iframe src="<?= htmlspecialchars($pdfPath) ?>" type="application/pdf" class="w-full h-full border-0"
                    style="min-height: 400px;">
                </iframe>
            <?php else: ?>
                <div class="text-center p-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-slate-400 mb-2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h11.25c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H5.625z" />
                    </svg>
                    <p class="text-sm">Tidak ada dokumen PDF</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Konten -->
        <div class="p-6">
            <h1 class="text-3xl font-extrabold text-slate-800 mb-4">
                <?= htmlspecialchars($data['judul']) ?>
            </h1>

            <p class="text-slate-700 leading-relaxed whitespace-pre-line">
                <?= nl2br(htmlspecialchars($data['deskripsi'])) ?>
            </p>

            <!-- Meta -->
            <div class="mt-6 flex items-center justify-between border-t border-slate-200 pt-4">
                <p class="text-sm text-slate-500">
                    <span class="font-medium">Sumber:</span> <?= htmlspecialchars($data['sumber']) ?>
                </p>
                <a href="layanan.php" class="text-sm font-semibold text-sky-600 hover:text-sky-700 transition-colors">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>
</section>