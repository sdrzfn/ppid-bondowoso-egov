<?php
$searchNumber = $_GET['number'] ?? '';
$ticketData = null;
$errorMessage = '';

if ($searchNumber) {
    // Initial search via AJAX or direct load
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "ticket-track.php?number=" . urlencode($searchNumber));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Validate response
    if ($httpCode === 200 && $response) {
        $result = json_decode($response, true);
        
        // Check if JSON is valid and has success key
        if ($result && isset($result['success'])) {
            if ($result['success']) {
                $ticketData = $result;
            } else {
                $errorMessage = $result['message'] ?? 'Tiket tidak ditemukan';
            }
        } else {
            $errorMessage = 'Format data tidak valid. Silakan coba lagi.';
            error_log("Invalid JSON response from ticket-track.php: " . substr($response, 0, 200));
        }
    } else {
        $errorMessage = 'Layanan tracking sedang tidak tersedia. Silakan hubungi admin.';
        error_log("ticket-track.php HTTP error: $httpCode, Response: " . substr($response, 0, 200));
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
        }

        .container-wide {
            max-width: 1200px;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.5s ease forwards;
        }

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

        /* Ticket Card Animation */
        .ticket-card {
            animation: slideDown 0.4s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-white text-slate-800">

    <?php include('navbar.php'); ?>

    <!-- Hero -->
    <header class="relative h-56 md:h-64 w-full bg-center bg-cover"
        style="background-image:url('assets/img/cover-informasi.jpg');">
        <div class="absolute inset-0 bg-sky-900/50"></div>
        <div class="absolute inset-0 flex items-center">
            <div class="container-wide mx-auto px-4">
                <h1 class="text-white text-3xl md:text-4xl font-extrabold tracking-wide">MONITORING TICKET</h1>
                <p class="text-white/90 text-sm mt-2">Lacak status permohonan informasi publik Anda secara real-time</p>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-10">

        <!-- Search Box -->
        <div class="bg-slate-900 rounded-2xl p-6 sm:p-8 text-white shadow-lg mb-8 animate-fade-in">
            <div class="max-w-2xl">
                <span
                    class="text-[11px] font-extrabold uppercase tracking-wider text-blue-400 bg-blue-950/80 px-2.5 py-1 rounded-md border border-blue-800/50">
                    Sistem Tracking Real-Time
                </span>
                <h2 class="text-xl sm:text-2xl font-extrabold mt-3 mb-2">Lacak Status Permohonan</h2>
                <p class="text-xs sm:text-sm text-slate-300 mb-6">Masukkan Nomor Resi yang Anda terima saat mengajukan
                    permohonan. Format: <span class="font-mono text-blue-300">[ID]-[NOMOR TIKET]</span></p>

                <form id="trackingForm" class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-grow">
                        <i class="fa-solid fa-ticket text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="text" id="ticketNumber" placeholder="Contoh: 1-PPID-20260824-A1B2C3D4"
                            class="w-full pl-10 pr-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-sm font-mono text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">
                    </div>
                    <button type="submit"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-xl transition flex items-center justify-center gap-2">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>Lacak Status</span>
                    </button>
                </form>
                <p id="errorMessage" class="text-red-400 text-xs mt-2 hidden"></p>
            </div>
        </div>

        <div id="ticketResult" class="hidden"></div>

        <!-- Server-side rendered ticket (jika ada dari GET parameter) -->
        <?php if ($ticketData): ?>
            <div class="animate-fade-in">
                <?php include('ticket-card.php'); ?>
            </div>
        <?php endif; ?>

    </main>

    <?php include('footer.php'); ?>

    <script src="https://cdn.userway.org/widget.js" data-account="d9ZmCPKv7k"></script>
    <script src="assets/js/scripts.js"></script>

    <script>
        // Tracking form handler dengan SweetAlert
        document.getElementById('trackingForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const number = document.getElementById('ticketNumber').value.trim();
            const resultContainer = document.getElementById('ticketResult');
            
            if (!number) {
                // Show SweetAlert error
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
                Toast.fire({
                    icon: 'warning',
                    title: 'Masukkan nomor resi terlebih dahulu',
                    background: '#fef3c7',
                    color: '#92400e',
                    iconColor: '#f59e0b'
                });
                return;
            }

            // Show loading
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Mencari...</span>';

            try {
                const response = await fetch(`ticket-track.php?number=${encodeURIComponent(number)}`);
                const result = await response.json();

                if (result.success) {
                    // Show success SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Tiket Ditemukan!',
                        text: `Nomor Resi: ${result.display_number}`,
                        confirmButtonColor: '#0284C7',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Render ticket card di bawah form
                    await renderTicketCard(result);
                    
                    // Clear input
                    document.getElementById('ticketNumber').value = '';
                } else {
                    // Show error SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Tiket Tidak Ditemukan',
                        text: result.message || 'Pastikan nomor resi yang Anda masukkan benar.',
                        confirmButtonColor: '#dc2626'
                    });
                    
                    // Hide result card if visible
                    resultContainer.classList.add('hidden');
                    resultContainer.innerHTML = '';
                }
            } catch (error) {
                console.error('Tracking error:', error);
                
                // Show error SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Silakan coba lagi dalam beberapa saat.',
                    confirmButtonColor: '#dc2626'
                });
                
                // Hide result card if visible
                resultContainer.classList.add('hidden');
                resultContainer.innerHTML = '';
            } finally {
                // Reset button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });

        // Function to render ticket card
        async function renderTicketCard(data) {
            const resultContainer = document.getElementById('ticketResult');
            const ticket = data.ticket;
            const status = ticket.status;
            
            // Status configuration
            const statusConfig = {
                'menunggu': { bg: 'bg-amber-50', text: 'text-amber-700', border: 'border-amber-200', dot: 'bg-amber-500', label: 'Menunggu Diproses' },
                'diproses': { bg: 'bg-blue-50', text: 'text-blue-700', border: 'border-blue-200', dot: 'bg-blue-500', label: 'Sedang Diproses' },
                'selesai': { bg: 'bg-green-50', text: 'text-green-700', border: 'border-green-200', dot: 'bg-green-500', label: 'Selesai' },
                'ditolak': { bg: 'bg-red-50', text: 'text-red-700', border: 'border-red-200', dot: 'bg-red-500', label: 'Ditolak' }
            };
            
            const config = statusConfig[status] || statusConfig['menunggu'];
            
            // Timeline steps
            const steps = [
                { label: 'Permohonan Berhasil Dikirim', desc: 'Pemohon telah melengkapi formulir dan mengunggah identitas KTP.', icon: 'fa-check', color: 'bg-emerald-600', ring: 'ring-emerald-50' },
                { label: 'Verifikasi Kelengkapan Berkas', desc: 'Petugas PPID menyetujui kelengkapan identitas dan legal standing pemohon.', icon: 'fa-check', color: 'bg-emerald-600', ring: 'ring-emerald-50' },
                { label: 'Disposisi & Pencarian Dokumen', desc: 'Tiket diteruskan ke Unit Teknis untuk menyiapkan salinan informasi yang diminta.', icon: 'fa-spinner', color: 'bg-blue-600', ring: 'ring-blue-100' },
                { label: 'Penyampaian Tanggapan & Dokumen', desc: 'Dokumen siap diunduh secara langsung oleh pemohon.', icon: 'fa-circle', color: 'bg-slate-300', ring: 'ring-slate-100' }
            ];
            
            const statusMap = { 'menunggu': 0, 'diproses': 2, 'selesai': 3, 'ditolak': 1 };
            const currentStep = statusMap[status] ?? 0;

            // Generate timeline HTML
            let timelineHTML = '';
            steps.forEach((step, index) => {
                const isActive = index <= currentStep && status != 'ditolak';
                const isCurrent = index == currentStep;
                timelineHTML += `
                    <div class="relative ${isActive ? '' : 'opacity-40'}">
                        <div class="absolute -left-6 top-1 w-4 h-4 rounded-full ${step.color} ring-4 ${step.ring} flex items-center justify-center text-white text-[8px]">
                            <i class="fa-solid ${step.icon}"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-900">${step.label}</span>
                                ${isCurrent && status == 'diproses' ? '<span class="text-[10px] text-blue-600 font-semibold">Proses Berjalan</span>' : ''}
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">${step.desc}</p>
                        </div>
                    </div>
                `;
            });

            // Generate card HTML
            const cardHTML = `
                <div class="ticket-card bg-white rounded-2xl border border-slate-200 shadow-lg overflow-hidden">
                    <!-- Ticket Header -->
                    <div class="bg-slate-900 p-6 text-white">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <span class="text-xs text-slate-400 font-mono block">NO. RESI TIKET</span>
                                <h2 class="text-lg font-mono font-extrabold">${data.display_number}</h2>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 ${config.bg} ${config.text} border ${config.border} rounded-full text-xs font-bold flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full ${config.dot} ${status == 'diproses' ? 'animate-pulse' : ''}"></span>
                                    ${config.label}
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
                                        <div class="text-lg font-extrabold text-blue-700 font-mono">${data.sla_remaining} Hari</div>
                                    </div>
                                </div>

                                <!-- Timeline -->
                                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-6">Alur Pemrosesan Tiket</h3>
                                <div class="relative pl-6 space-y-6">
                                    <div class="timeline-line"></div>
                                    ${timelineHTML}
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
                                            <span class="font-bold text-slate-800">${ticket.nama_lengkap}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Kategori</span>
                                            <span class="font-semibold text-slate-800 bg-slate-100 px-2 py-0.5 rounded text-[11px] capitalize">
                                                ${ticket.kategori_pemohon} (${ticket.jenis_identitas || 'WNI'})
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Rincian Informasi</span>
                                            <p class="text-slate-700 leading-relaxed font-medium bg-white p-3 rounded-lg border border-slate-100">
                                                ${ticket.rincian_informasi}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Tujuan Penggunaan</span>
                                            <p class="text-slate-600 font-medium">${ticket.tujuan_penggunaan}</p>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Format & Pengiriman</span>
                                            <span class="font-bold text-sky-700 flex items-center gap-1 capitalize">
                                                <i class="fa-solid fa-file-${ticket.format_salinan == 'softcopy' ? 'pdf' : 'print'}"></i>
                                                ${ticket.format_salinan} (${ticket.cara_penyampaian})
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block mb-0.5">Tanggal Pengajuan</span>
                                            <span class="font-medium text-slate-700">
                                                ${new Date(ticket.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}
                                                <span class="text-slate-400">${new Date(ticket.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} WIB</span>
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
            `;

            // Render card
            resultContainer.innerHTML = cardHTML;
            resultContainer.classList.remove('hidden');
            
            // Scroll to card
            resultContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    </script>

</body>

</html>