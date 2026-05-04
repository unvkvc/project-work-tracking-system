<?php
require '../backend/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';

// get current user
$stmt = $pdo->prepare("SELECT id, role_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// employees see only their tasks, managers/admins see all tasks
if ($user['role_id'] == 3) {
    $stmt = $pdo->prepare("
        SELECT 
            tasks.id AS task_id,
            tasks.name AS task_name,
            projects.name AS project_name
        FROM tasks
        JOIN projects ON tasks.project_id = projects.id
        WHERE tasks.assigned_user_id = ?
        ORDER BY projects.name, tasks.name
    ");
    $stmt->execute([$_SESSION['user_id']]);
} else {
    $stmt = $pdo->query("
        SELECT 
            tasks.id AS task_id,
            tasks.name AS task_name,
            projects.name AS project_name
        FROM tasks
        JOIN projects ON tasks.project_id = projects.id
        ORDER BY projects.name, tasks.name
    ");
}

$tasks = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['task_id'];
    $work_date = $_POST['work_date'];

    $hours = (int) $_POST['hours'];
    $minutes = (int) $_POST['minutes'];
    $hours_worked = $hours + ($minutes / 60);

    $description = trim($_POST['description']);

    if (empty($task_id) || empty($work_date) || ($hours == 0 && $minutes == 0)) {
        $message = "Task, date, and time are required.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO time_entries (user_id, task_id, work_date, hours_worked, description)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $_SESSION['user_id'],
            $task_id,
            $work_date,
            $hours_worked,
            $description
        ]);

        $message = "Time entry saved.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log Time</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow">
                <div class="card-body">

                    <h2 class="card-title mb-4">Log Time</h2>

                    <?php if ($message): ?>
                        <div class="alert alert-info">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <!-- Task -->
                        <div class="mb-3">
                            <label class="form-label">Project / Task</label>
                            <select name="task_id" class="form-select">
                                <option value="">Select task</option>
                                <?php foreach ($tasks as $task): ?>
                                    <option value="<?php echo $task['task_id']; ?>">
                                        <?php echo $task['project_name'] . ' - ' . $task['task_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Date -->
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="work_date" class="form-control">
                        </div>

                        <!-- Time -->
                        <div class="mb-3">
                            <label class="form-label">Hours Worked</label>
                            <div class="row">
                                <div class="col">
                                    <select name="hours" class="form-select">
                                        <?php for ($i = 0; $i <= 12; $i++): ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?> h</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col">
                                    <select name="minutes" class="form-select">
                                        <option value="0">00 min</option>
                                        <option value="15">15 min</option>
                                        <option value="30">30 min</option>
                                        <option value="45">45 min</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                Save Time
                            </button>

                            <div>
                                <a href="time_entries.php" class="btn btn-outline-secondary btn-sm">
                                    View Entries
                                </a>
                                <a href="dashboard.php" class="btn btn-outline-dark btn-sm">
                                    Dashboard
                                </a>
                            </div>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

