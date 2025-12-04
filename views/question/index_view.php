<?php
$questions = $data['questions'];
$total_questions = $data['total_questions'];
$total_pages = $data['total_pages'];
$page = $data['page'];
$filter = $data['filter'];
$tag_filter = $data['tag_filter'];
$tag_info = $data['tag_info'];

$content = '
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-question-circle me-2"></i>Semua Pertanyaan</h2>
        <span class="text-muted">' . number_format($total_questions) . ' pertanyaan</span>
    </div>

    <ul class="nav nav-tabs filter-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link ' . ($filter == 'terbaru' ? 'active' : '') . '" 
               href="index.php?filter=terbaru' . ($tag_filter ? '&tag=' . $tag_filter : '') . '">
                <i class="fas fa-clock me-1"></i>Terbaru
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link ' . ($filter == 'populer' ? 'active' : '') . '" 
               href="index.php?filter=populer' . ($tag_filter ? '&tag=' . $tag_filter : '') . '">
                <i class="fas fa-fire me-1"></i>Populer
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link ' . ($filter == 'a-z' ? 'active' : '') . '" 
               href="index.php?filter=a-z' . ($tag_filter ? '&tag=' . $tag_filter : '') . '">
                <i class="fas fa-sort-alpha-down me-1"></i>A-Z
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link ' . ($filter == 'z-a' ? 'active' : '') . '" 
               href="index.php?filter=z-a' . ($tag_filter ? '&tag=' . $tag_filter : '') . '">
                <i class="fas fa-sort-alpha-up me-1"></i>Z-A
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link ' . ($filter == 'terlama' ? 'active' : '') . '" 
               href="index.php?filter=terlama' . ($tag_filter ? '&tag=' . $tag_filter : '') . '">
                <i class="fas fa-history me-1"></i>Terlama
            </a>
        </li>
    </ul>';

if ($tag_filter && $tag_info) {
    $content .= '<div class="alert alert-info">
        <i class="fas fa-filter me-2"></i>Menampilkan pertanyaan dengan tag: <strong>' . htmlspecialchars($tag_info['nama_tag']) . '</strong>
        <a href="index.php" class="float-end text-decoration-none">×</a>
    </div>';
}

if (empty($questions)) {
    $content .= '
    <div class="text-center py-5">
        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
        <h4>Belum ada pertanyaan</h4>
        <p class="text-muted">Menjadi yang pertama mengajukan pertanyaan!</p>
        <a href="ask.php" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Ajukan Pertanyaan
        </a>
    </div>';
} else {
    foreach ($questions as $question) {
        $conn = get_db_connection();
        $user_vote = get_user_vote($conn, getUserId(), $question['id_pertanyaan']);
        mysqli_close($conn);

        $vote_up_class = ($user_vote == 'up') ? 'voted-up' : '';
        $vote_down_class = ($user_vote == 'down') ? 'voted-down' : '';
        
        $tags = '';
        if ($question['tags']) {
            $tag_names = explode(',', $question['tags']);
            foreach ($tag_names as $tag_name) {
                $tags .= '<span class="tag me-1">' . trim($tag_name) . '</span>';
            }
        }
        
        $content .= '
        <div class="question-card" onclick="window.location.href=\'question.php?id=' . $question['id_pertanyaan'] . '\'">
            <div class="row">
                <div class="col-auto">
                    <div class="vote-section">
                        <button class="vote-btn ' . $vote_up_class . '" onclick="event.stopPropagation(); vote(\'up\', ' . $question['id_pertanyaan'] . ', true)">
                            <i class="fas fa-arrow-up"></i>
                        </button>
                        <div class="vote-count">' . $question['total_vote'] . '</div>
                        <button class="vote-btn ' . $vote_down_class . '" onclick="event.stopPropagation(); vote(\'down\', ' . $question['id_pertanyaan'] . ', true)">
                            <i class="fas fa-arrow-down"></i>
                        </button>
                    </div>
                </div>
                <div class="col">
                    <div class="question-title">' . htmlspecialchars($question['judul']) . '</div>
                    <div class="question-excerpt text-muted mb-2">' . truncateText(strip_tags($question['isi']), 150) . '</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="question-meta">
                            <span><i class="fas fa-comment me-1"></i>' . $question['total_jawaban'] . ' jawaban</span>
                            <span><i class="fas fa-clock me-1"></i>' . formatTime($question['tanggal_post']) . '</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <a href="user.php?id=' . $question['id_user'] . '" onclick="event.stopPropagation()" class="text-decoration-none">
                                <img src="uploads/' . (!empty($question['photo']) ? $question['photo'] : 'default-avatar.png') . '" 
                                     alt="User" class="rounded-circle me-2" width="30" height="30" style="object-fit: cover;">
                            </a>
                            <div>
                                <a href="user.php?id=' . $question['id_user'] . '" onclick="event.stopPropagation()" class="fw-bold text-dark text-decoration-none">
                                    ' . htmlspecialchars($question['nama']) . '
                                </a> <br>
                                <small class="text-muted">' . htmlspecialchars($question['nama_sekolah'] ?? 'Unknown') . '</small>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        ' . $tags . '
                    </div>
                </div>
            </div>
        </div>';
    }
    
    if ($total_pages > 1) {
        $content .= '<nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination flex-wrap">';

        $base_url = "index.php?filter=$filter" . ($tag_filter ? '&tag=' . $tag_filter : '');

        if ($page > 1) {
            $content .= '<li class="page-item d-none d-md-block"><a class="page-link" href="' . $base_url . '&page=1">&laquo; Awal</a></li>';
        } else {
            $content .= '<li class="page-item d-none d-md-block disabled"><span class="page-link">&laquo; Awal</span></li>';
        }

        if ($page > 1) {
            $content .= '<li class="page-item d-none d-md-block"><a class="page-link" href="' . $base_url . '&page=' . ($page - 1) . '">&lsaquo; Prev</a></li>';
        } else {
            $content .= '<li class="page-item d-none d-md-block disabled"><span class="page-link">&lsaquo; Prev</span></li>';
        }

        $links_to_show = 3;
        $start = $page - floor($links_to_show / 2);
        $end = $page + floor($links_to_show / 2);

        if ($start < 1) { $start = 1; $end = min($total_pages, $links_to_show); }
        if ($end > $total_pages) { $end = $total_pages; $start = max(1, $total_pages - $links_to_show + 1); }

        if ($start > 1) { $content .= '<li class="page-item d-none d-md-block disabled"><span class="page-link">...</span></li>'; }
        for ($i = $start; $i <= $end; $i++) {
            $active = ($i == $page) ? 'active' : '';
            $url = $base_url . "&page=$i";
            $content .= "<li class='page-item d-none d-md-block $active'><a class='page-link' href='$url'>$i</a></li>";
        }
        if ($end < $total_pages) { $content .= '<li class="page-item d-none d-md-block disabled"><span class="page-link">...</span></li>'; }

        if ($page < $total_pages) {
            $content .= '<li class="page-item d-none d-md-block"><a class="page-link" href="' . $base_url . '&page=' . ($page + 1) . '">Next &rsaquo;</a></li>';
        } else {
            $content .= '<li class="page-item d-none d-md-block disabled"><span class="page-link">Next &rsaquo;</span></li>';
        }

        if ($page < $total_pages) {
            $content .= '<li class="page-item d-none d-md-block"><a class="page-link" href="' . $base_url . '&page=' . $total_pages . '">Akhir &raquo;</a></li>';
        } else {
            $content .= '<li class="page-item d-none d-md-block disabled"><span class="page-link">Akhir &raquo;</span></li>';
        }

        $content .= '
            <li class="page-item ms-3 d-none d-md-flex" style="max-width: 160px;">
                <form method="GET" action="index.php" class="input-group input-group-sm">
                    <input type="number" class="form-control" name="page" min="1" max="' . $total_pages . '" 
                           placeholder="Halaman..." value="' . $page . '" aria-label="Halaman">
                    <input type="hidden" name="filter" value="' . htmlspecialchars($filter) . '">
                    ' . ($tag_filter ? '<input type="hidden" name="tag" value="' . htmlspecialchars($tag_filter) . '">' : '') . '
                    <button class="btn btn-primary" type="submit">Go</button>
                </form>
            </li>';
            
        $content .= '
            <li class="page-item d-block d-md-none w-100 text-center">
                <a class="page-link" href="#" id="mobile-page-toggle" 
                   data-total-pages="' . $total_pages . '" 
                   data-base-url="' . htmlspecialchars($base_url) . '"
                   data-current-page="' . $page . '">
                   Halaman ' . $page . ' / ' . $total_pages . ' (Tampilkan Semua)
                </a>
            </li>
        ';
    
        $content .= '</ul></nav>';
        $content .= '<div id="mobile-page-list-container" class="mt-2" style="display: none;"></div>';
    }
}

$content .= '</div>';

echo $content;
?>