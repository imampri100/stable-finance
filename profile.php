<?php
include_once 'db_connect.php';
include_once 'repository/budget_repository.php';
include_once 'repository/debt_repository.php';
include_once 'repository/saving_repository.php';
include_once 'repository/session_repository.php';
include_once 'repository/transaction_repository.php';
include_once 'repository/user_repository.php';
include_once 'constant/transaction_constant.php';

global $conn;

session_start();

// get session_id from browser session
$session_id = isset($_SESSION['session_id']) ? $_SESSION['session_id'] : null;
if (!$session_id){
    header("Location: login.php");
    session_destroy();
    exit();
}

// check session to database
db_connect();
$session_repository = new SessionRepository($conn);
$session = $session_repository->get_by_id($session_id);
db_close();

if (!$session || $session["expired_at"] < time()){
    header("Location: login.php");
    session_destroy();
    exit();
}

// check user
db_connect();
$user_repository = new UserRepository($conn);
$user = $user_repository->get_by_id($session["user_id"]);
db_close();

if (!$user || $user["is_active"] != 1){
    echo "<script>
        alert('User is inactive!')
        window.location.href = 'login.php';
    </script>";
    session_destroy();
    exit();
}

if ($user["role"] != ROLE_USER){
    header("Location: index.php");
    exit();
}

// handle submit profile
if (isset($_POST['submit-profile'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    db_connect();
    $user_repository = new UserRepository($conn);
    $update_result = $user_repository->update($user['id'], $email, $user['password'], $name, $user['role'], $user['is_active']);
    db_close();

    echo "<script>
        alert('Edit profle success!');
        window.history.back();
    </script>";
    exit();
}

// handle submit password
if (isset($_POST['submit-password'])) {
    $old_password = $_POST['old_password'];
    if (hash('sha256', $old_password) != $user["password"]){
        echo "<script>
            alert('Old Password is wrong!');
            window.history.back();
        </script>";
        exit();
    }

    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    if ($new_password != $confirm_new_password){
        echo "<script>
            alert('New Password and Confirm New Password is not matching!');
            window.history.back();
        </script>";
        exit();
    }

    $new_password = hash('sha256', $new_password);

    db_connect();
    $user_repository = new UserRepository($conn);
    $update_result = $user_repository->update($user['id'], $user['email'], $new_password, $user['name'], $user['role'], $user['is_active']);
    db_close();

    echo "<script>
        alert('Edit password success!');
        window.history.back();
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income & Expense</title>
    <link rel="stylesheet" href="css/style2.css">
    <style>
        .nav-menu {
            list-style: none;
            padding: 0 20px;
        }

        .nav-item {
            padding: 15px 20px;
            cursor: pointer;
            border-radius: 6px;
            margin-bottom: 10px;
            transition: background-color 0.3s;
            text-decoration: none;
            color: white;
            display: block;
        }

        .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-item.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: #ddd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #333;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-toggle {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .dropdown-toggle:hover {
            background-color: rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            min-width: 180px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #333;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .dropdown-item:hover {
            background-color: #f5f7fa;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }

        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="date"]:focus,
        select:focus {
            outline: none;
            border-color: #4A90E2;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px 30px;
            max-width: 800px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
        }

        .form-actions {
            grid-column: span 2;
            display: flex;
            justify-content: flex-end;
        }

        .btn {
            background-color: #4A90E2;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn:hover {
            background-color: #3A7BD5;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }

            .logo {
                padding: 20px;
                text-align: center;
            }

            .nav-menu {
                padding: 0;
            }

            .nav-item {
                padding: 15px;
                text-align: center;
            }

            .nav-item span {
                display: none;
            }

            .main-content {
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<?php
include_once 'layout/sidebar_user_layout.php';
sidebarUserLayout(1);
?>

<div class="main-content">
    <?php
    include_once 'layout/header_layout.php';
    headerLayout('Profile', $user);
    ?>

    <div class="section-title">Edit Data</div>

    <form id="add-profile-form" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="name">
                    Name
                    <input type="text" placeholder="Name" name="name" value="<?= $user['name'] ?>" required>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">
                    Email
                    <input type="text" placeholder="Email" name="email" value="<?= $user['email'] ?>" required>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" name="submit-profile">Save Profile</button>
            </div>
        </div>
    </form>

    <br/>
    <br/>
    <br/>

    <form id="add-password-form" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="new_password">
                    New Password
                    <input type="password" id="new_password" placeholder="New Password" name="new_password" required>
                </label>
                <span class="password-toggle" onclick="togglePassword('new_password')">Show</span>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirm_new_password">
                    Confirm New Password
                    <input type="password" id="confirm_new_password" placeholder="Confirm New Password" name="confirm_new_password" required>
                </label>
                <span class="password-toggle" onclick="togglePassword('confirm_new_password')">Show</span>
            </div>

            <div class="form-group">
                <label class="form-label" for="old_password">
                    Old Password
                    <input type="password" id="old_password" placeholder="Old Password" name="old_password" required>
                </label>
                <span class="password-toggle" onclick="togglePassword('old_password')">Show</span>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" name="submit-password">Save Password</button>
            </div>
        </div>
    </form>


    <script>
        // Add interactivity to navigation items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Dropdown functionality
        const dropdownToggle = document.getElementById('dropdown-toggle');
        const dropdownMenu = document.getElementById('dropdown-menu');

        dropdownToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });

        // Close dropdown when clicking on menu items
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function() {
                dropdownMenu.classList.remove('show');
            });
        });

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
