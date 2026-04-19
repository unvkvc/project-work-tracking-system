<?php
require '../backend/db.php';

session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($first) || empty($last) || empty($email) || empty($password)) {
        $message = "All fields are required.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users (first_name, last_name, email, password, role_id)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([$first, $last, $email, $hashed, 3]);

        $_SESSION['user_id'] = $pdo->lastInsertId();
        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="width: 400px;">
        
        <h3 class="text-center mb-4">Create Account</h3>

        <?php if ($message): ?>
            <div class="alert alert-danger">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First name</label>
                    <input name="first_name" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Last name</label>
                    <input name="last_name" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input name="password" type="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Register
            </button>
        </form>

        <p class="text-center mt-3 mb-0">
            Already have an account?
            <a href="login.php">Login</a>
        </p>

    </div>
</div>

</body>
</html>