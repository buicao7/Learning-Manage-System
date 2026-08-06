<?php

require_once __DIR__ . "/../../Config/database.php";

class Assignment
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /* ===========================
       GET ALL ASSIGNMENTS
    =========================== */

    public function getAll()
    {
        $sql = "SELECT
                    a.assignment_id,
                    a.title,
                    a.description,
                    a.due_date,
                    c.course_name
                FROM assignments a
                INNER JOIN courses c
                    ON a.course_id = c.course_id
                ORDER BY a.assignment_id DESC";

        return $this->conn->query($sql);
    }

    /* ===========================
       GET ASSIGNMENT BY ID
    =========================== */

    public function getById($id)
    {
        $sql = "SELECT *
                FROM assignments
                WHERE assignment_id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i",$id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* ===========================
       CREATE ASSIGNMENT
    =========================== */

    public function create($data)
    {
        $sql = "INSERT INTO assignments
                (
                    course_id,
                    title,
                    description,
                    due_date
                )
                VALUES(?,?,?,?)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "isss",
            $data['course_id'],
            $data['title'],
            $data['description'],
            $data['due_date']
        );

        return $stmt->execute();
    }

    /* ===========================
       UPDATE ASSIGNMENT
    =========================== */

    public function update($id,$data)
    {
        $sql = "UPDATE assignments
                SET
                    course_id=?,
                    title=?,
                    description=?,
                    due_date=?
                WHERE assignment_id=?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "isssi",
            $data['course_id'],
            $data['title'],
            $data['description'],
            $data['due_date'],
            $id
        );

        return $stmt->execute();
    }

    /* ===========================
       DELETE ASSIGNMENT
    =========================== */

    public function delete($id)
    {
        $sql = "DELETE FROM assignments
                WHERE assignment_id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i",$id);

        return $stmt->execute();
    }

    /* ===========================
       TOTAL ASSIGNMENTS
    =========================== */

    public function totalAssignments()
    {
        $result = $this->conn->query(
            "SELECT COUNT(*) total
             FROM assignments"
        );

        return $result->fetch_assoc()['total'];
    }

    /* ===========================
       RECENT ASSIGNMENTS
    =========================== */

    public function recentAssignments($limit = 5)
    {
        $sql = "SELECT
                    a.assignment_id,
                    a.title,
                    c.course_name,
                    a.due_date
                FROM assignments a
                INNER JOIN courses c
                    ON a.course_id=c.course_id
                ORDER BY a.assignment_id DESC
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i",$limit);
        $stmt->execute();

        return $stmt->get_result();
    }

    /* ===========================
       GET COURSES
    =========================== */

    public function getCourses()
    {
        return $this->conn->query(
            "SELECT
                course_id,
                course_name
             FROM courses
             ORDER BY course_name"
        );
    }

    /* ===========================
       SEARCH ASSIGNMENT
    =========================== */

    public function searchAssignment($keyword)
    {
        $keyword = "%".$keyword."%";

        $sql = "SELECT
                    a.assignment_id,
                    a.title,
                    c.course_name,
                    a.due_date
                FROM assignments a
                INNER JOIN courses c
                    ON a.course_id=c.course_id
                WHERE
                    a.title LIKE ?
                    OR c.course_name LIKE ?
                ORDER BY a.assignment_id DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("ss",$keyword,$keyword);

        $stmt->execute();

        return $stmt->get_result();
    }

    /* ===========================
       TOTAL SUBMISSIONS
    =========================== */

    public function totalSubmissions($assignment_id)
    {
        $sql = "SELECT COUNT(*) total
                FROM submissions
                WHERE assignment_id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i",$assignment_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc()['total'];
    }

    /* ===========================
       ASSIGNMENT CHART
    =========================== */

    public function assignmentChart()
    {
        $sql = "SELECT
                    MONTH(due_date) month,
                    COUNT(*) total
                FROM assignments
                GROUP BY MONTH(due_date)
                ORDER BY MONTH(due_date)";

        return $this->conn->query($sql);
    }
}