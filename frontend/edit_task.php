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
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="mb-0">Edit Task</h2>
                        <a href="tasks.php" class="btn btn-outline-dark btn-sm">Back</a>
                    </div>

                    <?php if ($message): ?>
                        <div class="alert alert-info">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="todo" <?php if ($task['status'] == 'todo') echo 'selected'; ?>>
                                    To do
                                </option>
                                <option value="in_progress" <?php if ($task['status'] == 'in_progress') echo 'selected'; ?>>
                                    In progress
                                </option>
                                <option value="done" <?php if ($task['status'] == 'done') echo 'selected'; ?>>
                                    Done
                                </option>
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Description / Completed Work</label>
                            <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($task['description']); ?></textarea>
                        </div>

                        <!-- Submit -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                Update Task
                            </button>

                            <a href="tasks.php" class="btn btn-outline-secondary">
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

