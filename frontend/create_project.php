<?php
require '../backend/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// get current user
$stmt = $pdo->prepare("SELECT id, first_name, last_name, role_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// allow only admin (1) and project manager (2)
if ($user['role_id'] != 1 && $user['role_id'] != 2) {
    echo "Access denied.";
    exit;
}

$message = '';

$stmt = $pdo->prepare("SELECT id, first_name, last_name FROM users WHERE role_id = 2");
$stmt->execute();
$managers = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $manager_id = $_POST['manager_id'];

    if (empty($name) || empty($start_date) || empty($manager_id)) {
        $message = "Name, start date, and manager are required.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO projects (name, description, start_date, end_date, manager_id)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([$name, $description, $start_date, $end_date, $manager_id]);
        $message = "Project created successfully.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Project</title>
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

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="mb-0 fw-bold text-success">
            <i class="bi bi-folder-plus"></i> Create Project
        </h2>

        <a href="projects.php"
           class="btn btn-outline-secondary rounded-3">
            Back to projects
        </a>

    </div>

    <!-- Card -->
    <div class="card border-0 shadow-lg rounded-4">

        <div class="card-body p-5">

            <!-- Message -->
            <?php if ($message): ?>
                <div class="alert alert-info rounded-3 mb-4">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <!-- Project Name -->
                <div class="mb-4">

                    <label class="form-label fw-semibold text-secondary">
                        Project name
                    </label>

                    <input type="text"
                           name="name"
                           class="form-control form-control-lg rounded-3"
                           required>

                </div>

                <!-- Description -->
                <div class="mb-4">

                    <label class="form-label fw-semibold text-secondary">
                        Description
                    </label>

                    <textarea name="description"
                              class="form-control rounded-3"
                              rows="4"></textarea>

                </div>

                <!-- Dates -->
                <div class="row">

                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold text-secondary">
                            Start date
                        </label>

                        <input type="date"
                               name="start_date"
                               class="form-control form-control-lg rounded-3"
                               required>

                    </div>

                    <div class="col-md-6 mb-4">

                        <label class="form-label fw-semibold text-secondary">
                            End date
                        </label>

                        <input type="date"
                               name="end_date"
                               class="form-control form-control-lg rounded-3">

                    </div>

                </div>

                <!-- Project Manager -->
                <div class="mb-5">

                    <label class="form-label fw-semibold text-secondary">
                        Project manager
                    </label>

                    <select name="manager_id"
                            class="form-select form-select-lg rounded-3"
                            required>

                        <option value="">Select manager</option>

                        <?php foreach ($managers as $manager): ?>

                            <option value="<?php echo $manager['id']; ?>">

                                <?php echo $manager['first_name'] . ' ' . $manager['last_name']; ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <!-- Button -->
                <button type="submit"
                        class="btn w-100 py-3 rounded-3 fw-semibold"
                        style="background-color:#90EE90;">

                    <i class="bi bi-check-circle"></i> Create Project

                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>