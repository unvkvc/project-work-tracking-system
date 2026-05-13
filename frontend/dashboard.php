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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

    <div class="card shadow-sm">
        <div class="card-body text-center">

            <h2 class="mb-3">Dashboard</h2>

            <p class="mb-4">
                Logged in as User ID:
                <strong><?php echo $_SESSION['user_id']; ?></strong>
            </p>

            <div class="d-grid gap-3 col-6 mx-auto">

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

                <a href="project_progress.php" class="btn btn-secondary">
                    See Project Progress
                </a>

            <?php if ($user['role_id'] == 1): ?>
                <a href="manage_users.php" class="btn btn-dark">
                    Manage Users
                </a>
            <?php endif; ?>

            </div>

        </div>
    </div>

</div>

</body>
</html>