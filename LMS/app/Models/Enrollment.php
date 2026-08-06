<?php

require_once __DIR__ . "/../../Config/database.php";

class Enrollment
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /* ===========================
       GET ALL ENROLLMENTS
    =========================== */

    public function getAllEnrollments()
    {
        $sql = "SELECT
                    e.enrollment_id,
                    u.full_name AS student_name,
                    c.course_name,
                    e.enroll_date
                FROM enrollments e
                INNER JOIN users u
                    ON e.student_id = u.user_id
                INNER JOIN courses c
                    ON e.course_id = c.course_id
                ORDER BY e.enrollment_id DESC";

        return $this->conn->query($sql);
    }

    /* ===========================
       GET ENROLLMENT BY ID
    =========================== */

    public function getEnrollmentById($id)
    {
        $sql = "SELECT *
                FROM enrollments
                WHERE enrollment_id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* ===========================
       ADD ENROLLMENT
    =========================== */

    public function addEnrollment($student_id, $course_id)
    {
        $sql = "INSERT INTO enrollments
                (
                    student_id,
                    course_id
                )
                VALUES(?,?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $student_id, $course_id);

        return $stmt->execute();
    }

    /* ===========================
       DELETE ENROLLMENT
    =========================== */

    public function deleteEnrollment($id)
    {
        $sql = "DELETE FROM enrollments
                WHERE enrollment_id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    /* ===========================
       TOTAL ENROLLMENTS
    =========================== */

    public function totalEnrollments()
    {
        $result = $this->conn->query(
            "SELECT COUNT(*) AS total
             FROM enrollments"
        );

        return $result->fetch_assoc()['total'];
    }

    /* ===========================
       RECENT ENROLLMENTS
    =========================== */

    public function recentEnrollments($limit = 5)
    {
        $sql = "SELECT
                    u.full_name AS student_name,
                    c.course_name,
                    e.enroll_date
                FROM enrollments e
                INNER JOIN users u
                    ON e.student_id = u.user_id
                INNER JOIN courses c
                    ON e.course_id = c.course_id
                ORDER BY e.enrollment_id DESC
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();

        return $stmt->get_result();
    }

    /* ===========================
       STUDENTS NOT ENROLLED
    =========================== */

    public function availableStudents()
    {
        $sql = "SELECT
                    user_id,
                    full_name
                FROM users
                WHERE role='student'
                ORDER BY full_name";

        return $this->conn->query($sql);
    }

    /* ===========================
       AVAILABLE COURSES
    =========================== */

    public function availableCourses()
    {
        $sql = "SELECT
                    course_id,
                    course_name
                FROM courses
                ORDER BY course_name";

        return $this->conn->query($sql);
    }

    /* ===========================
       CHECK ENROLLMENT
    =========================== */

    public function checkEnrollment($student_id, $course_id)
    {
        $sql = "SELECT enrollment_id
                FROM enrollments
                WHERE student_id=?
                AND course_id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $student_id, $course_id);
        $stmt->execute();

        return $stmt->get_result()->num_rows > 0;
    }

    /* ===========================
       SEARCH ENROLLMENTS
    =========================== */

    public function searchEnrollment($keyword)
    {
        $keyword = "%".$keyword."%";

        $sql = "SELECT
                    e.enrollment_id,
                    u.full_name AS student_name,
                    c.course_name,
                    e.enroll_date
                FROM enrollments e
                INNER JOIN users u
                    ON e.student_id = u.user_id
                INNER JOIN courses c
                    ON e.course_id = c.course_id
                WHERE
                    u.full_name LIKE ?
                    OR c.course_name LIKE ?
                ORDER BY e.enrollment_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $keyword, $keyword);
        $stmt->execute();

        return $stmt->get_result();
    }

    /* ===========================
       ENROLLMENTS BY MONTH
    =========================== */

    public function enrollmentChart()
    {
        $sql = "SELECT
                    MONTH(enroll_date) AS month,
                    COUNT(*) AS total
                FROM enrollments
                GROUP BY MONTH(enroll_date)
                ORDER BY MONTH(enroll_date)";

        return $this->conn->query($sql);
    }
}