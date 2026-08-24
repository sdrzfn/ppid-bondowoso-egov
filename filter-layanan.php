<?php
include 'config/database.php';

$sumber = $_POST['sumber'] ?? '';
$sort = $_POST['sort'] ?? 'desc';

$sql = "SELECT * FROM layanan WHERE 1=1";

// filter sumber
if (!empty($sumber)) {
    $sql .= " AND sumber = '" . $conn->real_escape_string($sumber) . "'";
}

// urutan
$sql .= " ORDER BY id " . ($sort === 'asc' ? 'ASC' : 'DESC');

$query = $conn->query($sql);

if ($query->num_rows > 0) {
    while ($row = $query->fetch_assoc()): ?>
        <article class="rounded-xl border border-slate-200 overflow-hidden bg-white hover:shadow-md transition">
            <div class="aspect-[4/3] bg-slate-200 grid place-content-center text-slate-500">
                <img src="uploads/<?= $row['gambar'] ?>" alt="<?= $row['judul'] ?>" class="w-full h-full object-cover">
            </div>
            <div class="p-5 space-y-3">
                <h3 class="font-semibold text-lg"><?= $row['judul'] ?></h3>
                <p class="text-sm text-slate-600"><?= substr($row['deskripsi'], 0, 100) ?>...</p>
                <div class="flex items-center justify-between pt-2 text-xs">
                    <div class="flex items-center gap-2">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Lambang_Kabupaten_Bondowoso.png/40px-Lambang_Kabupaten_Bondowoso.png"
                            class="w-5 h-5" alt="PPID" />
                        <span class="font-medium"><?= $row['sumber'] ?></span>
                    </div>
                    <a href="detail-layanan.php?id=<?= $row['id'] ?>" class="text-sky-600 hover:underline">Selengkapnya</a>
                </div>
            </div>
        </article>
    <?php endwhile;
} else {
    echo "<p class='col-span-3 text-center text-slate-500 py-8'>Tidak ada layanan ditemukan.</p>";
}
?>