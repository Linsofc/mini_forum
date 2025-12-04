<?php
// views/user/my_answers_view.php
// Memuat variabel $data dari controller: 
// $my_answers, $total_answers, $total_pages, $page

$my_answers = $data['my_answers'];
$total_answers = $data['total_answers'];
$total_pages = $data['total_pages'];
$page = $data['page'];

$content = '
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-comment me-2"></i>Jawaban Saya</h2>
        <span class="text-muted">' . number_format($total_answers) . ' jawaban</span>
    </div>';

if (empty($my_answers)) {
    $content .= '
    <div class="text-center py-5">
        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
        <h4>Belum ada jawaban</h4>
        <p class="text-muted">Mulai menjawab pertanyaan untuk membantu orang lain!</p>
        <a href="index.php" class="btn btn-primary">
            <i class="fas fa-home me-2"></i>Lihat Pertanyaan
        </a>
    </div>';
} else {
    foreach ($my_answers as $answer) {
        $content .= '
        <div class="answer-card border rounded p-4 mb-4">
            <div class="answer-header mb-3">
                <h6 class="text-primary mb-2">
                    <i class="fas fa-question-circle me-2"></i>
                    <a href="question.php?id=' . $answer['id_pertanyaan'] . '" class="text-decoration-none text-primary">
                    ' . htmlspecialchars($answer['pertanyaan_judul']) . '
                    </a>
                </h6>
            </div>
            <div class="answer-content mb-3">
                ' . nl2br(htmlspecialchars($answer['isi'])) . '
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="answer-meta">
                    <div class="d-flex align-items-center">
                        <img src="uploads/' . (!empty($answer['photo']) ? $answer['photo'] : 'default-avatar.png') . '"
                             alt="User" class="rounded-circle me-2" width="30" height="30">
                        <div>
                            <div class="fw-bold">' . htmlspecialchars($answer['nama']) . '</div>
                            <small class="text-muted">
                                ' . htmlspecialchars($answer['nama_sekolah'] ?? 'Unknown') . ' • ' . formatTime($answer['tanggal_post']) . '
                            </small>
                        </div>
                    </div>
                </div>
                <div class="vote-section">
                    <div class="vote-count">' . $answer['total_vote'] . '</div>
                    <div class="text-muted small">votes</div>
                </div>
            </div>
        </div>';
    }
    
    if ($total_pages > 1) {
        $content .= '<nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">';
        
        for ($i = 1; $i <= $total_pages; $i++) {
            $active = ($i == $page) ? 'active' : '';
            $url = "my-answers.php?page=$i";
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