<?php
require_once "config/database.php";

$db = new Database();
$conn = $db->connect();

$courses = mysqli_query($conn,"
SELECT
course_id,
course_name,
description
FROM courses
ORDER BY course_id DESC
LIMIT 6
");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Management System</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="Public/css/style.css">
</head>

<body>

<!-- ================= NAVBAR ================= -->

<nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">

    <div class="container">

        <!-- Logo -->
        <a class="navbar-brand fw-bold fs-2 text-primary" href="index.php">
            🎓 LMS
        </a>

        <!-- Mobile -->
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarMenu">

            <span class="navbar-toggler-icon"></span>

        </button>

        <!-- Menu -->

        <div class="collapse navbar-collapse" id="navbarMenu">

            <ul class="navbar-nav mx-auto">

                <li class="nav-item">

                    <a class="nav-link active" href="#">
                        Home
                    </a>

                </li>

                <li class="nav-item">

                    <a class="nav-link" href="#courses">
                        Courses
                    </a>

                </li>

                <li class="nav-item">

                    <a class="nav-link" href="#features">
                        Features
                    </a>

                </li>

                <li class="nav-item">

                    <a class="nav-link" href="#about">
                        About
                    </a>

                </li>

                <li class="nav-item">

                    <a class="nav-link" href="#contact">
                        Contact
                    </a>

                </li>

            </ul>

            <!-- Login Register -->

            <div class="d-flex align-items-center">

                <a href="app/Views/auth/login.php"
                   class="btn btn-login me-3">

                    <i class="fa-solid fa-user me-2"></i>

                    Login

                </a>

                <a href="app/Views/auth/register.php"
                   class="btn btn-register">

                    <i class="fa-solid fa-user-plus me-2"></i>

                    Register

                </a>

            </div>

        </div>

    </div>

</nav>

<!-- ================= HERO ================= -->

<section class="hero">

<div class="container">

<div class="row align-items-center">

<div class="col-lg-6">

<span class="badge bg-primary px-1 py-2 mb-3">

Modern Learning Platform

</span>

<h1 class="display-3 fw-bold mb-4">

Learn Anytime,<br>

Anywhere.

</h1>

<p class="lead text-secondary mb-4">

A powerful Learning Management System designed for
students, lecturers and administrators.

Access courses, submit assignments, track progress
and achieve your learning goals.

</p>

<div class="d-flex flex-wrap gap-3">

<a href="app/Views/auth/register.php"
class="btn btn-primary btn-lg px-4">

Get Started

</a>

<a href="#courses"
class="btn btn-outline-primary btn-lg px-4">

Explore Courses

</a>

</div>

<div class="row mt-5">

<div class="col-4">

<h2 class="fw-bold text-primary">1000+</h2>

<p class="text-muted">Students</p>

</div>

<div class="col-4">

<h2 class="fw-bold text-primary">80+</h2>

<p class="text-muted">Courses</p>

</div>

<div class="col-4">

<h2 class="fw-bold text-primary">50+</h2>

<p class="text-muted">Lecturers</p>

</div>

</div>

</div>

<div class="col-lg-6 text-center">

<img src="Public/images/image.png"
class="img-fluid hero-image">

</div>

</div>

</div>

</section>

<!-- ================= FEATURED COURSES ================= -->
<section class="py-0 bg-light" id="courses">
    <div class="container">

        <div class="text-center mb-5">
            <h1 class="fw-bold display-5 mt-5">
                Featured Courses
            </h1>
            <p class="text-muted">
                Discover the newest courses available on our learning platform.
            </p>
        </div>

        <div class="row g-4">

            <!-- Course 1 -->
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100 course-card">

                    <img src="Public/images/software-engineering-challenges.webp"
                         class="card-img-top"
                         style="height:220px;object-fit:cover;">

                    <div class="card-body p-4">

                        <div class="d-flex justify-content-between mb-3">
                            <span class="badge bg-primary">Popular</span>

                            <span class="text-warning">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                            </span>
                        </div>

                        <h4 class="fw-bold">
                            Software Engineering
                        </h4>

                        <p class="text-muted mt-3">
                            Learn software development life cycle, Agile, UML,
                            project management and software testing.
                        </p>

                        <hr>

                        <div class="d-flex justify-content-between mb-3">
                            <span>
                                <i class="fa-solid fa-users text-primary"></i>
                                250 Students
                            </span>

                            <span>
                                <i class="fa-solid fa-book text-success"></i>
                                20 Lessons
                            </span>
                        </div>

                        <a href="app/Views/auth/login.php"
                           class="btn btn-primary w-100">
                            View Course
                        </a>

                    </div>
                </div>
            </div>

            <!-- Course 2 -->
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100 course-card">

                    <img src="Public/images/260756_bac8_3.webp"
                         class="card-img-top"
                         style="height:220px;object-fit:cover;">

                    <div class="card-body p-4">

                        <div class="d-flex justify-content-between mb-3">
                            <span class="badge bg-success">Trending</span>

                            <span class="text-warning">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star-half-stroke"></i>
                            </span>
                        </div>

                        <h4 class="fw-bold">
                            Web Development
                        </h4>

                        <p class="text-muted mt-3">
                            Master HTML, CSS, Bootstrap, JavaScript, PHP and
                            MySQL by building real-world web applications.
                        </p>

                        <hr>

                        <div class="d-flex justify-content-between mb-3">
                            <span>
                                <i class="fa-solid fa-users text-primary"></i>
                                320 Students
                            </span>

                            <span>
                                <i class="fa-solid fa-book text-success"></i>
                                24 Lessons
                            </span>
                        </div>

                        <a href="app/Views/auth/login.php"
                           class="btn btn-primary w-100">
                            View Course
                        </a>

                    </div>
                </div>
            </div>

            <!-- Course 3 -->
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden h-100 course-card">

                    <img src="Public/images/introduction-of-dbms.webp"
                         class="card-img-top"
                         style="height:220px;object-fit:cover;">

                    <div class="card-body p-4">

                        <div class="d-flex justify-content-between mb-3">
                            <span class="badge bg-danger">New</span>

                            <span class="text-warning">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-regular fa-star"></i>
                            </span>
                        </div>

                        <h4 class="fw-bold">
                            Database Management
                        </h4>

                        <p class="text-muted mt-3">
                            Learn SQL, MySQL, database design, normalization,
                            relationships and database optimization.
                        </p>

                        <hr>

                        <div class="d-flex justify-content-between mb-3">
                            <span>
                                <i class="fa-solid fa-users text-primary"></i>
                                180 Students
                            </span>

                            <span>
                                <i class="fa-solid fa-book text-success"></i>
                                18 Lessons
                            </span>
                        </div>

                        <a href="app/Views/auth/login.php"
                           class="btn btn-primary w-100">
                            View Course
                        </a>

                    </div>
                </div>
            </div>

        </div>

        <div class="text-center mt-5">
            <a href="app/Views/auth/login.php"
               class="btn btn-outline-primary btn-lg">
                View All Courses
            </a>
        </div>

    </div>
</section>

<!-- ================= WHY CHOOSE LMS ================= -->

<section class="py-5" id="features">

<div class="container">

<div class="text-center mb-5">

<span class="text-primary fw-bold">

WHY CHOOSE US

</span>

<h2 class="display-5 fw-bold mt-2">

Everything You Need To Learn Online

</h2>

<p class="text-muted">

Powerful features designed for students, lecturers and administrators.

</p>

</div>

<div class="row g-4">

<div class="col-lg-3 col-md-6">

<div class="feature-box">

<div class="feature-icon bg-primary-subtle">

<i class="fa-solid fa-book-open text-primary"></i>

</div>

<h4>Online Courses</h4>

<p>

Access hundreds of professional courses anytime and anywhere.

</p>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="feature-box">

<div class="feature-icon bg-success-subtle">

<i class="fa-solid fa-file-lines text-success"></i>

</div>

<h4>Assignments</h4>

<p>

Submit homework online and receive instant feedback.

</p>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="feature-box">

<div class="feature-icon bg-warning-subtle">

<i class="fa-solid fa-chart-column text-warning"></i>

</div>

<h4>Track Progress</h4>

<p>

Monitor your grades and learning performance in real time.

</p>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="feature-box">

<div class="feature-icon bg-danger-subtle">

<i class="fa-solid fa-bell text-danger"></i>

</div>

<h4>Notifications</h4>

<p>

Stay updated with announcements and important deadlines.

</p>

</div>

</div>

</div>

</div>

</section>

<!-- ================= ABOUT ================= -->

<section id="about" class="py-5">

<div class="container">

<div class="row align-items-center">

<div class="col-lg-6">

<img src="Public/images/images.jpg"
class="img-fluid rounded-4 shadow-lg">

</div>

<div class="col-lg-6">

<span class="text-primary fw-bold">

ABOUT LMS

</span>

<h2 class="display-5 fw-bold mt-2">

A Smarter Way To Learn Online

</h2>

<p class="text-muted mt-4">

Our Learning Management System provides an all-in-one
platform where students can learn, lecturers can teach,
and administrators can manage courses efficiently.

</p>

<div class="row mt-4">

<div class="col-6 mb-4">

<div class="d-flex">

<div class="me-3">

<i class="fa-solid fa-book-open fa-2x text-primary"></i>

</div>

<div>

<h5 class="fw-bold">

Course Management

</h5>

<p class="text-muted">

Organize and manage courses easily.

</p>

</div>

</div>

</div>

<div class="col-6 mb-4">

<div class="d-flex">

<div class="me-3">

<i class="fa-solid fa-file-lines fa-2x text-success"></i>

</div>

<div>

<h5 class="fw-bold">

Assignments

</h5>

<p class="text-muted">

Submit homework digitally.

</p>

</div>

</div>

</div>

<div class="col-6">

<div class="d-flex">

<div class="me-3">

<i class="fa-solid fa-chart-column fa-2x text-warning"></i>

</div>

<div>

<h5 class="fw-bold">

Progress

</h5>

<p class="text-muted">

Track learning performance.

</p>

</div>

</div>

</div>

<div class="col-6">

<div class="d-flex">

<div class="me-3">

<i class="fa-solid fa-certificate fa-2x text-danger"></i>

</div>

<div>

<h5 class="fw-bold">

Certificates

</h5>

<p class="text-muted">

Receive certificates after completion.

</p>

</div>

</div>

</div>

</div>

<a href="app/Views/auth/register.php"
class="btn btn-primary btn-lg mt-4">

Join LMS Today

</a>

</div>

</div>

</div>

</section>


<!-- ================= TESTIMONIALS ================= -->

<section class="py-5 bg-light">

<div class="container">

<div class="text-center mb-5">

<span class="text-primary fw-bold">

FeedBack

</span>

<h2 class="display-5 fw-bold mt-2">

What Our Students Say

</h2>

<p class="text-muted">

Thousands of students trust our Learning Management System.

</p>

</div>

<div class="row g-4">

<div class="col-lg-4">

<div class="testimonial-card">

<img
src="Public/images/3d-illustration-with-online-avatar_23-2151303097.avif"
class="testimonial-avatar"
style="width:120px;height:120px;object-fit:cover;border-radius:50%;display:block;margin:0 auto 20px;">

<div class="text-warning mb-3">

<i class="fa-solid fa-star"></i>
<i class="fa-solid fa-star"></i>
<i class="fa-solid fa-star"></i>
<i class="fa-solid fa-star"></i>
<i class="fa-solid fa-star"></i>

</div>

<p>

"LMS makes learning easier. I can access courses and submit assignments anywhere."

</p>

<h5>

John Doe

</h5>

<span>

Computer Science Student

</span>

</div>

</div>

<div class="col-lg-4">

<div class="testimonial-card">

<img
src="Public/images/3d-illustration-with-online-avatar_23-2151303050.avif"
class="testimonial-avatar"
style="width:120px;height:120px;object-fit:cover;border-radius:50%;display:block;margin:0 auto 20px;">

<div class="text-warning mb-3">

<i class="fa-solid fa-star"></i>
<i class="fa-solid fa-star"></i>
<i class="fa-solid fa-star"></i>
<i class="fa-solid fa-star"></i>
<i class="fa-solid fa-star"></i>

</div>

<p>

"The interface is beautiful and very easy to use. I enjoy studying with LMS."

</p>

<h5>

Emma Watson

</h5>

<span>

Business Student

</span>

</div>

</div>

<div class="col-lg-4">

<div class="testimonial-card">

<img
src="Public/images/3d-illustration-human-avatar-profile_23-2150671142.avif"
class="testimonial-avatar"
style="width:120px;height:120px;object-fit:cover;border-radius:50%;display:block;margin:0 auto 20px;">

<div class="text-warning mb-3">

<i class="fa-solid fa-star"></i>
<i class="fa-solid fa-star"></i>
<i class="fa-solid fa-star"></i>
<i class="fa-solid fa-star"></i>
<i class="fa-solid fa-star"></i>

</div>

<p>

"Excellent platform for online learning. Highly recommended."

</p>

<h5>

Michael Lee

</h5>

<span>

Software Engineering Student

</span>

</div>

</div>

</div>

</div>

</section>


<!-- ================= FAQ ================= -->

<section class="py-5 bg-light" id="faq">

<div class="container">

<div class="text-center mb-5">

<span class="text-primary fw-bold">
Frequently Asked Questions
</span>

<h2 class="display-5 fw-bold mt-2">
Have Any Questions?
</h2>

<p class="text-muted">
Find answers to the most common questions about our Learning Management System.
</p>

</div>

<div class="row justify-content-center">

<div class="col-lg-9">

<div class="accordion shadow rounded-4 overflow-hidden" id="faqAccordion">

<!-- FAQ 1 -->

<div class="accordion-item">

<h2 class="accordion-header">

<button class="accordion-button fw-bold" type="button"
data-bs-toggle="collapse"
data-bs-target="#faq1">

How do I register for an account?

</button>

</h2>

<div id="faq1"
class="accordion-collapse collapse show"
data-bs-parent="#faqAccordion">

<div class="accordion-body">

Click the <strong>Register</strong> button, fill in your information,
and create your account. You can then log in and start learning immediately.

</div>

</div>

</div>

<!-- FAQ 2 -->

<div class="accordion-item">

<h2 class="accordion-header">

<button class="accordion-button collapsed fw-bold"
type="button"
data-bs-toggle="collapse"
data-bs-target="#faq2">

How can I enroll in a course?

</button>

</h2>

<div id="faq2"
class="accordion-collapse collapse"
data-bs-parent="#faqAccordion">

<div class="accordion-body">

Browse the available courses, choose your preferred course,
and click <strong>Enroll</strong> after logging in.

</div>

</div>

</div>

<!-- FAQ 3 -->

<div class="accordion-item">

<h2 class="accordion-header">

<button class="accordion-button collapsed fw-bold"
type="button"
data-bs-toggle="collapse"
data-bs-target="#faq3">

Can I submit assignments online?

</button>

</h2>

<div id="faq3"
class="accordion-collapse collapse"
data-bs-parent="#faqAccordion">

<div class="accordion-body">

Yes. Students can upload assignments directly through the LMS before the deadline.

</div>

</div>

</div>

<!-- FAQ 4 -->

<div class="accordion-item">

<h2 class="accordion-header">

<button class="accordion-button collapsed fw-bold"
type="button"
data-bs-toggle="collapse"
data-bs-target="#faq4">

How do I check my grades?

</button>

</h2>

<div id="faq4"
class="accordion-collapse collapse"
data-bs-parent="#faqAccordion">

<div class="accordion-body">

After lecturers publish results, you can view your grades and learning progress from your dashboard.

</div>

</div>

</div>

<!-- FAQ 5 -->

<div class="accordion-item">

<h2 class="accordion-header">

<button class="accordion-button collapsed fw-bold"
type="button"
data-bs-toggle="collapse"
data-bs-target="#faq5">

Can lecturers create new courses?

</button>

</h2>

<div id="faq5"
class="accordion-collapse collapse"
data-bs-parent="#faqAccordion">

<div class="accordion-body">

Yes. Lecturers can create, edit, and manage their own courses, lessons, and assignments.

</div>

</div>

</div>

<!-- FAQ 6 -->

<div class="accordion-item">

<h2 class="accordion-header">

<button class="accordion-button collapsed fw-bold"
type="button"
data-bs-toggle="collapse"
data-bs-target="#faq6">

Is my personal information secure?

</button>

</h2>

<div id="faq6"
class="accordion-collapse collapse"
data-bs-parent="#faqAccordion">

<div class="accordion-body">

Yes. Our LMS protects your personal information using secure authentication and database security practices.

</div>

</div>

</div>

</div>

</div>

</div>

</div>

</section>

<!-- ================= CONTACT ================= -->

<section class="py-5" id="contact">

<div class="container">

<div class="text-center mb-5">

<span class="text-primary fw-bold">

CONTACT US

</span>

<h2 class="display-5 fw-bold mt-2">

Get In Touch

</h2>

<p class="text-muted">

Have questions? We'd love to hear from you.

</p>

</div>

<div class="row g-5">

<!-- Left -->

<div class="col-lg-5">

<div class="contact-info">

<h3 class="fw-bold mb-4">

Contact Information

</h3>

<p class="text-muted">

Feel free to contact us if you need any help regarding
courses, assignments or your LMS account.

</p>

<div class="contact-item">

<i class="fa-solid fa-location-dot"></i>

<div>

<h5>Address</h5>

<p>

123 Education Street, Ho Chi Minh City, Vietnam

</p>

</div>

</div>

<div class="contact-item">

<i class="fa-solid fa-envelope"></i>

<div>

<h5>Email</h5>

<p>

support@lms.edu.vn

</p>

</div>

</div>

<div class="contact-item">

<i class="fa-solid fa-phone"></i>

<div>

<h5>Phone</h5>

<p>

(+84) 123 456 789

</p>

</div>

</div>

<div class="contact-item">

<i class="fa-solid fa-clock"></i>

<div>

<h5>Working Hours</h5>

<p>

Monday - Friday
<br>

08:00 AM - 05:00 PM

</p>

</div>

</div>

</div>

</div>

<!-- Right -->

<div class="col-lg-7">

<div class="contact-form">

<form action="" method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<input
type="text"
class="form-control"
placeholder="Full Name"
required>

</div>

<div class="col-md-6 mb-3">

<input
type="email"
class="form-control"
placeholder="Email Address"
required>

</div>

</div>

<div class="mb-3">

<input
type="text"
class="form-control"
placeholder="Subject"
required>

</div>

<div class="mb-3">

<textarea
class="form-control"
rows="6"
placeholder="Write your message..."
required></textarea>

</div>

<button
class="btn btn-primary btn-lg">

Send Message

</button>

</form>

</div>

</div>

</div>

</div>

</section>

<!-- ================= FOOTER ================= -->

<footer class="footer">

<div class="container">

<div class="row gy-5">

<!-- Logo -->

<div class="col-lg-4">

<h2 class="footer-logo">

🎓 LMS

</h2>

<p>

Our Learning Management System helps students,
lecturers and administrators manage online learning
efficiently through one modern platform.

</p>

<div class="social-icons">

<a href="#">

<i class="fab fa-facebook-f"></i>

</a>

<a href="#">

<i class="fab fa-instagram"></i>

</a>

<a href="#">

<i class="fab fa-youtube"></i>

</a>

<a href="#">

<i class="fab fa-linkedin-in"></i>

</a>

</div>

</div>

<!-- Quick Links -->

<div class="col-lg-2">

<h4>

Quick Links

</h4>

<ul>

<li><a href="#">Home</a></li>

<li><a href="#courses">Courses</a></li>

<li><a href="#features">Features</a></li>

<li><a href="#about">About</a></li>

<li><a href="#contact">Contact</a></li>

</ul>

</div>

<!-- Services -->

<div class="col-lg-3">

<h4>

Services

</h4>

<ul>

<li>Online Courses</li>

<li>Assignments</li>

<li>Grade Management</li>

<li>Certificates</li>

<li>Student Dashboard</li>

</ul>

</div>

<!-- Contact -->

<div class="col-lg-3">

<h4>

Contact

</h4>

<p>

<i class="fa-solid fa-location-dot"></i>

Ho Chi Minh City, Vietnam

</p>

<p>

<i class="fa-solid fa-envelope"></i>

support@lms.edu.vn

</p>

<p>

<i class="fa-solid fa-phone"></i>

(+84) 123 456 789

</p>

</div>

</div>

<hr>

<div class="row align-items-center">

<div class="col-md-6">

<p class="copyright">

© 2026 Learning Management System.

All Rights Reserved.

</p>

</div>

<div class="col-md-6 text-md-end">

<a href="#" class="back-top">

<i class="fa-solid fa-arrow-up"></i>

Back To Top

</a>

</div>

</div>

</div>

</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>