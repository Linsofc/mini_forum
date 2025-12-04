<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../models/user_model.php';
require_once __DIR__ . '/../models/school_model.php';
require_once __DIR__ . '/../models/reputation_model.php';

function handle_login_page() {
    if (isLoggedIn()) { redirect('index.php'); }
    require_once __DIR__ . '/../views/auth/login_view.php';
}

function handle_register_page() {
    if (isLoggedIn()) { redirect('index.php'); }

    $conn = get_db_connection();
    require_once __DIR__ . '/../models/school_model.php';
    $schools = get_all_sekolah($conn);
    mysqli_close($conn);

    $data = ['schools' => $schools];
    require_once __DIR__ . '/../views/auth/register_view.php';
}

function handle_auth_action() {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    if (empty($action)) {
        redirect('login.php');
        return;
    }

    $conn = null;
    if ($action !== 'logout') {
        $conn = get_db_connection();
        require_once __DIR__ . '/../models/user_model.php';
        require_once __DIR__ . '/../models/reputation_model.php';
    }

    switch ($action) {
        case 'login':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('login.php'); }

            $email = sanitize($_POST['email']);
            $password = $_POST['password'];
            $login_result = login_user($conn, $email, $password);
            
            if ($login_result) {
                $_SESSION['user_id'] = $login_result['id_user'];
                $_SESSION['user_name'] = $login_result['nama'];
                $_SESSION['user_email'] = $login_result['email'];
                $_SESSION['user_role'] = $login_result['role'];
                $_SESSION['user_sekolah'] = $login_result['nama_sekolah'];
                $_SESSION['user_photo'] = !empty($login_result['photo']) ? $login_result['photo'] : 'default-avatar.png';
                $_SESSION['user_poin'] = $login_result['poin'];
                
                $_SESSION['success'] = "Selamat datang kembali, " . $login_result['nama'] . "!";
                redirect('index.php');
            } else {
                $_SESSION['error'] = "Email atau password salah!";
                redirect('login.php');
            }
            break;
            
        case 'register':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('register.php'); }

            $nama = sanitize($_POST['nama']);
            $email = sanitize($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $id_sekolah = intval($_POST['id_sekolah']);
            
            if ($password !== $confirm_password) {
                $_SESSION['error'] = "Password dan konfirmasi password tidak cocok!";
                redirect('register.php');
            }
            
            if (email_exists($conn, $email)) {
                $_SESSION['error'] = "Email sudah terdaftar!";
                redirect('register.php');
            }
            
            if (register_user($conn, $nama, $email, $password, $id_sekolah)) {
                trigger_reputation_action($conn, 'NEW_USER', null, $id_sekolah);
                $_SESSION['success'] = "Pendaftaran berhasil! Silakan login.";
                redirect('login.php');
            } else {
                $_SESSION['error'] = "Pendaftaran gagal! Silakan coba lagi.";
                redirect('register.php');
            }
            break;
            
        case 'logout':
            $_SESSION = [];
            session_destroy();
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            redirect('login.php');
            break;
    }

    if ($conn) {
        mysqli_close($conn);
    }
}
?>