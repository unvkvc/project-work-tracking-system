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

        .register-card {
            width: 430px;
            border: 0;
            border-radius: 20px;
            transition: 0.3s;
        }

        .register-card:hover {
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

        .register-btn {
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            transition: 0.2s;
        }

        .register-btn:hover {
            transform: scale(1.02);
        }
    </style>
</head>

<body>

<div class="container d-flex justify-content-center align-items-center min-vh-100">

    <!-- CARD -->
    <div class="card register-card shadow-lg p-4">

        <div class="card-body">

            <!-- HEADER -->
            <div class="text-center mb-4">

                <div class="mb-3">

                    <i class="bi bi-person-plus-fill text-primary"
                       style="font-size: 3rem;"></i>

                </div>

                <h2 class="fw-bold text-dark">
                    Create Account
                </h2>

                <p class="text-muted mb-0">
                    Register a new account
                </p>

            </div>

            <!-- MESSAGE -->
            <?php if ($message): ?>

                <div class="alert alert-danger rounded-3">
                    <?php echo $message; ?>
                </div>

            <?php endif; ?>

            <!-- FORM -->
            <form method="POST">

                <!-- NAME -->
                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold text-secondary">
                            First name
                        </label>

                        <input name="first_name"
                               class="form-control"
                               required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label fw-semibold text-secondary">
                            Last name
                        </label>

                        <input name="last_name"
                               class="form-control"
                               required>

                    </div>

                </div>

                <!-- EMAIL -->
                <div class="mb-3">

                    <label class="form-label fw-semibold text-secondary">
                        Email
                    </label>

                    <input name="email"
                           type="email"
                           class="form-control"
                           placeholder="example@email.com"
                           required>

                </div>

                <!-- PASSWORD -->
                <div class="mb-4">

                    <label class="form-label fw-semibold text-secondary">
                        Password
                    </label>

                    <input name="password"
                           type="password"
                           class="form-control"
                           placeholder="Enter password"
                           required>

                </div>

                <!-- BUTTON -->
                <button type="submit"
                        class="btn btn-primary w-100 register-btn">

                    <i class="bi bi-check-circle"></i> Register

                </button>

            </form>

            <!-- LOGIN -->
            <p class="text-center mt-4 mb-0 text-muted">

                Already have an account?

                <a href="login.php"
                   class="text-decoration-none fw-semibold">

                    Login

                </a>

            </p>

        </div>

    </div>

</div>

</body>
</html>