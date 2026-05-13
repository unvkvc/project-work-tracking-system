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

if ($user['role_id'] == 3) {
    $stmt = $pdo->prepare("
        SELECT 
            time_entries.id AS time_entry_id,
            time_entries.work_date,
            time_entries.hours_worked,
            time_entries.description,
            tasks.name AS task_name,
            projects.name AS project_name,
            users.first_name,
            users.last_name
        FROM time_entries
        JOIN tasks ON time_entries.task_id = tasks.id
        JOIN projects ON tasks.project_id = projects.id
        JOIN users ON time_entries.user_id = users.id
        WHERE time_entries.user_id = ?
        ORDER BY time_entries.work_date DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
} else {
    $stmt = $pdo->query("
        SELECT 
            time_entries.id AS time_entry_id,
            time_entries.work_date,
            time_entries.hours_worked,
            time_entries.description,
            tasks.name AS task_name,
            projects.name AS project_name,
            users.first_name,
            users.last_name
        FROM time_entries
        JOIN tasks ON time_entries.task_id = tasks.id
        JOIN projects ON tasks.project_id = projects.id
        JOIN users ON time_entries.user_id = users.id
        ORDER BY time_entries.work_date DESC
    ");
}

$entries = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Time Entries</title>
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
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
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

            <a href="tasks.php"
               class="btn btn-outline-warning btn-sm rounded-3">

                Tasks

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

<div class="container py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="mb-0 fw-bold text-primary">
            <i class="bi bi-clock-history"></i> Time Entries
        </h2>

        <div class="d-flex gap-2">

            <a href="log_time.php"
               class="btn btn-primary rounded-3">

                <i class="bi bi-plus-circle"></i> Log Time

            </a>

        </div>

    </div>

    <!-- CARD -->
    <div class="card shadow-lg">

        <div class="card-body p-4">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>
                            <th>Date</th>
                            <th>Project</th>
                            <th>Task</th>
                            <th>User</th>
                            <th>Hours</th>
                            <th>Description</th>
                            <th class="text-end">Action</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php if (count($entries) > 0): ?>

                        <?php foreach ($entries as $entry): ?>

                            <tr>

                                <td>
                                    <?php echo $entry['work_date']; ?>
                                </td>

                                <td class="fw-semibold">
                                    <?php echo $entry['project_name']; ?>
                                </td>

                                <td>
                                    <?php echo $entry['task_name']; ?>
                                </td>

                                <td>
                                    <?php echo $entry['first_name'] . ' ' . $entry['last_name']; ?>
                                </td>

                                <td>

                                    <span class="badge bg-primary">

                                        <?php echo $entry['hours_worked']; ?> h

                                    </span>

                                </td>

                                <td>
                                    <?php echo $entry['description']; ?>
                                </td>

                                <td class="text-end">

                                    <a href="edit_time_entry.php?id=<?php echo $entry['time_entry_id']; ?>"
                                       class="btn btn-sm btn-outline-primary rounded-3">

                                        <i class="bi bi-pencil"></i> Edit

                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="7"
                                class="text-center text-muted py-4">

                                No time entries found.

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>

