<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../models/user_model.php';
require_once __DIR__ . '/../models/school_model.php';
require_once __DIR__ . '/../models/comment_model.php';
require_once __DIR__ . '/../models/question_model.php';
require_once __DIR__ . '/../models/answer_model.php'; 

function handle_profile_page() {
    if (!isLoggedIn()) { redirect('login.php'); }

    $conn = get_db_connection();
    $user_id = getUserId();

    $user_data = get_user_by_id($conn, $user_id);
    $schools = get_all_sekolah($conn);
    $total_komentar = get_user_comment_count($conn, $user_id);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = sanitize($_POST['nama']);
        $email = sanitize($_POST['email']);
        $id_sekolah = intval($_POST['id_sekolah']);
        $remove_photo = isset($_POST['remove_photo']);
        
        if (email_exists($conn, $email, $user_id)) {
            $_SESSION['error'] = "Email sudah digunakan oleh user lain!";
        } else {
            $photo = $user_data['photo'];
            
            if ($remove_photo) {
                if ($user_data['photo'] && $user_data['photo'] !== 'default-avatar.png') {
                    $old_file = 'uploads/' . $user_data['photo'];
                    if (file_exists($old_file)) { unlink($old_file); }
                }
                $photo = 'default-avatar.png';
            } elseif (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $max_size = 2 * 1024 * 1024; // 2MB
                
                if (in_array($_FILES['photo']['type'], $allowed_types) && $_FILES['photo']['size'] <= $max_size) {
                    $upload_dir = 'uploads/';
                    if (!file_exists($upload_dir)) { mkdir($upload_dir, 0777, true); }
                    
                    $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                    $file_name = 'user_' . $user_id . '_' . time() . '.' . $file_extension;
                    $upload_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
                        if ($user_data['photo'] && $user_data['photo'] !== 'default-avatar.png') {
                            $old_file = $upload_dir . $user_data['photo'];
                            if (file_exists($old_file)) { unlink($old_file); }
                        }
                        $photo = $file_name;
                    }
                }
            }
            
            if (update_user($conn, $user_id, $nama, $email, $id_sekolah, $photo)) {
                $_SESSION['user_name'] = $nama;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_photo'] = $photo;
                
                $_SESSION['success'] = "Profil berhasil diperbarui!";
            } else {
                $_SESSION['error'] = "Gagal memperbarui profil!";
            }
        }
        mysqli_close($conn);
        redirect("profile.php");
    }

    mysqli_close($conn);

    $data = [
        'user_data' => $user_data,
        'schools' => $schools,
        'total_komentar' => $total_komentar
    ];

    ob_start();
    include __DIR__ . '/../views/user/profile_view.php';
    $content = ob_get_clean();
    require_once __DIR__ . '/../views/layout.php';
}

function handle_user_page() {
    if (!isLoggedIn()) { redirect('login.php'); }

    $conn = get_db_connection();
    $user_id = intval($_GET['id'] ?? 0);

    if ($user_id <= 0) {
        $_SESSION['error'] = "User tidak ditemukan!";
        redirect('index.php');
    }

    $user_data = get_user_by_id($conn, $user_id);

    if (!$user_data) {
        $_SESSION['error'] = "User tidak ditemukan!";
        redirect('index.php');
    }

    $total_komentar = get_user_comment_count($conn, $user_id);
    
    $user_questions = get_user_questions_for_display($conn, $user_id);
    $user_answers = get_user_answers_for_display($conn, $user_id);
    
    mysqli_close($conn);

    $data = [
        'user_data' => $user_data,
        'total_komentar' => $total_komentar,
        'user_questions' => $user_questions,
        'user_answers' => $user_answers,
    ];

    ob_start();
    include __DIR__ . '/../views/user/user_view.php';
    $content = ob_get_clean();
    require_once __DIR__ . '/../views/layout.php';
}

function handle_my_questions_page() {
    if (!isLoggedIn()) { redirect('login.php'); }

    $conn = get_db_connection();
    $user_id = getUserId();
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;

    $my_questions = get_user_questions($conn, $user_id, $per_page, $offset);
    $total_questions = get_total_user_questions($conn, $user_id);
    $total_pages = ceil($total_questions / $per_page);

    mysqli_close($conn);

    $data = [
        'my_questions' => $my_questions,
        'total_questions' => $total_questions,
        'total_pages' => $total_pages,
        'page' => $page,
    ];

    ob_start();
    include __DIR__ . '/../views/user/my_questions_view.php';
    $content = ob_get_clean();
    require_once __DIR__ . '/../views/layout.php';
}

function handle_my_answers_page() {
    if (!isLoggedIn()) { redirect('login.php'); }

    $conn = get_db_connection();
    $user_id = getUserId();
    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;

    $my_answers = get_user_answers($conn, $user_id, $per_page, $offset);
    $total_answers = get_total_user_answers($conn, $user_id);
    $total_pages = ceil($total_answers / $per_page);

    mysqli_close($conn);

    $data = [
        'my_answers' => $my_answers,
        'total_answers' => $total_answers,
        'total_pages' => $total_pages,
        'page' => $page,
    ];

    ob_start();
    include __DIR__ . '/../views/user/my_answers_view.php';
    $content = ob_get_clean();
    require_once __DIR__ . '/../views/layout.php';
}

function handle_change_password() {
    if (!isLoggedIn()) { redirect('login.php'); }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('profile.php'); }

    $conn = get_db_connection();
    $user_id = getUserId();
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $user_data = get_user_by_id($conn, $user_id);

    if (!$user_data || $current_password !== $user_data['password']) {
        $_SESSION['error'] = "Password saat ini salah!";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Password baru dan konfirmasi tidak cocok!";
    } elseif (strlen($new_password) < 6) {
        $_SESSION['error'] = "Password baru minimal 6 karakter!";
    } else {
        if (update_user_password($conn, $user_id, $new_password)) {
            $_SESSION['success'] = "Password berhasil diubah!";
        } else {
            $_SESSION['error'] = "Gagal mengubah password!";
        }
    }
    
    mysqli_close($conn);
    redirect('profile.php');
}

function handle_delete_account() {
    if (!isLoggedIn()) { redirect('login.php'); }
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('profile.php'); }

    $conn = get_db_connection();
    $user_id = getUserId();
    $confirm_delete = strtoupper($_POST['confirm_delete']);
    $password = $_POST['password'];

    $user_data = get_user_by_id($conn, $user_id);

    if ($confirm_delete !== 'HAPUS') {
        $_SESSION['error'] = "Konfirmasi tidak valid! Ketik 'HAPUS' untuk menghapus akun.";
    } elseif (!$user_data || $password !== $user_data['password']) {
        $_SESSION['error'] = "Password salah!";
    } else {
        if (delete_user_and_files($conn, $user_data, $user_id)) {
            session_destroy();
            session_start();
            $_SESSION['success'] = "Akun berhasil dihapus. Semua data Anda telah dihapus permanen.";
            mysqli_close($conn);
            redirect('login.php');
        } else {
            $_SESSION['error'] = "Gagal menghapus akun! Silakan coba lagi.";
        }
    }
    
    mysqli_close($conn);
    redirect('profile.php');
}

function delete_user_and_files($conn, $user_data, $user_id) {
    require_once __DIR__ . '/../models/user_model.php';
    $success = delete_user($conn, $user_id);
    
    if ($success && $user_data['photo'] && $user_data['photo'] !== 'default-avatar.png') {
        $photo_path = 'uploads/' . $user_data['photo']; 
        if (file_exists($photo_path)) {
            unlink($photo_path);
        }
    }
    return $success;
}
?>