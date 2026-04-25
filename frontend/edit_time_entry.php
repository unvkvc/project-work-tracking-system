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

$stmt = $pdo->prepare("SELECT id, role_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("
    SELECT * 
    FROM time_entries 
    WHERE id = ?
");
$stmt->execute([$entry_id]);
$entry = $stmt->fetch();

if (!$entry) {
    echo "Time entry not found.";
    exit;
}

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

$currentHours = floor($entry['hours_worked']);
$currentMinutes = round(($entry['hours_worked'] - $currentHours) * 60);
?>

<h2>Edit Time Entry</h2>

<p><?php echo $message; ?></p>

<form method="POST">
    <label>Date:</label><br>
    <input type="date" name="work_date" value="<?php echo $entry['work_date']; ?>"><br><br>

    <label>Hours:</label><br>
    <select name="hours">
        <?php for ($i = 0; $i <= 12; $i++): ?>
            <option value="<?php echo $i; ?>" <?php if ($i == $currentHours) echo 'selected'; ?>>
                <?php echo $i; ?>
            </option>
        <?php endfor; ?>
    </select><br><br>

    <label>Minutes:</label><br>
    <select name="minutes">
        <?php foreach ([0, 15, 30, 45] as $minute): ?>
            <option value="<?php echo $minute; ?>" <?php if ($minute == $currentMinutes) echo 'selected'; ?>>
                <?php echo str_pad($minute, 2, '0', STR_PAD_LEFT); ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Description:</label><br>
    <textarea name="description"><?php echo $entry['description']; ?></textarea><br><br>

    <button type="submit">Update Time Entry</button>
</form>

<br>
<a href="time_entries.php">Back to Time Entries</a>