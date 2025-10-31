<?php
include_once 'db_connect.php';
include_once 'repository/base_repository.php';
include_once 'repository/session_repository.php';
include_once 'repository/user_repository.php';

global $conn;

session_start();

if (isset($_SESSION['session_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password != $confirmPassword) {
        echo "<script>
            alert('Password dan Confirm Password is not match. Please recheck!');
            window.history.back();
        </script>";
        exit();
    }

    db_connect();
    $user_repository = new UserRepository($conn);
    $user_sql = $user_repository->get_by_email($email);
    db_close();

    if ($user_sql) {
        echo "<script>
            alert('Email is already registered. Please login!');
            window.history.back();
        </script>";
        exit();
    }

    db_connect();
    $user_repository = new UserRepository($conn);
    $user_repository->create(generateUUID(), $email, hash('sha256', $password), $name, ROLE_USER, 1);
    db_close();

    echo "<script>
        alert('Sign up success. Please login!');
        window.location.href='index.php';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an Account</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #4A90E2;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Stable Finance</h1>
            <h2>Create an account</h2>
        </div>
        
        <form id="signup-form" method="POST">
            <div class="form-group">
                <label class="form-label">Enter your name</label>
                <input type="text" placeholder="Name" name="name" required>
            </div>

            <div class="form-group">
                <label class="form-label">Enter your email</label>
                <input type="email" placeholder="Email" name="email" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Create password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" placeholder="Password" name="password" required>
                    <span class="password-toggle" onclick="togglePassword('password')">Show</span>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Confirm password</label>
                <div class="input-wrapper">
                    <input type="password" id="confirm-password" placeholder="Password" name="confirm_password" required>
                    <span class="password-toggle" onclick="togglePassword('confirm-password')">Show</span>
                </div>
            </div>
            
            <button type="submit" class="submit-btn" name="submit">Create an account</button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const toggle = input.parentElement.querySelector('.password-toggle');
            
            if (input.type === 'password') {
                input.type = 'text';
                toggle.textContent = 'Hide';
            } else {
                input.type = 'password';
                toggle.textContent = 'Show';
            }
        }
    </script>
</body>
</html>

