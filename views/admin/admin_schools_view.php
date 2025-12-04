<?php

$schools = $data['schools'];
$message = $data['message'];
$message_type = $data['message_type'];

$content = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Sekolah - Admin Panel</title>
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

        .school-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
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
            <a class="nav-link" href="users.php">
                <i class="fas fa-users me-2"></i>Manajemen User
            </a>
            <a class="nav-link active" href="schools.php">
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
                    <i class="fas fa-school me-2"></i>Manajemen Sekolah
                </span>
                <div class="ms-auto">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addSchoolModal">
                        <i class="fas fa-plus me-2"></i>Tambah Sekolah
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
        <div class="school-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>ID</th>
                            <th>Nama Sekolah</th>
                            <th>Alamat</th>
                            <th>Total User</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>';
                    
foreach ($schools as $s) {
    $user_count = $s['user_count']; 
    
    $content .= '
                        <tr>
                            <td>' . $s['id_sekolah'] . '</td>
                            <td>' . htmlspecialchars($s['nama_sekolah']) . '</td>
                            <td>' . htmlspecialchars($s['alamat']) . '</td>
                            <td>
                                <span class="badge bg-primary">' . $user_count . ' user</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info btn-action" 
                                        data-bs-toggle="modal" data-bs-target="#editSchoolModal' . $s['id_sekolah'] . '">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-action" 
                                        data-bs-toggle="modal" data-bs-target="#deleteSchoolModal' . $s['id_sekolah'] . '">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        
                        <div class="modal fade" id="editSchoolModal' . $s['id_sekolah'] . '" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Sekolah</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="edit">
                                        <input type="hidden" name="id" value="' . $s['id_sekolah'] . '">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Sekolah</label>
                                                <input type="text" class="form-control" name="nama_sekolah" required 
                                                       value="' . htmlspecialchars($s['nama_sekolah']) . '">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Alamat</label>
                                                <textarea class="form-control" name="alamat" rows="3" required>' . htmlspecialchars($s['alamat']) . '</textarea>
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
                        
                        <div class="modal fade" id="deleteSchoolModal' . $s['id_sekolah'] . '" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-danger">Hapus Sekolah</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="' . $s['id_sekolah'] . '">
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin menghapus sekolah <strong>' . htmlspecialchars($s['nama_sekolah']) . '</strong>?</p>';
    
    if ($user_count > 0) {
        $content .= '<p class="text-warning">Peringatan: ' . $user_count . ' user terdaftar di sekolah ini. User tersebut akan kehilangan informasi sekolahnya.</p>';
    }
    
    $content .= '
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

    <div class="modal fade" id="addSchoolModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Sekolah Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Sekolah</label>
                            <input type="text" class="form-control" name="nama_sekolah" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" name="alamat" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Tambah Sekolah</button>
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