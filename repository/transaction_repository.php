<?php
include_once 'base_repository.php';

class TransactionRepository extends BaseRepository {
    public function __construct($conn) {
        parent::__construct($conn, "Transaction");
    }

    public function create($id, $user_id, $transaction_date, $transaction_type, $transaction_category, $description, $amount) {
        $stmt = $this->conn->prepare("
            INSERT INTO Transaction (id, user_id, transaction_date, transaction_type, transaction_category, description, amount)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssssd", $id, $user_id, $transaction_date, $transaction_type, $transaction_category, $description, $amount);
        return $stmt->execute();
    }

    public function update($id, $transaction_date, $transaction_type, $transaction_category, $description, $amount) {
        $stmt = $this->conn->prepare("
            UPDATE Transaction 
            SET transaction_date=?, transaction_type=?, transaction_category=?, description=?, amount=? 
            WHERE id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("ssssds", $transaction_date, $transaction_type, $transaction_category, $description, $amount, $id);
        return $stmt->execute();
    }

    public function delete_by_id($id) {
        $stmt = $this->conn->prepare("
            UPDATE Transaction
            SET deleted_at=CURRENT_TIMESTAMP
            WHERE id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }

    public function delete_by_user_id_and_id($user_id, $id) {
        $stmt = $this->conn->prepare("
            UPDATE Transaction 
            SET deleted_at=NOW() 
            WHERE user_id=? AND id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("ss", $user_id, $id);
        return $stmt->execute();
    }

    public function get_by_id($id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM Transaction
            WHERE id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function get_by_user_id($user_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM Transaction
            WHERE user_id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function get_by_user_id_and_id($user_id, $id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM Transaction
            WHERE user_id=? AND id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("ss", $user_id, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    //     // Ambil semua transaksi per user & periode (bulan, tahun)
    // public function get_by_user_and_period($user_id, $month, $year) {
    //     $sql = "SELECT * FROM Transaction
    //             WHERE user_id = ?
    //               AND MONTH(transaction_date) = ?
    //               AND YEAR(transaction_date) = ?";
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->bind_param("iii", $user_id, $month, $year);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     return $result->fetch_all(MYSQLI_ASSOC);
    // }


    // // Hitung total income per user & periode
    // public function get_total_income($user_id, $month, $year) {
    //     $sql = "SELECT SUM(amount) as total_income
    //             FROM Transaction
    //             WHERE user_id = ?
    //               AND transaction_category = 'income'
    //               AND MONTH(transaction_date) = ?
    //               AND YEAR(transaction_date) = ?";
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->bind_param("iii", $user_id, $month, $year);
    //     $stmt->execute();
    //     $result = $stmt->get_result()->fetch_assoc();
    //     return $result['total_income'] ?? 0;
    // }

    // // Hitung total expense per user & periode
    // public function get_total_expense($user_id, $month, $year) {
    //     $sql = "SELECT SUM(amount) as total_expense
    //             FROM Transaction
    //             WHERE user_id = ?
    //               AND transaction_category = 'expense'
    //               AND MONTH(transaction_date) = ?
    //               AND YEAR(transaction_date) = ?";
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->bind_param("iii", $user_id, $month, $year);
    //     $stmt->execute();
    //     $result = $stmt->get_result()->fetch_assoc();
    //     return $result['total_expense'] ?? 0;
    // }

    public function get_by_user($user_id) {
        $sql = "SELECT * FROM Transaction 
                WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            // Debug jika prepare gagal
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("s", $user_id); // ganti "i" ke "s" karena UUID
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function get_total_income($user_id) {
        $sql = "SELECT SUM(amount) as total_income 
                FROM Transaction 
                WHERE user_id = ? 
                AND transaction_category = 'income'";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total_income'] ?? 0;
    }

    public function get_total_expense($user_id) {
        $sql = "SELECT SUM(amount) as total_expense 
                FROM Transaction 
                WHERE user_id = ? 
                AND transaction_category = 'expense'";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total_expense'] ?? 0;
    }

    public function get_expense_by_category($user_id) {
        $sql = "SELECT transaction_category, description, SUM(amount) as total
                FROM Transaction
                WHERE user_id = ? AND transaction_category = 'expense'
                GROUP BY description";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("s", $user_id); // UUID string
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
