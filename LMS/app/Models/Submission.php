<?php

class Submission
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Danh sách tất cả bài nộp
    public function getAll()
    {
        $sql = "SELECT
                    s.*,
                    a.title AS assignment_title,
                    u.full_name AS student_name
                FROM submissions s
                JOIN assignments a
                    ON s.assignment_id = a.assignment_id
                JOIN users u
                    ON s.student_id = u.user_id
                ORDER BY s.submission_id DESC";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy bài nộp theo Assignment
    public function getByAssignment($assignmentId)
    {
        $stmt = $this->conn->prepare(
            "SELECT
                s.*,
                u.full_name
             FROM submissions s
             JOIN users u
                ON s.student_id=u.user_id
             WHERE assignment_id=?"
        );

        $stmt->execute([$assignmentId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Kiểm tra đã nộp chưa
    public function checkSubmitted($assignment,$student)
    {
        $stmt = $this->conn->prepare(
            "SELECT *
             FROM submissions
             WHERE assignment_id=?
             AND student_id=?"
        );

        $stmt->execute([$assignment,$student]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm bài nộp
    public function create($data)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO submissions
            (assignment_id,student_id,file_path)
            VALUES(?,?,?)"
        );

        return $stmt->execute([
            $data['assignment_id'],
            $data['student_id'],
            $data['file_path']
        ]);
    }

    // Xóa
    public function delete($id)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM submissions
             WHERE submission_id=?"
        );

        return $stmt->execute([$id]);
    }

    // Chi tiết
    public function getById($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT *
             FROM submissions
             WHERE submission_id=?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}