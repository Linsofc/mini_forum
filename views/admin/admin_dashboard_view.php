<?php
$stats = $data['stats'];
$recent_questions = $data['recent_questions'];
$recent_users = $data['recent_users'];

$content = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Mini Forum</title>
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

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .recent-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 3px solid var(--info-color);
            transition: all 0.3s ease;
        }

        .recent-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .navbar-admin {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="p-4">
            <h4><i class="fas fa-cogs me-2"></i>Admin Panel</h4>
        </div>
        <nav class="nav flex-column">
            <a class="nav-link active" href="index.php">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </a>
            <a class="nav-link" href="users.php">
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
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </span>
                <div class="ms-auto">
                    <span class="text-muted">Selamat datang, <strong>' . getUserName() . '</strong></span>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-2">
                <div class="stat-card text-center">
                    <div class="stat-icon bg-primary text-white mx-auto">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number">' . number_format($stats['total_users']) . '</div>
                    <div class="text-muted">Total User</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card text-center">
                    <div class="stat-icon bg-success text-white mx-auto">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <div class="stat-number">' . number_format($stats['total_questions']) . '</div>
                    <div class="text-muted">Pertanyaan</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card text-center">
                    <div class="stat-icon bg-info text-white mx-auto">
                        <i class="fas fa-comment"></i>
                    </div>
                    <div class="stat-number">' . number_format($stats['total_answers']) . '</div>
                    <div class="text-muted">Jawaban</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card text-center">
                    <div class="stat-icon bg-warning text-white mx-auto">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-number">' . number_format($stats['total_comments']) . '</div>
                    <div class="text-muted">Komentar</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card text-center">
                    <div class="stat-icon bg-danger text-white mx-auto">
                        <i class="fas fa-school"></i>
                    </div>
                    <div class="stat-number">' . number_format($stats['total_schools']) . '</div>
                    <div class="text-muted">Sekolah</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="stat-card text-center">
                    <div class="stat-icon bg-secondary text-white mx-auto">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-number">' . round($stats['total_questions'] / max($stats['total_users'], 1), 1) . '</div>
                    <div class="text-muted">Rasio Q/User</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Pertanyaan Terbaru</h5>
                    </div>
                    <div class="card-body">';
                    
if (empty($recent_questions)) {
    $content .= '<p class="text-muted">Belum ada pertanyaan</p>';
} else {
    foreach ($recent_questions as $q) {
        $content .= '
                        <div class="recent-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>' . htmlspecialchars($q['judul']) . '</strong>
                                    <br><small class="text-muted">oleh ' . htmlspecialchars($q['user_name']) . '</small>
                                </div>
                                <small class="text-muted">' . formatTime($q['tanggal_post']) . '</small>
                            </div>
                        </div>';
    }
}

$content .= '
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>User Terbaru</h5>
                    </div>
                    <div class="card-body">';
                    
if (empty($recent_users)) {
    $content .= '<p class="text-muted">Belum ada user</p>';
} else {
    foreach ($recent_users as $u) {
        $role_class = $u['role'] === 'admin' ? 'bg-danger' : 'bg-primary';
        $content .= '
                        <div class="recent-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>' . htmlspecialchars($u['nama']) . '</strong>
                                    <br><small class="text-muted">' . htmlspecialchars($u['email']) . '</small>
                                </div>
                                <div>
                                    <span class="badge ' . $role_class . '">' . ucfirst($u['role']) . '</span>
                                </div>
                            </div>
                        </div>';
    }
}

$content .= '
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="users.php" class="btn btn-primary w-100">
                                    <i class="fas fa-user-plus me-2"></i>Manajemen User
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="schools.php" class="btn btn-success w-100">
                                    <i class="fas fa-school me-2"></i>Manajemen Sekolah
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="questions.php" class="btn btn-info w-100">
                                    <i class="fas fa-list me-2"></i>Manajemen Pertanyaan
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="reports.php" class="btn btn-warning w-100">
                                    <i class="fas fa-chart-bar me-2"></i>Lihat Laporan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';
echo $content;
?>