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

if (isset($_POST['submit'])) {
    $name = isset($_POST['name']) ? (string)$_POST['name'] : null;
    $role = isset($_POST['role']) ? (string)$_POST['role'] : null;
    $status = isset($_POST['status']) ? (string)$_POST['status'] : null;

    db_connect();
    $user_repository = new UserRepository($conn);
    $update_result = $user_repository->update($editing_user_id, $editing_user['email'], $editing_user['password'], $name, $role, $status);
    db_close();

    if (!$update_result) {
        echo "<script>
            alert('Failed update user: message: ' . $update_result->error)
            window.history.back();
        </script>";
        exit();
    }

    db_connect();
    $session_repository = new SessionRepository($conn);
    $delete_result = $session_repository->delete_by_user_id($editing_user_id);
    db_close();

    if (!$delete_result) {
        echo "<script>
            alert('Failed delete user session: message: ' . $delete_result->error)
            window.history.back();
        </script>";
        exit();
    }

    echo "<script>
        alert('Edit data success!');
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
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
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

    <div class="section-title">Edit Data</div>

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
                    <input type="text" placeholder="Name" name="name" value="<?= $editing_user["name"] ?>" required>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label" for="role">
                    Role
                    <select class="role" id="role" name="role" required>
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
                    <select class="status" id="status" name="status" required>
                        <option value="">- Select Status -</option>
                        <?php foreach ($status_options as $option_idx => $option_value): ?>
                            <option value="<?php echo $option_value['value'] ?>" <?php echo ($option_value['value'] == $editing_user['is_active']) ? 'selected': ''; ?>><?php echo $option_value['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" name="submit">Save</button>
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
