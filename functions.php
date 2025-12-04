<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/user_model.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getUserName() {
    return $_SESSION['user_name'] ?? '';
}

function getUserRole() {
    return $_SESSION['user_role'] ?? 'user';
}

function getUserPhoto() {
    if (isset($_SESSION['user_photo']) && !empty($_SESSION['user_photo'])) {
        return $_SESSION['user_photo'];
    }
    return 'default-avatar.png';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function formatTime($datetime) {
    try {
        $time_zone = new DateTimeZone('Asia/Jakarta');
        $now = new DateTime('now', $time_zone);
        $past = new DateTime($datetime, $time_zone);
        
        if ($past > $now) {
            $past = $now;
        }

        $diff = $now->getTimestamp() - $past->getTimestamp();

        if ($diff < 60) {
            return "baru saja";
        } elseif ($diff < 3600) {
            return floor($diff / 60) . " menit yang lalu";
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . " jam yang lalu";
        } elseif ($diff < 604800) {
            return floor($diff / 86400) . " hari yang lalu";
        } else {
            return $past->format('d M Y');
        }
    } catch (Exception $e) {
        return date('d M Y', strtotime($datetime));
    }
}

function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

function getUserStats() {
    if (!isLoggedIn()) return 0;
    
    $conn = get_db_connection();
    $user_id = getUserId();
    
    $user_data = get_user_by_id($conn, $user_id); 
    mysqli_close($conn);

    $poin = $user_data ? $user_data['poin'] : 0;
    $_SESSION['user_poin'] = $poin; 

    return $poin;
}

function nl_replace_tag($string)
{
    return str_replace(array("\r\n", "\r", "\n"), "<br>", $string);
}

function displayComment($comment, $form_id_prefix, $is_reply = false)
{
    $comment_html = '
    <div class="comment-item border rounded p-3 mb-2 ' . ($is_reply ? 'reply-item' : 'top-level-comment') . '" id="comment-' . $comment['id_komentar'] . '">
        <div class="d-flex align-items-start">
            <a href="user.php?id=' . $comment['id_user'] . '" class="text-decoration-none">
                <img src="uploads/' . (!empty($comment['photo']) ? $comment['photo'] : 'default-avatar.png') . '" 
                     alt="User" class="rounded-circle me-2" width="30" height="30" style="object-fit: cover;">
            </a>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between">
                    <div>
                        <a href="user.php?id=' . $comment['id_user'] . '" class="fw-bold text-dark text-decoration-none">
                            ' . htmlspecialchars($comment['nama']) . '
                        </a>';

    if (!empty($comment['reply_to_user_nama'])) {
        $comment_html .= ' <small class="text-muted"> membalas <a href="#comment-' . $comment['id_komentar_reply_to'] . '">@' . htmlspecialchars($comment['reply_to_user_nama']) . '</a></small>';
    }

    $comment_html .= '
                    </div>
                    <small class="text-muted">
                        <time class="time-ago" datetime="' . htmlspecialchars($comment['tanggal_post']) . '">'
                            . formatTime($comment['tanggal_post']) . 
                        '</time>
                    </small>
                </div>
                <div class="mt-1">' . nl_replace_tag(htmlspecialchars($comment['isi'])) . '</div>
                <div class="mt-1">
                    <a href="#" class="btn-reply small text-primary" 
                       data-comment-id="' . $comment['id_komentar'] . '" 
                       data-comment-user="' . htmlspecialchars($comment['nama']) . '" 
                       data-form-id="' . $form_id_prefix . '">
                       <i class="fas fa-reply"></i> Balas
                    </a>
                </div>
            </div>
        </div>
    </div>';
    return $comment_html;
}
?>