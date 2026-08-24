<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'super_admin') {
    header("Location: ../admin/login.php");
    exit;
}

include("../config/database.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $opd_id = $_POST['opd_id'] ?? NULL;

    $sql = "INSERT INTO users (name,email,password,role,opd_id) VALUES ('$name','$email','$password','$role','$opd_id')";
    $conn->query($sql);
}

$result = $conn->query("SELECT users.*, opd.name as opd_name FROM users LEFT JOIN opd ON users.opd_id=opd.id");
include("../admin/header.php");
include("../admin/sidebar.php");
?>
<h2>Kelola User</h2>
<form method="POST">
    Nama: <input type="text" name="name" required><br>
    Email: <input type="email" name="email" required><br>
    Password: <input type="password" name="password" required><br>
    Role:
    <select name="role">
        <option value="super_admin">Super Admin</option>
        <option value="admin_opd">Admin OPD</option>
    </select><br>
    OPD ID (jika Admin OPD): <input type="number" name="opd_id"><br>
    <button type="submit">Tambah User</button>
</form>

<table border="1">
    <tr>
        <th>Nama</th>
        <th>Email</th>
        <th>Role</th>
        <th>OPD</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['name'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['role'] ?></td>
            <td><?= $row['opd_name'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<?= include("../admin/footer.php"); ?>