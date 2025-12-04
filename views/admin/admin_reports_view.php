<?php
$stats = $data['stats'];
$recent_questions = $data['recent_questions'];
$recent_users = $data['recent_users'];
$top_questioners = $data['top_questioners'];
$top_answerers = $data['top_answerers'];

$question_ratio = $stats['total_users'] > 0 ? round($stats['total_questions'] / $stats['total_users'], 2) : 0;
$total_content = $stats['total_questions'] + $stats['total_answers'] + $stats['total_comments'];

$content = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Admin Panel</title>
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

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .stats-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .stats-label {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }

        .activity-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid var(--info-color);
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .navbar-admin {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .progress {
            height: 8px;
            border-radius: 4px;
            background: var(--light-color);
            overflow: hidden;
            margin-bottom: 5px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transition: width 0.6s ease;
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
            <a class="nav-link" href="schools.php">
                <i class="fas fa-school me-2"></i>Manajemen Sekolah
            </a>
            <a class="nav-link" href="questions.php">
                <i class="fas fa-question-circle me-2"></i>Manajemen Pertanyaan
            </a>
            <a class="nav-link active" href="reports.php">
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
                    <i class="fas fa-chart-bar me-2"></i>Dashboard Laporan
                </span>
                <div class="ms-auto">
                    <span class="text-muted">Selamat datang, <strong>' . getUserName() . '</strong></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-number">' . number_format($stats['total_users']) . '</div>
                    <div class="stats-label">Total User</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-number">' . number_format($stats['total_questions']) . '</div>
                    <div class="stats-label">Pertanyaan</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-number">' . number_format($stats['total_answers']) . '</div>
                    <div class="stats-label">Jawaban</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-number">' . number_format($stats['total_comments']) . '</div>
                    <div class="stats-label">Komentar</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-number">' . number_format($stats['total_schools']) . '</div>
                    <div class="stats-label">Sekolah</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="stats-number">' . $question_ratio . '</div>
                    <div class="stats-label">Rasio Q/User</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-container">
                    <h5 class="mb-3"><i class="fas fa-chart-pie me-2"></i>Distribusi Konten</h5>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="mb-3">
                                <div class="stats-number text-danger">' . number_format($stats['total_questions']) . '</div>
                                <small class="text-muted">Pertanyaan</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <div class="stats-number text-success">' . number_format($stats['total_answers']) . '</div>
                                <small class="text-muted">Jawaban</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-3">
                                <div class="stats-number text-info">' . number_format($stats['total_comments']) . '</div>
                                <small class="text-muted">Komentar</small>
                            </div>
                        </div>
                    </div>
                    <div class="progress" style="background-color: #3b82f640;">
                        <div class="progress bg-danger" style="width: ' . ($total_content > 0 ? round(($stats['total_questions'] / $total_content) * 100, 1) : 0) . '%;"></div>
                    </div>
                    <div class="progress" style="background-color: #10b98140;">
                        <div class="progress" style="width: ' . ($total_content > 0 ? round(($stats['total_answers'] / $total_content) * 100, 1) : 0) . '%; background-color: var(--success-color);"></div>
                    </div>
                    <div class="progress" style="background-color: #4f46e540;">
                        <div class="progress" style="width: ' . ($total_content > 0 ? round(($stats['total_comments'] / $total_content) * 100, 1) : 0) . '%; background-color: var(--info-color);"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="chart-container">
                    <h5 class="mb-3"><i class="fas fa-trophy me-2"></i>Top 10 Penanya</h5>
                    <div class="list-group">';

foreach ($top_questioners as $index => $user) {
    $rank = $index + 1;
    $badge_class = $rank == 1 ? 'bg-warning' : ($rank == 2 ? 'bg-secondary' : 'bg-info');
    $content .= '
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge ' . $badge_class . ' me-2">' . $rank . '</span>
                                <strong>' . htmlspecialchars($user['nama']) . '</strong>
                            </div>
                            <span class="badge bg-primary">' . $user['count'] . ' pertanyaan</span>
                        </div>';
}

$content .= '
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-container">
                    <h5 class="mb-3"><i class="fas fa-comment me-2"></i>Top 10 Penjawab</h5>
                    <div class="list-group">';

foreach ($top_answerers as $index => $user) {
    $rank = $index + 1;
    $badge_class = $rank == 1 ? 'bg-warning' : ($rank == 2 ? 'bg-secondary' : 'bg-success');
    $content .= '
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge ' . $badge_class . ' me-2">' . $rank . '</span>
                                <strong>' . htmlspecialchars($user['nama']) . '</strong>
                            </div>
                            <span class="badge bg-primary">' . $user['count'] . ' jawaban</span>
                        </div>';
}

$content .= '
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-container">
                    <h5 class="mb-3"><i class="fas fa-clock me-2"></i>Aktivitas Terbaru</h5>
                    <div class="list-group">';

foreach ($recent_questions as $question) {
    $content .= '
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>' . htmlspecialchars($question['judul']) . '</strong>
                                    <br><small class="text-muted">oleh ' . htmlspecialchars($question['user_name']) . '</small>
                                </div>
                                <small class="text-muted">' . formatTime($question['tanggal_post']) . '</small>
                            </div>
                        </div>';
}

$content .= '
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