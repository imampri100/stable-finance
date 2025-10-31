<?php
include_once 'base_repository.php';

class UserRepository extends BaseRepository {
    public function __construct($conn) {
        parent::__construct($conn, "User");
    }

    public function get_by_email($email)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM User WHERE LOWER(email) = LOWER(?) AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function get_by_role($role)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM User WHERE role = ? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function create($id, $email, $password, $name, $role, $is_active) {
        $stmt = $this->conn->prepare("
            INSERT INTO User (id, email, password, name, role, is_active)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssssi", $id, $email, $password, $name, $role, $is_active);
        return $stmt->execute();
    }

    public function update($id, $email, $password, $name, $role, $is_active) {
        $stmt = $this->conn->prepare("
            UPDATE User
            SET email=?, password=?, name=?, role=?, is_active=?
            WHERE id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("ssssis", $email, $password, $name, $role, $is_active, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("
            UPDATE User
            SET deleted_at=CURRENT_TIMESTAMP
            WHERE id=? AND deleted_at IS NULL
        ");
        $stmt->bind_param("s", $id);
        return $stmt->execute();
    }
}

?>