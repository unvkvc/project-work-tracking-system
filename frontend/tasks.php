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

        .card {
            border: 0;
            border-radius: 16px;
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .table thead th {
            font-weight: 600;
            border-bottom: none;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge {
            padding: 8px 10px;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

    <div class="container">

        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="bi bi-kanban"></i> My System
        </a>

        <div class="ms-auto d-flex gap-2">

            <a href="projects.php"
               class="btn btn-outline-success btn-sm rounded-3">

                Projects

            </a>

            <a href="dashboard.php"
               class="btn btn-outline-light btn-sm rounded-3">

                Dashboard

            </a>

            <a href="logout.php"
               class="btn btn-danger btn-sm rounded-3">

                Logout

            </a>

        </div>

    </div>

</nav>

<!-- PAGE CONTENT -->
<div class="container py-5">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="mb-0 fw-bold text-warning">
            <i class="bi bi-list-task"></i> Tasks
        </h2>

        <?php if ($user['role_id'] == 1 || $user['role_id'] == 2): ?>

            <a href="create_task.php"
               class="btn btn-warning rounded-3 px-4">

                <i class="bi bi-plus-circle"></i> Create Task

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
    <div class="card shadow-lg">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th class="ps-4">Task name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Deadline</th>
                            <th>Project</th>
                            <th>Assigned user</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php if (count($tasks) === 0): ?>

                        <tr>

                            <td colspan="7"
                                class="text-center py-5 text-muted">

                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>

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

                            <!-- TASK NAME -->
                            <td class="ps-4 fw-semibold">
                                <?php echo $task['task_name']; ?>
                            </td>

                            <!-- DESCRIPTION -->
                            <td>
                                <?php echo $task['description']; ?>
                            </td>

                            <!-- STATUS -->
                            <td>

                                <?php
                                $status = $task['status'];

                                if ($status === 'done' || $status === 'completed') {

                                    echo '<span class="badge rounded-pill bg-success">
                                            <i class="bi bi-check-circle"></i> Done
                                          </span>';

                                } elseif ($status === 'in_progress') {

                                    echo '<span class="badge rounded-pill bg-warning text-dark">
                                            <i class="bi bi-clock-history"></i> In Progress
                                          </span>';

                                } else {

                                    echo '<span class="badge rounded-pill bg-secondary">
                                            <i class="bi bi-circle"></i> To Do
                                          </span>';
                                }
                                ?>

                            </td>

                            <!-- DEADLINE -->
                            <td>
                                <?php echo $task['deadline']; ?>
                            </td>

                            <!-- PROJECT -->
                            <td>

                                <span class="fw-semibold text-<?php echo $color; ?>">

                                    <?php echo $task['project_name']; ?>

                                </span>

                            </td>

                            <!-- USER -->
                            <td>

                                <?php echo $task['first_name'] . ' ' . $task['last_name']; ?>

                            </td>

                            <!-- ACTION -->
                            <td class="text-end pe-4">

                                <a href="edit_task.php?id=<?php echo $task['task_id']; ?>"
                                   class="btn btn-sm btn-outline-primary rounded-3">

                                    <i class="bi bi-pencil"></i> Edit

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