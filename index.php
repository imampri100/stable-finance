<?php
include_once 'db_connect.php';
include_once 'repository/session_repository.php';
include_once 'repository/transaction_repository.php';
include_once 'constant/transaction_constant.php';

session_start();

global $conn;

// migrate database
db_connect();
db_migrate();
seed_admin();
seed_user();
db_close();

// get session_id from browser session
$session_id = isset($_SESSION['session_id']) ? $_SESSION['session_id'] : null;
if (!$session_id) {
    header("Location: login.php");
    session_destroy();
    exit();
}

// check session to database
db_connect();
$session_repository = new SessionRepository($conn);
$session = $session_repository->get_by_id($session_id);
db_close();

if (!$session || $session["expired_at"] < time()) {
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
    header("Location: login.php");
    session_destroy();
    exit();
}

if ($user["role"] == ROLE_ADMIN){
    header("Location: user_management.php");
    exit();
} else if ($user["role"] == ROLE_USER){
    header("Location: income_expense.php");
    exit();
} else {
    header("Location: login.php");
    session_destroy();
    exit();
}

