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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .project-item:hover {
            background-color: #f8f9fa;
            transition: 0.2s;
        }
    </style>
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">My System</a>

        <div class="ms-auto">
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container py-5">

    <!-- MAIN DASHBOARD CARD -->
    <div class="card shadow-sm mb-4">
        <div class="card-body text-center">

            <h2 class="mb-3">Dashboard</h2>

            <p class="mb-4">
                Logged in as
                <strong>
                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                </strong>
            </p>

            <div class="d-grid gap-3 col-md-6 mx-auto">

                <a href="projects.php" class="btn btn-primary">
                    View Projects
                </a>

                <a href="create_project.php" class="btn btn-success">
                    Create Project
                </a>

                <a href="create_task.php" class="btn btn-warning">
                    Create Task
                </a>

                <a href="tasks.php" class="btn btn-info">
                    View Tasks
                </a>

                <a href="log_time.php" class="btn btn-secondary">
                    Log Time
                </a>

                <a href="time_entries.php" class="btn btn-secondary">
                    View Time Entries
                </a>

                <?php if ($user['role_id'] == 1): ?>
                    <a href="manage_users.php" class="btn btn-dark">
                        Manage Users
                    </a>
                <?php endif; ?>

            </div>

        </div>
    </div>

    <!-- PROJECT MANAGER CARD -->
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