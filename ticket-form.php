<?php
session_start();
$step = $_GET['step'] ?? 1;
$ticketData = $_SESSION['ticket_data'] ?? [];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Permohonan Informasi - PPID Bondowoso</title>
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
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
        }

        .container-wide {
            max-width: 1200px;
        }

        .step-connector {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: #e2e8f0;
            z-index: 0;
            transform: translateY(-50%);
        }

        .step-connector-active {
            position: absolute;
            top: 50%;
            left: 0;
            height: 2px;
            background: #0284C7;
            z-index: 0;
            transform: translateY(-50%);
            transition: width 0.5s ease;
        }

        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
            animation: fadeIn 0.5s ease;
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
    </style>
</head>

<body class="bg-white text-slate-800">

    <?php include('navbar.php'); ?>

    <!-- Hero Header -->
    <header class="relative h-60 md:h-72 w-full bg-center bg-cover"
        style="background-image:url('assets/img/cover-layanan.jpg');">
        <div class="absolute inset-0 bg-sky-900/40"></div>
        <div class="absolute inset-0 flex items-center">
            <div class="container-wide mx-auto px-4">
                <h1 class="text-white text-3xl md:text-4xl font-extrabold tracking-wide">FORM PERMOHONAN INFORMASI</h1>
                <p class="text-white/90 text-sm mt-2 text-center">Pengisian Formulir Pengajuan Informasi</p>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-10">
        <div class="mb-6 text-center">
            <span
                class="inline-flex items-center gap-2 px-4 py-2 bg-sky-50 text-sky-700 text-xs font-bold rounded-full">
                <i class="fa-solid fa-circle-info"></i>
                Pilih Jenis Form:
                <select id="formTypeSelector"
                    class="bg-transparent font-bold text-sky-700 focus:outline-none cursor-pointer border-b border-sky-300">
                    <option value="permohonan" <?= ($ticketData['form_type'] ?? 'permohonan') == 'permohonan' ? 'selected' : '' ?>>Permohonan Informasi Publik</option>
                    <option value="keberatan" <?= ($ticketData['form_type'] ?? '') == 'keberatan' ? 'selected' : '' ?>>
                        Pengajuan Keberatan (Pasal 35 UU KIP)</option>
                </select>
            </span>
        </div>

        <!-- Progress Stepper -->
        <div class="mb-10 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between relative">
                <div class="step-connector"></div>
                <div class="step-connector-active" id="progressBar" style="width: 0%"></div>

                <!-- Step 1 -->
                <div class="relative z-10 flex flex-col items-center step-item" data-step="1">
                    <div
                        class="w-10 h-10 rounded-full bg-sky-700 text-white font-bold flex items-center justify-center shadow-md ring-4 ring-blue-50 step-circle">
                        1
                    </div>
                    <span class="text-xs font-bold text-sky-700 mt-2 hidden sm:block">Data Pemohon</span>
                </div>

                <!-- Step 2 -->
                <div class="relative z-10 flex flex-col items-center step-item" data-step="2">
                    <div
                        class="w-10 h-10 rounded-full bg-white border-2 border-slate-300 text-slate-400 font-bold flex items-center justify-center step-circle">
                        2
                    </div>
                    <span class="text-xs font-medium text-slate-400 mt-2 hidden sm:block">Rincian Informasi</span>
                </div>

                <!-- Step 3 -->
                <div class="relative z-10 flex flex-col items-center step-item" data-step="3">
                    <div
                        class="w-10 h-10 rounded-full bg-white border-2 border-slate-300 text-slate-400 font-bold flex items-center justify-center step-circle">
                        3
                    </div>
                    <span class="text-xs font-medium text-slate-400 mt-2 hidden sm:block">Konfirmasi & Resi</span>
                </div>

                <!-- Step 4: Keberatan (hanya tampil jika form type = keberatan) -->
                <div class="relative z-10 flex flex-col items-center step-item keberatan-step hidden" data-step="4">
                    <div
                        class="w-10 h-10 rounded-full bg-rose-600 text-white font-bold flex items-center justify-center shadow-md ring-4 ring-rose-50 step-circle">
                        <i class="fa-solid fa-gavel text-sm"></i>
                    </div>
                    <span class="text-xs font-bold text-rose-600 mt-2 hidden sm:block">Keberatan</span>
                </div>
            </div>
        </div>

        <!-- Form Container -->
        <form id="ticketForm" class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

            <!-- STEP 1: Data Pemohon -->
            <div class="form-section active" data-step="1">
                <div class="p-6 sm:p-8 border-b border-slate-100">
                    <h2 class="text-lg font-bold text-slate-900 mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-address-card text-sky-700"></i> Identitas Pemohon
                    </h2>
                    <p class="text-xs text-slate-500 mb-6">Diperlukan untuk verifikasi legal standing permohonan sesuai
                        ketentuan UU KIP.</p>

                    <div class="space-y-6">
                        <!-- Kategori Pemohon -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Kategori Pemohon <span class="text-rose-500">*</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label
                                    class="relative border-2 border-sky-700 bg-sky-50/50 p-3.5 rounded-xl flex items-center space-x-3 cursor-pointer transition">
                                    <input type="radio" name="kategori_pemohon" value="perorangan" checked
                                        class="h-4 w-4 text-sky-700 focus:ring-sky-500">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-900">Perorangan</span>
                                        <span class="block text-[11px] text-slate-500">Menggunakan KIK/KTP WNI</span>
                                    </div>
                                </label>
                                <label
                                    class="relative border border-slate-200 hover:border-slate-300 p-3.5 rounded-xl flex items-center space-x-3 cursor-pointer transition">
                                    <input type="radio" name="kategori_pemohon" value="lembaga"
                                        class="h-4 w-4 text-sky-700 focus:ring-sky-500">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-900">Lembaga / Badan
                                            Hukum</span>
                                        <span class="block text-[11px] text-slate-500">Melampirkan Akta Pendirian /
                                            SK</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- NIK & Nama -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Nomor Induk Kependudukan (NIK) <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="nik" placeholder="16 digit angka sesuai KTP"
                                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:border-sky-700 focus:ring-1 focus:ring-sky-700 transition"
                                    required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" name="nama_lengkap" placeholder="Nama lengkap tanpa gelar"
                                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:border-sky-700 focus:ring-1 focus:ring-sky-700 transition"
                                    required>
                            </div>
                        </div>

                        <!-- Jenis Identitas & No Identitas -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Identitas</label>
                                <select name="jenis_identitas"
                                    class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-sky-700 transition">
                                    <option value="ktp">KTP</option>
                                    <option value="passport">Passport</option>
                                    <option value="kitas">KITAS</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Identitas</label>
                                <input type="text" name="no_identitas" placeholder="Nomor identitas lain"
                                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:border-sky-700 transition">
                            </div>
                        </div>

                        <!-- Upload KTP -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Unggah Scan / Foto KTP <span class="text-rose-500">*</span>
                            </label>
                            <div id="ktpUploadContainer"
                                class="border-2 border-dashed border-slate-200 hover:border-sky-500 rounded-xl p-6 text-center bg-slate-50/50 hover:bg-slate-50 transition cursor-pointer">
                                <i class="fa-solid fa-cloud-arrow-up text-2xl text-sky-600 mb-2"></i>
                                <p class="text-xs font-semibold text-slate-700">
                                    Tarik berkas KTP ke sini atau <span class="text-sky-700 underline">pilih
                                        berkas</span>
                                </p>
                                <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, atau PDF (Maksimal 2 MB)
                                </p>
                                <input type="file" name="lampiran_ktp" accept=".jpg,.jpeg,.png,.pdf" class="hidden"
                                    id="ktpUpload">
                            </div>
                        </div>

                        <!-- Kontak -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Email Aktif <span class="text-rose-500">*</span>
                                </label>
                                <input type="email" name="email" placeholder="nama@email.com"
                                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:border-sky-700 transition"
                                    required>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Nomor WhatsApp / HP <span class="text-rose-500">*</span>
                                </label>
                                <input type="tel" name="no_whatsapp" placeholder="081234567890"
                                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:border-sky-700 transition"
                                    required>
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Alamat Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="alamat" rows="3" placeholder="Alamat sesuai domisili"
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:border-sky-700 transition"
                                required></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 2: Rincian Informasi -->
            <div class="form-section" data-step="2">
                <div class="p-6 sm:p-8 border-b border-slate-100 bg-slate-50/30">
                    <h2 class="text-lg font-bold text-slate-900 mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-file-circle-question text-sky-700"></i> Rincian Informasi yang Diminta
                    </h2>
                    <p class="text-xs text-slate-500 mb-6">Tuliskan deskripsi informasi dengan spesifik agar memudahkan
                        pencarian dokumen.</p>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Rincian Informasi yang Dibutuhkan <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="rincian_informasi" rows="4"
                                placeholder="Contoh: Laporan Realisasi Anggaran (LRA) Dinas Pendidikan Tahun Anggaran 2025..."
                                class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-sky-700 transition"
                                required></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Tujuan Penggunaan Informasi <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="tujuan_penggunaan" rows="3"
                                placeholder="Contoh: Untuk keperluan riset akademis/skripsi mahasiswa..."
                                class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-sky-700 transition"
                                required></textarea>
                        </div>

                        <!-- Format & Cara Penyampaian -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Format Salinan Informasi <span class="text-rose-500">*</span>
                                </label>
                                <select name="format_salinan"
                                    class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-sky-700 transition font-medium">
                                    <option value="softcopy">Softcopy / Salinan Digital (PDF / Excel)</option>
                                    <option value="hardcopy">Hardcopy / Cetak Fisik (Kertas)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Cara Penyampaian / Penerimaan <span class="text-rose-500">*</span>
                                </label>
                                <select name="cara_penyampaian"
                                    class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-sky-700 transition font-medium">
                                    <option value="email">Unduh Langsung (Email & Portal Online)</option>
                                    <option value="ambil_langsung">Diambil Langsung ke Meja Layanan PPID</option>
                                    <option value="pos">Jasa Pengiriman / Pos (Biaya ditanggung pemohon)</option>
                                    <option value="kurir">Kurir / Expedisi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STEP 3: Konfirmasi & Resi -->
            <div class="form-section" data-step="3">
                <div class="p-6 sm:p-8 border-b border-slate-100">
                    <h2 class="text-lg font-bold text-slate-900 mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-check text-sky-700"></i> Konfirmasi Data & Resi Permohonan
                    </h2>
                    <p class="text-xs text-slate-500 mb-6">Periksa kembali data Anda sebelum mengirim permohonan.</p>

                    <div id="confirmationData" class="space-y-6">
                        <!-- Data will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- STEP 4: Pengajuan Keberatan -->
            <div class="form-section" data-step="4">
                <div class="p-6 sm:p-8 border-b border-slate-100">
                    <h2 class="text-lg font-bold text-slate-900 mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-gavel text-rose-600"></i> Pengajuan Keberatan Informasi
                    </h2>
                    <p class="text-xs text-slate-500 mb-6">Ajukan sanggahan resmi kepada Atasan PPID sesuai Pasal 35 UU
                        No. 14 Tahun 2008.</p>

                    <div class="space-y-6">
                        <!-- Referensi Tiket Permohonan Awal -->
                        <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-200">
                            <h3
                                class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-ticket text-rose-600"></i> Referensi Tiket Permohonan Awal
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Nomor Registrasi Permohonan <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="reference_ticket_number"
                                        placeholder="Contoh: PPID-20260824-XXXX"
                                        class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-rose-600 transition"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                                        Nama Pemohon <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" name="nama_pemohon" placeholder="Nama sesuai permohonan awal"
                                        class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-rose-600 transition"
                                        required>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Informasi yang Dimohonkan Sebelumnya
                                </label>
                                <textarea name="info_sebelumnya" rows="2"
                                    placeholder="Tuliskan informasi yang Anda ajukan sebelumnya..."
                                    class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:border-rose-600 transition"></textarea>
                            </div>
                        </div>

                        <!-- Alasan Keberatan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">
                                Alasan Keberatan <span class="text-rose-500">*</span>
                            </label>
                            <p class="text-[11px] text-slate-500 mb-3">Pilih satu atau lebih alasan pengajuan keberatan
                                sesuai Pasal 35 UU KIP:</p>
                            <div class="space-y-2">
                                <label
                                    class="flex items-start gap-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition">
                                    <input type="checkbox" name="alasan[]" value="penolakan"
                                        class="mt-0.5 h-4 w-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-900">Penolakan atas Permohonan
                                            Informasi</span>
                                        <span class="block text-[11px] text-slate-500">Permohonan ditolak dengan alasan
                                            yang tidak sah/tidak sesuai pasal pengecualian.</span>
                                    </div>
                                </label>
                                <label
                                    class="flex items-start gap-3 p-3 border border-rose-200 bg-rose-50/30 rounded-xl hover:bg-rose-50/50 cursor-pointer transition">
                                    <input type="checkbox" name="alasan[]" value="tepat_waktu" checked
                                        class="mt-0.5 h-4 w-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-900">Informasi Tidak Disediakan
                                            Tepat Waktu</span>
                                        <span class="block text-[11px] text-slate-500">Tanggapan/dokumen melewati batas
                                            waktu SLA maksimum (10 + 7 hari kerja).</span>
                                    </div>
                                </label>
                                <label
                                    class="flex items-start gap-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition">
                                    <input type="checkbox" name="alasan[]" value="tidak_ditanggapi"
                                        class="mt-0.5 h-4 w-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-900">Permohonan Informasi Tidak
                                            Ditanggapi</span>
                                        <span class="block text-[11px] text-slate-500">Petugas PPID tidak memberikan
                                            konfirmasi atau tanggapan tertulis sama sekali.</span>
                                    </div>
                                </label>
                                <label
                                    class="flex items-start gap-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition">
                                    <input type="checkbox" name="alasan[]" value="tidak_sesuai"
                                        class="mt-0.5 h-4 w-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-900">Informasi Diberikan Tidak
                                            Sesuai yang Diminta</span>
                                        <span class="block text-[11px] text-slate-500">Dokumen yang diterima tidak
                                            lengkap atau berbeda dari rincian permohonan.</span>
                                    </div>
                                </label>
                                <label
                                    class="flex items-start gap-3 p-3 border border-slate-200 rounded-xl hover:bg-slate-50 cursor-pointer transition">
                                    <input type="checkbox" name="alasan[]" value="biaya_tidak_wajar"
                                        class="mt-0.5 h-4 w-4 text-rose-600 rounded border-slate-300 focus:ring-rose-500">
                                    <div>
                                        <span class="block text-xs font-bold text-slate-900">Pengenaan Biaya yang Tidak
                                            Makul/Wajar</span>
                                        <span class="block text-[11px] text-slate-500">Biaya penggandaan salinan dokumen
                                            melebihi standar biaya yang ditetapkan.</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Penjelasan & Bukti -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Rincian Penjelasan Keberatan <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="penjelasan_keberatan" rows="4"
                                placeholder="Tuliskan kronologi singkat atau alasan mengapa Anda mengajukan keberatan..."
                                class="w-full px-3.5 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-rose-600 transition"
                                required></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Unggah Dokumen Pendukung / Surat Penolakan (Opsional)
                            </label>
                            <div
                                class="border-2 border-dashed border-slate-200 hover:border-rose-500 rounded-xl p-5 text-center bg-slate-50/50 transition cursor-pointer">
                                <i class="fa-solid fa-file-arrow-up text-xl text-rose-600 mb-1"></i>
                                <p class="text-xs font-semibold text-slate-700">Tarik berkas atau <span
                                        class="text-rose-700 underline">pilih berkas</span></p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Format: PDF, JPG, PNG (Maksimal 5 MB)</p>
                                <input type="file" name="lampiran_bukti" accept=".pdf,.jpg,.jpeg,.png" class="hidden"
                                    id="buktiUpload">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="p-6 sm:p-8 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-start gap-2 text-xs text-slate-500 max-w-md">
                    <i class="fa-solid fa-clock text-sky-700 mt-0.5"></i>
                    <span>Berdasarkan UU KIP, PPID wajib memberikan tanggapan tertulis maksimal <strong>10 hari
                            kerja</strong> sejak permohonan diterima.</span>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button type="button" id="prevBtn"
                        class="hidden px-5 py-2.5 text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-100 rounded-xl transition">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Sebelumnya
                    </button>
                    <button type="button" id="nextBtn"
                        class="px-6 py-2.5 text-xs font-bold text-white bg-sky-700 hover:bg-sky-800 rounded-xl shadow-md transition flex items-center gap-2">
                        Selanjutnya<i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <button type="submit" id="submitBtn"
                        class="hidden px-6 py-2.5 text-xs font-bold text-white bg-green-600 hover:bg-green-700 rounded-xl shadow-md transition flex items-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i> Kirim Permohonan
                    </button>
                </div>
            </div>
        </form>

        <!-- Hidden input for form type -->
        <input type="hidden" name="form_type" id="formTypeInput"
            value="<?= $ticketData['form_type'] ?? 'permohonan' ?>">

    </main>

    <?php include('footer.php'); ?>

    <script src="https://cdn.userway.org/widget.js" data-account="d9ZmCPKv7k"></script>
    <script src="assets/js/scripts.js"></script>

    <script>
        // Multi-step form logic
        let currentStep = 1;
        let totalSteps = 3;
        let currentFormType = '<?= $ticketData['form_type'] ?? 'permohonan' ?>';

        // Update UI for current step
        function updateStepUI() {
            // Get current form type from hidden input
            const formTypeInput = document.getElementById('formTypeInput');
            const currentForm = formTypeInput ? formTypeInput.value : 'permohonan';
            updateRequiredFields();

            // Determine total steps based on form type
            totalSteps = currentForm === 'keberatan' ? 4 : 3;

            // Show/hide keberatan step indicator
            const keberatanStep = document.querySelector('.keberatan-step');
            if (keberatanStep) {
                keberatanStep.classList.toggle('hidden', currentForm !== 'keberatan');
            }

            // Update step circles
            document.querySelectorAll('.step-item').forEach(item => {
                const step = parseInt(item.dataset.step);
                const circle = item.querySelector('.step-circle');
                const label = item.querySelector('span');

                if (step < currentStep) {
                    circle.className = 'w-10 h-10 rounded-full bg-green-600 text-white font-bold flex items-center justify-center shadow-md ring-4 ring-green-50 step-circle';
                    circle.innerHTML = '<i class="fa-solid fa-check text-sm"></i>';
                    label.className = 'text-xs font-bold text-green-600 mt-2 hidden sm:block';
                } else if (step === currentStep) {
                    circle.className = 'w-10 h-10 rounded-full bg-sky-700 text-white font-bold flex items-center justify-center shadow-md ring-4 ring-blue-50 step-circle';
                    circle.innerHTML = step;
                    label.className = 'text-xs font-bold text-sky-700 mt-2 hidden sm:block';
                } else {
                    circle.className = 'w-10 h-10 rounded-full bg-white border-2 border-slate-300 text-slate-400 font-bold flex items-center justify-center step-circle';
                    circle.innerHTML = step;
                    label.className = 'text-xs font-medium text-slate-400 mt-2 hidden sm:block';
                }
            });

            // Update progress bar
            const progress = ((currentStep - 1) / (totalSteps - 1)) * 100;
            document.getElementById('progressBar').style.width = progress + '%';

            // Show/hide form sections
            document.querySelectorAll('.form-section').forEach(section => {
                section.classList.remove('active');
                if (parseInt(section.dataset.step) === currentStep) {
                    section.classList.add('active');
                }
            });

            // Update buttons
            document.getElementById('prevBtn').classList.toggle('hidden', currentStep === 1);
            document.getElementById('nextBtn').classList.toggle('hidden', currentStep === totalSteps);
            document.getElementById('submitBtn').classList.toggle('hidden', currentStep !== totalSteps);

            // Update submit button text based on form type
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn && currentStep === totalSteps) {
                if (currentForm === 'keberatan') {
                    submitBtn.innerHTML = '<i class="fa-solid fa-gavel"></i> Kirim Keberatan Resmi';
                    submitBtn.className = 'px-6 py-2.5 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md transition flex items-center gap-2';
                } else {
                    submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Kirim Permohonan';
                    submitBtn.className = 'px-6 py-2.5 text-xs font-bold text-white bg-green-600 hover:bg-green-700 rounded-xl shadow-md transition flex items-center gap-2';
                }
            }
        }

        // Collect form data
        function collectFormData() {
            const form = document.getElementById('ticketForm');
            const formData = new FormData(form);
            const data = {};

            for (let [key, value] of formData.entries()) {
                if (data[key]) {
                    if (Array.isArray(data[key])) {
                        data[key].push(value);
                    } else {
                        data[key] = [data[key], value];
                    }
                } else {
                    data[key] = value;
                }
            }
            return data;
        }

        // Show confirmation for permohonan
        function showConfirmation() {
            const data = collectFormData();
            const container = document.getElementById('confirmationData');
            const formType = document.getElementById('formTypeInput').value;

            if (formType === 'keberatan') {
                // Show keberatan confirmation
                const alasanChecked = document.querySelectorAll('input[name="alasan[]"]:checked');
                const alasanList = Array.from(alasanChecked).map(cb => cb.parentElement.textContent.trim()).join(', ');

                container.innerHTML = `
            <div class="bg-rose-50 p-4 rounded-xl border border-rose-200">
                <h4 class="text-xs font-bold text-rose-700 uppercase tracking-wider mb-3">Referensi Tiket Awal</h4>
                <div class="space-y-2 text-sm">
                    <p><span class="text-slate-500">No. Referensi:</span> <span class="font-medium font-mono">${data.reference_ticket_number || ''}</span></p>
                    <p><span class="text-slate-500">Nama Pemohon:</span> <span class="font-medium">${data.nama_pemohon || ''}</span></p>
                </div>
            </div>
            <div class="bg-slate-50 p-4 rounded-xl">
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Alasan Keberatan</h4>
                <p class="text-sm text-slate-700">${alasanList || 'Tidak ada alasan yang dipilih'}</p>
            </div>
            <div class="bg-sky-50 p-4 rounded-xl border border-sky-200">
                <h4 class="text-xs font-bold text-sky-700 uppercase tracking-wider mb-2">Penjelasan Keberatan</h4>
                <p class="text-sm text-slate-700">${data.penjelasan_keberatan || ''}</p>
            </div>
            <div class="bg-amber-50 p-4 rounded-xl border border-amber-200 flex items-start gap-3">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-0.5"></i>
                <p class="text-xs text-amber-800">Pastikan data di atas sudah benar. Pengajuan keberatan akan diteruskan kepada Atasan PPID untuk diproses maksimal 30 hari kerja.</p>
            </div>
        `;
            } else {
                // Show permohonan confirmation (original)
                container.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-slate-50 p-4 rounded-xl">
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Data Pemohon</h4>
                    <div class="space-y-2 text-sm">
                        <p><span class="text-slate-500">Nama:</span> <span class="font-medium">${data.nama_lengkap || ''}</span></p>
                        <p><span class="text-slate-500">NIK:</span> <span class="font-medium">${data.nik || ''}</span></p>
                        <p><span class="text-slate-500">Kategori:</span> <span class="font-medium capitalize">${data.kategori_pemohon || ''}</span></p>
                        <p><span class="text-slate-500">Email:</span> <span class="font-medium">${data.email || ''}</span></p>
                        <p><span class="text-slate-500">WhatsApp:</span> <span class="font-medium">${data.no_whatsapp || ''}</span></p>
                    </div>
                </div>
                <div class="bg-slate-50 p-4 rounded-xl">
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Rincian Permohonan</h4>
                    <div class="space-y-2 text-sm">
                        <p><span class="text-slate-500">Format:</span> <span class="font-medium capitalize">${data.format_salinan || ''}</span></p>
                        <p><span class="text-slate-500">Cara Penyampaian:</span> <span class="font-medium capitalize">${(data.cara_penyampaian || '').replace('_', ' ')}</span></p>
                    </div>
                </div>
            </div>
            <div class="bg-sky-50 p-4 rounded-xl border border-sky-200">
                <h4 class="text-xs font-bold text-sky-700 uppercase tracking-wider mb-2">Informasi yang Diminta</h4>
                <p class="text-sm text-slate-700">${data.rincian_informasi || ''}</p>
                <p class="text-xs text-slate-500 mt-2"><strong>Tujuan:</strong> ${data.tujuan_penggunaan || ''}</p>
            </div>
            <div class="bg-amber-50 p-4 rounded-xl border border-amber-200 flex items-start gap-3">
                <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-0.5"></i>
                <p class="text-xs text-amber-800">Pastikan data di atas sudah benar. Nomor tiket akan digenerate setelah Anda mengirimkan permohonan ini.</p>
            </div>
        `;
            }
        }

        // Event Listeners
        document.getElementById('nextBtn').addEventListener('click', async function () {
            const currentSection = document.querySelector(`.form-section[data-step="${currentStep}"]`);
            const inputs = currentSection.querySelectorAll('input[required], textarea[required], select[required]');
            let valid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.classList.add('border-red-500');
                } else {
                    input.classList.remove('border-red-500');
                }
            });

            if (!valid) {
                alert('Mohon lengkapi semua field yang wajib diisi.');
                return;
            }

            if (currentStep < totalSteps) {
                // For permohonan, save step data to server
                const formType = document.getElementById('formTypeInput').value;
                if (formType === 'permohonan') {
                    const formData = new FormData(document.getElementById('ticketForm'));
                    // Wajib: server pakai $_POST['step'] untuk menentukan blok mana yang dijalankan
                    formData.append('step', currentStep.toString());

                    try {
                        const response = await fetch('ticket-proses.php', {
                            method: 'POST',
                            body: formData,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });

                        const result = await response.json();
                        if (result.success) {
                            currentStep = result.next_step || (currentStep + 1);
                            updateStepUI();

                            if (currentStep === 3) {
                                showConfirmation();
                            }
                        }
                    } catch (error) {
                        // For demo, just advance
                        currentStep++;
                        updateStepUI();
                        if (currentStep === 3) {
                            showConfirmation();
                        }
                    }
                } else {
                    // For keberatan, just advance without server call
                    currentStep++;
                    updateStepUI();
                    if (currentStep === totalSteps) {
                        showConfirmation();
                    }
                }
            }
        });

        document.getElementById('prevBtn').addEventListener('click', function () {
            if (currentStep > 1) {
                currentStep--;
                updateStepUI();
            }
        });

        document.getElementById('ticketForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('step', totalSteps.toString());
            formData.append('form_type', document.getElementById('formTypeInput').value);

            try {
                const response = await fetch('ticket-proses.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    window.location.href = result.redirect;
                } else {
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                }
            } catch (error) {
                // Demo fallback
                window.location.href = 'ticket.php?number=DEMO-' + Date.now();
            }
        });

        // Form Type Selector
        const formTypeSelector = document.getElementById('formTypeSelector');
        const formTypeInput = document.getElementById('formTypeInput');

        formTypeSelector?.addEventListener('change', function () {
            const selectedType = this.value;
            formTypeInput.value = selectedType;
            currentFormType = selectedType;

            // Reset to step 1 when changing form type
            currentStep = 1;

            // Show/hide form sections based on type
            document.querySelectorAll('.form-section').forEach(section => {
                section.classList.remove('active');
            });

            // Show first section
            const firstSection = document.querySelector('.form-section[data-step="1"]');
            if (firstSection) {
                firstSection.classList.add('active');
            }

            updateStepUI();
            updateRequiredFields();
        });

        // File upload visual feedback
        (function () {
            const container = document.getElementById('ktpUploadContainer');
            const input = document.getElementById('ktpUpload');

            if (!container || !input) return;

            // Click container to trigger file input
            container.addEventListener('click', function (e) {
                if (e.target.tagName !== 'INPUT' || e.target.type !== 'file') {
                    input.click();
                }
            });

            // Drag and drop handlers
            container.addEventListener('dragover', function (e) {
                e.preventDefault();
                e.stopPropagation();
                container.classList.add('border-sky-500', 'bg-sky-50');
            });

            container.addEventListener('dragleave', function (e) {
                e.preventDefault();
                e.stopPropagation();
                container.classList.remove('border-sky-500', 'bg-sky-50');
            });

            container.addEventListener('drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
                container.classList.remove('border-sky-500', 'bg-sky-50');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    input.files = files;
                    updateUploadUI(container, input, files[0].name);
                }
            });

            // File input change handler
            input.addEventListener('change', function () {
                if (this.files.length > 0) {
                    updateUploadUI(container, input, this.files[0].name);
                }
            });

            function updateUploadUI(container, input, fileName) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
                const fileExtension = fileName.split('.').pop().toLowerCase();
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];

                if (!allowedExtensions.includes(fileExtension)) {
                    alert('Format file tidak didukung. Gunakan JPG, PNG, atau PDF.');
                    input.value = '';
                    return;
                }

                // Validate file size (2MB max)
                const maxSize = 2 * 1024 * 1024; // 2MB
                if (input.files[0].size > maxSize) {
                    alert('Ukuran file terlalu besar. Maksimal 2 MB.');
                    input.value = '';
                    return;
                }

                // Update UI to show selected file
                container.innerHTML = `
            <i class="fa-solid fa-file-check text-2xl text-green-600 mb-2"></i>
            <p class="text-xs font-semibold text-slate-700">${fileName}</p>
            <p class="text-[11px] text-slate-400 mt-1">Klik untuk ganti berkas</p>
            <input type="file" name="lampiran_ktp" accept=".jpg,.jpeg,.png,.pdf" class="hidden" id="ktpUpload">
        `;

                // Re-attach event listeners to new elements
                const newInput = document.getElementById('ktpUpload');
                const newContainer = document.getElementById('ktpUploadContainer');

                if (newContainer && newInput) {
                    // Click handler
                    newContainer.addEventListener('click', function (e) {
                        if (e.target.tagName !== 'INPUT' || e.target.type !== 'file') {
                            newInput.click();
                        }
                    });

                    // Drag and drop handlers
                    newContainer.addEventListener('dragover', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        newContainer.classList.add('border-sky-500', 'bg-sky-50');
                    });

                    newContainer.addEventListener('dragleave', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        newContainer.classList.remove('border-sky-500', 'bg-sky-50');
                    });

                    newContainer.addEventListener('drop', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        newContainer.classList.remove('border-sky-500', 'bg-sky-50');

                        const files = e.dataTransfer.files;
                        if (files.length > 0) {
                            newInput.files = files;
                            updateUploadUI(newContainer, newInput, files[0].name);
                        }
                    });

                    // Change handler
                    newInput.addEventListener('change', function () {
                        if (this.files.length > 0) {
                            updateUploadUI(newContainer, newInput, this.files[0].name);
                        }
                    });
                }
            }
        })();

        // Fungsi untuk update required fields berdasarkan form type dan step
        function updateRequiredFields() {
            const formType = document.getElementById('formTypeInput').value;
            const currentStepVal = currentStep;

            document.querySelectorAll('#ticketForm input, #ticketForm textarea, #ticketForm select').forEach(field => {
                if (field.hasAttribute('required') && field.closest('.form-section')) {
                    field.removeAttribute('required');
                }
            });

            let activeStep;
            if (formType === 'keberatan') {
                activeStep = 4;
            } else {
                activeStep = 3;
            }

            document.querySelectorAll(`#ticketForm .form-section[data-step="${currentStepVal}"] input[required], #ticketForm .form-section[data-step="${currentStepVal}"] textarea[required], #ticketForm .form-section[data-step="${currentStepVal}"] select[required]`).forEach(field => {
                field.setAttribute('required', 'required');
            });

            if (currentStepVal === activeStep) {
                document.querySelectorAll(`#ticketForm .form-section[data-step="${currentStepVal}"] input, #ticketForm .form-section[data-step="${currentStepVal}"] textarea, #ticketForm .form-section[data-step="${currentStepVal}"] select`).forEach(field => {
                    if (field.type !== 'hidden' && field.type !== 'submit' && field.type !== 'button') {
                        field.setAttribute('required', 'required');
                    }
                });
            }
        }

        // Initialize
        updateStepUI();
    </script>

</body>

</html>