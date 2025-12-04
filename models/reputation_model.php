<?php
require_once __DIR__ . '/user_model.php';
require_once __DIR__ . '/answer_model.php';

function get_school_for_user($conn, $user_id)
{
    $stmt = $conn->prepare("SELECT id_sekolah FROM user WHERE id_user = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $school_id = $result->fetch_assoc()['id_sekolah'] ?? null;
    $stmt->close();
    return $school_id;
}

function get_author_for_answer($conn, $answer_id)
{
    $stmt = $conn->prepare("SELECT id_user FROM jawaban WHERE id_jawaban = ?");
    $stmt->bind_param("i", $answer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $author_id = $result->fetch_assoc()['id_user'] ?? null;
    $stmt->close();
    return $author_id;
}

function trigger_reputation_action($conn, $action, $actor_user_id, $target_object_id = null, $vote_details = [])
{
    $school_id = null;
    $school_points = 0;
    $target_user_id = null;
    $user_points = 0;

    switch ($action) {
        case 'NEW_USER':
            $school_id = $target_object_id;
            $school_points = 5;
            break;

        case 'NEW_COMMENT':
            $target_user_id = $actor_user_id;
            $school_id = get_school_for_user($conn, $target_user_id);
            $user_points = 10;
            $school_points = 1;
            break;

        case 'NEW_ANSWER':
            $target_user_id = $actor_user_id;
            $user_points = 20;
            break;

        case 'VOTE_ANSWER':
            if (empty($vote_details) || $target_object_id === null) break;

            $target_user_id = get_author_for_answer($conn, $target_object_id);
            if (!$target_user_id) break;

            $school_id = get_school_for_user($conn, $target_user_id);
            $vote_type = $vote_details['vote_type'];
            $change_type = $vote_details['change'];

            $point_map = ['up' => 5, 'down' => -2];
            $point_value = $point_map[$vote_type];

            if ($change_type == 'new') {
                $user_points = $point_value;
            } elseif ($change_type == 'undo') {
                $user_points = -$point_value;
            } elseif ($change_type == 'swap') {
                $old_vote_type = ($vote_type == 'up') ? 'down' : 'up';
                $old_point_value = $point_map[$old_vote_type];
                $user_points = $point_value - $old_point_value;
            }

            $school_points = $user_points;
            break;
    }

    if ($user_points != 0 && $target_user_id) {
        $stmt = $conn->prepare("UPDATE user SET poin = poin + ? WHERE id_user = ?");
        $stmt->bind_param("ii", $user_points, $target_user_id);
        $stmt->execute();
        $stmt->close();
    }

    if ($school_points != 0 && $school_id) {
        $stmt = $conn->prepare("UPDATE sekolah SET score = score + ? WHERE id_sekolah = ?");
        $stmt->bind_param("ii", $school_points, $school_id);
        $stmt->execute();
        $stmt->close();
    }
}

function recalculate_all_points($conn) {
    $conn->begin_transaction();

    try {
        $conn->query("UPDATE user SET poin = 0");
        $conn->query("UPDATE sekolah SET score = 0");

        $conn->query("
            UPDATE user u
            SET u.poin = 
                ( (SELECT COUNT(*) FROM jawaban j WHERE j.id_user = u.id_user) * 20 ) +
                ( (SELECT COUNT(*) FROM komentar k WHERE k.id_user = u.id_user) * 10 ) +
                ( (SELECT COUNT(*) FROM vote v JOIN jawaban j ON v.id_jawaban = j.id_jawaban WHERE j.id_user = u.id_user AND v.tipe_vote = 'up') * 5 ) +
                ( (SELECT COUNT(*) FROM vote v JOIN jawaban j ON v.id_jawaban = j.id_jawaban WHERE j.id_user = u.id_user AND v.tipe_vote = 'down') * -2 )
        ");

        $conn->query("
            UPDATE sekolah s
            SET s.score = 
                ( (SELECT COUNT(*) FROM user u WHERE u.id_sekolah = s.id_sekolah) * 5 ) +
                ( (SELECT COUNT(*) FROM komentar k JOIN user u ON k.id_user = u.id_user WHERE u.id_sekolah = s.id_sekolah) * 1 ) +

                ( (SELECT COUNT(*) 
                FROM vote v 
                JOIN jawaban j ON v.id_jawaban = j.id_jawaban 
                JOIN user u ON j.id_user = u.id_user 
                WHERE u.id_sekolah = s.id_sekolah AND v.tipe_vote = 'up') * 5 ) +

                ( (SELECT COUNT(*) 
                FROM vote v 
                JOIN jawaban j ON v.id_jawaban = j.id_jawaban 
                JOIN user u ON j.id_user = u.id_user 
                WHERE u.id_sekolah = s.id_sekolah AND v.tipe_vote = 'down') * -2 )
        ");

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Recalculation Error: " . $e->getMessage());
        return false;
    }
}
?>