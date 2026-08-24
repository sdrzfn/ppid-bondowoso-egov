<?php
include("../admin/header.php");
include("../admin/sidebar.php");
include("../config/database.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $sql = "INSERT INTO categories (name) VALUES ('$name')";
    $conn->query($sql);
}

$cats = $conn->query("SELECT * FROM categories");
?>

<div class="p-6">
    <h2 class="text-xl font-bold mb-4">Kelola Kategori</h2>
    <form method="POST" class="mb-4 flex gap-2">
        <input type="text" name="name" placeholder="Nama Kategori" required class="border rounded p-2 flex-1">
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Tambah</button>
    </form>

    <table class="min-w-full border border-gray-200 text-sm">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-3 py-2 border">ID</th>
                <th class="px-3 py-2 border">Nama Kategori</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $cats->fetch_assoc()): ?>
                <tr>
                    <td class="px-3 py-2 border"><?= $row['id'] ?></td>
                    <td class="px-3 py-2 border"><?= htmlspecialchars($row['name']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include("../admin/footer.php"); ?>