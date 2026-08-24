<?php
session_start();
include("../config/database.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $conn->real_escape_string($_POST['role']);

    $sql = "SELECT * FROM users WHERE email='$email' AND role='$role' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['opd_id'] = $user['opd_id'];
            $_SESSION['name'] = $user['name'];

            if ($role === 'super_admin') {
                header("Location: ../admin/index.php");
            } elseif ($role === 'admin_opd') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../admin/index.php");
            }
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "User tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin PPID</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="/../assets/img/bondowoso.ico">
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md">
        <div class="bg-white shadow-lg rounded-lg p-8">
            <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Login Admin PPID</h2>

            <?php if (!empty($error)): ?>
                <div class="mb-4 bg-red-100 text-red-700 px-4 py-2 rounded">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-gray-600 mb-1">Username/Email</label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-600 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-600 mb-1">Role</label>
                    <select name="role" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-400 focus:outline-none">
                        <option value="">-- Pilih Role --</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="admin_opd">Admin OPD</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    Login
                </button>
            </form>
        </div>
        <p class="text-center text-gray-500 text-sm mt-6">
            &copy; <?= date("Y") ?> PPID Bondowoso. All rights reserved.
        </p>
    </div>

</body>

</html>