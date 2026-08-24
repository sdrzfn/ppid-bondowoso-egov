<?php
include 'config/database.php';

$keyword = isset($_POST['q']) ? trim($_POST['q']) : '';
$sort = isset($_POST['sort']) ? $_POST['sort'] : 'desc';
$status = isset($_POST['status']) ? $_POST['status'] : 'aktif';

$sql = "SELECT * FROM homepage_cards WHERE 1=1";
$params = [];
$types = '';

if (!empty($keyword)) {
    $sql .= " AND (judul LIKE ? OR deskripsi LIKE ? OR kontak LIKE ?)";
    $like = "%" . $keyword . "%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'sss';
}

if (!empty($status)) {
    $sql .= " AND status = ?";
    $params[] = $status;
    $types .= 's';
}

$sortOrder = ($sort === 'asc') ? 'ASC' : 'DESC';
$sql .= " ORDER BY urutan $sortOrder, id $sortOrder LIMIT 3";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($c = $result->fetch_assoc()):
        ?>
        <div class="information-card bg-white rounded-lg card-shadow overflow-hidden border border-slate-200">
            <div class="h-48 bg-slate-100">
                <?php if ($c['gambar']): ?>
                    <img src="<?= htmlspecialchars($c['gambar']) ?>" alt="<?= htmlspecialchars($c['judul']) ?>"
                        class="w-full h-full object-cover" />
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center text-gray-400">No Image</div>
                <?php endif; ?>
            </div>
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-2 leading-tight"><?= htmlspecialchars($c['judul']) ?></h3>
                <p class="text-gray-600 text-sm mb-4 line-clamp-3"><?= htmlspecialchars($c['deskripsi']) ?></p>
                <a href="<?= getCardLink($c['judul']) ?>" class="text-blue-500 text-sm hover:underline">Selengkapnya &gt;&gt;&gt;</a>
                <div class="flex items-center justify-between mt-4 pt-4 border-t">
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500">Uploaded by</span>
                        <img src="assets/img/bondowoso.png" alt="PPID" class="w-6 h-6" />
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-500">Contact</div>
                        <div class="text-xs text-gray-600"><?= htmlspecialchars($c['kontak']) ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    endwhile;
} else {
    echo '<div class="col-span-full text-center text-gray-500 py-8">Tidak ada card yang ditemukan.</div>';
}
?>