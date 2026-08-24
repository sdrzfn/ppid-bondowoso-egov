<?php
$searchNumber = $_GET['number'] ?? '';
$ticketData = null;
$errorMessage = '';

if ($searchNumber) {
    // Initial search via AJAX or direct load
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "ticket-track.php?number=" . urlencode($searchNumber));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    if ($result['success']) {
        $ticketData = $result;
    } else {
        $errorMessage = $result['message'] ?? 'Tiket tidak ditemukan';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Ticket - PPID Bondowoso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="assets/img/bondowoso.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; }
        .container-wide { max-width: 1200px; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.5s ease forwards; }
        .timeline-line {
            position: absolute;
            left: 11px;
            top: 24px;
            bottom: 0;
            width: 2px;
            background: #e2e8f0;
        }
        .timeline-line-active {
            position: absolute;
            left: 11px;
            top: 24px;
            height: 0;
            width: 2px;
            background: #0284C7;
            transition: height 0.5s ease;
        }
    </style>
</head>
<body class="bg-white text-slate-800">

<?php include('navbar.php'); ?>

<!-- Hero -->
<header class="relative h-56 md:h-64 w-full bg-center bg-cover" style="background-image:url('assets/img/cover-informasi.jpg');">
    <div class="absolute inset-0 bg-sky-900/50"></div>
    <div class="absolute inset-0 flex items-center">
        <div class="container-wide mx-auto px-4">
            <h1 class="text-white text-3xl md:text-4xl font-extrabold tracking-wide">MONITORING TICKET</h1>
            <p class="text-white/90 text-sm mt-2">Lacak status permohonan informasi publik Anda secara real-time</p>
        </div>
    </div>
</header>

<main class="max-w-5xl mx-auto px-4 py-10">

    <!-- Search Box (hanya tampil jika belum ada tiket yang dicari) -->
    <?php if (!$ticketData): ?>
    <div class="bg-slate-900 rounded-2xl p-6 sm:p-8 text-white shadow-lg mb-8 animate-fade-in">
        <div class="max-w-2xl">
            <span class="text-[11px] font-extrabold uppercase tracking-wider text-blue-400 bg-blue-950/80 px-2.5 py-1 rounded-md border border-blue-800/50">
                Sistem Tracking Real-Time
            </span>
            <h2 class="text-xl sm:text-2xl font-extrabold mt-3 mb-2">Lacak Status Permohonan</h2>
            <p class="text-xs sm:text-sm text-slate-300 mb-6">Masukkan Nomor Resi yang Anda terima saat mengajukan permohonan. Format: <span class="font-mono text-blue-300">[ID]-[NOMOR TIKET]</span></p>
            
            <form id="trackingForm" class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-grow">
                    <i class="fa-solid fa-ticket text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                    <input type="text" id="ticketNumber" placeholder="Contoh: 1-PPID-20260824-A1B2C3D4" 
                        class="w-full pl-10 pr-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-sm font-mono text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">
                </div>
                <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition flex items-center justify-center gap-2">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Lacak Status</span>
                </button>
            </form>
            <p id="errorMessage" class="text-red-400 text-xs mt-2 hidden"></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Ticket Detail (hanya tampil jika ada tiket) -->
    <?php if ($ticketData): ?>
    <div class="animate-fade-in">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column: Status & Timeline -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Ticket Summary -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4 mb-4">
                        <div>
                            <span class="text-xs text-slate-400 font-mono block">NO. RESI TIKET</span>
                            <h2 class="text-lg font-mono font-extrabold text-slate-900"><?= htmlspecialchars($ticketData['display_number']) ?></h2>
                        </div>
                        <div class="flex items-center gap-2">
                            <?php
                            $status = $ticketData['ticket']['status'];
                            $statusConfig = [
                                'menunggu' => ['bg-amber-50 text-amber-700 border-amber-200', 'Menunggu Diproses', 'bg-amber-500'],
                                'diproses' => ['bg-blue-50 text-blue-700 border-blue-200', 'Sedang Diproses', 'bg-blue-500'],
                                'selesai' => ['bg-green-50 text-green-700 border-green-200', 'Selesai', 'bg-green-500'],
                                'ditolak' => ['bg-red-50 text-red-700 border-red-200', 'Ditolak', 'bg-red-500']
                            ];
                            $config = $statusConfig[$status] ?? $statusConfig['menunggu'];
                            ?>
                            <span class="px-3 py-1 <?= $config[0] ?> border rounded-full text-xs font-bold flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full <?= $config[2] ?> <?= $status == 'diproses' ? 'animate-pulse' : '' ?>"></span>
                                <?= $config[1] ?>
                            </span>
                        </div>
                    </div>

                    <!-- SLA Countdown -->
                    <div class="bg-blue-50/70 border border-blue-100 rounded-xl p-4 mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-700 text-white rounded-xl flex items-center justify-center text-lg flex-shrink-0">
                                <i class="fa-solid fa-hourglass-half"></i>
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-slate-900">Tenggat Waktu Respons (SLA UU KIP)</h3>
                                <p class="text-[11px] text-slate-500">Maksimal 10 hari kerja sejak tanggal permohonan.</p>
                            </div>
                        </div>
                        <div class="text-right whitespace-nowrap bg-white px-4 py-2 rounded-lg border border-blue-200/60 shadow-sm">
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Sisa Waktu</div>
                            <div class="text-lg font-extrabold text-blue-700 font-mono"><?= $ticketData['sla_remaining'] ?> Hari</div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-6">Alur Pemrosesan Tiket</h3>
                    
                    <div class="relative pl-6 space-y-6">
                        <div class="timeline-line"></div>
                        
                        <?php
                        $steps = [
                            ['label' => 'Permohonan Berhasil Dikirim', 'desc' => 'Pemohon telah melengkapi formulir dan mengunggah identitas KTP.', 'icon' => 'fa-check', 'color' => 'bg-emerald-600', 'ring' => 'ring-emerald-50', 'text_color' => 'text-emerald-600'],
                            ['label' => 'Verifikasi Kelengkapan Berkas', 'desc' => 'Petugas PPID menyetujui kelengkapan identitas dan legal standing pemohon.', 'icon' => 'fa-check', 'color' => 'bg-emerald-600', 'ring' => 'ring-emerald-50', 'text_color' => 'text-emerald-600'],
                            ['label' => 'Disposisi & Pencarian Dokumen', 'desc' => 'Tiket diteruskan ke Unit Teknis untuk menyiapkan salinan informasi yang diminta.', 'icon' => 'fa-spinner', 'color' => 'bg-blue-600', 'ring' => 'ring-blue-100', 'text_color' => 'text-blue-600'],
                            ['label' => 'Penyampaian Tanggapan & Dokumen', 'desc' => 'Dokumen siap diunduh secara langsung oleh pemohon.', 'icon' => 'fa-circle', 'color' => 'bg-slate-300', 'ring' => 'ring-slate-100', 'text_color' => 'text-slate-400']
                        ];
                        
                        $currentStep = match($status) {
                            'menunggu' => 0,
                            'diproses' => 2,
                            'selesai' => 3,
                            'ditolak' => 1,
                            default => 0
                        };
                        
                        foreach ($steps as $index => $step):
                            $isActive = $index <= $currentStep && $status != 'ditolak';
                            $isCurrent = $index == $currentStep;
                        ?>
                        <div class="relative <?= $isActive ? '' : 'opacity-40' ?>">
                            <div class="absolute -left-6 top-1 w-4 h-4 rounded-full <?= $step['color'] ?> ring-4 <?= $step['ring'] ?> flex items-center justify-center text-white text-[8px]">
                                <i class="fa-solid <?= $step['icon'] ?>"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-slate-900"><?= $step['label'] ?></span>
                                    <?php if ($isCurrent && $status == 'diproses'): ?>
                                        <span class="text-[10px] text-blue-600 font-semibold">Proses Berjalan</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5"><?= $step['desc'] ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Keberatan Banner -->
                <?php if ($status != 'selesai' && $status != 'ditolak'): ?>
                <div class="bg-white border border-slate-200 rounded-2xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-rose-50 text-rose-600 rounded-lg flex items-center justify-center font-bold text-sm flex-shrink-0">
                            <i class="fa-solid fa-gavel"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">Tidak Puas dengan Hasil atau Terlambat?</h4>
                            <p class="text-[11px] text-slate-500">Anda berhak mengajukan keberatan kepada Atasan PPID sesuai Pasal 35 UU KIP.</p>
                        </div>
                    </div>
                    <button class="w-full sm:w-auto px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition whitespace-nowrap">
                        Ajukan Keberatan
                    </button>
                </div>
                <?php endif; ?>

            </div>

            <!-- Right Column: Details -->
            <div class="space-y-6">
                
                <!-- Ticket Details -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 border-b border-slate-100 pb-2">Rincian Permohonan</h3>
                    
                    <div class="space-y-4 text-xs">
                        <div>
                            <span class="text-slate-400 block mb-0.5">Nama Pemohon</span>
                            <span class="font-bold text-slate-800"><?= htmlspecialchars($ticketData['ticket']['nama_lengkap']) ?></span>
                        </div>
                        
                        <div>
                            <span class="text-slate-400 block mb-0.5">Kategori</span>
                            <span class="font-semibold text-slate-800 bg-slate-100 px-2 py-0.5 rounded text-[11px] capitalize">
                                <?= $ticketData['ticket']['kategori_pemohon'] ?> (<?= $ticketData['ticket']['jenis_identitas'] ?? 'WNI' ?>)
                            </span>
                        </div>
                        
                        <div>
                            <span class="text-slate-400 block mb-0.5">Rincian Informasi</span>
                            <p class="text-slate-700 leading-relaxed font-medium bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <?= htmlspecialchars($ticketData['ticket']['rincian_informasi']) ?>
                            </p>
                        </div>
                        
                        <div>
                            <span class="text-slate-400 block mb-0.5">Tujuan Penggunaan</span>
                            <p class="text-slate-600 font-medium"><?= htmlspecialchars($ticketData['ticket']['tujuan_penggunaan']) ?></p>
                        </div>
                        
                        <div>
                            <span class="text-slate-400 block mb-0.5">Format & Pengiriman</span>
                            <span class="font-bold text-sky-700 flex items-center gap-1 capitalize">
                                <i class="fa-solid fa-file-<?= $ticketData['ticket']['format_salinan'] == 'softcopy' ? 'pdf' : 'print' ?>"></i>
                                <?= str_replace('_', ' ', $ticketData['ticket']['format_salinan']) ?> 
                                (<?= str_replace('_', ' ', $ticketData['ticket']['cara_penyampaian']) ?>)
                            </span>
                        </div>

                        <div>
                            <span class="text-slate-400 block mb-0.5">Tanggal Pengajuan</span>
                            <span class="font-medium text-slate-700">
                                <?= date('d F Y', strtotime($ticketData['ticket']['created_at'])) ?> 
                                <span class="text-slate-400"><?= date('H:i', strtotime($ticketData['ticket']['created_at'])) ?> WIB</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Support Card -->
                <div class="bg-sky-700 rounded-2xl p-5 text-white shadow-md">
                    <h4 class="font-bold text-sm mb-1">Butuh Bantuan?</h4>
                    <p class="text-xs text-sky-100 mb-4">Hubungi Desk Layanan PPID jika membutuhkan bantuan informasi terkait tiket ini.</p>
                    <a href="kontak.php" class="inline-flex items-center gap-2 text-xs font-bold bg-white text-sky-800 px-3.5 py-2 rounded-lg hover:bg-blue-50 transition">
                        <i class="fa-solid fa-headset"></i> Contact Desk PPID
                    </a>
                </div>

                <!-- Action: Buat Baru -->
                <a href="ticket-form.php" class="block bg-white border border-slate-200 rounded-2xl p-4 text-center hover:bg-slate-50 transition">
                    <span class="text-xs font-bold text-slate-600 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plus"></i> Buat Permohonan Baru
                    </span>
                </a>

            </div>
        </div>
    </div>
    <?php endif; ?>

</main>

<?php include('footer.php'); ?>

<script src="https://cdn.userway.org/widget.js" data-account="d9ZmCPKv7k"></script>
<script src="assets/js/scripts.js"></script>

<script>
// Tracking form handler
document.getElementById('trackingForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const number = document.getElementById('ticketNumber').value.trim();
    const errorEl = document.getElementById('errorMessage');
    
    if (!number) {
        errorEl.textContent = 'Masukkan nomor resi terlebih dahulu';
        errorEl.classList.remove('hidden');
        return;
    }
    
    try {
        const response = await fetch(`ticket-track.php?number=${encodeURIComponent(number)}`);
        const result = await response.json();
        
        if (result.success) {
            // Redirect dengan parameter
            window.location.href = `ticket-monitoring.php?number=${encodeURIComponent(number)}`;
        } else {
            errorEl.textContent = result.message || 'Tiket tidak ditemukan';
            errorEl.classList.remove('hidden');
        }
    } catch (error) {
        errorEl.textContent = 'Terjadi kesalahan. Silakan coba lagi.';
        errorEl.classList.remove('hidden');
    }
});
</script>

</body>
</html>