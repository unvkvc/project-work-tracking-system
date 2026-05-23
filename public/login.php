<?php
require 'C:\xampp\htdocs\project-work-tracking\backend/db.php';

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        $user = $stmt->fetch();

        if (!$user) {
            $error = "Email does not exist.";
        } elseif (!password_verify($password, $user['password'])) {
            $error = "Wrong password.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            header("Location: dashboard.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background: linear-gradient(to right, #f4f6f9, #eef2f7);
        }

        .login-card {
            width: 400px;
            border: 0;
            border-radius: 20px;
            transition: 0.3s;
        }

        .login-card:hover {
            transform: translateY(-3px);
        }

        .form-control {
            border: 1px solid #dfe3e8;
            border-radius: 12px;
            padding: 12px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.15rem rgba(13,110,253,.15);
        }

        .login-btn {
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            transition: 0.2s;
        }

        .login-btn:hover {
            transform: scale(1.02);
        }
    </style>
</head>

<body>

<div class="container d-flex justify-content-center align-items-center min-vh-100">

    <!-- CARD -->
    <div class="card login-card shadow-lg p-4">

        <div class="card-body">

            <!-- HEADER -->
            <div class="text-center mb-4">

                <div class="mb-3">

                    <i class="bi bi-box-arrow-in-right text-primary"
                       style="font-size: 3rem;"></i>

                </div>

                <h2 class="fw-bold text-dark">
                    Login
                </h2>

                <p class="text-muted mb-0">
                    Sign in to continue
                </p>

            </div>

            <!-- ERROR -->
            <?php if ($error): ?>

                <div class="alert alert-danger rounded-3">
                    <?php echo $error; ?>
                </div>

            <?php endif; ?>

            <!-- FORM -->
            <form method="POST">

                <!-- EMAIL -->
                <div class="mb-3">

                    <label class="form-label fw-semibold text-secondary">
                        Email
                    </label>

                    <input name="email"
                           type="email"
                           class="form-control"
                           placeholder="Enter email">

                </div>

                <!-- PASSWORD -->
                <div class="mb-4">

                    <label class="form-label fw-semibold text-secondary">
                        Password
                    </label>

                    <input name="password"
                           type="password"
                           class="form-control"
                           placeholder="Enter password">

                </div>

                <!-- BUTTON -->
                <button type="submit"
                        class="btn btn-primary w-100 login-btn">

                    <i class="bi bi-check-circle"></i> Login

                </button>

            </form>

            <!-- REGISTER -->
            <p class="text-center mt-4 mb-0 text-muted">

                Don't have an account?

                <a href="register.php"
                   class="text-decoration-none fw-semibold">

                    Register

                </a>

            </p>

        </div>

    </div>

</div>

</body>
</html>