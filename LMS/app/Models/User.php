<?php

require_once __DIR__ . "/../../Config/database.php";

class User
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /* ===========================
       LOGIN
    =========================== */

    public function login($email)
    {
        $sql = "SELECT * FROM users WHERE email=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* ===========================
       REGISTER
    =========================== */

    public function register($name, $email, $password, $role)
    {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users(full_name,email,password,role)
                VALUES(?,?,?,?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $name, $email, $password, $role);

        return $stmt->execute();
    }

    /* ===========================
       GET ALL USERS
    =========================== */

    public function getAllUsers()
    {
        $sql = "SELECT *
                FROM users
                ORDER BY full_name ASC";

        return $this->conn->query($sql);
    }

    /* ===========================
       GET USER BY ID
    =========================== */

    public function getUserById($id)
    {
        $sql = "SELECT *
                FROM users
                WHERE user_id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* ===========================
       UPDATE USER
    =========================== */

    public function updateUser($id, $name, $email, $role)
    {
        $sql = "UPDATE users
                SET full_name=?,
                    email=?,
                    role=?
                WHERE user_id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $role, $id);

        return $stmt->execute();
    }

    /* ===========================
       DELETE USER
    =========================== */

    public function deleteUser($id)
    {
        $sql = "DELETE FROM users
                WHERE user_id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    /* ===========================
       CHANGE PASSWORD
    =========================== */

    public function changePassword($id, $password)
    {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE users
                SET password=?
                WHERE user_id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $password, $id);

        return $stmt->execute();
    }

    /* ===========================
       DASHBOARD STATISTICS
    =========================== */

    public function totalUsers()
    {
        $result = $this->conn->query(
            "SELECT COUNT(*) AS total FROM users"
        );

        return $result->fetch_assoc()['total'];
    }

    public function totalStudents()
    {
        $result = $this->conn->query(
            "SELECT COUNT(*) AS total
             FROM users
             WHERE role='student'"
        );

        return $result->fetch_assoc()['total'];
    }

    public function totalLecturers()
    {
        $result = $this->conn->query(
            "SELECT COUNT(*) AS total
             FROM users
             WHERE role='lecturer'"
        );

        return $result->fetch_assoc()['total'];
    }

    public function totalAdmins()
    {
        $result = $this->conn->query(
            "SELECT COUNT(*) AS total
             FROM users
             WHERE role='admin'"
        );

        return $result->fetch_assoc()['total'];
    }

    /* ===========================
       RECENT USERS
    =========================== */

    public function recentUsers($limit = 5)
    {
        $sql = "SELECT *
                FROM users
                ORDER BY user_id DESC
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();

        return $stmt->get_result();
    }

    /* ===========================
       SEARCH USER
    =========================== */

    public function searchUser($keyword)
    {
        $keyword = "%".$keyword."%";

        $sql = "SELECT *
                FROM users
                WHERE full_name LIKE ?
                   OR email LIKE ?
                ORDER BY full_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $keyword, $keyword);
        $stmt->execute();

        return $stmt->get_result();
    }

    /* ===========================
       CHECK EMAIL EXISTS
    =========================== */

    public function emailExists($email)
    {
        $sql = "SELECT user_id
                FROM users
                WHERE email=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        return $stmt->get_result()->num_rows > 0;
    }
}