<?php

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

    // Create Transaction table
    $transactionSql = "CREATE TABLE IF NOT EXISTS Transaction (
        id CHAR(36) PRIMARY KEY NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at TIMESTAMP,
        transaction_date DATE NOT NULL,
        transaction_type VARCHAR(30) NOT NULL,
        transaction_category VARCHAR(30) NOT NULL,
        description TEXT NOT NULL,
        amount BIGINT NOT NULL
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
        name VARCHAR(100) NOT NULL,
        month INT NOT NULL,
        year INT NOT NULL,
        collected_amount BIGINT NOT NULL,
        remaining_amount BIGINT NOT NULL,
        target_amount BIGINT NOT NULL
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
        name VARCHAR(100) NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        collected_amount BIGINT NOT NULL,
        remaining_amount BIGINT NOT NULL,
        target_amount BIGINT NOT NULL
    )";

    if (mysqli_query($conn, $savingSql)) {
//        echo "Table Saving created successfully";
    } else {
        error_log("Error creating table Saving: " . $conn->error);
    }

    $has_migrated = true;
}

function db_close() {
    global $conn;

    if ($conn) {
        mysqli_close($conn);
    }

    $conn = null;
}
?>