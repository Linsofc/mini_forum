<?php
$questions = $data['questions'];
$total_questions = $data['total_questions'];
$total_pages = $data['total_pages'];
$page = $data['page'];
$message = $data['message'];
$message_type = $data['message_type'];

$content = '
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pertanyaan - Admin Panel</title>
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
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
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
            padding: 15px 20px;
        }

        .question-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .question-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .question-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .question-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
        }

        .tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 10px;
        }

        .tag {
            background: var(--info-color);
            color: white;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .stats-row {
            display: flex;
            gap: 15px;
            margin-bottom: 5px;
        }

        .stat-item {
            background: var(--light-color);
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-action {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 10px;
            }
            
            .stats-row {
                flex-direction: column;
                gap: 5px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
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
            <a class="nav-link active" href="questions.php">
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
        <div class="navbar-admin">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="mb-0">
                    <i class="fas fa-question-circle me-2"></i>Manajemen Pertanyaan
                </h1>
                <div class="d-flex gap-2">
                    <span class="text-muted">
                        Total: <strong>' . number_format($total_questions) . '</strong> pertanyaan
                    </span>
                    <a href="../index.php" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i>Lihat Forum
                    </a>
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
        <div class="row">';

foreach ($questions as $q) {
    $tags_html = '';
    if ($q['tags']) {
        $tag_names = explode(',', $q['tags']);
        foreach ($tag_names as $tag_name) {
            $tags_html .= '<span class="tag">' . trim($tag_name) . '</span>';
        }
    }

    $content .= '
            <div class="col-md-6">
                <div class="question-card">
                    <div class="question-title">' . htmlspecialchars($q['judul']) . '</div>
                    
                    <div class="stats-row">
                        <div class="stat-item">
                            <i class="fas fa-vote-yea me-1"></i>' . $q['total_vote'] . ' Votes
                        </div>
                        <div class="stat-item">
                            <i class="fas fa-comments me-1"></i>' . $q['total_komentar'] . ' Komentar
                        </div>
                    </div>
                    
                    <div class="question-meta">
                        <div>
                            <strong>User:</strong> ' . htmlspecialchars($q['user_nama']) . '
                            <br><small class="text-muted">' . htmlspecialchars($q['user_email']) . '</small>
                            <br><small class="text-muted">' . htmlspecialchars($q['nama_sekolah'] ?? 'Unknown') . '</small>
                        </div>
                        <div>
                            <strong>' . formatTime($q['tanggal_post']) . '</strong>
                        </div>
                    </div>
                    
                    <div class="tags">
                        ' . $tags_html . '
                    </div>
                    
                    <div class="action-buttons">
                        <a href="../question.php?id=' . $q['id_pertanyaan'] . '" class="btn-action btn-primary" style="text-decoration: none;">
                            <i class="fas fa-eye me-1"></i>Lihat Detail
                        </a>
                        <button class="btn-action" style="color: red;" onclick="confirmDelete(' . $q['id_pertanyaan'] . ', \'question\')">
                            <i class="fas fa-trash me-1"></i>Hapus
                        </button>
                    </div>
                </div>
            </div>';
}

$content .= '
        </div>';

// Pagination
if ($total_pages > 1) {
    $content .= '
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">';

    for ($i = 1; $i <= $total_pages; $i++) {
        $active = ($i == $page) ? 'active' : '';
        $url = "questions.php?page=$i";
        $content .= "<li class='page-item $active'>
            <a class='page-link' href='$url'>$i</a>
        </li>";
    }

    $content .= '
            </ul>
        </nav>';
}

$content .= '
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id, type) {
            if (confirm("Apakah Anda yakin ingin menghapus " + (type === "question" ? "pertanyaan" : "jawaban") + " ini?\\n\\nTindakan ini tidak dapat dibatalkan!")) {
                const form = document.createElement("form");
                form.method = "POST";
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                    <input type="hidden" name="type" value="${type}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>';
echo $content;
?>