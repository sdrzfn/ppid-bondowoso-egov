<!DOCTYPE html>
<html lang="id">

<!-- TOP BAR: Aksesibilitas & Informasi Hukum -->
<div id="topBar" class="bg-slate-900 text-slate-300 text-xs py-2 px-4 sm:px-8 flex justify-between items-center border-b border-slate-800 transition-all duration-300">
    <div class="flex items-center space-x-6">
        <span class="font-medium flex items-center gap-1.5">
            <i class="fa-solid fa-building-columns text-slate-400"></i> Republik Indonesia
        </span>
        <span class="hidden md:inline border-l border-slate-700 pl-6 text-slate-400">
            Portal Resmi Keterbukaan Informasi Publik (UU No. 14 Tahun 2008)
        </span>
    </div>
    <div class="flex items-center space-x-4">
        <!-- High Contrast Toggle -->
        <button onclick="toggleHighContrast()" class="hover:text-white flex items-center gap-1 transition" title="Mode Kontras Tinggi">
            <i class="fa-solid fa-circle-half-stroke"></i>
            <span class="hidden sm:inline">Aksesibilitas</span>
        </button>
        <span class="text-slate-700">|</span>
        <!-- Text Size Adjuster -->
        <div class="flex items-center space-x-1 font-bold">
            <button onclick="changeFontSize('normal')" class="px-1 hover:text-white" title="Ukuran Teks Normal">A</button>
            <button onclick="changeFontSize('medium')" class="px-1 hover:text-white text-sm" title="Ukuran Teks Sedang">A+</button>
            <button onclick="changeFontSize('large')" class="px-1 hover:text-white text-base" title="Ukuran Teks Besar">A++</button>
        </div>
    </div>
</div>

<!-- NAVIGATION BAR -->
<header id="mainNavbar" class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm transition-all duration-300">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6 lg:px-8 h-16 sm:h-20">
        <!-- Logo Brand -->
        <a class="flex items-center gap-3" href="index.php">
            <img src="assets/img/bondowoso.png" alt="Logo PPID" class="w-10 h-12" />
            <div>
                <div class="font-extrabold text-slate-900 text-lg leading-tight tracking-tight">PPID</div>
                <div class="text-[10px] sm:text-xs text-slate-500 font-medium">Pejabat Pengelola Informasi & Dokumentasi</div>
            </div>
        </a>

        <!-- Desktop Nav Menu -->
        <nav class="hidden lg:flex items-center space-x-8 text-sm font-semibold text-slate-600">
            <a href="index.php" class="nav-link group relative pb-1 <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-sky-700 active' : '' ?> hover:text-sky-700 transition-all duration-300 hover:-translate-y-0.5">
                Beranda
                <span class="absolute bottom-0 left-0 h-0.5 bg-sky-700 transition-all duration-300 <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'w-full' : 'w-0 group-hover:w-full' ?>"></span>
            </a>
            <a href="profil.php" class="nav-link group relative pb-1 <?= basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'text-sky-700 active' : '' ?> hover:text-sky-700 transition-all duration-300 hover:-translate-y-0.5">
                Profil PPID
                <span class="absolute bottom-0 left-0 h-0.5 bg-sky-700 transition-all duration-300 <?= basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'w-full' : 'w-0 group-hover:w-full' ?>"></span>
            </a>
            <a href="informasi.php" class="nav-link group relative pb-1 <?= basename($_SERVER['PHP_SELF']) == 'informasi.php' ? 'text-sky-700 active' : '' ?> hover:text-sky-700 transition-all duration-300 hover:-translate-y-0.5">
                Katalog Informasi
                <span class="absolute bottom-0 left-0 h-0.5 bg-sky-700 transition-all duration-300 <?= basename($_SERVER['PHP_SELF']) == 'informasi.php' ? 'w-full' : 'w-0 group-hover:w-full' ?>"></span>
            </a>
            <a href="layanan.php" class="nav-link group relative pb-1 <?= basename($_SERVER['PHP_SELF']) == 'layanan.php' ? 'text-sky-700 active' : '' ?> hover:text-sky-700 transition-all duration-300 hover:-translate-y-0.5">
                Layanan Informasi
                <span class="absolute bottom-0 left-0 h-0.5 bg-sky-700 transition-all duration-300 <?= basename($_SERVER['PHP_SELF']) == 'layanan.php' ? 'w-full' : 'w-0 group-hover:w-full' ?>"></span>
            </a>
            <a href="ticket.php" class="nav-link group relative pb-1 <?= in_array(basename($_SERVER['PHP_SELF']), ['ticket.php', 'ticket-form.php', 'ticket-monitoring.php']) ? 'text-sky-700 active' : '' ?> hover:text-sky-700 transition-all duration-300 hover:-translate-y-0.5">
                Keberatan
                <span class="absolute bottom-0 left-0 h-0.5 bg-sky-700 transition-all duration-300 <?= in_array(basename($_SERVER['PHP_SELF']), ['ticket.php', 'ticket-form.php', 'ticket-monitoring.php']) ? 'w-full' : 'w-0 group-hover:w-full' ?>"></span>
            </a>
        </nav>

        <!-- CTA Buttons -->
        <div class="hidden md:flex items-center space-x-3">
            <a href="ticket-monitoring.php" class="inline-flex items-center justify-center px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                <i class="fa-solid fa-magnifying-glass mr-2 text-slate-500"></i> Cek Status
            </a>
            <a href="ticket-form.php" class="inline-flex items-center justify-center px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold text-white bg-sky-700 hover:bg-sky-800 rounded-lg shadow-sm transition">
                Ajukan Permohonan
            </a>
        </div>

        <!-- Mobile Menu Button -->
        <button id="navToggle" class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg hover:bg-slate-100">
            <span class="sr-only">Menu</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                <path d="M3.75 5.25h16.5v1.5H3.75zM3.75 11.25h16.5v1.5H3.75zM3.75 17.25h16.5v1.5H3.75z" />
            </svg>
        </button>
    </div>

    <!-- Mobile Nav -->
    <div id="mobileNav" class="md:hidden hidden border-t border-slate-200 bg-white">
        <div class="container-wide mx-auto px-4 py-3 grid gap-2">
            <a href="index.php" class="nav-link py-2 <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-sky-700 active' : '' ?>">Beranda</a>
            <a href="profil.php" class="nav-link py-2 <?= basename($_SERVER['PHP_SELF']) == 'profil.php' ? 'text-sky-700 active' : '' ?>">Profil PPID</a>
            <a href="informasi.php" class="nav-link py-2 <?= basename($_SERVER['PHP_SELF']) == 'informasi.php' ? 'text-sky-700 active' : '' ?>">Katalog Informasi</a>
            <a href="layanan.php" class="nav-link py-2 <?= basename($_SERVER['PHP_SELF']) == 'layanan.php' ? 'text-sky-700 active' : '' ?>">Layanan Informasi</a>
            <a href="ticket.php" class="nav-link py-2 <?= in_array(basename($_SERVER['PHP_SELF']), ['ticket.php', 'ticket-form.php', 'ticket-monitoring.php']) ? 'text-sky-700 active' : '' ?>">Keberatan</a>
            <div class="border-t border-slate-100 pt-2 mt-2 space-y-2">
                <a href="ticket-monitoring.php" class="flex items-center gap-2 py-2 text-slate-700">
                    <i class="fa-solid fa-magnifying-glass"></i> Cek Status
                </a>
                <a href="ticket-form.php" class="flex items-center gap-2 py-2 text-sky-700 font-semibold">
                    <i class="fa-solid fa-plus"></i> Ajukan Permohonan
                </a>
            </div>
        </div>
    </div>
</header>