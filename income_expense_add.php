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
    header("Location: login.php");
    session_destroy();
    exit();
}

if ($user["role"] != ROLE_USER){
    header("Location: index.php");
    exit();
}

// get type options
$types = [
    TRANSACTION_TYPE_INCOME,
];
array_push($types, ...TRANSACTION_TYPE_EXPENSE_OPTION);

// get category options
db_connect();
$budget_repository = new BudgetRepository($conn);
$budgets = $budget_repository->get_by_user_id($user['id']);
db_close();

db_connect();
$saving_repository = new SavingRepository($conn);
$savings = $saving_repository->get_by_user_id($user['id']);
db_close();

db_connect();
$debt_repository = new DebtRepository($conn);
$debts = $debt_repository->get_by_user_id($user['id']);
db_close();

$categories_by_type = [
    TRANSACTION_TYPE_INCOME => [TRANSACTION_TYPE_INCOME],
    TRANSACTION_TYPE_BUDGET => $budgets,
    TRANSACTION_TYPE_SAVING => $savings,
    TRANSACTION_TYPE_DEBT => $debts,
];

// handle submit
if (isset($_POST['submit'])) {
    $type = $_POST['type'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $nominal = $_POST['nominal'];
    $date = $_POST['date'];

    db_connect();
    $transaction_repository = new TransactionRepository($conn);
    $transaction = $transaction_repository->create(generateUUID(), $user['id'], $date, $type, $category, $description, $nominal);
    db_close();

    if (!$transaction) {
        echo "<script>
            alert('Failed insert transaction: message: ' . $transaction->error)
            window.history.back();
        </script>";
        exit();
    }

    echo "<script>
        alert('Add data success!');
        window.location.href='income_expense.php';
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
        a {
            color
        }

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
<div class="sidebar">
    <div class="logo">Stable Finance</div>
    <ul class="nav-menu">
        <a href="dashboard.php" class="nav-item">Dashboard</a>
        <a href="income_expense.php" class="nav-item active">Income & Expense</a>
        <a href="planner.php" class="nav-item">Budget Planner</a>
        <a href="goals.php" class="nav-item">Saving Goals</a>
        <a href="manager.php" class="nav-item">Debt Manager</a>
        <a href="financial.php" class="nav-item">Financial Health</a>
    </ul>
</div>

<div class="main-content">
    <div class="header">
        <div class="page-title">Income & Expense</div>
        <div class="user-info">
            <div class="user-avatar">JD</div>
            <div class="dropdown">
                <div class="dropdown-toggle" id="dropdown-toggle">
                    <span>John Doe</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 9l6 6 6-6"></path>
                    </svg>
                </div>
                <div class="dropdown-menu" id="dropdown-menu">
                    <a href="profile.php" class="dropdown-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Profile
                    </a>
                    <a href="settings.php" class="dropdown-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="3"></circle>
                            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path>
                        </svg>
                        Settings
                    </a>
                    <a href="logout.php" class="dropdown-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Sign out
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="section-title">Edit Data</div>

    <form id="add-transaction-form" method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label class="form-label" for="type">
                    Type
                    <select class="type" id="type" name="type" required>
                        <option value="">- Select Type -</option>
                        <?php foreach ($types as $option_key => $option_value): ?>
                            <option value="<?php echo $option_value ?>"><?php echo $option_value ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label" for="category">
                    Category
                    <select class="category" id="category" name="category" required>
                        <option value="">- Select Category -</option>
                        <!-- Other options handled by JS -->
                    </select>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">
                    Description
                    <input type="text" placeholder="Description" name="description" required>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label" for="nominal">
                    Nominal (Rupiah)
                    <input type="number" placeholder="Nominal" name="nominal" required>
                </label>
            </div>

            <div class="form-group">
                <label class="form-label" for="date">
                    Date
                    <input type="date" placeholder="Date" name="date" required>
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

        const typeSelect = document.getElementById('type');
        const categorySelect = document.getElementById('category');
        const categoriesByType = <?php echo json_encode($categories_by_type); ?>;

        // Empty category selection and reset option if the type selection has been changed
        typeSelect.addEventListener('change', function() {
            // Reset category to empty
            categorySelect.value = '';
            categorySelect.innerHTML = '<option value="">- Select Category -</option>';

            const selectedType = this.value;

            if (categoriesByType[selectedType]) {
                categoriesByType[selectedType].forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat;
                    option.textContent = cat;
                    categorySelect.appendChild(option);
                });
            }
        });
    </script>
</body>
</html>
