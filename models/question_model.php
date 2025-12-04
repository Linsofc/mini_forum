<?php
function create_pertanyaan($conn, $id_user, $judul, $isi) {
    $query = "INSERT INTO pertanyaan (id_user, judul, isi) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $id_user, $judul, $isi);
    
    $success = $stmt->execute();
    $id_pertanyaan = $stmt->insert_id;
    $stmt->close();
    
    return $success ? $id_pertanyaan : false;
}

function add_question_tag($conn, $id_pertanyaan, $id_tag) {
    $query = "INSERT INTO pertanyaan_tag (id_pertanyaan, id_tag) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $id_pertanyaan, $id_tag);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function get_all_pertanyaan($conn, $limit = 10, $offset = 0, $filter = 'terbaru', $tag_filter = null) {
    $order_by = "ORDER BY p.tanggal_post DESC";
    
    switch($filter) {
        case 'terlama': $order_by = "ORDER BY p.tanggal_post ASC"; break;
        case 'populer': $order_by = "ORDER BY p.total_vote DESC, p.tanggal_post DESC"; break;
        case 'a-z': $order_by = "ORDER BY p.judul ASC"; break;
        case 'z-a': $order_by = "ORDER BY p.judul DESC"; break;
    }
    
    $where_clause = "";
    $types = "ii";
    $params = [$limit, $offset];

    if ($tag_filter !== null) {
        $where_clause = "AND pt.id_tag = ?";
        $types = "i" . $types;
        array_unshift($params, $tag_filter);
    }
    
    $query = "SELECT 
                p.*, u.nama, u.photo, s.nama_sekolah,
                COUNT(DISTINCT j.id_jawaban) as total_jawaban,
                GROUP_CONCAT(DISTINCT t.nama_tag) as tags
              FROM pertanyaan p
              JOIN user u ON p.id_user = u.id_user
              LEFT JOIN sekolah s ON u.id_sekolah = s.id_sekolah
              LEFT JOIN jawaban j ON p.id_pertanyaan = j.id_pertanyaan
              LEFT JOIN pertanyaan_tag pt ON p.id_pertanyaan = pt.id_pertanyaan
              LEFT JOIN tag t ON pt.id_tag = t.id_tag
              WHERE 1=1 $where_clause
              GROUP BY p.id_pertanyaan
              $order_by
              LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $questions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $questions;
}

function get_pertanyaan_by_id($conn, $id) {
    $query = "SELECT 
                p.*, u.nama, u.photo, s.nama_sekolah
              FROM pertanyaan p
              JOIN user u ON p.id_user = u.id_user
              LEFT JOIN sekolah s ON u.id_sekolah = s.id_sekolah
              WHERE p.id_pertanyaan = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $question = $result->fetch_assoc();
    $stmt->close();
    return $question;
}

function search_pertanyaan($conn, $keyword, $limit = 10, $offset = 0) {
    $query = "SELECT 
                p.*, u.nama, u.photo, s.nama_sekolah,
                COUNT(DISTINCT j.id_jawaban) as total_jawaban,
                GROUP_CONCAT(DISTINCT t.nama_tag) as tags
              FROM pertanyaan p
              JOIN user u ON p.id_user = u.id_user
              LEFT JOIN sekolah s ON u.id_sekolah = s.id_sekolah
              LEFT JOIN jawaban j ON p.id_pertanyaan = j.id_pertanyaan
              LEFT JOIN pertanyaan_tag pt ON p.id_pertanyaan = pt.id_pertanyaan
              LEFT JOIN tag t ON pt.id_tag = t.id_tag
              WHERE p.judul LIKE ? OR p.isi LIKE ? OR t.nama_tag LIKE ?
              GROUP BY p.id_pertanyaan
              ORDER BY p.tanggal_post DESC
              LIMIT ? OFFSET ?";
    
    $search_term = "%$keyword%";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssii", $search_term, $search_term, $search_term, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $questions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $questions;
}

function get_total_pertanyaan($conn, $tag_filter = null) {
    $where_clause = "";
    $types = "";
    $params = [];

    if ($tag_filter !== null) {
        $where_clause = "WHERE pt.id_tag = ?";
        $types = "i";
        $params[] = $tag_filter;
    }
    
    $query = "SELECT COUNT(DISTINCT p.id_pertanyaan) as total
              FROM pertanyaan p
              LEFT JOIN pertanyaan_tag pt ON p.id_pertanyaan = pt.id_pertanyaan
              $where_clause";
    
    $stmt = $conn->prepare($query);
    if ($tag_filter !== null) { $stmt->bind_param($types, ...$params); }
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result->fetch_assoc()['total'];
    $stmt->close();
    return $total;
}

function get_total_user_questions($conn, $user_id) {
    $query = "SELECT COUNT(*) as total FROM pertanyaan WHERE id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result->fetch_assoc()['total'];
    $stmt->close();
    return $total;
}

function get_user_questions($conn, $user_id, $limit, $offset) {
    $query = "SELECT p.*, u.nama, u.photo, s.nama_sekolah,
                     COUNT(DISTINCT j.id_jawaban) as total_jawaban,
                     GROUP_CONCAT(DISTINCT t.nama_tag) as tags
              FROM pertanyaan p
              JOIN user u ON p.id_user = u.id_user
              LEFT JOIN sekolah s ON u.id_sekolah = s.id_sekolah
              LEFT JOIN jawaban j ON p.id_pertanyaan = j.id_pertanyaan
              LEFT JOIN pertanyaan_tag pt ON p.id_pertanyaan = pt.id_pertanyaan
              LEFT JOIN tag t ON pt.id_tag = t.id_tag
              WHERE p.id_user = ?
              GROUP BY p.id_pertanyaan
              ORDER BY p.tanggal_post DESC
              LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $user_id, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $questions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $questions;
}

function get_user_questions_for_display($conn, $user_id) {
    $query = "SELECT p.*, COUNT(j.id_jawaban) as total_jawaban
              FROM pertanyaan p
              LEFT JOIN jawaban j ON p.id_pertanyaan = j.id_pertanyaan
              WHERE p.id_user = ?
              GROUP BY p.id_pertanyaan
              ORDER BY p.tanggal_post DESC
              LIMIT 5";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $questions = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $questions;
}
?>