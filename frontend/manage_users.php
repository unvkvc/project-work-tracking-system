<?php
require '../backend/db.php';
session_start();

// must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// get current user
$stmt = $pdo->prepare("SELECT role_id FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch();

// only admin allowed
if ($currentUser['role_id'] != 1) {
    echo "Access denied.";
    exit;
}

$message = '';

// handle role update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role_id'];

    $stmt = $pdo->prepare("UPDATE users SET role_id = ? WHERE id = ?");
    $stmt->execute([$new_role, $user_id]);

    $message = "Role updated.";
}

// fetch all users + role names
$stmt = $pdo->query("
    SELECT users.id, users.first_name, users.last_name, users.email, roles.role_name, users.role_id
    FROM users
    JOIN roles ON users.role_id = roles.id
    ORDER BY users.id
");

$users = $stmt->fetchAll();

// fetch roles for dropdown
$stmt = $pdo->query("SELECT id, role_name FROM roles");
$roles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Manage Users</h2>

        <a href="dashboard.php" class="btn btn-outline-secondary">
            ← Back to Dashboard
        </a>
    </div>

    <!-- MESSAGE -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-success">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- CARD -->
    <div class="card shadow-sm">
        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover table-striped mb-0 align-middle">

                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Current Role</th>
                            <th>Change Role</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($users as $user): ?>
                        <tr>

                            <td>
                                <strong>
                                    <?php echo $user['first_name'] . ' ' . $user['last_name']; ?>
                                </strong>
                            </td>

                            <td><?php echo $user['email']; ?></td>

                            <td>
                                <span class="badge bg-primary">
                                    <?php echo $user['role_name']; ?>
                                </span>
                            </td>

                            <td>

                                <form method="POST" class="d-flex gap-2">

                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                                    <select name="role_id" class="form-select form-select-sm">

                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?php echo $role['id']; ?>"
                                                <?php if ($role['id'] == $user['role_id']) echo 'selected'; ?>>
                                                <?php echo $role['role_name']; ?>
                                            </option>
                                        <?php endforeach; ?>

                                    </select>

                                    <button type="submit" class="btn btn-sm btn-success">
                                        Update
                                    </button>

                                </form>

                            </td>

                        </tr>
                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>

</body>
</html>