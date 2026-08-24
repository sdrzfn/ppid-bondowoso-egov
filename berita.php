<?php include 'config/database.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PPID Bondowoso - Berita</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<script src="assets/js/speech-consent.js"></script>
<script src="https://cdn.userway.org/widget.js" data-account="d9ZmCPKv7k"></script>


<body class="bg-gray-50 font-sans">

    <?php include 'navbar.php'; ?>

    <!-- Headline -->
    <section class="max-w-6xl mx-auto px-4 py-8">
        <?php
        $headline = $conn->query("SELECT * FROM berita ORDER BY tanggal DESC LIMIT 1")->fetch_assoc();
        ?>
        <div class="grid md:grid-cols-2 gap-6 items-center">
            <img src="<?= $headline['gambar'] ?>" alt="headline"
                class="rounded-2xl shadow-lg hover:scale-105 transition">
            <div>
                <span class="text-sm text-gray-500">Headline</span>
                <h2 class="text-3xl font-bold text-gray-800 mb-4"><?= $headline['judul'] ?></h2>
                <p class="text-gray-600 mb-6"><?= substr($headline['isi'], 0, 150) ?>...</p>
                <a href="detail-berita.php?id=<?= $headline['id'] ?>"
                    class="px-6 py-2 bg-green-700 text-white rounded-lg shadow hover:bg-green-800 transition">Baca
                    Selengkapnya</a>
            </div>
        </div>
    </section>

    <!-- Grid Berita Lain -->
    <section class="max-w-6xl mx-auto px-4 py-10">
        <h3 class="text-2xl font-bold text-gray-800 mb-6">Berita Terbaru</h3>
        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-6">
            <?php
            $berita = $conn->query("SELECT * FROM berita ORDER BY tanggal DESC LIMIT 6 OFFSET 1");
            while ($row = $berita->fetch_assoc()):
                ?>
                <a href="detail-berita.php?id=<?= $row['id'] ?>"
                    class="bg-white rounded-xl shadow hover:shadow-lg transition transform hover:-translate-y-1">
                    <img src="<?= $row['gambar'] ?>" class="rounded-t-xl" alt="berita">
                    <div class="p-4">
                        <h4 class="font-semibold text-lg text-gray-800"><?= $row['judul'] ?></h4>
                        <p class="text-sm text-gray-600 mt-2"><?= substr($row['isi'], 0, 100) ?>...</p>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    </section>

    <?php include 'footer.php'; ?>

</body>

</html>