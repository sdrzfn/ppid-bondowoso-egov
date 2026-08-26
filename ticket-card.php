<?php
$ticket = $ticketData['ticket'];
$status = $ticket['status'];

$statusConfig = [
    'menunggu' => ['bg-amber-50', 'text-amber-700', 'border-amber-200', 'bg-amber-500', 'Menunggu Diproses'],
    'diproses' => ['bg-blue-50', 'text-blue-700', 'border-blue-200', 'bg-blue-500', 'Sedang Diproses'],
    'selesai' => ['bg-green-50', 'text-green-700', 'border-green-200', 'bg-green-500', 'Selesai'],
    'ditolak' => ['bg-red-50', 'text-red-700', 'border-red-200', 'bg-red-500', 'Ditolak']
];
$config = $statusConfig[$status] ?? $statusConfig['menunggu'];

$steps = [
    ['label' => 'Permohonan Berhasil Dikirim', 'desc' => 'Pemohon telah melengkapi formulir dan mengunggah identitas KTP.', 'icon' => 'fa-check', 'color' => 'bg-emerald-600', 'ring' => 'ring-emerald-50'],
    ['label' => 'Verifikasi Kelengkapan Berkas', 'desc' => 'Petugas PPID menyetujui kelengkapan identitas dan legal standing pemohon.', 'icon' => 'fa-check', 'color' => 'bg-emerald-600', 'ring' => 'ring-emerald-50'],
    ['label' => 'Disposisi & Pencarian Dokumen', 'desc' => 'Tiket diteruskan ke Unit Teknis untuk menyiapkan salinan informasi yang diminta.', 'icon' => 'fa-spinner', 'color' => 'bg-blue-600', 'ring' => 'ring-blue-100'],
    ['label' => 'Penyampaian Tanggapan & Dokumen', 'desc' => 'Dokumen siap diunduh secara langsung oleh pemohon.', 'icon' => 'fa-circle', 'color' => 'bg-slate-300', 'ring' => 'ring-slate-100']
];

$statusMap = ['menunggu' => 0, 'diproses' => 2, 'selesai' => 3, 'ditolak' => 1];
$currentStep = $statusMap[$status] ?? 0;
?>

<div class="ticket-card bg-white rounded-2xl border border-slate-200 shadow-lg overflow-hidden">
    <!-- Ticket Header -->
    <div class="bg-slate-900 p-6 text-white">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <span class="text-xs text-slate-400 font-mono block">NO. RESI TIKET</span>
                <h2 class="text-lg font-mono font-extrabold"><?= htmlspecialchars($ticketData['display_number']) ?></h2>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 <?= $config[0] ?> <?= $config[1] ?> border <?= $config[2] ?> rounded-full text-xs font-bold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full <?= $config[3] ?> <?= $status == 'diproses' ? 'animate-pulse' : '' ?>"></span>
                    <?= $config[4] ?>
                </span>
            </div>
        </div>
    </div>

    <div class="p-6 sm:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Timeline -->
            <div class="lg:col-span-2">
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
                    <?php foreach ($steps as $index => $step): 
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

            <!-- Right Column: Details -->
            <div class="space-y-6">
                <!-- Ticket Details -->
                <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 border-b border-slate-100 pb-2">Rincian Permohonan</h3>
                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-slate-400 block mb-0.5">Nama Pemohon</span>
                            <span class="font-bold text-slate-800"><?= htmlspecialchars($ticket['nama_lengkap']) ?></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-0.5">Kategori</span>
                            <span class="font-semibold text-slate-800 bg-slate-100 px-2 py-0.5 rounded text-[11px] capitalize">
                                <?= htmlspecialchars($ticket['kategori_pemohon']) ?> (<?= htmlspecialchars($ticket['jenis_identitas'] ?? 'WNI') ?>)
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-0.5">Rincian Informasi</span>
                            <p class="text-slate-700 leading-relaxed font-medium bg-white p-3 rounded-lg border border-slate-100">
                                <?= htmlspecialchars($ticket['rincian_informasi']) ?>
                            </p>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-0.5">Tujuan Penggunaan</span>
                            <p class="text-slate-600 font-medium"><?= htmlspecialchars($ticket['tujuan_penggunaan']) ?></p>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-0.5">Format & Pengiriman</span>
                            <span class="font-bold text-sky-700 flex items-center gap-1 capitalize">
                                <i class="fa-solid fa-file-<?= $ticket['format_salinan'] == 'softcopy' ? 'pdf' : 'print' ?>"></i>
                                <?= htmlspecialchars($ticket['format_salinan']) ?> (<?= htmlspecialchars($ticket['cara_penyampaian']) ?>)
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 block mb-0.5">Tanggal Pengajuan</span>
                            <span class="font-medium text-slate-700">
                                <?= date('d F Y', strtotime($ticket['created_at'])) ?>
                                <span class="text-slate-400"><?= date('H:i', strtotime($ticket['created_at'])) ?> WIB</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Support Card -->
                <div class="bg-sky-700 rounded-xl p-5 text-white shadow-md">
                    <h4 class="font-bold text-sm mb-1">Butuh Bantuan?</h4>
                    <p class="text-xs text-sky-100 mb-4">Hubungi Desk Layanan PPID jika membutuhkan bantuan informasi terkait tiket ini.</p>
                    <a href="kontak.php" class="inline-flex items-center gap-2 text-xs font-bold bg-white text-sky-800 px-3.5 py-2 rounded-lg hover:bg-blue-50 transition">
                        <i class="fa-solid fa-headset"></i> Contact Desk PPID
                    </a>
                </div>

                <!-- Action: Buat Baru -->
                <a href="ticket-form.php" class="block bg-white border border-slate-200 rounded-xl p-4 text-center hover:bg-slate-50 transition">
                    <span class="text-xs font-bold text-slate-600 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-plus"></i> Buat Permohonan Baru
                    </span>
                </a>
            </div>
        </div>
    </div>
</div>