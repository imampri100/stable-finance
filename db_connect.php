<?php
include_once 'repository/user_repository.php';
include_once 'constant/role_constant.php';

$conn = null;
$has_migrated = false;

function db_connect() {
    global $conn;

    if ($conn) {
        return;
    }

    $servername = "mysql";
    $username = "user";
    $password = "password";
    $dbname = "main_db";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
//    echo "Connected successfully";
}

function db_migrate()
{
    global $conn;
    global $has_migrated;

    if ($conn == null) {
        return;
    }

    if ($has_migrated) {
        return;
    }

    // Create User table
    $userSql = "CREATE TABLE IF NOT EXISTS User (
        id CHAR(36) PRIMARY KEY NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP,
        email VARCHAR(100) NOT NULL,
        password VARCHAR(1000) NOT NULL,
        name VARCHAR(100) NOT NULL,
        role VARCHAR(30) NOT NULL,
        is_active TINYINT(1) DEFAULT 0
    )";

    if (mysqli_query($conn, $userSql)) {
//        echo "Table User created successfully";
    } else {
        error_log("Error creating table User: " . $conn->error);
    }

    // Create Session table
    $sessionSql = "CREATE TABLE IF NOT EXISTS Session (
        id CHAR(36) PRIMARY KEY NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP,
        user_id CHAR(36) NOT NULL,
        expired_at TIMESTAMP
    )";

    if (mysqli_query($conn, $sessionSql)) {
//        echo "Table Session created successfully";
    } else {
        error_log("Error creating table Session: " . $conn->error);
    }

    // Create Transaction table
    $transactionSql = "CREATE TABLE IF NOT EXISTS Transaction (
        id CHAR(36) PRIMARY KEY NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP,
        user_id CHAR(36) NOT NULL,
        transaction_date DATE NOT NULL,
        transaction_type VARCHAR(30) NOT NULL,
        transaction_category VARCHAR(30) NOT NULL,
        description TEXT NOT NULL,
        amount BIGINT NOT NULL,
        budget_id CHAR(36),
        saving_id CHAR(36),
        debt_id CHAR(36)
    )";

    if (mysqli_query($conn, $transactionSql)) {
//        echo "Table Transaction created successfully";
    } else {
        error_log("Error creating table Transaction: " . $conn->error);
    }

    // Create Budget table
    $budgetSql = "CREATE TABLE IF NOT EXISTS Budget (
        id CHAR(36) PRIMARY KEY NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP,
        user_id CHAR(36) NOT NULL,
        name VARCHAR(100) NOT NULL,
        month INT NOT NULL,
        year INT NOT NULL,
        collected_amount BIGINT NOT NULL,
        remaining_amount BIGINT NOT NULL,
        target_amount BIGINT NOT NULL,
        percentage FLOAT NOT NULL
    )";

    if (mysqli_query($conn, $budgetSql)) {
//        echo "Table Budget created successfully";
    } else {
        error_log("Error creating table Budget: " . $conn->error);
    }

    // Create Saving table
    $savingSql = "CREATE TABLE IF NOT EXISTS Saving (
        id CHAR(36) PRIMARY KEY NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP,
        user_id CHAR(36) NOT NULL,
        name VARCHAR(100) NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        collected_amount BIGINT NOT NULL,
        remaining_amount BIGINT NOT NULL,
        target_amount BIGINT NOT NULL,
        percentage FLOAT NOT NULL
    )";

    if (mysqli_query($conn, $savingSql)) {
//        echo "Table Saving created successfully";
    } else {
        error_log("Error creating table Saving: " . $conn->error);
    }

    // Create Debt table
    $debtSql = "CREATE TABLE IF NOT EXISTS Debt (
        id CHAR(36) PRIMARY KEY NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP,
        user_id CHAR(36) NOT NULL,
        name VARCHAR(100) NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        collected_amount BIGINT NOT NULL,
        remaining_amount BIGINT NOT NULL,
        target_amount BIGINT NOT NULL,
        percentage FLOAT NOT NULL
    )";

    if (mysqli_query($conn, $debtSql)) {
//        echo "Table Debt created successfully";
    } else {
        error_log("Error creating table Debt: " . $conn->error);
    }

    $has_migrated = true;
}

function seed_admin() {
    global $conn;

    db_connect();
    $user_repository = new UserRepository($conn);
    $users = $user_repository->get_by_role(ROLE_ADMIN);
    if (count($users) == 0) {
        $user_repository->create(generateUUID(), "admin@mail.com", hash('sha256', 'password'), 'Admin', ROLE_ADMIN, 1);
    }
    db_close();
}

function seed_user() {
    global $conn;

    db_connect();
    $user_repository = new UserRepository($conn);
    $users = $user_repository->get_by_role(ROLE_USER);
    if (count($users) == 0) {
        $user_repository->create(generateUUID(), "user@mail.com", hash('sha256', 'password'), 'User', ROLE_USER, 1);
    }
    db_close();
}

function db_close() {
    global $conn;

    if ($conn) {
        mysqli_close($conn);
    }

    $conn = null;
}
?>