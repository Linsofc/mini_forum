<?php
function get_all_sekolah($conn) {
    $query = "SELECT * FROM sekolah ORDER BY nama_sekolah";
    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_sekolah_by_id($conn, $id) {
    $query = "SELECT * FROM sekolah WHERE id_sekolah = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $school = $result->fetch_assoc();
    $stmt->close();
    return $school;
}

function add_sekolah($conn, $nama_sekolah, $alamat) {
    $query = "INSERT INTO sekolah (nama_sekolah, alamat) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $nama_sekolah, $alamat);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function update_sekolah($conn, $id, $nama_sekolah, $alamat) {
    $query = "UPDATE sekolah SET nama_sekolah = ?, alamat = ? WHERE id_sekolah = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $nama_sekolah, $alamat, $id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function delete_sekolah($conn, $id) {
    $conn->begin_transaction();
    try {
        $update_users_query = "UPDATE user SET id_sekolah = NULL WHERE id_sekolah = ?";
        $stmt_users = $conn->prepare($update_users_query);
        $stmt_users->bind_param("i", $id);
        $stmt_users->execute();
        $stmt_users->close();

        $delete_school_query = "DELETE FROM sekolah WHERE id_sekolah = ?";
        $stmt_school = $conn->prepare($delete_school_query);
        $stmt_school->bind_param("i", $id);
        $stmt_school->execute();
        $stmt_school->close();

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error deleting school $id: " . $e->getMessage());
        return false;
    }
}

function get_school_leaderboard($conn) {
    $query = "SELECT 
        s.id_sekolah,
        s.nama_sekolah,
        s.score,
        COUNT(DISTINCT u.id_user) as total_users,
        COALESCE(v.total_votes, 0) as total_votes
      FROM sekolah s
      LEFT JOIN user u ON s.id_sekolah = u.id_sekolah

      LEFT JOIN (
        SELECT 
            u_jawaban.id_sekolah,
            COUNT(v.id_vote) as total_votes
        FROM vote v
        JOIN jawaban j ON v.id_jawaban = j.id_jawaban
        JOIN user u_jawaban ON j.id_user = u_jawaban.id_user
        WHERE u_jawaban.id_sekolah IS NOT NULL
        GROUP BY u_jawaban.id_sekolah
      ) v ON s.id_sekolah = v.id_sekolah

      GROUP BY s.id_sekolah, s.nama_sekolah, s.score, v.total_votes
      ORDER BY s.score DESC, total_users DESC
      LIMIT 10";

    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function get_school_user_count($conn, $id_sekolah) {
    $query = "SELECT COUNT(*) as count FROM user WHERE id_sekolah = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_sekolah);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];
    $stmt->close();
    return $count;
}
?>