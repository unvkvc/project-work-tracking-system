<?php
session_start();

require '../backend/db.php';

$stmt = $pdo->prepare("SELECT role_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f4f6f9;
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .dashboard-card {
            border: 0;
            border-radius: 20px;
            transition: 0.3s;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
        }

        .dashboard-btn {
            padding: 14px;
            border-radius: 14px;
            font-weight: 600;
            transition: 0.2s;
            border: none;
        }

        .dashboard-btn:hover {
            transform: scale(1.02);
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

    <div class="container">

        <a class="navbar-brand fw-bold" href="#">
            <i class="bi bi-kanban"></i> My System
        </a>

        <div class="ms-auto">

            <a href="logout.php"
               class="btn btn-danger btn-sm rounded-3">

                Logout

            </a>

        </div>

    </div>

</nav>

<!-- MAIN CONTENT -->
<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-7">

            <!-- CARD -->
            <div class="card dashboard-card shadow-lg">

                <div class="card-body text-center p-5">

                    <!-- TITLE -->
                    <h2 class="fw-bold mb-3 text-primary">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </h2>

                    <!-- USER -->
                    <p class="text-secondary mb-5">

                        Logged in as User ID:

                        <strong>
                            <?php echo $_SESSION['user_id']; ?>
                        </strong>

                    </p>

                    <!-- BUTTONS -->
                    <div class="d-grid gap-3">

                        <!-- PROJECTS -->
                        <a href="projects.php"
                           class="btn dashboard-btn text-white"
                           style="background-color:#198754">

                            <i class="bi bi-folder2-open"></i> View Projects

                        </a>

                        <!-- CREATE PROJECT -->
                        <a href="create_project.php"
                           class="btn dashboard-btn"
                           style="background-color:#90EE90">

                            <i class="bi bi-folder-plus"></i> Create Project

                        </a>

                        <!-- TASKS -->
                        <a href="tasks.php"
                           class="btn dashboard-btn"
                           style="background-color:#FFC107">

                            <i class="bi bi-list-task"></i> View Tasks

                        </a>

                        <!-- CREATE TASK -->
                        <a href="create_task.php"
                           class="btn dashboard-btn"
                           style="background-color:#FFF59D">

                            <i class="bi bi-plus-circle"></i> Create Task

                        </a>

                        <!-- TIME ENTRIES -->
                        <a href="time_entries.php"
                           class="btn dashboard-btn text-white"
                           style="background-color:#0d6efd">

                            <i class="bi bi-clock-history"></i> View Time Entries

                        </a>

                        <!-- LOG TIME -->
                        <a href="log_time.php"
                           class="btn dashboard-btn"
                           style="background-color:#ADD8E6">

                            <i class="bi bi-stopwatch"></i> Log Time

                        </a>

                        <!-- MANAGE USERS -->
                        <?php if ($user['role_id'] == 1): ?>

                            <a href="manage_users.php"
                               class="btn dashboard-btn text-white"
                               style="background-color:#212529">

                                <i class="bi bi-people"></i> Manage Users

                            </a>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>