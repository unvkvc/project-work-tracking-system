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
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="mb-0">Edit Time Entry</h2>
                        <a href="time_entries.php" class="btn btn-outline-dark btn-sm">Back</a>
                    </div>

                    <?php if ($message): ?>
                        <div class="alert alert-info">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <!-- Date -->
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="work_date" class="form-control"
                                   value="<?php echo $entry['work_date']; ?>">
                        </div>

                        <!-- Time -->
                        <div class="mb-3">
                            <label class="form-label">Hours Worked</label>
                            <div class="row">
                                <div class="col">
                                    <select name="hours" class="form-select">
                                        <?php for ($i = 0; $i <= 12; $i++): ?>
                                            <option value="<?php echo $i; ?>" <?php if ($i == $currentHours) echo 'selected'; ?>>
                                                <?php echo $i; ?> h
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div class="col">
                                    <select name="minutes" class="form-select">
                                        <?php foreach ([0, 15, 30, 45] as $minute): ?>
                                            <option value="<?php echo $minute; ?>" <?php if ($minute == $currentMinutes) echo 'selected'; ?>>
                                                <?php echo str_pad($minute, 2, '0', STR_PAD_LEFT); ?> min
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($entry['description']); ?></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                Update Time Entry
                            </button>

                            <a href="time_entries.php" class="btn btn-outline-secondary">
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

