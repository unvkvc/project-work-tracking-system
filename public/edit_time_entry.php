<?php
require '../backend/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Time entry ID missing.";
    exit;
}

$entry_id = $_GET['id'];
$message = '';

// get user
$stmt = $pdo->prepare("SELECT id, role_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// get entry
$stmt = $pdo->prepare("SELECT * FROM time_entries WHERE id = ?");
$stmt->execute([$entry_id]);
$entry = $stmt->fetch();

if (!$entry) {
    echo "Time entry not found.";
    exit;
}

// permission check
if ($user['role_id'] == 3 && $entry['user_id'] != $_SESSION['user_id']) {
    echo "Access denied.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $work_date = $_POST['work_date'];
    $hours = (int) $_POST['hours'];
    $minutes = (int) $_POST['minutes'];
    $hours_worked = $hours + ($minutes / 60);
    $description = trim($_POST['description']);

    if (empty($work_date) || ($hours == 0 && $minutes == 0)) {
        $message = "Date and time are required.";
    } elseif (
        $work_date == $entry['work_date'] &&
        $hours_worked == $entry['hours_worked'] &&
        $description == $entry['description']
    ) {
        $message = "No changes were made.";
    } else {
        $stmt = $pdo->prepare("
            UPDATE time_entries
            SET work_date = ?, hours_worked = ?, description = ?
            WHERE id = ?
        ");

        $stmt->execute([$work_date, $hours_worked, $description, $entry_id]);

        $message = "Time entry updated.";

        $stmt = $pdo->prepare("SELECT * FROM time_entries WHERE id = ?");
        $stmt->execute([$entry_id]);
        $entry = $stmt->fetch();
    }
}

// split hours back into dropdown values
$currentHours = floor($entry['hours_worked']);
$currentMinutes = round(($entry['hours_worked'] - $currentHours) * 60);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Time Entry</title>
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
                            <i class="bi bi-pencil-square"></i> Edit Time Entry
                        </h2>

                        <a href="time_entries.php"
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

                        <!-- DATE -->
                        <div class="mb-4">

                            <label class="form-label fw-semibold text-secondary">
                                Date
                            </label>

                            <input type="date"
                                   name="work_date"
                                   class="form-control form-control-lg rounded-3"
                                   value="<?php echo $entry['work_date']; ?>">

                        </div>

                        <!-- HOURS -->
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

                                            <option value="<?php echo $i; ?>"
                                                <?php if ($i == $currentHours) echo 'selected'; ?>>

                                                <?php echo $i; ?> h

                                            </option>

                                        <?php endfor; ?>

                                    </select>

                                </div>

                                <!-- MINUTES -->
                                <div class="col-md-6 mb-3">

                                    <select name="minutes"
                                            class="form-select form-select-lg rounded-3">

                                        <?php foreach ([0, 15, 30, 45] as $minute): ?>

                                            <option value="<?php echo $minute; ?>"
                                                <?php if ($minute == $currentMinutes) echo 'selected'; ?>>

                                                <?php echo str_pad($minute, 2, '0', STR_PAD_LEFT); ?> min

                                            </option>

                                        <?php endforeach; ?>

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
                                      rows="5"><?php echo htmlspecialchars($entry['description']); ?></textarea>

                        </div>

                        <!-- BUTTONS -->
                        <div class="d-flex justify-content-between">

                            <button type="submit"
                                    class="btn btn-primary px-4 py-2 rounded-3 fw-semibold">

                                <i class="bi bi-check-circle"></i> Update Time Entry

                            </button>

                            <a href="time_entries.php"
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

