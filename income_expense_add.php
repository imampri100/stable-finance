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

$budget_categories = [];
foreach ($budgets as $budget){
    $formatted_amount = "Rp" . number_format($budget["target_amount"]);

    $concat_date = new DateTime("{$budget["year"]}-{$budget["month"]}-01");
    $month_year_formatted = date_format($concat_date,'M Y');

    $budget_categories[] = [
        'id' => $budget["id"],
        'name' => "{$budget["name"]} - {$formatted_amount} ($month_year_formatted)",
        'original_name' => $budget["name"],
    ];
}

db_connect();
$saving_repository = new SavingRepository($conn);
$savings = $saving_repository->get_by_user_id($user['id']);
db_close();

$saving_categories = [];
foreach ($savings as $saving){
    $formatted_amount = "Rp" . number_format($saving["target_amount"]);

    $start_date = date_create($saving['start_date']);
    $start_date_formatted = date_format($saving,'d-m-Y');

    $end_date = date_create($saving['end_date']);
    $end_date_formatted = date_format($saving,'d-m-Y');

    $saving_categories[] = [
        'id' => $saving["id"],
        'name' => "{$saving["name"]} - $formatted_amount ($start_date_formatted-$end_date_formatted)",
        'original_name' => $saving["name"],
    ];
}

db_connect();
$debt_repository = new DebtRepository($conn);
$debts = $debt_repository->get_by_user_id($user['id']);
db_close();

$debt_categories = [];
foreach ($debts as $debt){
    $formatted_amount = "Rp" . number_format($debt["target_amount"]);

    $start_date = date_create($debt['start_date']);
    $start_date_formatted = date_format($debt,'d-m-Y');

    $end_date = date_create($debt['end_date']);
    $end_date_formatted = date_format($debt,'d-m-Y');

    $debt_categories[] = [
        'id' => $debt["id"],
        'name' => "{$debt["name"]} - $formatted_amount ($start_date_formatted-$end_date_formatted)",
        'original_name' => $debt["name"],
    ];
}

$categories_by_type = [
    TRANSACTION_TYPE_INCOME => [[
        'id' => "0000-0000-0000-0000",
        'name' => TRANSACTION_TYPE_INCOME,
        "value" => TRANSACTION_TYPE_INCOME,
    ]],
    TRANSACTION_TYPE_BUDGET => $budget_categories,
    TRANSACTION_TYPE_SAVING => $saving_categories,
    TRANSACTION_TYPE_DEBT => $debt_categories,
];

// handle submit
if (isset($_POST['submit'])) {
    $type = $_POST['type'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    $nominal = $_POST['nominal'];
    $date = $_POST['date'];

    // add transaction
    $category_name = '';
    $budget_id = null;
    $saving_id = null;
    $debt_id = null;

    if ($type == TRANSACTION_TYPE_INCOME){
        $category_name = TRANSACTION_TYPE_INCOME;
    } else {
        foreach ($categories_by_type[$type] as $cat){
            if ($cat['id'] == $category_id){
                $category_name = $cat['original_name'];

                switch ($type) {
                    case TRANSACTION_TYPE_BUDGET:
                        $budget_id = $cat['id'];
                        break;
                    case TRANSACTION_TYPE_SAVING:
                        $saving_id = $cat['id'];
                        break;
                    case TRANSACTION_TYPE_DEBT:
                        $debt_id = $cat['id'];
                        break;
                }

                break;
            }
        }
    }

    db_connect();
    $transaction_repository = new TransactionRepository($conn);
    $transaction = $transaction_repository->create(generateUUID(), $user['id'], $date, $type, $category_name, $description, $nominal, $budget_id, $saving_id, $debt_id);
    db_close();

    if (!$transaction) {
        echo "<script>
            alert('Failed insert transaction: message: ' . $transaction->error)
            window.history.back();
        </script>";
        exit();
    }

    // update budget
    if ($budget_id != null){
        db_connect();
        $budget_repository = new BudgetRepository($conn);
        $budget = $budget_repository->get_by_user_id_and_id($user['id'], $budget_id);

        // calculating
        $budget['collected_amount'] = $budget['collected_amount'] + $nominal;
        $budget['remaining_amount'] = $budget['remaining_amount'] - $nominal;
        if (!empty($budget['collected_amount']) && $budget['collected_amount'] > 0) {
            $budget['percentage'] = ($budget['collected_amount'] / $budget['target_amount']) * 100;
        } else {
            $budget['percentage'] = 0;
        }

        $budget_repository->update($budget['id'], $budget['name'], $budget['month'], $budget['year'], $budget['collected_amount'], $budget['remaining_amount'], $budget['target_amount'], $budget['percentage']);
        db_close();
    }

    // update saving
    if ($saving_id != null){
        db_connect();
        $saving_repository = new SavingRepository($conn);
        $saving = $saving_repository->get_by_user_id_and_id($user['id'], $saving_id);

        // calculating
        $saving['collected_amount'] = $saving['collected_amount'] + $nominal;
        $saving['remaining_amount'] = $saving['remaining_amount'] - $nominal;
        if (!empty($saving['collected_amount']) && $saving['collected_amount'] > 0) {
            $saving['percentage'] = ($saving['collected_amount'] / $saving['target_amount']) * 100;
        } else {
            $saving['percentage'] = 0;
        }

        $saving_repository->update($saving['id'], $saving['name'], $saving['start_date'], $saving['end_date'], $saving['collected_amount'], $saving['remaining_amount'], $saving['target_amount'], $saving['percentage']);
        db_close();
    }

    // update debt
    if ($debt_id != null){
        db_connect();
        $debt_repository = new DebtRepository($conn);
        $debt = $debt_repository->get_by_user_id_and_id($user['id'], $debt_id);

        // calculating
        $debt['collected_amount'] = $debt['collected_amount'] + $nominal;
        $debt['remaining_amount'] = $debt['remaining_amount'] - $nominal;
        if (!empty($debt['collected_amount']) && $debt['collected_amount'] > 0) {
            $debt['percentage'] = ($debt['collected_amount'] / $debt['target_amount']) * 100;
        } else {
            $debt['percentage'] = 0;
        }

        $debt_repository->update($debt['id'], $debt['name'], $debt['start_date'], $debt['end_date'], $debt['collected_amount'], $debt['remaining_amount'], $debt['target_amount'], $debt['percentage']);
        db_close();
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
<?php
include_once 'layout/sidebar_user_layout.php';
sidebarUserLayout(1);
?>

<div class="main-content">
    <?php
    include_once 'layout/header_layout.php';
    headerLayout('Income & Expense', $user);
    ?>

    <div class="section-title">Add Data</div>

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
                <label class="form-label" for="category_id">
                    Category
                    <select class="category_id" id="category_id" name="category_id" required>
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
        const categorySelect = document.getElementById('category_id');
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
                    option.value = cat['id'];
                    option.textContent = cat['name'];
                    categorySelect.appendChild(option);
                });
            }
        });
    </script>
</body>
</html>
