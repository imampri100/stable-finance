<?php
include_once 'base_repository.php';

class BudgetRepository extends BaseRepository {
    public function __construct($conn) {
        parent::__construct($conn, "Budget");
    }

    public function create($id, $user_id, $name, $month, $year, $collected_amount, $remaining_amount, $target_amount, $percentage) {
        $stmt = $this->conn->prepare("
            INSERT INTO Budget (id, user_id, name, month, year, collected_amount, remaining_amount, target_amount, percentage)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssiidddi", $id, $user_id, $name, $month, $year, $collected_amount, $remaining_amount, $target_amount, $percentage);
        return $stmt->execute();
    }

    public function update($id, $name, $month, $year, $collected_amount, $remaining_amount, $target_amount, $percentage) {
        $stmt = $this->conn->prepare("
            UPDATE Budget 
            SET name=?, month=?, year=?, collected_amount=?, remaining_amount=?, target_amount=?, percentage=? 
            WHERE id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("ssiiddds", $name, $month, $year, $collected_amount, $remaining_amount, $target_amount, $percentage, $id);
        return $stmt->execute();
    }

    public function get_by_user_id($user_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM Budget 
            WHERE user_id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function get_by_user_id_and_id($user_id, $id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM Budget 
            WHERE user_id=? AND id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("ss", $user_id, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function delete_by_user_id($user_id) {
        $stmt = $this->conn->prepare("
            UPDATE Budget 
            SET deleted_at=NOW() 
            WHERE user_id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $user_id);
        return $stmt->execute();
    }

    public function delete_by_user_id_and_id($user_id, $id) {
        $stmt = $this->conn->prepare("
            UPDATE Budget 
            SET deleted_at=NOW() 
            WHERE user_id=? AND id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("ss", $user_id, $id);
        return $stmt->execute();
    }

    public function get_by_user($user_id)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM {$this->table}
            WHERE deleted_at IS NULL AND user_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function get_by_user_and_period($user_id, $month, $year)
    {
        $query = "
            SELECT *
            FROM Budget
            WHERE user_id = ?
            AND month = ?
            AND year = ?
            AND deleted_at IS NULL
        ";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            die("Query prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("sii", $user_id, $month, $year);
        $stmt->execute();
        $result = $stmt->get_result();

        $budgets = [];
        while ($row = $result->fetch_assoc()) {
            $budgets[] = $row;
        }

        $stmt->close();
        return $budgets;
    }


}

?>