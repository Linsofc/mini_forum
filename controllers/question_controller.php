<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../models/question_model.php';
require_once __DIR__ . '/../models/tag_model.php';
require_once __DIR__ . '/../models/answer_model.php';
require_once __DIR__ . '/../models/comment_model.php';
require_once __DIR__ . '/../models/vote_model.php';
require_once __DIR__ . '/../models/reputation_model.php';

function handle_index_page()
{
    if (!isLoggedIn()) {
        redirect('login.php');
    }

    $conn = get_db_connection();

    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    if ($page < 1) $page = 1;

    $per_page = 10;
    $offset = ($page - 1) * $per_page;

    $filter = $_GET['filter'] ?? 'terbaru';
    $tag_filter = $_GET['tag'] ?? null;
    $tag_filter = $tag_filter ? intval($tag_filter) : null;

    $questions = get_all_pertanyaan($conn, $per_page, $offset, $filter, $tag_filter);
    $total_questions = get_total_pertanyaan($conn, $tag_filter);
    $total_pages = ceil($total_questions / $per_page);

    $tag_info = null;
    if ($tag_filter) {
        $tag_info = get_tag_by_id($conn, $tag_filter);
    }

    $data = [
        'questions' => $questions,
        'total_questions' => $total_questions,
        'total_pages' => $total_pages,
        'page' => $page,
        'filter' => $filter,
        'tag_filter' => $tag_filter,
        'tag_info' => $tag_info,
    ];

    mysqli_close($conn);

    ob_start();
    include __DIR__ . '/../views/question/index_view.php';
    $content = ob_get_clean();
    require_once __DIR__ . '/../views/layout.php';
}

function handle_question_page()
{
    if (!isLoggedIn()) {
        redirect('login.php');
    }

    $conn = get_db_connection();
    $id = intval($_GET['id'] ?? 0);

    if ($id <= 0) {
        $_SESSION['error'] = "Pertanyaan tidak ditemukan!";
        redirect('index.php');
    }

    $question = get_pertanyaan_by_id($conn, $id);

    if (!$question) {
        $_SESSION['error'] = "Pertanyaan tidak ditemukan!";
        redirect('index.php');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add_answer':
                    $isi = $_POST['isi'];
                    if (!empty($isi)) {
                        if (create_jawaban($conn, $id, getUserId(), $isi)) {
                            trigger_reputation_action($conn, 'NEW_ANSWER', getUserId());
                            $_SESSION['success'] = "Jawaban berhasil ditambahkan!";
                        }
                    }
                    break;

                case 'add_comment':
                    $comment_text = sanitize($_POST['comment']);
                    $comment_type = $_POST['comment_type'];
                    $target_id = intval($_POST['target_id']);

                    $id_komentar_reply_to = !empty($_POST['id_komentar_parent']) ? intval($_POST['id_komentar_parent']) : null;
                    $id_komentar_root = null;

                    if ($id_komentar_reply_to) {
                        $parent_comment = get_komentar_by_id($conn, $id_komentar_reply_to);
                        if ($parent_comment) {
                            $id_komentar_root = $parent_comment['id_komentar_root'] ?? $parent_comment['id_komentar'];
                        }
                    }

                    if (!empty($comment_text)) {
                        $result = false;

                        if ($comment_type === 'question') {
                            $result = create_komentar($conn, getUserId(), $comment_text, $id, null, $id_komentar_root, $id_komentar_reply_to);
                        } else {
                            $result = create_komentar($conn, getUserId(), $comment_text, null, $target_id, $id_komentar_root, $id_komentar_reply_to);
                        }

                        if ($result) {
                            trigger_reputation_action($conn, 'NEW_COMMENT', getUserId());
                            $_SESSION['success'] = "Komentar berhasil ditambahkan!";
                        }
                    }
                    break;
            }
        }
        mysqli_close($conn);
        redirect("question.php?id=$id");
    }

    $answers = get_jawaban_by_pertanyaan($conn, $id);
    $question_comments = get_komentar_by_pertanyaan($conn, $id);
    $question_tags = get_tags_by_pertanyaan($conn, $id);
    $user_vote_question = get_user_vote($conn, getUserId(), $id, null);

    foreach ($answers as &$answer) {
        $answer['comments'] = get_komentar_by_jawaban($conn, $answer['id_jawaban']);
        $answer['user_vote'] = get_user_vote($conn, getUserId(), null, $answer['id_jawaban']);
    }
    unset($answer);

    mysqli_close($conn);

    $data = [
        'id' => $id,
        'question' => $question,
        'answers' => $answers,
        'question_comments' => $question_comments,
        'question_tags' => $question_tags,
        'user_vote_question' => $user_vote_question,
    ];

    ob_start();
    include __DIR__ . '/../views/question/question_detail_view.php';
    $content = ob_get_clean();
    require_once __DIR__ . '/../views/layout.php';
}

function handle_ask_page()
{
    if (!isLoggedIn()) {
        redirect('login.php');
    }

    $conn = get_db_connection();

    $judul_prev = sanitize($_POST['judul'] ?? '');
    $isi_prev = sanitize($_POST['isi'] ?? '');
    $tags_input_prev = sanitize($_POST['tags_input'] ?? '');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $judul = $judul_prev;
        $isi = $isi_prev;
        $tags_input = $tags_input_prev;

        if (empty($judul) || empty($isi)) {
            $_SESSION['error'] = "Judul dan isi pertanyaan tidak boleh kosong!";
            mysqli_close($conn);
            redirect('ask.php');
        }

        $final_tag_ids = [];
        $tag_names = array_map('trim', array_filter(explode(',', $tags_input)));

        if (empty($tag_names)) {
            $_SESSION['error'] = "Masukkan minimal satu tag!";
            mysqli_close($conn);
            redirect('ask.php');
        }

        foreach ($tag_names as $tag_name) {
            $tag_name = strtolower($tag_name);
            if (empty($tag_name)) continue;

            $existing_tag = get_tag_by_name($conn, $tag_name);

            if (!$existing_tag) {
                if (create_tag($conn, $tag_name)) {
                    $new_tag = get_tag_by_name($conn, $tag_name);
                    if ($new_tag) {
                        $final_tag_ids[] = $new_tag['id_tag'];
                    }
                }
            } else {
                $final_tag_ids[] = $existing_tag['id_tag'];
            }
        }

        $final_tag_ids = array_unique($final_tag_ids);

        if (empty($final_tag_ids)) {
            $_SESSION['error'] = "Tag yang dimasukkan tidak valid atau tidak bisa dibuat!";
            mysqli_close($conn);
            redirect('ask.php');
        }

        $id_pertanyaan = create_pertanyaan($conn, getUserId(), $judul, $isi);

        if ($id_pertanyaan) {
            foreach ($final_tag_ids as $tag_id) {
                add_question_tag($conn, $id_pertanyaan, $tag_id);
            }

            $_SESSION['success'] = "Pertanyaan berhasil diposting!";
            mysqli_close($conn);
            redirect('question.php?id=' . $id_pertanyaan);
        } else {
            $_SESSION['error'] = "Gagal memposting pertanyaan!";
            mysqli_close($conn);
            redirect('ask.php');
        }
    }

    mysqli_close($conn);

    $data = [
        'judul_prev' => $judul_prev,
        'isi_prev' => $isi_prev,
        'tags_input_prev' => $tags_input_prev
    ];

    ob_start();
    include __DIR__ . '/../views/question/ask_view.php';
    $content = ob_get_clean();
    require_once __DIR__ . '/../views/layout.php';
}

function handle_search_page()
{
    if (!isLoggedIn()) {
        redirect('login.php');
    }

    $conn = get_db_connection();

    $query = sanitize($_GET['q'] ?? '');
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;

    $results = [];
    $total_results = 0;

    if (!empty($query)) {
        $results = search_pertanyaan($conn, $query, $per_page, $offset);
        $total_results = count($results);
    }

    mysqli_close($conn);

    $data = [
        'query' => $query,
        'results' => $results,
        'total_results' => $total_results,
        'page' => $page,
        'per_page' => $per_page
    ];

    ob_start();
    include __DIR__ . '/../views/question/search_view.php';
    $content = ob_get_clean();
    require_once __DIR__ . '/../views/layout.php';
}
