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

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f4f6f9;
        }

        .card {
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-2px);
        }

        .form-control,
        .form-select {
            border: 1px solid #dfe3e8;
        }

        .form-control:focus,
        .form-select:focus {
            box-shadow: 0 0 0 0.15rem rgba(13,110,253,.15);
        }
    </style>
</head>

<body>

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-lg-8">

            <!-- CARD -->
            <div class="card border-0 shadow-lg rounded-4">

                <div class="card-body p-5">

                    <!-- HEADER -->
                    <div class="d-flex justify-content-between align-items-center mb-4">

                        <h2 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-stopwatch"></i> Log Time
                        </h2>

                        <div class="d-flex gap-2">

                            <a href="time_entries.php"
                               class="btn btn-outline-secondary rounded-3">

                                View Entries

                            </a>

                            <a href="dashboard.php"
                               class="btn btn-outline-dark rounded-3">

                                Dashboard

                            </a>

                        </div>

                    </div>

                    <!-- MESSAGE -->
                    <?php if ($message): ?>

                        <div class="alert alert-info rounded-3 mb-4">
                            <?php echo $message; ?>
                        </div>

                    <?php endif; ?>

                    <form method="POST">

                        <!-- TASK -->
                        <div class="mb-4">

                            <label class="form-label fw-semibold text-secondary">
                                Project / Task
                            </label>

                            <select name="task_id"
                                    class="form-select form-select-lg rounded-3">

                                <option value="">Select task</option>

                                <?php foreach ($tasks as $task): ?>

                                    <option value="<?php echo $task['task_id']; ?>">

                                        <?php echo $task['project_name'] . ' - ' . $task['task_name']; ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <!-- DATE -->
                        <div class="mb-4">

                            <label class="form-label fw-semibold text-secondary">
                                Date
                            </label>

                            <input type="date"
                                   name="work_date"
                                   class="form-control form-control-lg rounded-3">

                        </div>

                        <!-- TIME -->
                        <div class="mb-4">

                            <label class="form-label fw-semibold text-secondary">
                                Hours Worked
                            </label>

                            <div class="row">

                                <!-- HOURS -->
                                <div class="col-md-6 mb-3">

                                    <select name="hours"
                                            class="form-select form-select-lg rounded-3">

                                        <?php for ($i = 0; $i <= 12; $i++): ?>

                                            <option value="<?php echo $i; ?>">

                                                <?php echo $i; ?> h

                                            </option>

                                        <?php endfor; ?>

                                    </select>

                                </div>

                                <!-- MINUTES -->
                                <div class="col-md-6 mb-3">

                                    <select name="minutes"
                                            class="form-select form-select-lg rounded-3">

                                        <option value="0">00 min</option>
                                        <option value="15">15 min</option>
                                        <option value="30">30 min</option>
                                        <option value="45">45 min</option>

                                    </select>

                                </div>

                            </div>

                        </div>

                        <!-- DESCRIPTION -->
                        <div class="mb-5">

                            <label class="form-label fw-semibold text-secondary">
                                Description
                            </label>

                            <textarea name="description"
                                      class="form-control rounded-3"
                                      rows="5"></textarea>

                        </div>

                        <!-- BUTTON -->
                        <button type="submit"
                                class="btn btn-primary w-100 py-3 rounded-3 fw-semibold">

                            <i class="bi bi-check-circle"></i> Save Time

                        </button>

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
