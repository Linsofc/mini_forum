<?php
$query = $data['query'];
$results = $data['results'];
$total_results = $data['total_results'];
$page = $data['page'];
$per_page = $data['per_page'];

$content = '
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-search me-2"></i>Hasil Pencarian</h2>
    </div>';

if (!empty($query)) {
    $content .= '
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Menampilkan hasil pencarian untuk: <strong>"' . htmlspecialchars($query) . '"</strong>
        <span class="float-end">' . number_format($total_results) . ' hasil ditemukan</span>
    </div>';
    
    if (empty($results)) {
        $content .= '
        <div class="text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4>Tidak ada hasil ditemukan</h4>
            <p class="text-muted">Coba gunakan kata kunci yang berbeda atau periksa ejaan Anda</p>
            <div class="mt-3">
                <h5>Saran:</h5>
                <ul class="text-start list-unstyled">
                    <li><i class="fas fa-lightbulb text-warning me-2"></i>Gunakan kata kunci yang lebih spesifik</li>
                    <li><i class="fas fa-lightbulb text-warning me-2"></i>Coba variasi kata kunci</li>
                    <li><i class="fas fa-lightbulb text-warning me-2"></i>Periksa ejaan kata kunci</li>
                    <li><i class="fas fa-lightbulb text-warning me-2"></i>Gunakan kata kunci yang lebih umum</li>
                </ul>
            </div>
            <div class="mt-4">
                <a href="ask.php" class="btn btn-primary me-2">
                    <i class="fas fa-plus-circle me-2"></i>Ajukan Pertanyaan
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-home me-2"></i>Kembali ke Beranda
                </a>
            </div>
        </div>';
    } else {
        $content .= '<div class="search-results">';
        
        foreach ($results as $question) {
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
            
            $highlighted_title = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark>$1</mark>', htmlspecialchars($question['judul']));
            $highlighted_content = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<mark>$1</mark>', truncateText(strip_tags($question['isi']), 200));
            
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
                        <div class="question-title">' . $highlighted_title . '</div>
                        <div class="question-excerpt text-muted mb-2">' . $highlighted_content . '</div>
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
                                    </a>
                                    <div><small class="text-muted">' . htmlspecialchars($question['nama_sekolah'] ?? 'Unknown') . '</small></div>
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
        
        $content .= '</div>';
        
        if ($total_results == $per_page) {
            $content .= '<div class="text-center mt-4">
                <button class="btn btn-outline-primary" onclick="loadMoreResults()">
                    <i class="fas fa-plus me-2"></i>Muat Lebih Banyak
                </button>
            </div>';
        }
    }
} else {
    $content .= '
    <div class="text-center py-5">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h4>Masukkan kata kunci pencarian</h4>
        <p class="text-muted">Ketik kata kunci di kotak pencarian di atas untuk menemukan pertanyaan yang relevan</p>
    </div>';
}

$content .= '
    <div class="search-tips mt-5">
        <h5><i class="fas fa-lightbulb text-warning me-2"></i>Tips Pencarian</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="card border-left-primary mb-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-check-circle text-success me-2"></i>Gunakan Kata Kunci Spesifik</h6>
                        <p class="card-text small">Gunakan kata kunci yang spesifik seperti "PHP MySQL connection" daripada "connection"</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-left-info mb-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-check-circle text-info me-2"></i>Coba Variasi Kata</h6>
                        <p class="card-text small">Jika tidak menemukan hasil, coba gunakan sinonim atau kata yang terkait</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-left-warning mb-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-check-circle text-warning me-2"></i>Gunakan Bahasa Inggris</h6>
                        <p class="card-text small">Beberapa pertanyaan mungkin menggunakan istilah teknis dalam bahasa Inggris</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-left-success mb-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-check-circle text-success me-2"></i>Cari berdasarkan Tag</h6>
                        <p class="card-text small">Gunakan tag seperti "php", "javascript", "mysql" untuk mencari topik spesifik</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
mark {
    background-color: #fff3cd;
    padding: 2px 4px;
    border-radius: 3px;
}

.border-left-primary {
    border-left: 4px solid var(--primary-color) !important;
}

.border-left-info {
    border-left: 4px solid var(--info-color) !important;
}

.border-left-warning {
    border-left: 4px solid var(--warning-color) !important;
}

.border-left-success {
    border-left: 4px solid var(--success-color) !important;
}
</style>

<script>
let currentPage = 1;
let isLoading = false;

function loadMoreResults() {
    if (isLoading) return;
    
    isLoading = true;
    currentPage++;
    
    const query = "' . htmlspecialchars($query) . '";
    const url = `search.php?q=${encodeURIComponent(query)}&page=${currentPage}`;
    
    fetch(url)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, "text/html");
            const newResults = doc.querySelectorAll(".search-results .question-card");
            
            if (newResults.length > 0) {
                const resultsContainer = document.querySelector(".search-results");
                newResults.forEach(card => {
                    resultsContainer.appendChild(card.cloneNode(true));
                });
            }
            
            if (newResults.length < ' . $per_page . ') {
                document.querySelector(".btn-outline-primary").style.display = "none";
            }
            
            isLoading = false;
        })
        .catch(error => {
            console.error("Error:", error);
            isLoading = false;
        });
}
</script>';

echo $content;
?>