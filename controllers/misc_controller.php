<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../models/comment_model.php';
require_once __DIR__ . '/../models/vote_model.php';
require_once __DIR__ . '/../models/reputation_model.php';
require_once __DIR__ . '/../models/school_model.php';
require_once __DIR__ . '/../models/tag_model.php';
require_once __DIR__ . '/../models/user_model.php';

function handle_load_replies() {
    if (!isLoggedIn()) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Silakan login terlebih dahulu!']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid request method!']);
        exit;
    }

    $conn = get_db_connection();
    
    $id_root = intval($_POST['id_root'] ?? 0);
    $limit = intval($_POST['limit'] ?? 3);
    $offset = intval($_POST['offset'] ?? 0);
    $form_id = sanitize($_POST['form_id'] ?? 'commentForm');

    if ($id_root <= 0) {
        mysqli_close($conn);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid comment ID!']);
        exit;
    }

    $replies = get_komentar_replies_paginated($conn, $id_root, $limit, $offset);
    mysqli_close($conn);

    $html = '';
    foreach ($replies as $reply) {
        $html .= displayComment($reply, $form_id, true);
    }

    $replies_fetched = count($replies);
    $new_offset = $offset + $replies_fetched;

    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'html' => $html,
        'new_offset' => $new_offset,
        'replies_fetched' => $replies_fetched
    ]);
}

function handle_vote_action() {
    if (!isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu!']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Invalid request method!']);
        exit;
    }

    $type = $_POST['type'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    $is_question = isset($_POST['is_question']) && $_POST['is_question'] == '1';

    if (!in_array($type, ['up', 'down']) || $id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters!']);
        exit;
    }

    $conn = get_db_connection();
    $user_id = getUserId();
    
    $result = process_vote($conn, $user_id, $type, $is_question ? $id : null, $is_question ? null : $id);

    if ($result['success']) {
        if (!$is_question) {
            trigger_reputation_action($conn, 'VOTE_ANSWER', $user_id, $result['target_id'], $result['vote_details']);
        }
        
        mysqli_close($conn);
        echo json_encode(['success' => true]);
    } else {
        mysqli_close($conn);
        echo json_encode(['success' => false, 'message' => 'Gagal melakukan vote!']);
    }
}

function handle_leaderboard_page() {
    if (!isLoggedIn()) { redirect('login.php'); }

    $conn = get_db_connection();
    
    $current_user_data = get_user_by_id($conn, getUserId());
    $id_sekolah_user = $current_user_data['id_sekolah'];
    $nama_sekolah_user = $current_user_data['nama_sekolah'];
    
    $user_leaderboard_global = get_user_leaderboard($conn);
    
    $user_leaderboard_school = get_school_top_users($conn, $id_sekolah_user);
    
    mysqli_close($conn);

    $data = [
        'user_leaderboard_global' => $user_leaderboard_global,
        'user_leaderboard_school' => $user_leaderboard_school,
        'nama_sekolah_user' => $nama_sekolah_user
    ];

    ob_start();
    include __DIR__ . '/../views/leaderboard_view.php';
    $content = ob_get_clean();
    require_once __DIR__ . '/../views/layout.php';
}
?>