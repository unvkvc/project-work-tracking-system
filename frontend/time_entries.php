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
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Time Entries</h2>
        <div>
            <a href="log_time.php" class="btn btn-primary btn-sm">+ Log Time</a>
            <a href="dashboard.php" class="btn btn-outline-dark btn-sm">Dashboard</a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="table-dark">
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
                                    <td><?php echo $entry['work_date']; ?></td>
                                    <td><?php echo $entry['project_name']; ?></td>
                                    <td><?php echo $entry['task_name']; ?></td>
                                    <td>
                                        <?php echo $entry['first_name'] . ' ' . $entry['last_name']; ?>
                                    </td>
                                    <td><?php echo $entry['hours_worked']; ?></td>
                                    <td><?php echo $entry['description']; ?></td>
                                    <td class="text-end">
                                        <a href="edit_time_entry.php?id=<?php echo $entry['time_entry_id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                           Edit
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

