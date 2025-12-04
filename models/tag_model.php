<?php
function get_all_tags($conn) {
    $query = "SELECT t.*, COUNT(pt.id_pertanyaan) as total_pertanyaan 
              FROM tag t 
              LEFT JOIN pertanyaan_tag pt ON t.id_tag = pt.id_tag 
              GROUP BY t.id_tag 
              ORDER BY total_pertanyaan DESC, t.nama_tag ASC";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_tag_by_id($conn, $id) {
    $query = "SELECT * FROM tag WHERE id_tag = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tag = $result->fetch_assoc();
    $stmt->close();
    return $tag;
}

function get_tag_by_name($conn, $nama_tag) {
    $query = "SELECT * FROM tag WHERE nama_tag = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nama_tag);
    $stmt->execute();
    $result = $stmt->get_result();
    $tag = $result->fetch_assoc();
    $stmt->close();
    return $tag;
}

function create_tag($conn, $nama_tag) {
    $query = "INSERT INTO tag (nama_tag) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nama_tag);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function get_tags_by_pertanyaan($conn, $id_pertanyaan) {
    $query = "SELECT t.* FROM tag t
              JOIN pertanyaan_tag pt ON t.id_tag = pt.id_tag
              WHERE pt.id_pertanyaan = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_pertanyaan);
    $stmt->execute();
    $result = $stmt->get_result();
    $tags = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $tags;
}

function get_popular_tags($conn, $limit = 10) {
    $query = "SELECT t.*, COUNT(pt.id_pertanyaan) as total_pertanyaan 
              FROM tag t 
              LEFT JOIN pertanyaan_tag pt ON t.id_tag = pt.id_tag 
              GROUP BY t.id_tag 
              ORDER BY total_pertanyaan DESC 
              LIMIT ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $tags = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $tags;
}

function check_question_tag_exists($conn, $id_pertanyaan, $id_tag) {
    $query = "SELECT COUNT(*) as count FROM pertanyaan_tag WHERE id_pertanyaan = ? AND id_tag = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $id_pertanyaan, $id_tag);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->fetch_assoc()['count'] > 0;
    $stmt->close();
    return $exists;
}
?>