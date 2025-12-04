<?php
function register_user($conn, $nama, $email, $password, $id_sekolah) {
    $query = "INSERT INTO user (nama, email, password, id_sekolah) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $nama, $email, $password, $id_sekolah);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function login_user($conn, $email, $password) {
    $query = "SELECT u.*, s.nama_sekolah 
              FROM user u 
              LEFT JOIN sekolah s ON u.id_sekolah = s.id_sekolah 
              WHERE u.email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if ($user && $password === $user['password']) {
        return $user;
    }
    return false;
}

function get_user_by_id($conn, $id) {
    $query = "SELECT u.*, s.nama_sekolah 
              FROM user u 
              LEFT JOIN sekolah s ON u.id_sekolah = s.id_sekolah 
              WHERE u.id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

function get_all_users($conn) {
    $query = "SELECT u.*, s.nama_sekolah 
              FROM user u 
              LEFT JOIN sekolah s ON u.id_sekolah = s.id_sekolah 
              ORDER BY u.id_user DESC";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function update_user($conn, $id, $nama, $email, $id_sekolah, $photo = null) {
    $success = false;
    if ($photo !== null) {
        $query = "UPDATE user SET nama = ?, email = ?, id_sekolah = ?, photo = ? WHERE id_user = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssisi", $nama, $email, $id_sekolah, $photo, $id);
        $success = $stmt->execute();
        $stmt->close();
    } else {
        $query = "UPDATE user SET nama = ?, email = ?, id_sekolah = ? WHERE id_user = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssii", $nama, $email, $id_sekolah, $id);
        $success = $stmt->execute();
        $stmt->close();
    }
    return $success;
}

function update_user_password($conn, $id, $new_password) {
    $query = "UPDATE user SET password = ? WHERE id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $new_password, $id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function email_exists($conn, $email, $exclude_id = null) {
    $query = "SELECT COUNT(*) as count FROM user WHERE email = ?";
    $types = "s";
    $params = [$email];

    if ($exclude_id !== null) {
        $query .= " AND id_user != ?";
        $types .= "i";
        $params[] = $exclude_id;
    }
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    $stmt->close();
    return $count > 0;
}

function get_user_comment_count($conn, $user_id) {
    $query = "SELECT COUNT(*) as total FROM komentar WHERE id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['total'];
    $stmt->close();
    return $count;
}

function delete_user($conn, $id) {
    
    $conn->begin_transaction();
    try {
        $conn->query("DELETE FROM vote WHERE id_user = $id");
        $conn->query("DELETE FROM komentar WHERE id_user = $id");

        $answers_result = $conn->query("SELECT id_jawaban FROM jawaban WHERE id_user = $id");
        $answers = [];
        while ($row = $answers_result->fetch_assoc()) { $answers[] = $row['id_jawaban']; }
        if ($answers) {
            $in_clause = implode(',', $answers);
            $conn->query("DELETE FROM komentar WHERE id_jawaban IN ($in_clause)");
            $conn->query("DELETE FROM vote WHERE id_jawaban IN ($in_clause)");
            $conn->query("DELETE FROM jawaban WHERE id_user = $id");
        }

        $questions_result = $conn->query("SELECT id_pertanyaan FROM pertanyaan WHERE id_user = $id");
        $questions = [];
        while ($row = $questions_result->fetch_assoc()) { $questions[] = $row['id_pertanyaan']; }
        if ($questions) {
            $in_clause = implode(',', $questions);
            $conn->query("DELETE FROM jawaban WHERE id_pertanyaan IN ($in_clause)");
            $conn->query("DELETE FROM komentar WHERE id_pertanyaan IN ($in_clause)");
            $conn->query("DELETE FROM vote WHERE id_pertanyaan IN ($in_clause)");
            $conn->query("DELETE FROM pertanyaan_tag WHERE id_pertanyaan IN ($in_clause)");
            $conn->query("DELETE FROM pertanyaan WHERE id_user = $id");
        }

        $conn->query("DELETE FROM user WHERE id_user = $id");

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error deleting user $id: " . $e->getMessage());
        return false;
    }
}
function get_school_top_users($conn, $id_sekolah) {
    if ($id_sekolah === null) {
        return [];
    }
    
    $query = "SELECT 
                u.id_user,
                u.nama,
                u.photo,
                u.poin, 
                s.nama_sekolah
              FROM user u
              JOIN sekolah s ON u.id_sekolah = s.id_sekolah
              WHERE u.id_sekolah = ?
              ORDER BY u.poin DESC
              LIMIT 10";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_sekolah);
    $stmt->execute();
    $result = $stmt->get_result();
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return $users;
}
?>