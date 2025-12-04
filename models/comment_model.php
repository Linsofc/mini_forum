<?php

function create_komentar($conn, $id_user, $isi, $id_pertanyaan = null, $id_jawaban = null, $id_komentar_root = null, $id_komentar_reply_to = null)
{
    $query = "INSERT INTO komentar (id_user, id_pertanyaan, id_jawaban, id_komentar_root, id_komentar_reply_to, isi) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iiiiis', $id_user, $id_pertanyaan, $id_jawaban, $id_komentar_root, $id_komentar_reply_to, $isi);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function get_komentar_by_pertanyaan($conn, $id_pertanyaan)
{
    $query = "SELECT 
                k.*, u.nama, u.photo, s.nama_sekolah,
                (SELECT COUNT(*) FROM komentar r WHERE r.id_komentar_root = k.id_komentar) as reply_count
              FROM komentar k
              JOIN user u ON k.id_user = u.id_user
              LEFT JOIN sekolah s ON u.id_sekolah = s.id_sekolah
              WHERE k.id_pertanyaan = ? AND k.id_komentar_root IS NULL
              ORDER BY k.tanggal_post ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_pertanyaan);
    $stmt->execute();
    $result = $stmt->get_result();
    $comments = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $comments;
}

function get_komentar_by_jawaban($conn, $id_jawaban)
{
    $query = "SELECT 
                k.*, u.nama, u.photo, s.nama_sekolah,
                (SELECT COUNT(*) FROM komentar r WHERE r.id_komentar_root = k.id_komentar) as reply_count
              FROM komentar k
              JOIN user u ON k.id_user = u.id_user
              LEFT JOIN sekolah s ON u.id_sekolah = s.id_sekolah
              WHERE k.id_jawaban = ? AND k.id_komentar_root IS NULL
              ORDER BY k.tanggal_post ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_jawaban);
    $stmt->execute();
    $result = $stmt->get_result();
    $comments = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $comments;
}

function get_komentar_replies_paginated($conn, $id_komentar_root, $limit, $offset)
{
    $query = "SELECT 
                k.*, u.nama, u.photo, s.nama_sekolah,
                reply_user.nama AS reply_to_user_nama
              FROM komentar k
              JOIN user u ON k.id_user = u.id_user
              LEFT JOIN sekolah s ON u.id_sekolah = s.id_sekolah
              LEFT JOIN komentar parent_komentar ON k.id_komentar_reply_to = parent_komentar.id_komentar
              LEFT JOIN user reply_user ON parent_komentar.id_user = reply_user.id_user
              WHERE k.id_komentar_root = ?
              ORDER BY k.tanggal_post ASC
              LIMIT ? OFFSET ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('iii', $id_komentar_root, $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    $replies = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $replies;
}

function get_komentar_by_id($conn, $id_komentar)
{
    $query = "SELECT * FROM komentar WHERE id_komentar = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id_komentar);
    $stmt->execute();
    $result = $stmt->get_result();
    $comment = $result->fetch_assoc();
    $stmt->close();
    return $comment;
}

function get_user_leaderboard($conn)
{
    $query = "SELECT 
            u.id_user,
            u.nama,
            u.photo,
            u.poin, 
            s.nama_sekolah,
            COUNT(k.id_komentar) as total_komentar
          FROM user u
          LEFT JOIN sekolah s ON u.id_sekolah = s.id_sekolah
          LEFT JOIN komentar k ON u.id_user = k.id_user
          GROUP BY u.id_user
          ORDER BY u.poin DESC
          LIMIT 10";

    $result = $conn->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}
?>