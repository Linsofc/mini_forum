<?php
// views/user/my_questions_view.php
// Memuat variabel $data dari controller: 
// $my_questions, $total_questions, $total_pages, $page

$my_questions = $data['my_questions'];
$total_questions = $data['total_questions'];
$total_pages = $data['total_pages'];
$page = $data['page'];

$content = '
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-question-circle me-2"></i>Pertanyaan Saya</h2>
        <span class="text-muted">' . number_format($total_questions) . ' pertanyaan</span>
    </div>';

if (empty($my_questions)) {
    $content .= '
    <div class="text-center py-5">
        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
        <h4>Belum ada pertanyaan</h4>
        <p class="text-muted">Mulai dengan mengajukan pertanyaan pertama Anda!</p>
        <a href="ask.php" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Ajukan Pertanyaan
        </a>
    </div>';
} else {
    foreach ($my_questions as $question) {
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
                        <div class="vote-count">' . $question['total_vote'] . '</div>
                        <div class="text-muted small">votes</div>
                    </div>
                </div>
                <div class="col">
                    <div class="question-title">' . htmlspecialchars($question['judul']) . '</div>
                    <div class="question-excerpt text-muted mb-2">' . truncateText(strip_tags($question['isi']), 150) . '</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="question-meta">
                            <span><i class="fas fa-comment me-1"></i>' . $question['total_jawaban'] . ' jawaban</span>
                            <span><i class="fas fa-eye me-1"></i>' . rand(10, 500) . ' views</span>
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
        $content .= '<nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">';
        
        for ($i = 1; $i <= $total_pages; $i++) {
            $active = ($i == $page) ? 'active' : '';
            $url = "my-questions.php?page=$i";
            $content .= "<li class='page-item $active'>
                <a class='page-link' href='$url'>$i</a>
            </li>";
        }
        
        $content .= '</ul></nav>';
    }
}

$content .= '</div>';

echo $content;
?>