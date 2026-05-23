<?php
require '../backend/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch projects list
$stmt = $pdo->query("
    SELECT projects.*, users.first_name, users.last_name
    FROM projects
    JOIN users ON projects.manager_id = users.id
    ORDER BY projects.id DESC
");
$projects = $stmt->fetchAll();

// Fetch project progress data
$stmt = $pdo->query("
    SELECT 
        projects.id,
        projects.name,
        projects.status,
        COUNT(DISTINCT tasks.id) AS total_tasks,
        COUNT(
            DISTINCT CASE
                WHEN tasks.status IN ('done', 'completed')
                THEN tasks.id
            END
        ) AS completed_tasks,
        COALESCE(SUM(time_entries.hours_worked), 0) AS total_hours
    FROM projects
    LEFT JOIN tasks
        ON tasks.project_id = projects.id
    LEFT JOIN time_entries
        ON time_entries.task_id = tasks.id
    GROUP BY
        projects.id,
        projects.name,
        projects.status
    ORDER BY projects.id DESC
");
$projectProgress = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects</title>

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

<!-- PAGE CONTENT -->
<div class="container py-5">

    <!-- PROJECTS HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="mb-0 fw-bold text-success">
            <i class="bi bi-folder2-open"></i> Projects
        </h2>

        <a href="create_project.php"
           class="btn btn-success rounded-3 px-4">
            <i class="bi bi-plus-circle"></i> Create Project
        </a>

    </div>

    <!-- PROJECTS TABLE -->
    <div class="card shadow-lg mb-5">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Name</th>
                            <th>Description</th>
                            <th>Start date</th>
                            <th>End date</th>
                            <th>Manager</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (count($projects) === 0): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x fs-3 d-block mb-2"></i>
                                No projects found
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($projects as $project): ?>
                        <tr>

                            <td class="ps-4 fw-semibold">
                                <?php echo htmlspecialchars($project['name']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($project['description']); ?>
                            </td>

                            <td>
                                <?php echo $project['start_date']; ?>
                            </td>

                            <td>
                                <?php echo $project['end_date']; ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($project['first_name'] . ' ' . $project['last_name']); ?>
                            </td>

                            <td>
                                <?php if ($project['status'] == 'active'): ?>
                                    <span class="badge rounded-pill bg-success">
                                        <i class="bi bi-check-circle"></i> Active
                                    </span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-secondary">
                                        <i class="bi bi-pause-circle"></i> Inactive
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="text-end pe-4">
                                <a href="edit_projects.php?id=<?php echo $project['id']; ?>"
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

    <!-- PROJECT PROGRESS HEADER -->
    <div class="mb-4">
        <h2 class="mb-0 fw-bold text-primary">
            <i class="bi bi-bar-chart-line"></i> Project Progress
        </h2>
    </div>

    <!-- PROJECT PROGRESS TABLE -->
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>Project</th>
                            <th>Status</th>
                            <th>Total Tasks</th>
                            <th>Completed Tasks</th>
                            <th>Progress</th>
                            <th>Logged Hours</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if (count($projectProgress) === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No project progress found
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($projectProgress as $project): ?>

                        <?php
                        $totalTasks = (int) $project['total_tasks'];
                        $completedTasks = (int) $project['completed_tasks'];
                        $totalHours = (float) $project['total_hours'];

                        $progress = 0;

                        if ($totalTasks > 0) {
                            $progress = round(($completedTasks / $totalTasks) * 100);
                        }

                        if ($progress >= 100) {
                            $progressColor = 'success';
                        } elseif ($progress >= 50) {
                            $progressColor = 'info';
                        } elseif ($progress > 0) {
                            $progressColor = 'warning';
                        } else {
                            $progressColor = 'secondary';
                        }
                        ?>

                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($project['name']); ?></strong>
                            </td>

                            <td>
                                <?php if ($project['status'] === 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>

                            <td><?php echo $totalTasks; ?></td>

                            <td><?php echo $completedTasks; ?></td>

                            <td style="min-width: 220px;">
                                <?php if ($totalTasks == 0): ?>
                                    <span class="text-muted">No tasks</span>
                                <?php else: ?>
                                    <div class="progress" style="height: 24px;">
                                        <div class="progress-bar bg-<?php echo $progressColor; ?>"
                                             role="progressbar"
                                             style="width: <?php echo $progress; ?>%;">
                                            <?php echo $progress; ?>%
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td><?php echo number_format($totalHours, 2); ?></td>
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