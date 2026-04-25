<?php
require '../backend/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// get current user
$stmt = $pdo->prepare("SELECT id, role_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// employee sees only their tasks
if ($user['role_id'] == 3) {
    $stmt = $pdo->prepare("
        SELECT 
            tasks.id AS task_id,
            tasks.name AS task_name,
            tasks.description,
            tasks.status,
            tasks.deadline,
            projects.name AS project_name,
            users.first_name,
            users.last_name
        FROM tasks
        JOIN projects ON tasks.project_id = projects.id
        JOIN users ON tasks.assigned_user_id = users.id
        WHERE tasks.assigned_user_id = ?
        ORDER BY tasks.id DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
} else {
    $stmt = $pdo->query("
        SELECT
            tasks.id AS task_id,
            tasks.name AS task_name,
            tasks.description,
            tasks.status,
            tasks.deadline,
            projects.name AS project_name,
            users.first_name,
            users.last_name
        FROM tasks
        JOIN projects ON tasks.project_id = projects.id
        JOIN users ON tasks.assigned_user_id = users.id
        ORDER BY tasks.id DESC
    ");
}

$tasks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tasks</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">

        <a class="navbar-brand" href="dashboard.php">My System</a>

        <div class="ms-auto d-flex gap-2">

             <a href="projects.php" class="btn btn-outline-light btn-sm">
                Projects
            </a>
            
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">
                Dashboard
            </a>

            <a href="logout.php" class="btn btn-danger btn-sm">
                Logout
            </a>

        </div>

    </div>
</nav>

<!-- PAGE CONTENT -->
<div class="container py-5">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Tasks</h2>

        <?php if ($user['role_id'] == 1 || $user['role_id'] == 2): ?>
            <a href="create_task.php" class="btn btn-success">
                + Create Task
            </a>
        <?php endif; ?>
    </div>

    <!-- COLOR SETUP -->
    <?php
    $projectColors = [];
    $colors = [
        'primary',
        'success',
        'warning',
        'info',
        'danger',
        'secondary'
    ];
    ?>

    <!-- CARD -->
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0 align-middle">

                    <thead class="table-dark">
                        <tr>
                            <th>Task name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Deadline</th>
                            <th>Project</th>
                            <th>Assigned user</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (count($tasks) === 0): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                You don't have any tasks assigned
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($tasks as $task): ?>

                        <?php
                        $projectName = $task['project_name'];

                        if (!isset($projectColors[$projectName])) {
                            $projectColors[$projectName] = $colors[crc32($projectName) % count($colors)];
                        }

                        $color = $projectColors[$projectName];
                        ?>

                        <tr>

                            <td><strong><?php echo $task['task_name']; ?></strong></td>

                            <td><?php echo $task['description']; ?></td>

                            <!-- STATUS -->
                            <td>
                                <?php
                                $status = $task['status'];

                                if ($status === 'done' || $status === 'completed') {
                                    echo '<span class="badge bg-success">Done</span>';
                                } elseif ($status === 'in_progress') {
                                    echo '<span class="badge bg-warning text-dark">In Progress</span>';
                                } else {
                                    echo '<span class="badge bg-secondary">To Do</span>';
                                }
                                ?>
                            </td>

                            <td><?php echo $task['deadline']; ?></td>

                            <!-- PROJECT COLORED TEXT -->
                            <td>
                                <span class="fw-semibold text-<?php echo $color; ?>">
                                    <?php echo $task['project_name']; ?>
                                </span>
                            </td>

                            <td>
                                <?php echo $task['first_name'] . ' ' . $task['last_name']; ?>
                            </td>

                            <td>
                                <a href="edit_task.php?id=<?php echo $task['task_id']; ?>" class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>

</body>
</html>