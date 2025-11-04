<?php
include_once 'db_connect.php';
include_once 'repository/session_repository.php';
include_once 'repository/transaction_repository.php';
include_once 'constant/transaction_constant.php';
include_once 'constant/role_constant.php';

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

// check role
db_connect();
$user_repository = new UserRepository($conn);
$user = $user_repository->get_by_id($session["user_id"]);
db_close();

if (!$user || $user["is_active"] != 1){
    header("Location: login.php");
    session_destroy();
    exit();
}

if ($user["role"] != ROLE_ADMIN){
    header("Location: index.php");
    exit();
}

// get user data
$editing_user_id = isset($_GET['user_id']) ? (string)$_GET['user_id'] : null;
if (!$editing_user_id){
    echo "<script>
        alert('User ID does not exist');
        window.history.back();
    </script>";;
    exit();
}

if ($editing_user_id == $user["id"]){
    echo "<script>
        alert('Not allowed, change your own data in profile page');
        window.history.back();
    </script>";;
    exit();
}

db_connect();
$user_repository = new UserRepository($conn);
$editing_user = $user_repository->get_by_id($editing_user_id);
db_close();

if (!$editing_user){
    echo "<script>
        alert('Failed get editing user data');
        window.history.back();
    </script>";;
    exit();
}

$status_options = [
    [
        "name" => "Inactive",
        "value" => 0
    ],
    [
        "name" => "Active",
        "value" => 1
    ],
];

$role_options = ROLES;

// handle submit
if (isset($_POST['submit'])) {
    echo "<script>
        window.location.href='user_management.php';
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
    include_once 'layout/sidebar_admin_layout.php';
    sidebarAdminLayout(0);
?>

<div class="main-content">
    <?php
        include_once 'layout/header_layout.php';
        headerLayout('User Management', $user);
    ?>

    <div class="section-title">View Data</div>

    <form id="edit-user-form" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="sign_up_date">
                    Sign Up Date
                    <input type="date" placeholder="Sign Up Date" name="sign_up_date" value="<?= date_format(date_create($editing_user['created_at']),'Y-m-d') ?>" disabled>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">
                    Email
                    <input type="text" placeholder="Email" name="email" value="<?= $editing_user["email"] ?>" disabled>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label" for="name">
                    Name
                    <input type="text" placeholder="Name" name="name" value="<?= $editing_user["name"] ?>" disabled>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label" for="role">
                    Role
                    <select class="role" id="role" name="role" disabled>
                        <option value="">- Select Role -</option>
                        <?php foreach ($role_options as $option_key => $option_value): ?>
                            <option value="<?php echo $option_value ?>" <?php echo ($option_value == $editing_user['role']) ? 'selected': ''; ?>><?php echo $option_value ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label" for="status">
                    Status
                    <select class="status" id="status" name="status" disabled>
                        <option value="">- Select Status -</option>
                        <?php foreach ($status_options as $option_idx => $option_value): ?>
                            <option value="<?php echo $option_value['value'] ?>" <?php echo ($option_value['value'] == $editing_user['is_active']) ? 'selected': ''; ?>><?php echo $option_value['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" name="submit">Back</button>
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
    </script>
</body>
</html>
