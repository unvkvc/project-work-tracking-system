<?php
require '../backend/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Task ID missing.";
    exit;
}

$task_id = $_GET['id'];

// get current user
$stmt = $pdo->prepare("SELECT id, role_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// get task
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
$stmt->execute([$task_id]);
$task = $stmt->fetch();

if (!$task) {
    echo "Task not found.";
    exit;
}

// permission check
if ($user['role_id'] == 3 && $task['assigned_user_id'] != $_SESSION['user_id']) {
    echo "Access denied.";
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $description = trim($_POST['description']);

    if ($status === $task['status'] && $description === $task['description']) {
        $message = "No changes were made.";
    } else {
        $stmt = $pdo->prepare("
            UPDATE tasks
            SET status = ?, description = ?
            WHERE id = ?
        ");

        $stmt->execute([$status, $description, $task_id]);

        $message = "Task updated.";

        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$task_id]);
        $task = $stmt->fetch();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Task</title>
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

                        <h2 class="mb-0 fw-bold text-warning">
                            <i class="bi bi-pencil-square"></i> Edit Task
                        </h2>

                        <a href="tasks.php"
                           class="btn btn-outline-secondary rounded-3">

                            Back

                        </a>

                    </div>

                    <!-- MESSAGE -->
                    <?php if ($message): ?>

                        <div class="alert alert-info rounded-3 mb-4">
                            <?php echo $message; ?>
                        </div>

                    <?php endif; ?>

                    <form method="POST">

                        <!-- STATUS -->
                        <div class="mb-4">

                            <label class="form-label fw-semibold text-secondary">
                                Status
                            </label>

                            <select name="status"
                                    class="form-select form-select-lg rounded-3">

                                <option value="todo"
                                    <?php if ($task['status'] == 'todo') echo 'selected'; ?>>

                                    To do

                                </option>

                                <option value="in_progress"
                                    <?php if ($task['status'] == 'in_progress') echo 'selected'; ?>>

                                    In progress

                                </option>

                                <option value="done"
                                    <?php if ($task['status'] == 'done') echo 'selected'; ?>>

                                    Done

                                </option>

                            </select>

                        </div>

                        <!-- DESCRIPTION -->
                        <div class="mb-5">

                            <label class="form-label fw-semibold text-secondary">
                                Description / Completed Work
                            </label>

                            <textarea name="description"
                                      class="form-control rounded-3"
                                      rows="5"><?php echo htmlspecialchars($task['description']); ?></textarea>

                        </div>

                        <!-- BUTTONS -->
                        <div class="d-flex justify-content-between">

                            <button type="submit"
                                    class="btn btn-warning px-4 py-2 rounded-3 fw-semibold">

                                <i class="bi bi-check-circle"></i> Update Task

                            </button>

                            <a href="tasks.php"
                               class="btn btn-light border px-4 py-2 rounded-3">

                                Cancel

                            </a>

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

