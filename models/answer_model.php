<?php
function create_jawaban($conn, $id_pertanyaan, $id_user, $isi) {
    $query = "INSERT INTO jawaban (id_pertanyaan, id_user, isi) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $id_pertanyaan, $id_user, $isi);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function get_jawaban_by_pertanyaan($conn, $id_pertanyaan) {
    $query = "SELECT 
                j.*, u.nama, u.photo, s.nama_sekolah
              FROM jawaban j
              JOIN user u ON j.id_user = u.id_user
              LEFT JOIN sekolah s ON u.id_sekolah = s.id_sekolah
              WHERE j.id_pertanyaan = ?
              ORDER BY j.total_vote DESC, j.tanggal_post ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_pertanyaan);
    $stmt->execute();
    $result = $stmt->get_result();
    $answers = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $answers;
}

function get_user_answers($conn, $user_id, $limit, $offset) {
    $query = "SELECT j.*, p.judul as pertanyaan_judul, u.nama, u.photo, s.nama_sekolah
              FROM jawaban j
              JOIN pertanyaan p ON j.id_pertanyaan = p.id_pertanyaan
              JOIN user u ON j.id_user = u.id_user
              LEFT JOIN sekolah s ON u.id_sekolah = s.id_sekolah
              WHERE j.id_user = ?
              ORDER BY j.tanggal_post DESC
              LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $user_id, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $answers = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $answers;
}

function get_total_user_answers($conn, $user_id) {
    $query = "SELECT COUNT(*) as total FROM jawaban WHERE id_user = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $total = $result->fetch_assoc()['total'];
    $stmt->close();
    return $total;
}

function get_jawaban_by_id($conn, $id) {
    $query = "SELECT 
                j.*, u.nama, u.photo, s.nama_sekolah
              FROM jawaban j
              JOIN user u ON j.id_user = u.id_user
              LEFT JOIN sekolah s ON u.id_sekolah = s.id_sekolah
              WHERE j.id_jawaban = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $answer = $result->fetch_assoc();
    $stmt->close();
    return $answer;
}

function get_user_answers_for_display($conn, $user_id) {
    $query = "SELECT j.*, p.judul as pertanyaan_judul
              FROM jawaban j
              JOIN pertanyaan p ON j.id_pertanyaan = p.id_pertanyaan
              WHERE j.id_user = ?
              ORDER BY j.tanggal_post DESC
              LIMIT 5";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $answers = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $answers;
}
?>