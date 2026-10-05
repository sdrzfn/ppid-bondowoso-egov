<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../admin/login.php");
    exit;
}

include '../config/database.php';

// Handle Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id != $_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE id = $id");
        header("Location: users.php?status=deleted");
        exit;
    }
}

// Handle Create
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $conn->real_escape_string($_POST['role']);
    $opd_id = !empty($_POST['opd_id']) ? (int)$_POST['opd_id'] : NULL;

    $conn->query("INSERT INTO users (name, email, password, role, opd_id) 
                  VALUES ('$name', '$email', '$password', '$role', " . ($opd_id ? $opd_id : 'NULL') . ")");
    header("Location: users.php?status=created");
    exit;
}

// Handle Update (khusus super_admin)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    if ($_SESSION['role'] != 'super_admin') {
        header("Location: users.php");
        exit;
    }

    $id = (int)$_POST['id'];
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $role = $conn->real_escape_string($_POST['role']);
    $opd_id = !empty($_POST['opd_id']) ? (int)$_POST['opd_id'] : NULL;
    $password = $_POST['password'] ?? '';

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET name='$name', email='$email', role='$role', opd_id=" . ($opd_id ? $opd_id : 'NULL') . ", password='$hashed' WHERE id=$id");
    } else {
        $conn->query("UPDATE users SET name='$name', email='$email', role='$role', opd_id=" . ($opd_id ? $opd_id : 'NULL') . " WHERE id=$id");
    }

    header("Location: users.php?status=updated");
    exit;
}

// Query users
$result = $conn->query("SELECT users.*, opd.name as opd_name FROM users LEFT JOIN opd ON users.opd_id=opd.id ORDER BY users.id ASC");
$opds = $conn->query("SELECT * FROM opd ORDER BY name ASC");

include '../admin/header.php';
include '../admin/sidebar.php';
?>

<h2 class="text-2xl font-bold mb-6 ml-6 mt-6">Daftar User</h2>

<?php if (isset($_GET['status']) && $_GET['status'] == 'created'): ?>
    <div class="ml-6 mr-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
        User berhasil ditambahkan.
    </div>
<?php elseif (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
    <div class="ml-6 mr-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
        User berhasil dihapus.
    </div>
<?php elseif (isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
    <div class="ml-6 mr-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
        Data user berhasil diperbarui.
    </div>
<?php endif; ?>

<!-- Form Tambah User -->
<div class="ml-6 mr-6 bg-white shadow-md rounded-lg p-6 mb-8">
    <h3 class="text-lg font-semibold mb-4">Tambah User Baru</h3>
    <form method="POST" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-1">Nama</label>
                <input type="text" name="name" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
            </div>
            <div>
                <label class="block font-semibold mb-1">Email</label>
                <input type="email" name="email" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
            </div>
            <div>
                <label class="block font-semibold mb-1">Role</label>
                <select name="role" id="roleSelect"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
                    <option value="super_admin">Super Admin</option>
                    <option value="admin_opd">Admin OPD</option>
                </select>
            </div>
        </div>
        <div id="opdField" class="hidden">
            <label class="block font-semibold mb-1">Pilih OPD</label>
            <select name="opd_id"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring focus:ring-blue-200">
                <option value="">-- Tidak Ada / N/A --</option>
                <?php while ($o = $opds->fetch_assoc()): ?>
                    <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" name="create"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition">
            <i class="fa-solid fa-user-plus mr-1"></i> Tambah User
        </button>
    </form>
</div>

<!-- Tabel User -->
<div class="ml-6 mr-6 bg-white shadow-md rounded-lg overflow-hidden">
    <table class="min-w-full border border-gray-200">
        <thead class="bg-blue-600 text-white">
            <tr>
                <th class="px-4 py-3 text-left">ID</th>
                <th class="px-4 py-3 text-left">Nama</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Role</th>
                <th class="px-4 py-3 text-left">OPD</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-500"><?= $row['id'] ?></td>
                        <td class="px-4 py-3 font-medium text-gray-800"><?= htmlspecialchars($row['name']) ?></td>
                        <td class="px-4 py-3 text-sm"><?= htmlspecialchars($row['email']) ?></td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-sm font-semibold
                                <?= $row['role'] === 'super_admin' ? 'bg-purple-100 text-purple-700' : 'bg-yellow-100 text-yellow-700' ?>">
                                <?= $row['role'] === 'super_admin' ? 'Super Admin' : 'Admin OPD' ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <?= $row['opd_name'] ? htmlspecialchars($row['opd_name']) : '<span class="text-gray-400">-</span>' ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php if ($_SESSION['role'] === 'super_admin'): ?>
                                <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                    <button
                                        onclick="openEditModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>', '<?= $row['role'] ?>', <?= $row['opd_id'] ?: 0 ?>)"
                                        class="text-blue-600 hover:text-blue-800 mr-2 text-xs font-semibold" title="Edit">
                                        <i class="fa-solid fa-edit"></i> Edit
                                    </button>
                                    <a href="?delete=<?= $row['id'] ?>"
                                        onclick="return confirm('Yakin ingin menghapus user <?= htmlspecialchars($row['name'], ENT_QUOTES) ?>?')"
                                        class="text-red-600 hover:text-red-800 text-xs font-semibold" title="Hapus">
                                        <i class="fa-solid fa-trash"></i> Hapus
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-400 text-xs">(Anda)</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">Tidak ada user</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Edit User -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Edit User</h2>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Form Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <form method="POST" class="space-y-4">
                <input type="hidden" name="id" id="editId">
                <div>
                    <label class="block font-semibold mb-1">Nama</label>
                    <input type="text" name="name" id="editName" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Email</label>
                    <input type="email" name="email" id="editEmail" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Password (kosongkan jika tidak ingin diubah)</label>
                    <input type="password" name="password" id="editPassword"
                        placeholder="Biarkan kosong untuk mempertahankan password lama"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block font-semibold mb-1">Role</label>
                    <select name="role" id="editRole"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="super_admin">Super Admin</option>
                        <option value="admin_opd">Admin OPD</option>
                    </select>
                </div>
                <div id="editOpdField" class="hidden">
                    <label class="block font-semibold mb-1">Pilih OPD</label>
                    <select name="opd_id" id="editOpd"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Tidak Ada / N/A --</option>
                        <?php
                        $opds->data_seek(0);
                        while ($o = $opds->fetch_assoc()): ?>
                            <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Footer Actions -->
                <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeModal()"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" name="update"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fa-solid fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Toggle OPD field based on role (form tambah)
    document.getElementById('roleSelect')?.addEventListener('change', function () {
        const opdField = document.getElementById('opdField');
        if (this.value === 'admin_opd') {
            opdField.classList.remove('hidden');
        } else {
            opdField.classList.add('hidden');
        }
    });

    // Toggle OPD field based on role (modal edit)
    document.getElementById('editRole')?.addEventListener('change', function () {
        const opdField = document.getElementById('editOpdField');
        if (this.value === 'admin_opd') {
            opdField.classList.remove('hidden');
        } else {
            opdField.classList.add('hidden');
        }
    });

    function openEditModal(id, name, email, role, opdId) {
        document.getElementById('editId').value = id;
        document.getElementById('editName').value = name;
        document.getElementById('editEmail').value = email;
        document.getElementById('editPassword').value = '';
        document.getElementById('editRole').value = role;
        document.getElementById('editOpd').value = opdId || '';

        // Toggle OPD field
        const opdField = document.getElementById('editOpdField');
        if (role === 'admin_opd') {
            opdField.classList.remove('hidden');
        } else {
            opdField.classList.add('hidden');
        }

        const modal = document.getElementById('editModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModal() {
        const modal = document.getElementById('editModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Close modal when clicking outside
    document.getElementById('editModal')?.addEventListener('click', function (e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('editModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeModal();
            }
        }
    });
</script>

<?php include("../admin/footer.php"); ?>