<?php
include_once 'base_repository.php';

class DebtRepository extends BaseRepository {
    public function __construct($conn) {
        parent::__construct($conn, "Debt");
    }

    public function create($id, $user_id, $name, $start_date, $end_date, $collected_amount, $remaining_amount, $target_amount, $percentage) {
        $stmt = $this->conn->prepare("
            INSERT INTO Debt (id, user_id, name, start_date, end_date, collected_amount, remaining_amount, target_amount, percentage)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssiiid", $id, $user_id, $name, $start_date, $end_date, $collected_amount, $remaining_amount, $target_amount, $percentage);
        return $stmt->execute();
    }

    public function update($id, $name, $start_date, $end_date, $collected_amount, $remaining_amount, $target_amount, $percentage) {
        $stmt = $this->conn->prepare("
            UPDATE Debt 
            SET name=?, start_date=?, end_date=?, collected_amount=?, remaining_amount=?, target_amount=?, percentage=? 
            WHERE id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("sssiiids", $name, $start_date, $end_date, $collected_amount, $remaining_amount, $target_amount, $percentage, $id);
        return $stmt->execute();
    }

    public function get_by_user_id($user_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM Debt 
            WHERE user_id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function get_by_user_id_and_id($user_id, $id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM Debt 
            WHERE user_id=? AND id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("ss", $user_id, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function delete_by_user_id($user_id) {
        $stmt = $this->conn->prepare("
            UPDATE Debt 
            SET deleted_at=NOW() 
            WHERE user_id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $user_id);
        return $stmt->execute();
    }

    public function delete_by_user_id_and_id($user_id, $id) {
        $stmt = $this->conn->prepare("
            UPDATE Debt 
            SET deleted_at=NOW() 
            WHERE user_id=? AND id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("ss", $user_id, $id);
        return $stmt->execute();
    }
}
?>
