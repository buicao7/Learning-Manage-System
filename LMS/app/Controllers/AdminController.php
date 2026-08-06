<?php

require_once "app/Models/User.php";
require_once "app/Models/Course.php";
require_once "app/Models/Enrollment.php";
require_once "app/Models/Assignment.php";

class AdminController
{
    private $user;
    private $course;
    private $enrollment;
    private $assignment;

    public function __construct()
    {
        $this->user = new User();
        $this->course = new Course();
        $this->enrollment = new Enrollment();
        $this->assignment = new Assignment();
    }

    /* ==============================
        DASHBOARD
    ============================== */

    public function dashboard()
    {

        $data = [];

        // Cards
        $data['users'] = $this->user->totalUsers();
        $data['students'] = $this->user->totalStudents();
        $data['lecturers'] = $this->user->totalLecturers();
        $data['admins'] = $this->user->totalAdmins();

        $data['courses'] = $this->course->totalCourses();

        $data['assignments'] = $this->assignment->totalAssignments();

        $data['enrollments'] = $this->enrollment->totalEnrollments();

        // Tables

        $data['recentUsers'] = $this->user->recentUsers();

        $data['recentCourses'] = $this->course->recentCourses();

        $data['recentAssignments'] = $this->assignment->recentAssignments();

        $data['recentEnrollments'] = $this->enrollment->recentEnrollments();

        require "app/Views/admin/dashboard.php";
    }

    /* ==============================
        USERS
    ============================== */

    public function users()
    {

        $users = $this->user->getAllUsers();

        require "app/Views/admin/users.php";

    }

    /* ==============================
        COURSES
    ============================== */

    public function courses()
    {

        $courses = $this->course->getAllCourses();

        require "app/Views/admin/courses.php";

    }

    /* ==============================
        ASSIGNMENTS
    ============================== */

    public function assignments()
    {

        $assignments = $this->assignment->getAll();

        require "app/Views/admin/assignments.php";

    }

    /* ==============================
        ENROLLMENTS
    ============================== */

    public function enrollments()
    {

        $enrollments = $this->enrollment->getAllEnrollments();

        require "app/Views/admin/enrollments.php";

    }

}