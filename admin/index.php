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
$total_cat = $conn->query("SELECT COUNT(*) AS jml FROM categories")->fetch_assoc()['jml'];
$total_docs = $conn->query("SELECT COUNT(*) AS jml FROM documents")->fetch_assoc()['jml'];

include("../admin/header.php");
include("../admin/sidebar.php");
?>

<div class="p-6">
    <h2 class="text-2xl font-bold mb-6">Dashboard</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white shadow rounded-lg p-4 flex items-center gap-4">
            <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
                👤
            </div>
            <div>
                <p class="text-gray-500 text-sm">Total User</p>
                <p class="text-xl font-semibold"><?= $total_users ?></p>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4 flex items-center gap-4">
            <div class="bg-green-100 text-green-600 p-3 rounded-full">
                🏢
            </div>
            <div>
                <p class="text-gray-500 text-sm">Total OPD</p>
                <p class="text-xl font-semibold"><?= $total_opd ?></p>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4 flex items-center gap-4">
            <div class="bg-yellow-100 text-yellow-600 p-3 rounded-full">
                🏷️
            </div>
            <div>
                <p class="text-gray-500 text-sm">Total Kategori</p>
                <p class="text-xl font-semibold"><?= $total_cat ?></p>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4 flex items-center gap-4">
            <div class="bg-red-100 text-red-600 p-3 rounded-full">
                📄
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