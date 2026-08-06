<?php

require_once __DIR__ . "/../../Config/database.php";

class Course
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /* ===========================
       GET ALL COURSES
    =========================== */

    public function getAllCourses()
    {
        $sql = "SELECT
                    c.*,
                    u.full_name AS lecturer_name
                FROM courses c
                LEFT JOIN users u
                    ON c.lecturer_id = u.user_id
                ORDER BY c.course_id DESC";

        return $this->conn->query($sql);
    }

    /* ===========================
       GET COURSE BY ID
    =========================== */

    public function getCourseById($id)
    {
        $sql = "SELECT *
                FROM courses
                WHERE course_id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }

    /* ===========================
       ADD COURSE
    =========================== */

    public function addCourse($course_name, $description, $lecturer_id, $start_date, $end_date)
    {
        $sql = "INSERT INTO courses
                (
                    course_name,
                    description,
                    lecturer_id,
                    start_date,
                    end_date
                )
                VALUES(?,?,?,?,?)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "ssiss",
            $course_name,
            $description,
            $lecturer_id,
            $start_date,
            $end_date
        );

        return $stmt->execute();
    }

    /* ===========================
       UPDATE COURSE
    =========================== */

    public function updateCourse($id, $course_name, $description, $lecturer_id, $start_date, $end_date)
    {
        $sql = "UPDATE courses
                SET
                    course_name=?,
                    description=?,
                    lecturer_id=?,
                    start_date=?,
                    end_date=?
                WHERE course_id=?";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "ssissi",
            $course_name,
            $description,
            $lecturer_id,
            $start_date,
            $end_date,
            $id
        );

        return $stmt->execute();
    }

    /* ===========================
       DELETE COURSE
    =========================== */

    public function deleteCourse($id)
    {
        $sql = "DELETE FROM courses
                WHERE course_id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    /* ===========================
       TOTAL COURSES
    =========================== */

    public function totalCourses()
    {
        $sql = "SELECT COUNT(*) AS total
                FROM courses";

        $result = $this->conn->query($sql);

        return $result->fetch_assoc()['total'];
    }

    /* ===========================
       RECENT COURSES
    =========================== */

    public function recentCourses($limit = 5)
    {
        $sql = "SELECT
                    c.course_id,
                    c.course_name,
                    u.full_name AS lecturer_name,
                    c.start_date,
                    c.end_date
                FROM courses c
                LEFT JOIN users u
                    ON c.lecturer_id = u.user_id
                ORDER BY c.course_id DESC
                LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $limit);
        $stmt->execute();

        return $stmt->get_result();
    }

    /* ===========================
       GET LECTURERS
    =========================== */

    public function getLecturers()
    {
        $sql = "SELECT
                    user_id,
                    full_name
                FROM users
                WHERE role='lecturer'
                ORDER BY full_name";

        return $this->conn->query($sql);
    }

    /* ===========================
       SEARCH COURSE
    =========================== */

    public function searchCourse($keyword)
    {
        $keyword = "%".$keyword."%";

        $sql = "SELECT
                    c.*,
                    u.full_name AS lecturer_name
                FROM courses c
                LEFT JOIN users u
                    ON c.lecturer_id=u.user_id
                WHERE
                    c.course_name LIKE ?
                ORDER BY c.course_name";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $keyword);
        $stmt->execute();

        return $stmt->get_result();
    }

    /* ===========================
       TOTAL STUDENTS OF COURSE
    =========================== */

    public function totalStudents($course_id)
    {
        $sql = "SELECT COUNT(*) AS total
                FROM enrollments
                WHERE course_id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc()['total'];
    }

    /* ===========================
       DASHBOARD CHART
    =========================== */

    public function coursesPerLecturer()
    {
        $sql = "SELECT
                    u.full_name,
                    COUNT(c.course_id) AS total
                FROM users u
                LEFT JOIN courses c
                    ON u.user_id = c.lecturer_id
                WHERE u.role='lecturer'
                GROUP BY u.user_id
                ORDER BY total DESC";

        return $this->conn->query($sql);
    }
}