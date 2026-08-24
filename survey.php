<?php
include('config/database.php');

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $conn->real_escape_string($_POST['nama']);
    $email = $conn->real_escape_string($_POST['email']);
    $rating = $conn->real_escape_string($_POST['rating']);
    $saran = $conn->real_escape_string($_POST['saran']);

    $stmt = $conn->prepare("INSERT INTO surveys (nama, email, rating, saran) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $email, $rating, $saran);

    if ($stmt->execute()) {
        $success = true;
    } else {
        $error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Survey PPID</title>
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
            font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, 'Apple Color Emoji', 'Segoe UI Emoji';
        }

        .container-wide {
            max-width: 1200px;
        }
    </style>
</head>

<script src="https://cdn.userway.org/widget.js" data-account="d9ZmCPKv7k"></script>

<body class="bg-white text-slate-800">

    <!-- Navbar -->
    <?php include('navbar.php'); ?>

    <!-- Hero -->
    <header class="relative h-60 md:h-72 w-full bg-center bg-cover"
        style="background-image:url('assets/img/cover-survey.jpg');">
        <div class="absolute inset-0 bg-sky-900/40"></div>
        <div class="absolute inset-0 flex items-center">
            <div class="container-wide mx-auto px-4">
                <h1 class="text-white text-3xl md:text-4xl font-extrabold tracking-wide">SURVEY PPID</h1>
            </div>
        </div>
    </header>

    <!-- Survey Content -->
    <main class="max-w-3xl mx-auto px-4 py-12">
        <div class="bg-white shadow rounded-xl p-8">
            <h2 class="text-2xl font-semibold mb-6 text-center">Survey Kepuasan Masyarakat</h2>
            <p class="text-gray-600 text-center mb-8">
                Silakan isi survey berikut untuk membantu kami meningkatkan kualitas layanan PPID Kabupaten Bondowoso.
            </p>

            <!-- Success Message -->
            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    Terima kasih! Survey Anda telah berhasil dikirim.
                </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    Terjadi kesalahan. Silakan coba lagi.
                </div>
            <?php endif; ?>

            <form action="survey.php" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Lengkap<span class="text-red-500">*</span></label>
                    <input type="text" name="nama" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Email<span class="text-red-500">*</span></label>
                    <input type="email" name="email" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-500" />
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Bagaimana penilaian Anda terhadap layanan kami?<span class="text-red-500">*</span></label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2">
                            <input type="radio" name="rating" value="sangat_baik" checked class="text-blue-500" />
                            <span class="text-sm">Sangat Baik</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="rating" value="baik" class="text-blue-500" />
                            <span class="text-sm">Baik</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="rating" value="cukup" class="text-blue-500" />
                            <span class="text-sm">Cukup</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio" name="rating" value="kurang" class="text-blue-500" />
                            <span class="text-sm">Kurang</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Saran & Masukan</label>
                    <textarea name="saran" rows="4"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-sky-500"></textarea>
                </div>

                <div class="pt-4 text-center">
                    <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-semibold px-6 py-2 rounded-lg">
                        Kirim Survey
                    </button>
                </div>
            </form>
        </div>
    </main>

    <?php include('footer.php'); ?>
</body>
</html>