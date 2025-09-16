<?php
// header.php

include('admin/database_connection.php');
session_start();

if (!isset($_SESSION["teacher_id"])) {
    header('location:login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Attendance System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/dataTables.bootstrap4.min.css">

    <!-- jQuery + Bootstrap JS -->
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <!-- DataTables JS -->
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap4.min.js"></script>

    <style>
        /* Consistent style with admin header */
        .navbar-brand {
            font-weight: bold;
            font-size: 1.3rem;
        }
        .navbar-nav .nav-link {
            padding: 0.6rem 1rem;
        }
        .jumbotron-small {
            background: #f8f9fa;
            padding: 1rem 2rem;
            border-bottom: 2px solid #dee2e6;
        }
        .jumbotron-small h1 {
            font-size: 1.8rem;
            margin: 0;
            color: #343a40;
        }
    </style>
</head>
<body>

<div class="jumbotron-small text-center">
    <h1>📚 Student Attendance System</h1>
</div>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="index.php">🏠 Home</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbar">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item"><a class="nav-link" href="profile.php">👤 Profile</a></li>
            <li class="nav-item"><a class="nav-link" href="attendance.php">🗓 Attendance</a></li>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="logout.php">🚪 Logout</a></li>
        </ul>
    </div>
</nav>
