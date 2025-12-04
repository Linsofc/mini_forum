<?php
$users = $data['users'];
$schools = $data['schools'];
$message = $data['message'];
$message_type = $data['message_type'];

$content = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #7c3aed;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --light-color: #f8fafc;
            --dark-color: #1e293b;
        }

        body {
            background-color: var(--light-color);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            color: white;
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            z-index: 1000;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 10px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .navbar-admin {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .user-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .btn-action {
            padding: 5px 10px;
            margin: 0 2px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="p-4">
            <h4><i class="fas fa-cogs me-2"></i>Admin Panel</h4>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link" href="index.php">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a class="nav-link active" href="users.php">
                <i class="fas fa-users me-2"></i>Manajemen User
            </a>
            <a class="nav-link" href="schools.php">
                <i class="fas fa-school me-2"></i>Manajemen Sekolah
            </a>
            <a class="nav-link" href="questions.php">
                <i class="fas fa-question-circle me-2"></i>Manajemen Pertanyaan
            </a>
            <a class="nav-link" href="reports.php">
                <i class="fas fa-chart-bar me-2"></i>Laporan
            </a>
            <a class="nav-link" href="../index.php">
                <i class="fas fa-home me-2"></i>Kembali ke Forum
            </a>
            <a class="nav-link text-warning" href="recalculate_points.php" onclick="return confirm(`Anda yakin ingin menghitung ulang semua poin dan skor? Ini akan me-reset semua skor dan menghitung ulang dari awal berdasarkan data historis.`)">
                <i class="fas fa-calculator me-2"></i>Hitung Ulang Poin
            </a>
            <a class="nav-link text-warning" href="../auth.php?action=logout">
                <i class="fas fa-sign-out-alt me-2"></i>Keluar
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="navbar-admin navbar navbar-expand-lg">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1">
                    <i class="fas fa-users me-2"></i>Manajemen User
                </span>
                <div class="ms-auto">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-plus me-2"></i>Tambah User
                    </button>
                </div>
            </div>
        </div>';

if (!empty($message)) {
    $content .= '
        <div class="alert alert-' . $message_type . ' alert-dismissible fade show" role="alert">
            ' . $message . '
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>';
}

$content .= '
        <div class="user-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Sekolah</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>';
                    
foreach ($users as $u) {
    $role_badge = $u['role'] === 'admin' ? 'bg-danger' : 'bg-primary';
    $content .= '
                        <tr>
                            <td>' . $u['id_user'] . '</td>
                            <td>
                                <img src="../uploads/' . (!empty($u['photo']) ? $u['photo'] : 'default-avatar.png') . '" 
                                     alt="Avatar" class="user-avatar">
                            </td>
                            <td>' . htmlspecialchars($u['nama']) . '</td>
                            <td>' . htmlspecialchars($u['email']) . '</td>
                            <td>' . htmlspecialchars($u['nama_sekolah'] ?? 'Unknown') . '</td>
                            <td>
                                <span class="badge ' . $role_badge . '">' . ucfirst($u['role']) . '</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info btn-action" 
                                        data-bs-toggle="modal" data-bs-target="#editUserModal' . $u['id_user'] . '">
                                    <i class="fas fa-edit"></i>
                                </button>';
    
    if ($u['id_user'] != getUserId()) {
        $content .= '
                                <button type="button" class="btn btn-sm btn-danger btn-action" 
                                        data-bs-toggle="modal" data-bs-target="#deleteUserModal' . $u['id_user'] . '">
                                    <i class="fas fa-trash"></i>
                                </button>';
    }
    
    $content .= '
                            </td>
                        </tr>
                        
                        <div class="modal fade" id="editUserModal' . $u['id_user'] . '" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="id" value="' . $u['id_user'] . '">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nama</label>
                                                <input type="text" class="form-control" name="nama" required 
                                                       value="' . htmlspecialchars($u['nama']) . '">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" class="form-control" name="email" required 
                                                       value="' . htmlspecialchars($u['email']) . '">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Sekolah</label>
                                                <select class="form-select" name="id_sekolah" required>';
                                                
foreach ($schools as $school) {
    $selected = ($u['id_sekolah'] == $school['id_sekolah']) ? 'selected' : '';
    $content .= '<option value="' . $school['id_sekolah'] . '" ' . $selected . '>' . htmlspecialchars($school['nama_sekolah']) . '</option>';
}

$content .= '
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Role</label>
                                                <select class="form-select" name="role" required>
                                                    <option value="user" ' . ($u['role'] === 'user' ? 'selected' : '') . '>User</option>
                                                    <option value="admin" ' . ($u['role'] === 'admin' ? 'selected' : '') . '>Admin</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal fade" id="deleteUserModal' . $u['id_user'] . '" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-danger">Hapus User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="' . $u['id_user'] . '">
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin menghapus user <strong>' . htmlspecialchars($u['nama']) . '</strong>?</p>
                                            <p class="text-warning">Semua data terkait user ini akan dihapus.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>';
}

$content .= '
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah User Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sekolah</label>
                            <select class="form-select" name="id_sekolah" required>';
                            
foreach ($schools as $school) {
    $content .= '<option value="' . $school['id_sekolah'] . '">' . htmlspecialchars($school['nama_sekolah']) . '</option>';
}

$content .= '
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambah User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
echo $content;
?>