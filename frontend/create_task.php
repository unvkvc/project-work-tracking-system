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

// only admin and project manager can create tasks
if ($user['role_id'] != 1 && $user['role_id'] != 2) {
    echo "Access denied.";
    exit;
}

$message = '';

// fetch projects
$stmt = $pdo->query("SELECT id, name FROM projects ORDER BY name");
$projects = $stmt->fetchAll();

// fetch employees
$stmt = $pdo->query("SELECT id, first_name, last_name FROM users WHERE role_id = 3 ORDER BY first_name, last_name");
$employees = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    $deadline = $_POST['deadline'];
    $project_id = $_POST['project_id'];
    $assigned_user_id = $_POST['assigned_user_id'];

    if (empty($name) || empty($status) || empty($project_id) || empty($assigned_user_id)) {
        $message = "Name, status, project, and assigned user are required.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO tasks (name, description, status, deadline, project_id, assigned_user_id)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([$name, $description, $status, $deadline, $project_id, $assigned_user_id]);
        $message = "Task created successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Task</title>
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

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="mb-0 fw-bold text-warning">
            <i class="bi bi-list-check"></i> Create Task
        </h2>

        <a href="tasks.php"
           class="btn btn-outline-secondary rounded-3">

            ← Back to Tasks

        </a>

    </div>

    <!-- MESSAGE -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-info rounded-3 mb-4">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- FORM CARD -->
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-body p-5">

            <form method="POST">

                <!-- TASK NAME -->
                <div class="mb-4">

                    <label class="form-label fw-semibold text-secondary">
                        Task name
                    </label>

                    <input type="text"
                           name="name"
                           class="form-control form-control-lg rounded-3"
                           required>

                </div>

                <!-- DESCRIPTION -->
                <div class="mb-4">

                    <label class="form-label fw-semibold text-secondary">
                        Description
                    </label>

                    <textarea name="description"
                              class="form-control rounded-3"
                              rows="4"></textarea>

                </div>

                <!-- STATUS + DEADLINE -->
                <div class="row">

                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold text-secondary">
                            Status
                        </label>

                        <select name="status"
                                class="form-select form-select-lg rounded-3"
                                required>

                            <option value="">Select status</option>
                            <option value="todo">To do</option>
                            <option value="in_progress">In progress</option>
                            <option value="done">Done</option>

                        </select>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold text-secondary">
                            Deadline
                        </label>

                        <input type="date"
                               name="deadline"
                               class="form-control form-control-lg rounded-3">

                    </div>

                </div>

                <!-- PROJECT -->
                <div class="mb-4">

                    <label class="form-label fw-semibold text-secondary">
                        Project
                    </label>

                    <select name="project_id"
                            class="form-select form-select-lg rounded-3"
                            required>

                        <option value="">Select project</option>

                        <?php foreach ($projects as $project): ?>

                            <option value="<?php echo $project['id']; ?>">

                                <?php echo $project['name']; ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- ASSIGNED USER -->
                <div class="mb-5">

                    <label class="form-label fw-semibold text-secondary">
                        Assign to employee
                    </label>

                    <select name="assigned_user_id"
                            class="form-select form-select-lg rounded-3"
                            required>

                        <option value="">Select employee</option>

                        <?php foreach ($employees as $employee): ?>

                            <option value="<?php echo $employee['id']; ?>">

                                <?php echo $employee['first_name'] . ' ' . $employee['last_name']; ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- SUBMIT -->
                <button type="submit"
                        class="btn w-100 py-3 rounded-3 fw-semibold"
                        style="background-color:#FFF59D;">

                    <i class="bi bi-check-circle"></i> Create Task

                </button>

            </form>

        </div>
    </div>

</div>

</body>
</html>