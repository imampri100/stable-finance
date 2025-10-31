<?php
include_once 'base_repository.php';

class SavingRepository extends BaseRepository {
    public function __construct($conn) {
        parent::__construct($conn, "Saving");
    }

    public function create($id, $user_id, $name, $start_date, $end_date, $collected_amount, $remaining_amount, $target_amount) {
        $stmt = $this->conn->prepare("
            INSERT INTO Saving (id, user_id, name, start_date, end_date, collected_amount, remaining_amount, target_amount)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssddd", $id, $user_id, $name, $start_date, $end_date, $collected_amount, $remaining_amount, $target_amount);
        return $stmt->execute();
    }

    public function update($id, $name, $start_date, $end_date, $collected_amount, $remaining_amount, $target_amount) {
        $stmt = $this->conn->prepare("
            UPDATE Saving 
            SET name=?, start_date=?, end_date=?, collected_amount=?, remaining_amount=?, target_amount=? 
            WHERE id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("sssddds", $name, $start_date, $end_date, $collected_amount, $remaining_amount, $target_amount, $id);
        return $stmt->execute();
    }

    public function get_by_user_id($user_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM Saving 
            WHERE user_id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function get_by_user_id_and_id($user_id, $id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM Saving 
            WHERE user_id=? AND id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("ss", $user_id, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function delete_by_user_id($user_id) {
        $stmt = $this->conn->prepare("
            UPDATE Saving 
            SET deleted_at=NOW() 
            WHERE user_id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $user_id);
        return $stmt->execute();
    }

    public function delete_by_user_id_and_id($user_id, $id) {
        $stmt = $this->conn->prepare("
            UPDATE Saving 
            SET deleted_at=NOW() 
            WHERE user_id=? AND id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("ss", $user_id, $id);
        return $stmt->execute();
    }
}
?>
