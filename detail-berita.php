<?php include 'config/database.php'; ?>
<?php
$id = $_GET['id'];
$detail = $conn->query("SELECT * FROM berita WHERE id=$id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $detail['judul'] ?> - PPID Bondowoso</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<script src="assets/js/speech-consent.js"></script>
<script src="https://cdn.userway.org/widget.js" data-account="d9ZmCPKv7k"></script>


<body class="bg-gray-50 font-sans">

    <?php include 'navbar.php'; ?>

    <!-- Detail Berita -->
    <main class="max-w-4xl mx-auto px-4 py-10">
        <img src="<?= $detail['gambar'] ?>" class="rounded-2xl shadow-lg mb-6 w-full object-cover max-h-[500px]"
            alt="gambar berita">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4"><?= htmlspecialchars($detail['judul']) ?></h2>
        <p class="text-gray-500 text-sm mb-6 pb-4 border-b border-gray-200">
            Dipublikasikan pada: <span class="font-medium"><?= date("d M Y", strtotime($detail['tanggal'])) ?></span> |
            Oleh: <span class="font-medium"><?= htmlspecialchars($detail['penulis']) ?></span>
        </p>
        <article class="prose max-w-none text-gray-700 text-justify leading-relaxed space-y-4">
            <?php
            $isi_berita = str_replace(["\r\n", "\r"], "\n", $detail['isi']);
            $paragraf = explode("\n\n", $isi_berita);

            foreach ($paragraf as $p) {
                if (trim($p) !== '') {
                    echo '<p>' . nl2br(htmlspecialchars(trim($p))) . '</p>';
                }
            }
            ?>
        </article>
    </main>

    <!-- Berita Lain -->
    <section class="max-w-6xl mx-auto px-4 py-10">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">Berita Lainnya</h3>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6">
            <?php
            $lain = $conn->query("SELECT * FROM berita WHERE id!=$id ORDER BY tanggal DESC LIMIT 3");
            while ($row = $lain->fetch_assoc()):
                ?>
                <a href="detail-berita.php?id=<?= $row['id'] ?>"
                    class="bg-white rounded-xl shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <img src="<?= $row['gambar'] ?>" class="rounded-t-xl" alt="berita">
                    <div class="p-4">
                        <h4 class="font-semibold text-lg text-gray-800"><?= $row['judul'] ?></h4>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    </section>

    <?php include 'footer.php'; ?>

</body>

</html>