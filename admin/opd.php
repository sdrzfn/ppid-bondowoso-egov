<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header("Location: ../admin/login.php");
    exit;
}

include("../config/database.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $sql = "INSERT INTO opd (name) VALUES ('$name')";
    $conn->query($sql);
}

$opds = $conn->query("SELECT * FROM opd");
include("../admin/header.php");
include("../admin/sidebar.php");
?>

<div class="p-6">
    <h2 class="text-xl font-bold mb-4">Kelola OPD</h2>
    <form method="POST" class="mb-4 flex gap-2">
        <input type="text" name="name" placeholder="Nama OPD" required class="border rounded p-2 flex-1">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Tambah</button>
    </form>

    <table class="min-w-full border border-gray-200 text-sm">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-3 py-2 border">ID</th>
                <th class="px-3 py-2 border">Nama OPD</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $opds->fetch_assoc()): ?>
                <tr>
                    <td class="px-3 py-2 border"><?= $row['id'] ?></td>
                    <td class="px-3 py-2 border"><?= htmlspecialchars($row['name']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include("../admin/footer.php"); ?>