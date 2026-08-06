<?php

class Grade
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Danh sách điểm
    public function getAll()
    {
        $sql = "SELECT
                    g.grade_id,
                    g.score,
                    g.feedback,
                    g.graded_at,
                    u.full_name AS student_name,
                    a.title AS assignment_title
                FROM grades g
                JOIN submissions s
                    ON g.submission_id = s.submission_id
                JOIN users u
                    ON s.student_id = u.user_id
                JOIN assignments a
                    ON s.assignment_id = a.assignment_id
                ORDER BY g.grade_id DESC";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy theo submission
    public function getBySubmission($submissionId)
    {
        $stmt = $this->conn->prepare(
            "SELECT *
             FROM grades
             WHERE submission_id=?"
        );

        $stmt->execute([$submissionId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm điểm
    public function create($data)
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO grades
            (submission_id,score,feedback)
            VALUES(?,?,?)"
        );

        return $stmt->execute([
            $data['submission_id'],
            $data['score'],
            $data['feedback']
        ]);
    }

    // Cập nhật điểm
    public function update($submissionId,$data)
    {
        $stmt = $this->conn->prepare(
            "UPDATE grades
             SET score=?,
                 feedback=?
             WHERE submission_id=?"
        );

        return $stmt->execute([
            $data['score'],
            $data['feedback'],
            $submissionId
        ]);
    }

    // Xóa
    public function delete($id)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM grades
             WHERE grade_id=?"
        );

        return $stmt->execute([$id]);
    }

    // Student xem điểm
    public function getStudentGrades($studentId)
    {
        $stmt = $this->conn->prepare(
            "SELECT
                a.title,
                g.score,
                g.feedback,
                g.graded_at
             FROM grades g
             JOIN submissions s
                ON g.submission_id=s.submission_id
             JOIN assignments a
                ON s.assignment_id=a.assignment_id
             WHERE s.student_id=?"
        );

        $stmt->execute([$studentId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}