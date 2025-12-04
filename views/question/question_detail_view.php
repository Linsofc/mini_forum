<?php
$id = $data['id'];
$question = $data['question'];
$answers = $data['answers'];
$question_comments = $data['question_comments'];
$question_tags = $data['question_tags'];
$user_vote_question = $data['user_vote_question'];

$tags_html = '';
foreach ($question_tags as $t) {
    $tags_html .= '<a href="index.php?tag=' . $t['id_tag'] . '" class="tag me-1">' . htmlspecialchars($t['nama_tag']) . '</a>';
}

$vote_up_class = ($user_vote_question == 'up') ? 'voted-up' : '';
$vote_down_class = ($user_vote_question == 'down') ? 'voted-down' : '';

$content = '
<div class="main-content">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Beranda</a></li>
                <li class="breadcrumb-item active">Pertanyaan</li>
            </ol>
        </nav>
    </div>

    <div class="question-card mb-4">
        <div class="row">
            <div class="col-auto">
                <div class="vote-section">
                    <button class="vote-btn ' . $vote_up_class . '" onclick="vote(\'up\', ' . $id . ', true)">
                        <i class="fas fa-arrow-up"></i>
                    </button>
                    <div class="vote-count">' . $question['total_vote'] . '</div>
                    <button class="vote-btn ' . $vote_down_class . '" onclick="vote(\'down\', ' . $id . ', true)">
                        <i class="fas fa-arrow-down"></i>
                    </button>
                </div>
            </div>
            <div class="col">
                <h1 class="mb-3">' . htmlspecialchars($question['judul']) . '</h1>
                <div class="question-content mb-3">
                    ' . nl2br(htmlspecialchars($question['isi'])) . '
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <div class="tags mb-2">
                        ' . $tags_html . '
                    </div>
                    <div class="question-meta">
                        <div class="d-flex align-items-center">
                            <a href="user.php?id=' . $question['id_user'] . '" class="text-decoration-none">
                                <img src="uploads/' . (!empty($question['photo']) ? $question['photo'] : 'default-avatar.png') . '" 
                                     alt="User" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                            </a>
                            <div>
                                <a href="user.php?id=' . $question['id_user'] . '" class="fw-bold text-dark text-decoration-none">
                                    ' . htmlspecialchars($question['nama']) . '
                                </a>
                                <div>
                                    <small class="text-muted">
                                        ' . htmlspecialchars($question['nama_sekolah'] ?? 'Unknown') . ' • ' . formatTime($question['tanggal_post']) . '
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>';

if (!empty($question_comments)) {
    $content .= '<div class="comments-section mb-4">
        <h5><i class="fas fa-comments me-2"></i>Komentar (' . count($question_comments) . ')</h5>';

    foreach ($question_comments as $comment) {
        $form_id = 'questionCommentForm';

        $content .= displayComment($comment, $form_id, false);

        $content .= '<div class="replies-container" 
                          id="replies-for-' . $comment['id_komentar'] . '" 
                          style="margin-left: 30px; border-left: 2px solid #eee; padding-left: 10px; margin-top: -10px; margin-bottom: 10px;">
                     </div>';

        if ($comment['reply_count'] > 0) {
            $content .= '<a href="#" class="btn-load-replies small fw-bold" 
                            id="load-replies-for-' . $comment['id_komentar'] . '"
                            data-root-id="' . $comment['id_komentar'] . '" 
                            data-limit="3" 
                            data-offset="0" 
                            data-total="' . $comment['reply_count'] . '"
                            data-form-id="' . $form_id . '"
                            data-target-container="#replies-for-' . $comment['id_komentar'] . '"
                            style="margin-left: 40px; margin-bottom: 10px; display: inline-block;">
                            <i class="fas fa-comment-dots"></i>
                            Lihat (' . $comment['reply_count'] . ') balasan
                         </a>';
        }
    }

    $content .= '</div>';
}

$content .= '
    <form method="POST" class="mb-4" id="questionCommentForm">
        <input type="hidden" name="action" value="add_comment">
        <input type="hidden" name="comment_type" value="question">
        <input type="hidden" name="target_id" value="' . $id . '">

        <div id="questionCommentReplyStatus" class="reply-status mb-2"></div>
        <div id="questionCommentParentInput" class="parent-input-container"></div>

        <div class="mb-3">
            <label class="form-label fw-bold" id="questionCommentLabel">Tambah Komentar</label>
            <textarea class="form-control" name="comment" rows="2" placeholder="Tulis komentar Anda..." required></textarea>
        </div>
        <button type="submit" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-comment me-1"></i>Komentar
        </button>
    </form>

    <div class="answers-section">
        <h3 class="mb-4">
            <i class="fas fa-comments me-2"></i>' . count($answers) . ' Jawaban
        </h3>';

if (empty($answers)) {
    $content .= '
    <div class="text-center py-4 border rounded">
        <i class="fas fa-inbox fa-2x text-muted mb-3"></i>
        <p class="text-muted">Belum ada jawaban. Jadilah yang pertama menjawab!</p>
    </div>';
} else {
    foreach ($answers as $answer) {
        $user_vote_answer = $answer['user_vote'];
        $vote_up_class_ans = ($user_vote_answer == 'up') ? 'voted-up' : '';
        $vote_down_class_ans = ($user_vote_answer == 'down') ? 'voted-down' : '';
        $answer_comments = $answer['comments'];

        $content .= '
        <div class="answer-card border rounded p-4 mb-4" id="answer-' . $answer['id_jawaban'] . '">
            <div class="row">
                <div class="col-auto">
                    <div class="vote-section">
                        <button class="vote-btn ' . $vote_up_class_ans . '" onclick="vote(\'up\', ' . $answer['id_jawaban'] . ', false)">
                            <i class="fas fa-arrow-up"></i>
                        </button>
                        <div class="vote-count">' . $answer['total_vote'] . '</div>
                        <button class="vote-btn ' . $vote_down_class_ans . '" onclick="vote(\'down\', ' . $answer['id_jawaban'] . ', false)">
                            <i class="fas fa-arrow-down"></i>
                        </button>
                    </div>
                </div>
                <div class="col">
                    <div class="answer-content mb-3">
                        ' . nl2br(htmlspecialchars($answer['isi'])) . '
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="answer-meta">
                            <div class="d-flex align-items-center">
                                <a href="user.php?id=' . $answer['id_user'] . '" class="text-decoration-none">
                                    <img src="uploads/' . (!empty($answer['photo']) ? $answer['photo'] : 'default-avatar.png') . '"
                                         alt="User" class="rounded-circle me-2" width="30" height="30" style="object-fit: cover;">
                                </a>
                                <div>
                                    <a href="user.php?id=' . $answer['id_user'] . '" class="fw-bold text-dark text-decoration-none">
                                        ' . htmlspecialchars($answer['nama']) . '
                                    </a>
                                    <div>
                                        <small class="text-muted">
                                            ' . htmlspecialchars($answer['nama_sekolah'] ?? 'Unknown') . ' • ' . formatTime($answer['tanggal_post']) . '
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';

        if (!empty($answer_comments)) {
            $content .= '<div class="answer-comments mt-3">
        <div class="comments-list">';

            foreach ($answer_comments as $comment) {
                $form_id = 'answerCommentForm-' . $answer['id_jawaban'];

                $content .= displayComment($comment, $form_id, false);

                $content .= '<div class="replies-container" 
                          id="replies-for-' . $comment['id_komentar'] . '" 
                          style="margin-left: 30px; border-left: 2px solid #eee; padding-left: 10px; margin-top: -10px; margin-bottom: 10px;">
                     </div>';

                if ($comment['reply_count'] > 0) {
                    $content .= '<a href="#" class="btn-load-replies small fw-bold" 
                            id="load-replies-for-' . $comment['id_komentar'] . '"
                            data-root-id="' . $comment['id_komentar'] . '" 
                            data-limit="3" 
                            data-offset="0" 
                            data-total="' . $comment['reply_count'] . '"
                            data-form-id="' . $form_id . '"
                            data-target-container="#replies-for-' . $comment['id_komentar'] . '"
                            style="margin-left: 40px; margin-bottom: 10px; display: inline-block;">
                            <i class="fas fa-comment-dots"></i>
                            Lihat (' . $comment['reply_count'] . ') balasan
                         </a>';
                }
            }

            $content .= '</div>';
        }

        $content .= '
            <form method="POST" class="mt-3" id="answerCommentForm-' . $answer['id_jawaban'] . '">
                <input type="hidden" name="action" value="add_comment">
                <input type="hidden" name="comment_type" value="answer">
                <input type="hidden" name="target_id" value="' . $answer['id_jawaban'] . '">

                <div id="answerCommentReplyStatus-' . $answer['id_jawaban'] . '" class="reply-status mb-2"></div>
                <div id="answerCommentParentInput-' . $answer['id_jawaban'] . '" class="parent-input-container"></div>

                <div class="mb-3">
                    <label class="form-label fw-bold" id="answerCommentLabel-' . $answer['id_jawaban'] . '">Tambah Komentar</label>
                    <textarea class="form-control" name="comment" rows="2" placeholder="Tulis komentar Anda..." required></textarea>
                </div>
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-comment me-1"></i>Komentar
                </button>
            </form>
        </div>
    </div>
</div>';
    }
}

if ($question['id_user'] != getUserId()) {
    $content .= '
    <div class="add-answer-section">
        <h4 class="mb-3"><i class="fas fa-plus-circle me-2"></i>Tulis Jawaban</h4>
        <form method="POST">
            <input type="hidden" name="action" value="add_answer">
            <div class="mb-3">
                <textarea class="form-control" name="isi" rows="8" required 
                          placeholder="Tulis jawaban Anda yang membantu..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Kirim Jawaban
            </button>
        </form>
    </div>';
}

$content .= '
    </div>
</div>';

echo $content;
?>