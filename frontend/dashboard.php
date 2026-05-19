<?php
session_start();
require '../backend/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get current user
$stmt = $pdo->prepare("
    SELECT id, first_name, last_name, role_id
    FROM users
    WHERE id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Get projects managed by current user (only for project managers)
$managedProjects = [];

if ($user['role_id'] == 2) {
    $stmt = $pdo->prepare("
        SELECT id, name, status, start_date, end_date
        FROM projects
        WHERE manager_id = ?
        ORDER BY start_date DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $managedProjects = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<<<<<<< HEAD
    <style>
        .project-item:hover {
            background-color: #f8f9fa;
            transition: 0.2s;
=======
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
>>>>>>> feature/projects-edit
        }
    </style>
</head>

<body>

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

<div class="container py-5">

<<<<<<< HEAD
    <div class="card shadow-sm mb-4">
        <div class="card-body text-center">
=======
    <div class="row justify-content-center">
>>>>>>> feature/projects-edit

        <div class="col-lg-7">

<<<<<<< HEAD
            <p class="mb-4">
                Logged in as
                <strong>
                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                </strong>
            </p>

            <div class="d-grid gap-3 col-md-6 mx-auto">
=======
            <!-- CARD -->
            <div class="card dashboard-card shadow-lg">

                <div class="card-body text-center p-5">
>>>>>>> feature/projects-edit

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

<<<<<<< HEAD
                    <!-- BUTTONS -->
                    <div class="d-grid gap-3">

<<<<<<< HEAD
                <?php if ($user['role_id'] == 1): ?>
                    <a href="manage_users.php" class="btn btn-dark">
                        Manage Users
                    </a>
                <?php endif; ?>
=======
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
>>>>>>> feature/projects-edit
=======
                <a href="project_progress.php" class="btn btn-secondary">
                    See Project Progress
                </a>

            <?php if ($user['role_id'] == 1): ?>
                <a href="manage_users.php" class="btn btn-dark">
                    Manage Users
                </a>
            <?php endif; ?>
>>>>>>> feature/project-progress

            </div>

        </div>

    </div>

    <?php if ($user['role_id'] == 2): ?>
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-sm">
                    <div class="card-body">

                        <h5 class="mb-3">My Projects</h5>

                        <?php if (count($managedProjects) === 0): ?>
                            <p class="text-muted mb-0">
                                You are not managing any projects yet.
                            </p>
                        <?php else: ?>

                            <?php foreach ($managedProjects as $project): ?>
                                <a href="edit_projects.php?id=<?php echo $project['id']; ?>"
                                   class="text-decoration-none text-dark">

                                    <div class="project-item d-flex justify-content-between align-items-center border-bottom py-2 px-2 rounded">

                                        <div>
                                            <strong>
                                                <?php echo htmlspecialchars($project['name']); ?>
                                            </strong><br>

                                            <small class="text-muted">
                                                <?php echo $project['start_date']; ?>
                                                -
                                                <?php echo $project['end_date']; ?>
                                            </small>
                                        </div>

                                        <?php if ($project['status'] == 'active'): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>

                                    </div>

                                </a>
                            <?php endforeach; ?>

                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

</body>
</html>