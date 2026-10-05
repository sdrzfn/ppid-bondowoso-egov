<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../admin/login.php");
    exit;
}

include("../config/database.php");

// Hitung data
$total_users = $conn->query("SELECT COUNT(*) AS jml FROM users")->fetch_assoc()['jml'];
$total_opd = $conn->query("SELECT COUNT(*) AS jml FROM opd")->fetch_assoc()['jml'];
$total_permohonan = $conn->query("SELECT COUNT(*) AS jml FROM tickets")->fetch_assoc()['jml'];
$total_docs = $conn->query("SELECT COUNT(*) AS jml FROM documents")->fetch_assoc()['jml'];

include("../admin/header.php");
include("../admin/sidebar.php");
?>

<div class="p-6">
    <h2 class="text-2xl font-bold mb-6">Dashboard</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white shadow rounded-lg p-4 flex items-center gap-4">
            <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Total User</p>
                <p class="text-xl font-semibold"><?= $total_users ?></p>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4 flex items-center gap-4">
            <div class="bg-green-100 text-green-600 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Total OPD</p>
                <p class="text-xl font-semibold"><?= $total_opd ?></p>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4 flex items-center gap-4">
            <div class="bg-yellow-100 text-yellow-600 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Total Permohonan</p>
                <p class="text-xl font-semibold"><?= $total_permohonan ?></p>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4 flex items-center gap-4">
            <div class="bg-red-100 text-red-600 p-3 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Total Dokumen</p>
                <p class="text-xl font-semibold"><?= $total_docs ?></p>
            </div>
        </div>
    </div>

    <div class="mt-8">
        <h3 class="text-lg font-semibold mb-3">Selamat Datang</h3>
        <div class="bg-white shadow rounded-lg p-4">
            <p class="text-gray-700">
                Halo <span class="font-semibold"><?= $_SESSION['name'] ?></span>,
                Anda login sebagai <span class="italic"><?= $_SESSION['role'] ?></span>.
            </p>
        </div>
    </div>
</div>

<?php include("../admin/footer.php"); ?>