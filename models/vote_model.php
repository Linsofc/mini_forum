<?php
function process_vote($conn, $id_user, $tipe_vote, $id_pertanyaan = null, $id_jawaban = null)
{
    $conn->begin_transaction();
    try {
        $existing_vote = null;

        $check_query = "SELECT * FROM vote WHERE id_user = ?";
        if ($id_pertanyaan !== null) {
            $check_query .= " AND id_pertanyaan = ? AND id_jawaban IS NULL";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("ii", $id_user, $id_pertanyaan);
        } else {
            $check_query .= " AND id_jawaban = ? AND id_pertanyaan IS NULL";
            $stmt = $conn->prepare($check_query);
            $stmt->bind_param("ii", $id_user, $id_jawaban);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $existing_vote = $result->fetch_assoc();
        $stmt->close();

        $vote_details = [];
        $increment = 0;

        if ($existing_vote) {
            if ($existing_vote['tipe_vote'] === $tipe_vote) {
                $delete_query = "DELETE FROM vote WHERE id_vote = ?";
                $stmt = $conn->prepare($delete_query);
                $stmt->bind_param("i", $existing_vote['id_vote']);
                $stmt->execute();
                $stmt->close();

                $increment = ($tipe_vote == 'up') ? -1 : 1;
                $vote_details = ['vote_type' => $tipe_vote, 'change' => 'undo'];
            } else {
                $update_query = "UPDATE vote SET tipe_vote = ? WHERE id_vote = ?";
                $stmt = $conn->prepare($update_query);
                $stmt->bind_param("si", $tipe_vote, $existing_vote['id_vote']);
                $stmt->execute();
                $stmt->close();

                $increment = ($tipe_vote == 'up') ? 2 : -2;
                $vote_details = ['vote_type' => $tipe_vote, 'change' => 'swap'];
            }
        } else {
            $insert_query = "INSERT INTO vote (id_user, id_pertanyaan, id_jawaban, tipe_vote) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("iiis", $id_user, $id_pertanyaan, $id_jawaban, $tipe_vote);
            $stmt->execute();
            $stmt->close();

            $increment = ($tipe_vote == 'up') ? 1 : -1;
            $vote_details = ['vote_type' => $tipe_vote, 'change' => 'new'];
        }

        if ($id_pertanyaan !== null) {
            $update_query = "UPDATE pertanyaan SET total_vote = total_vote + ? WHERE id_pertanyaan = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ii", $increment, $id_pertanyaan);
        } else {
            $update_query = "UPDATE jawaban SET total_vote = total_vote + ? WHERE id_jawaban = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ii", $increment, $id_jawaban);
        }
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        
        return [
            'success' => true, 
            'vote_details' => $id_jawaban !== null ? $vote_details : null,
            'target_id' => $id_jawaban,
        ];
    } catch (Exception $exception) {
        $conn->rollback();
        error_log("Vote Error: " . $exception->getMessage());
        return ['success' => false, 'message' => $exception->getMessage()];
    }
}

function get_user_vote($conn, $id_user, $id_pertanyaan = null, $id_jawaban = null)
{
    $query = "SELECT tipe_vote FROM vote WHERE id_user = ?";
    
    if ($id_pertanyaan !== null) {
        $query .= " AND id_pertanyaan = ? AND id_jawaban IS NULL";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $id_user, $id_pertanyaan);
    } else {
        $query .= " AND id_jawaban = ? AND id_pertanyaan IS NULL";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $id_user, $id_jawaban);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $vote_type = $result->fetch_assoc()['tipe_vote'] ?? null;
    $stmt->close();

    return $vote_type;
}
?>