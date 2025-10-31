<?php
include_once 'db_connect.php';
include_once 'repository/base_repository.php';
include_once 'repository/session_repository.php';
include_once 'repository/user_repository.php';

global $conn;

session_start();

if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    db_connect();
    $user_repository = new UserRepository($conn);
    $user_sql = $user_repository->get_by_email($email);
    db_close();

    if (!$user_sql) {
        echo "<script>
            alert('Email or password is wrong. Please try again!')
            window.history.back();
        </script>";
        exit();
    }

    if (hash('sha256',$password) !== $user_sql['password']) {
        echo "<script>
            alert('Email or password is wrong. Please try again!')
            window.history.back();
        </script>";
        exit();
    }

    $new_session_id = generateUUID();
    $expires_in = 60 * 60 * 24 * 30; // 30 days in seconds
    $expired_at = date('Y-m-d H:i:s', time() + $expires_in);

    db_connect();
    $session_repository = new SessionRepository($conn);
    $result = $session_repository->create($new_session_id, $user_sql['id'], $expired_at);
    db_close();

    if (!$result) {
        echo "<script>
                alert('Login failed, message: ' . $result->error);
                window.history.back();
            </script>";
        exit();
    }

    $_SESSION['session_id'] = $new_session_id;
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            <h2>Login</h2>
        </div>
        
        <form id="login-form" method="POST">
            <div class="form-group">
                <label class="form-label" for="email">Your email</label>
                <input type="email" placeholder="Email" name="email" required>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="password">Your password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" placeholder="Password" name="password" required>
                    <span class="password-toggle" onclick="togglePassword('password')">Hide</span>
                </div>
                <div class="forgot-password">
                    <a href="#">Forget your password</a>
                </div>
            </div>
            
            <button type="submit" class="submit-btn" name="submit">Login</button>
        </form>
        
        <div class="signup-link">
            Don't have an account? <a href="register.php">Sign up</a>
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

