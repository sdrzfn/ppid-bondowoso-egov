<?php
$ticketNumber = $_GET['number'] ?? '';
$formType = $_GET['type'] ?? 'permohonan';
$isFromSubmission = !empty($ticketNumber);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if ($isFromSubmission): ?>
    <title>Permohonan Berhasil - PPID Bondowoso</title>
    <?php else: ?>
    <title>Permohonan & Keberatan Informasi - PPID Bondowoso</title>
    <?php endif; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="assets/img/bondowoso.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; }
        .container-wide { max-width: 1200px; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.6s ease forwards; }
        .card-hover { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="bg-white text-slate-800">

<?php include('navbar.php'); ?>

<?php if ($isFromSubmission): ?>
<!-- Success Header -->
<header class="relative h-60 md:h-72 w-full bg-center bg-cover" style="background-image:url('assets/img/cover-layanan.jpg');">
    <div class="absolute inset-0 bg-sky-900/60"></div>
    <div class="absolute inset-0 flex items-center">
        <div class="container-wide mx-auto px-4 animate-fade-in">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 <?= $formType == 'keberatan' ? 'bg-rose-500' : 'bg-green-500' ?> rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-check text-white text-xl"></i>
                </div>
                <span class="text-white font-bold text-sm <?= $formType == 'keberatan' ? 'bg-rose-500/20 border-rose-400/30' : 'bg-green-500/20 border-green-400/30' ?> px-3 py-1 rounded-full border">
                    <?= $formType == 'keberatan' ? 'Keberatan Berhasil Dikirim' : 'Permohonan Berhasil' ?>
                </span>
            </div>
            <h1 class="text-white text-3xl md:text-4xl font-extrabold tracking-wide">
                <?= $formType == 'keberatan' ? 'PENGAJUAN KEBERATAN ANDA TELAH DITERIMA' : 'PERMOHONAN ANDA TELAH DITERIMA' ?>
            </h1>
            <p class="text-white/90 text-sm mt-2 max-w-xl">
                <?= $formType == 'keberatan' 
                    ? 'Nomor resi pengajuan keberatan Anda telah digenerate. Simpan nomor ini untuk melakukan tracking status.'
                    : 'Nomor resi permohonan Anda telah digenerate. Simpan nomor ini untuk melakukan tracking status permohonan.' 
                ?>
            </p>
        </div>
    </div>
</header>

<!-- Info Box -->
<div class="mt-8 <?= $formType == 'keberatan' ? 'bg-rose-50 border-rose-100' : 'bg-blue-50 border-blue-100' ?> border rounded-xl p-4 flex items-start gap-3">
    <i class="fa-solid fa-circle-info <?= $formType == 'keberatan' ? 'text-rose-700' : 'text-blue-700' ?> mt-0.5"></i>
    <p class="text-xs <?= $formType == 'keberatan' ? 'text-rose-800' : 'text-blue-800' ?>">
        <?php if ($formType == 'keberatan'): ?>
            <strong>Informasi:</strong> Berdasarkan Pasal 37 UU KIP, Atasan PPID wajib memberikan tanggapan tertulis atas keberatan ini maksimal <strong>30 hari kerja</strong>.
        <?php else: ?>
            <strong>Informasi:</strong> Status permohonan akan diperbarui secara otomatis oleh petugas PPID. Maksimal waktu respons adalah <strong>10 hari kerja</strong> sesuai UU KIP.
        <?php endif; ?>
    </p>
</div>

<main class="max-w-5xl mx-auto px-4 py-10">

    <!-- Ticket Number Display -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-8 text-center animate-fade-in">
        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Nomor Resi Permohonan Anda</p>
        <div class="inline-flex items-center gap-3 bg-slate-100 px-8 py-4 rounded-xl border border-slate-200">
            <i class="fa-solid fa-ticket text-sky-700 text-xl"></i>
            <span class="text-2xl font-bold text-slate-900 tracking-wider font-mono"><?= htmlspecialchars($ticketNumber) ?></span>
        </div>
        <p class="text-xs text-slate-500 mt-3">Gunakan nomor ini untuk melacak status permohonan Anda kapan saja.</p>
        
        <div class="flex items-center justify-center gap-3 mt-6">
            <button onclick="window.print()" class="px-4 py-2 text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-lg transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak Resi
            </button>
            <a href="ticket-monitoring.php?number=<?= htmlspecialchars($ticketNumber) ?>" class="px-4 py-2 text-xs font-bold text-white bg-sky-700 hover:bg-sky-800 rounded-lg transition flex items-center gap-2">
                <i class="fa-solid fa-magnifying-glass"></i> Lacak Status Sekarang
            </a>
        </div>
    </div>

    <!-- Choice Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-in">
        
        <!-- Card 1: Buat Ticket Baru -->
        <a href="ticket-form.php" class="card-hover bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-start gap-4 group">
            <div class="w-12 h-12 bg-sky-50 text-sky-700 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-sky-700 group-hover:text-white transition">
                <i class="fa-solid fa-plus text-lg"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900 mb-1">Buat Permohonan Baru</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Ajukan permohonan informasi publik baru atau pengajuan keberatan untuk permohonan yang sedang diproses.</p>
                <span class="inline-flex items-center gap-1 text-xs font-bold text-sky-700 mt-3 group-hover:gap-2 transition-all">
                    Buat Permohonan <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </span>
            </div>
        </a>

        <!-- Card 2: Monitoring -->
        <a href="ticket-monitoring.php" class="card-hover bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-start gap-4 group">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-700 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-700 group-hover:text-white transition">
                <i class="fa-solid fa-magnifying-glass text-lg"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900 mb-1">Monitoring / Lacak Status</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Masukkan nomor resi permohonan untuk melihat status proses, timeline, dan detail permohonan Anda.</p>
                <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 mt-3 group-hover:gap-2 transition-all">
                    Lacak Sekarang <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </span>
            </div>
        </a>

    </div>

    <!-- Info Box
    <div class="mt-8 bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
        <i class="fa-solid fa-circle-info text-blue-700 mt-0.5"></i>
        <p class="text-xs text-blue-800">
            <strong>Informasi:</strong> Status permohonan akan diperbarui secara otomatis oleh petugas PPID. Anda akan menerima notifikasi melalui email atau WhatsApp ketika ada perubahan status. 
            Maksimal waktu respons adalah <strong>10 hari kerja</strong> sesuai UU KIP.
        </p>
    </div> -->
    
</main>

<?php else: ?>
    <!-- ==================== GATE VIEW (dari navbar) ==================== -->
    <header class="relative h-60 md:h-72 w-full bg-center bg-cover" style="background-image:url('assets/img/cover-layanan.jpg');">
        <div class="absolute inset-0 bg-sky-900/60"></div>
        <div class="absolute inset-0 flex items-center">
            <div class="container-wide mx-auto px-4 animate-fade-in">
                <h1 class="text-white text-3xl md:text-4xl font-extrabold tracking-wide">PERMOHONAN & KEBERATAN INFORMASI</h1>
                <p class="text-white/90 text-sm mt-2 max-w-xl">Pilih layanan yang Anda butuhkan: membuat permohonan informasi baru atau melacak status permohonan yang sudah ada.</p>
            </div>
        </div>
    </header>
    
    <main class="max-w-5xl mx-auto px-4 py-10">
        <!-- Info Box -->
        <div class="mb-8 bg-blue-50 border border-blue-100 rounded-xl p-4 flex items-start gap-3">
            <i class="fa-solid fa-circle-info text-blue-700 mt-0.5"></i>
            <p class="text-xs text-blue-800">
                <strong>Informasi:</strong> Anda dapat mengajukan permohonan informasi publik secara online. Setelah permohonan diterima, Anda akan mendapatkan nomor resi untuk tracking status. 
                Maksimal waktu respons adalah <strong>10 hari kerja</strong> sesuai UU KIP.
            </p>
        </div>
        
        <!-- Choice Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 animate-fade-in">
        
        <!-- Card 1: Buat Ticket Baru -->
        <a href="ticket-form.php" class="card-hover bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-start gap-4 group">
            <div class="w-12 h-12 bg-sky-50 text-sky-700 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-sky-700 group-hover:text-white transition">
                <i class="fa-solid fa-plus text-lg"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900 mb-1">Buat Permohonan Baru</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Ajukan permohonan informasi publik baru atau pengajuan keberatan untuk permohonan yang sedang diproses.</p>
                <span class="inline-flex items-center gap-1 text-xs font-bold text-sky-700 mt-3 group-hover:gap-2 transition-all">
                    Buat Permohonan <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </span>
            </div>
        </a>

        <!-- Card 2: Monitoring -->
        <a href="ticket-monitoring.php" class="card-hover bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex items-start gap-4 group">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-700 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:bg-emerald-700 group-hover:text-white transition">
                <i class="fa-solid fa-magnifying-glass text-lg"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900 mb-1">Monitoring / Lacak Status</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Masukkan nomor resi permohonan untuk melihat status proses, timeline, dan detail permohonan Anda.</p>
                <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 mt-3 group-hover:gap-2 transition-all">
                    Lacak Sekarang <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </span>
            </div>
        </a>

    </div>


</main>
<?php endif; ?>

<?php include('footer.php'); ?>

<script src="https://cdn.userway.org/widget.js" data-account="d9ZmCPKv7k"></script>
<script src="assets/js/scripts.js"></script>

</body>
</html>