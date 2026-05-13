<?php
require '../backend/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

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

$projects = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Progress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Project Progress</h2>

        <a href="dashboard.php" class="btn btn-outline-secondary">
            Back to Dashboard
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">

                    <thead class="table-dark">
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

                    <?php if (count($projects) === 0): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No projects found.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($projects as $project): ?>

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
                            $progressColor = 'warning';
                        } else {
                            $progressColor = 'danger';
                        }
                        ?>

                        <tr>
                            <td>
                                <strong><?php echo $project['name']; ?></strong>
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

                            <td style="min-width: 200px;">
                                <?php if ($totalTasks == 0): ?>
                                    <span class="text-muted">No tasks</span>
                                <?php else: ?>
                                    <div class="progress">
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