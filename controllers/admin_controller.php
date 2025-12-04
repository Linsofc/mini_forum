<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../models/user_model.php';
require_once __DIR__ . '/../models/school_model.php';
require_once __DIR__ . '/../models/reputation_model.php';
require_once __DIR__ . '/../models/question_model.php';
require_once __DIR__ . '/../models/tag_model.php';

function check_admin_access() {
    if (!isLoggedIn() || !isAdmin()) {
        redirect('../login.php');
    }
}

function handle_admin_dashboard() {
    check_admin_access();
    $conn = get_db_connection();

    $stats = get_admin_dashboard_stats($conn);
    $recent_questions = get_recent_questions($conn, 5);
    $recent_users = get_recent_users($conn, 5);

    mysqli_close($conn);
    
    $data = [
        'stats' => $stats,
        'recent_questions' => $recent_questions,
        'recent_users' => $recent_users,
    ];

    ob_start();
    include __DIR__ . '/../views/admin/admin_dashboard_view.php';
    $content = ob_get_clean();
    echo $content;
}

function handle_admin_users() {
    check_admin_access();
    $conn = get_db_connection();
    $message = '';
    $message_type = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $result = process_admin_user_action($conn, $_POST);
        $message = $result['message'];
        $message_type = $result['type'];
    }

    $users = get_all_users($conn);
    $schools = get_all_sekolah($conn);

    mysqli_close($conn);

    $data = [
        'users' => $users,
        'schools' => $schools,
        'message' => $message,
        'message_type' => $message_type
    ];

    ob_start();
    include __DIR__ . '/../views/admin/admin_users_view.php';
    $content = ob_get_clean();
    echo $content;
}

function process_admin_user_action($conn, $post_data) {
    $action = $post_data['action'];
    $message = '';
    $message_type = 'danger';

    switch ($action) {
        case 'add':
            $nama = sanitize($post_data['nama']);
            $email = sanitize($post_data['email']);
            $password = $post_data['password'];
            $role = $post_data['role'];
            $id_sekolah = intval($post_data['id_sekolah']);
            
            if (register_user($conn, $nama, $email, $password, $id_sekolah)) {
                $query = "UPDATE user SET role = ? WHERE email = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('ss', $role, $email);
                $stmt->execute();
                $stmt->close();

                $message = "User berhasil ditambahkan!";
                $message_type = "success";
            } else {
                $message = "Gagal menambahkan user!";
            }
            break;
            
        case 'edit':
            $id = intval($post_data['id']);
            $nama = sanitize($post_data['nama']);
            $email = sanitize($post_data['email']);
            $role = $post_data['role'];
            $id_sekolah = intval($post_data['id_sekolah']);
            
            if (update_user($conn, $id, $nama, $email, $id_sekolah)) {
                $query = "UPDATE user SET role = ? WHERE id_user = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('si', $role, $id);
                $stmt->execute();
                $stmt->close();
                
                $message = "User berhasil diperbarui!";
                $message_type = "success";
            } else {
                $message = "Gagal memperbarui user!";
            }
            break;
            
        case 'delete':
            $id = intval($post_data['id']);
            if ($id != getUserId()) {
                $user_data_to_delete = get_user_by_id($conn, $id);
                if (delete_user_and_files_admin($conn, $user_data_to_delete, $id)) {
                    $message = "User berhasil dihapus!";
                    $message_type = "success";
                } else {
                    $message = "Gagal menghapus user!";
                }
            } else {
                $message = "Tidak dapat menghapus akun sendiri!";
            }
            break;
    }
    return ['message' => $message, 'type' => $message_type];
}

function delete_user_and_files_admin($conn, $user_data, $user_id) {
    $success = delete_user($conn, $user_id);
    
    if ($success && $user_data['photo'] && $user_data['photo'] !== 'default-avatar.png') {
        $photo_path = '../uploads/' . $user_data['photo'];
        if (file_exists($photo_path)) {
            unlink($photo_path);
        }
    }
    return $success;
}

function handle_admin_schools() {
    check_admin_access();
    $conn = get_db_connection();
    $message = '';
    $message_type = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $result = process_admin_school_action($conn, $_POST);
        $message = $result['message'];
        $message_type = $result['type'];
    }

    $schools = get_all_sekolah($conn);
    
    foreach ($schools as &$s) {
        $s['user_count'] = get_school_user_count($conn, $s['id_sekolah']);
    }
    unset($s);

    mysqli_close($conn);

    $data = [
        'schools' => $schools,
        'message' => $message,
        'message_type' => $message_type
    ];

    ob_start();
    include __DIR__ . '/../views/admin/admin_schools_view.php';
    $content = ob_get_clean();
    echo $content;
}

function process_admin_school_action($conn, $post_data) {
    $action = $post_data['action'];
    $message = '';
    $message_type = 'danger';
    $id = intval($post_data['id'] ?? 0);
    $nama_sekolah = sanitize($post_data['nama_sekolah'] ?? '');
    $alamat = sanitize($post_data['alamat'] ?? '');

    switch ($action) {
        case 'add':
            if (add_sekolah($conn, $nama_sekolah, $alamat)) {
                $message = "Sekolah berhasil ditambahkan!";
                $message_type = "success";
            } else {
                $message = "Gagal menambahkan sekolah!";
            }
            break;
            
        case 'edit':
            if (update_sekolah($conn, $id, $nama_sekolah, $alamat)) {
                $message = "Sekolah berhasil diperbarui!";
                $message_type = "success";
            } else {
                $message = "Gagal memperbarui sekolah!";
            }
            break;
            
        case 'delete':
            if (delete_sekolah($conn, $id)) {
                $message = "Sekolah berhasil dihapus!";
                $message_type = "success";
            } else {
                $message = "Gagal menghapus sekolah! Mungkin masih ada user yang terdaftar.";
            }
            break;
    }
    return ['message' => $message, 'type' => $message_type];
}

function handle_admin_questions() {
    check_admin_access();
    $conn = get_db_connection();
    $message = '';
    $message_type = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $result = process_admin_question_action($conn, $_POST);
        $message = $result['message'];
        $message_type = $result['type'];
    }

    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $per_page = 20;
    $offset = ($page - 1) * $per_page;

    $questions = get_admin_questions_list($conn, $per_page, $offset);
    $total_questions = get_total_pertanyaan($conn);
    $total_pages = ceil($total_questions / $per_page);
    
    foreach ($questions as &$q) {
        $q['tags'] = get_tags_by_pertanyaan_string($conn, $q['id_pertanyaan']);
        $q['total_komentar'] = get_total_comments_for_question_admin($conn, $q['id_pertanyaan']);
        $q['total_jawaban'] = get_total_answers_by_question_id($conn, $q['id_pertanyaan']);
    }
    unset($q);

    mysqli_close($conn);

    $data = [
        'questions' => $questions,
        'total_questions' => $total_questions,
        'total_pages' => $total_pages,
        'page' => $page,
        'message' => $message,
        'message_type' => $message_type
    ];

    ob_start();
    include __DIR__ . '/../views/admin/admin_questions_view.php';
    $content = ob_get_clean();
    echo $content;
}

function process_admin_question_action($conn, $post_data) {
    $action = $post_data['action'];
    $message = '';
    $message_type = 'danger';
    $id = intval($post_data['id'] ?? 0);
    $type = $post_data['type'] ?? '';

    if ($action !== 'delete') return ['message' => 'Aksi tidak valid', 'type' => 'danger'];

    if ($type === 'question') {
        if (delete_full_question($conn, $id)) {
            $message = "Pertanyaan dan semua data terkait berhasil dihapus!";
            $message_type = "success";
        } else {
            $message = "Gagal menghapus data!";
        }
    } elseif ($type === 'answer') {
        if (delete_full_answer($conn, $id)) {
            $message = "Jawaban berhasil dihapus!";
            $message_type = "success";
        } else {
            $message = "Gagal menghapus jawaban!";
        }
    }
    return ['message' => $message, 'type' => $message_type];
}

function handle_admin_reports() {
    check_admin_access();
    $conn = get_db_connection();

    $stats = get_admin_dashboard_stats($conn);
    $recent_questions = get_recent_questions($conn, 5);
    $recent_users = get_recent_users_simple($conn, 5);
    $top_questioners = get_top_questioners($conn, 10);
    $top_answerers = get_top_answerers($conn, 10);

    mysqli_close($conn);
    
    $data = [
        'stats' => $stats,
        'recent_questions' => $recent_questions,
        'recent_users' => $recent_users,
        'top_questioners' => $top_questioners,
        'top_answerers' => $top_answerers,
    ];

    ob_start();
    include __DIR__ . '/../views/admin/admin_reports_view.php';
    $content = ob_get_clean();
    echo $content;
}

function handle_recalculate_points() {
    check_admin_access();
    $conn = get_db_connection();

    if (recalculate_all_points($conn)) {
        $_SESSION['success'] = "Rekalkulasi Poin & Skor berhasil! Semua data historis telah dihitung ulang.";
    } else {
        $_SESSION['error'] = "Gagal melakukan rekalkulasi!";
    }
    
    mysqli_close($conn);
    redirect('index.php');
}


function get_admin_dashboard_stats($conn) {
    $stats = [];
    $stats['total_users'] = $conn->query("SELECT COUNT(*) as count FROM user")->fetch_assoc()['count'];
    $stats['total_questions'] = $conn->query("SELECT COUNT(*) as count FROM pertanyaan")->fetch_assoc()['count'];
    $stats['total_answers'] = $conn->query("SELECT COUNT(*) as count FROM jawaban")->fetch_assoc()['count'];
    $stats['total_comments'] = $conn->query("SELECT COUNT(*) as count FROM komentar")->fetch_assoc()['count'];
    $stats['total_schools'] = $conn->query("SELECT COUNT(*) as count FROM sekolah")->fetch_assoc()['count'];
    return $stats;
}

function get_recent_questions($conn, $limit) {
    $query = "SELECT p.*, u.nama as user_name FROM pertanyaan p JOIN user u ON p.id_user = u.id_user ORDER BY p.tanggal_post DESC LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $questions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $questions;
}

function get_recent_users($conn, $limit) {
    $query = "SELECT * FROM user ORDER BY id_user DESC LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $users;
}

function get_recent_users_simple($conn, $limit) {
    $query = "SELECT nama, email, role FROM user ORDER BY id_user DESC LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $users;
}

function get_top_questioners($conn, $limit) {
    $query = "SELECT u.nama, COUNT(p.id_pertanyaan) as count 
              FROM user u 
              LEFT JOIN pertanyaan p ON u.id_user = p.id_user 
              GROUP BY u.id_user 
              ORDER BY count DESC LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $users;
}

function get_top_answerers($conn, $limit) {
    $query = "SELECT u.nama, COUNT(j.id_jawaban) as count 
              FROM user u 
              LEFT JOIN jawaban j ON u.id_user = j.id_user 
              GROUP BY u.id_user 
              ORDER BY count DESC LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $users;
}

function get_admin_questions_list($conn, $limit, $offset) {
    $query = "SELECT p.id_pertanyaan, p.judul, p.tanggal_post, p.total_vote, u.nama as user_nama, u.email as user_email, s.nama_sekolah
              FROM pertanyaan p
              JOIN user u ON p.id_user = u.id_user
              LEFT JOIN sekolah s ON u.id_sekolah = s.id_sekolah
              ORDER BY p.tanggal_post DESC
              LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $questions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $questions;
}

function get_tags_by_pertanyaan_string($conn, $id_pertanyaan) {
    $query = "SELECT GROUP_CONCAT(t.nama_tag) as tags 
              FROM pertanyaan_tag pt
              JOIN tag t ON pt.id_tag = t.id_tag
              WHERE pt.id_pertanyaan = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_pertanyaan);
    $stmt->execute();
    $result = $stmt->get_result();
    $tags = $result->fetch_assoc()['tags'] ?? '';
    $stmt->close();
    return $tags;
}

function get_total_comments_for_question_admin($conn, $id_pertanyaan) {
    $query = "SELECT COUNT(k.id_komentar) as total
              FROM komentar k
              LEFT JOIN jawaban j ON k.id_jawaban = j.id_jawaban
              WHERE k.id_pertanyaan = ? OR j.id_pertanyaan = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $id_pertanyaan, $id_pertanyaan);
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result->fetch_assoc()['total'] ?? 0;
    $stmt->close();
    return $total;
}

function get_total_answers_by_question_id($conn, $id_pertanyaan) {
    $query = "SELECT COUNT(*) as total FROM jawaban WHERE id_pertanyaan = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_pertanyaan);
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result->fetch_assoc()['total'] ?? 0;
    $stmt->close();
    return $total;
}

function delete_full_question($conn, $id) {
    $conn->begin_transaction();
    try {
        $answer_ids_stmt = $conn->prepare("SELECT id_jawaban FROM jawaban WHERE id_pertanyaan = ?");
        $answer_ids_stmt->bind_param('i', $id);
        $answer_ids_stmt->execute();
        $answer_ids_result = $answer_ids_stmt->get_result();
        $answer_ids = [];
        while ($row = $answer_ids_result->fetch_assoc()) { $answer_ids[] = $row['id_jawaban']; }
        $answer_ids_stmt->close();

        if (!empty($answer_ids)) {
            $in_clause = implode(',', $answer_ids);
            $conn->query("DELETE FROM vote WHERE id_jawaban IN ($in_clause)");
            $conn->query("DELETE FROM komentar WHERE id_jawaban IN ($in_clause)");
        }

        $conn->query("DELETE FROM jawaban WHERE id_pertanyaan = $id");
        $conn->query("DELETE FROM vote WHERE id_pertanyaan = $id");
        $conn->query("DELETE FROM komentar WHERE id_pertanyaan = $id");
        $conn->query("DELETE FROM pertanyaan_tag WHERE id_pertanyaan = $id");
        $conn->query("DELETE FROM pertanyaan WHERE id_pertanyaan = $id");

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error deleting question $id: " . $e->getMessage());
        return false;
    }
}

function delete_full_answer($conn, $id) {
    $conn->begin_transaction();
    try {
        $conn->query("DELETE FROM vote WHERE id_jawaban = $id");
        $conn->query("DELETE FROM komentar WHERE id_jawaban = $id");
        $conn->query("DELETE FROM jawaban WHERE id_jawaban = $id");

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error deleting answer $id: " . $e->getMessage());
        return false;
    }
}
?>