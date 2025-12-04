<?php
// views/user/user_view.php
// Memuat variabel $data dari controller: 
// $user_data, $total_komentar, $user_questions, $user_answers

$user_data = $data['user_data'];
$total_komentar = $data['total_komentar'];
$user_questions = $data['user_questions'];
$user_answers = $data['user_answers'];

$content = '
<div class="main-content">
    <div class="row">
        <div class="col-md-4">
            <div class="card text-center mb-4">
                <div class="card-body">
                    <img src="uploads/' . (!empty($user_data['photo']) ? $user_data['photo'] : 'default-avatar.png') . '"
                         alt="Profile" class="rounded-circle mb-3" width="150" height="150" 
                         style="object-fit: cover; border: 4px solid var(--primary-color);">
                    <h4>' . htmlspecialchars($user_data['nama']) . '</h4>
                    <p class="text-muted">' . htmlspecialchars($user_data['email']) . '</p>
                    <span class="badge bg-primary">' . ucfirst($user_data['role']) . '</span>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-school me-1"></i>' . htmlspecialchars($user_data['nama_sekolah'] ?? 'Unknown') . '
                        </small>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistik</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="stat-item">
                                <h4 class="text-primary">' . $total_komentar . '</h4>
                                <small class="text-muted">Komentar</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <h4 class="text-success">' . $user_data['poin'] . '</h4>
                                <small class="text-muted">Poin</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i>Bergabung: ' . date('d M Y', strtotime($user_data['tanggal_post'] ?? 'now')) . '
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Pertanyaan Terakhir</h5>
                </div>
                <div class="card-body">';
                
if (empty($user_questions)) {
    $content .= '<p class="text-muted text-center">User ini belum mengajukan pertanyaan.</p>';
} else {
    foreach ($user_questions as $q) {
        $content .= '
            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center mb-2 border rounded p-3">
                <a href="question.php?id=' . $q['id_pertanyaan'] . '" class="text-decoration-none text-dark flex-grow-1">
                    ' . htmlspecialchars($q['judul']) . '
                </a>
                <span class="badge bg-primary ms-3">' . $q['total_jawaban'] . ' Jawaban</span>
                <span class="badge bg-info ms-2">' . $q['total_vote'] . ' Votes</span>
            </div>';
    }
}
                
$content .= '
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-comment me-2"></i>Jawaban Terakhir</h5>
                </div>
                <div class="card-body">';

if (empty($user_answers)) {
    $content .= '<p class="text-muted text-center">User ini belum memberikan jawaban.</p>';
} else {
    foreach ($user_answers as $a) {
        $content .= '
            <div class="list-group-item list-group-item-action mb-2 border rounded p-3">
                <a href="question.php?id=' . $a['id_pertanyaan'] . '#answer-' . $a['id_jawaban'] . '" class="text-decoration-none">
                    <p class="text-dark mb-1">' . truncateText(htmlspecialchars($a['isi']), 100) . '</p>
                    <small class="text-muted">Menjawab untuk: <strong>' . htmlspecialchars($a['pertanyaan_judul']) . '</strong></small>
                </a>
                <span class="badge bg-success float-end">' . $a['total_vote'] . ' Votes</span>
            </div>';
    }
}
                
$content .= '
                </div>
            </div>
        </div>
    </div>
</div>';

echo $content;
?>