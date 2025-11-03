<?php
include_once 'db_connect.php';
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

// get transactions data
db_connect();
$transaction_repository = new TransactionRepository($conn);
$result = $transaction_repository->get_by_user_id($user['id']);
db_close();

$total_income = 0;
$total_expense = 0;
$total_balance = 0;

$now_year = date('Y');
$now_month = date('n');

$selected_year = isset($_GET['year']) ? intval($_GET['year']) : $now_year;
$selected_month = isset($_GET['month']) ? intval($_GET['month']) : $now_month;

$temp_result = array();

foreach ($result as $transaction) {
    if ($transaction["transaction_type"] == TRANSACTION_TYPE_INCOME) {
        $total_income += $transaction['amount'];
        $total_balance += $transaction['amount'];
    } else {
        $total_expense += $transaction['amount'];
        $total_balance -= $transaction['amount'];
    }

    $is_match = true;
    if ($selected_year != null){
        $year = intval(date('Y', strtotime($transaction['transaction_date'])));
        $is_match = $year == $selected_year;

        if ($selected_month != null){
            $month = intval(date('n', strtotime($transaction['transaction_date'])));
            $is_match = $year == $selected_year && $month == $selected_month;
        }
    }

    if ($is_match){
        $temp_result[] = $transaction;
    }
}

$result = $temp_result;

$total_data = count($result);
$per_page = isset($_GET['size']) ? max(1, (int)$_GET['size']) : 10;
$total_pages = max(1, ceil($total_data / $per_page));

$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
if ($current_page > $total_pages) $current_page = $total_pages;

$start_order = ($current_page - 1) * $per_page;
$end_order = min($total_data, $start_order + $per_page);

$result = array_slice($result, $start_order, $per_page);
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
            text-decoration: none;
            color: inherit;
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
        
        .stats-container {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .stat-card {
            background-color: #e6f2ff;
            padding: 20px;
            border-radius: 10px;
            min-width: 200px;
            flex: 1;
        }
        
        .stat-title {
            font-size: 16px;
            color: #666;
            margin-bottom: 10px;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
        }
        
        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .filter-group {
            display: flex;
            gap: 10px;
        }
        
        .filter-label {
            font-size: 14px;
            color: #666;
            margin-right: 5px;
            display: flex;
            align-items: center;
        }
        
        .filter-select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            background-color: white;
            cursor: pointer;
        }
        
        .search-box {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .search-input {
            padding: 8px 12px;
            border: none;
            outline: none;
            flex: 1;
        }
        
        .search-icon {
            padding: 8px 12px;
            background-color: #f5f7fa;
            cursor: pointer;
        }
        
        .add-button {
            padding: 8px 16px;
            background-color: #4A90E2;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .add-button:hover {
            background-color: #3A7BD5;
        }
        
        .table-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .table-header {
            background-color: #6c9bd8;
            color: white;
            padding: 12px 20px;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 2fr 1fr 1fr;
            font-weight: bold;
        }
        
        .table-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 2fr 1fr 1fr;
            padding: 12px 20px;
            border-bottom: 1px solid #eee;
            align-items: center;
        }
        
        .table-row:last-child {
            border-bottom: none;
        }
        
        .table-cell {
            padding: 0 5px;
            word-break: break-word;
        }
        
        .action-icons {
            display: flex;
            gap: 8px;
        }
        
        .action-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        
        .action-icon:hover {
            background-color: #f5f7fa;
        }
        
        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding: 10px 0;
        }
        
        .page-size {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .page-size-select {
            padding: 5px 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .pagination-controls {
            display: flex;
            gap: 5px;
        }
        
        .page-number {
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .page-number.active {
            background-color: #4A90E2;
            color: white;
            border-color: #4A90E2;
        }
        
        .page-number:hover {
            background-color: #f5f7fa;
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
            
            .stats-container {
                flex-direction: column;
            }
            
            .controls {
                flex-direction: column;
                gap: 15px;
            }
            
            .filter-group {
                flex-wrap: wrap;
            }
            
            .table-header,
            .table-row {
                grid-template-columns: 1fr 1fr 1fr 1fr;
            }
            
            .table-cell:nth-child(4),
            .table-cell:nth-child(5),
            .table-cell:nth-child(6) {
                display: none;
            }
            
            .action-icons {
                justify-content: center;
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
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-title">Total Income</div>
                <div class="stat-value">Rp<?php echo number_format($total_income) ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Total Expense</div>
                <div class="stat-value">Rp<?php echo number_format($total_expense) ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Total Balance</div>
                <div class="stat-value">Rp<?php echo number_format($total_balance) ?></div>
            </div>
        </div>
        
        <div class="section-title">Income and Expense History</div>
        
        <div class="controls">
            <div class="filter-container">
                <form method="GET" style="display: flex; gap: 20px; align-items: center;">
                    <div class="filter-group">
                        <div class="filter-label">Year</div>
                        <select id="filter-year" class="filter-select" name="year" onchange="this.form.submit()">
                            <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                                <option value="<?= $y ?>" <?= $y == $selected_year ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <div class="filter-label">Month</div>
                        <select id="filter-month" class="filter-select" name="month" onchange="this.form.submit()">
                            <?php
                            $months = [
                                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                            ];
                            foreach ($months as $num => $name): ?>
                                <option value="<?= $num ?>" <?= $num == $selected_month ? 'selected' : '' ?>><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
            
            <a href="income_expense_add.php" class="add-button">+ Add Data</a>
        </div>
        
        <div class="table-container">
            <div class="table-header">
                <div>Date</div>
                <div>Type</div>
                <div>Category</div>
                <div>Description</div>
                <div>Nominal</div>
                <div>Action</div>
            </div>
            
            <?php
                foreach ($result as $row):
                    $date = date_create($row['transaction_date']);
                    $dateFormatted = date_format($date,'d-m-Y');

                    $amountFormatted = number_format($row['amount']);
            ?>
                     <div class='table-row'>
                         <div class='table-cell'><?php echo $dateFormatted ?></div>
                         <div class='table-cell'><?php echo $row['transaction_type'] ?></div>
                         <div class='table-cell'><?php echo $row['transaction_category'] ?></div>
                         <div class='table-cell'><?php echo $row['description'] ?></div>
                         <div class='table-cell'>Rp<?php echo $amountFormatted ?></div>
                         <div class='table-cell'>
                             <div class='action-icons'>
                                 <div class='action-icon'>
                                     <a href="income_expense_view.php?transaction_id=<?php echo $row['id'] ?>">
                                         <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                             <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
                                             <line x1="12" y1="9" x2="12" y2="15"></line>
                                         </svg>
                                     </a>
                                 </div>
                                 <div class='action-icon'>
                                     <a href="income_expense_edit.php?transaction_id=<?php echo $row['id'] ?>">
                                         <svg xmlns="http://www.w3.org/2000/svg" width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap="round" stroke-linejoin="round">
                                             <path d='M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7'></path>
                                             <path d='M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 4a2.121 2.121 0 0 1-3-3L14.5 6.5a2.121 2.121 0 0 1 3 3z'></path>
                                         </svg>
                                     </a>
                                 </div>
                                 <div class='action-icon'>
                                     <a href="income_expense_remove.php?transaction_id=<?php echo $row['id'] ?>">
                                         <svg xmlns="http://www.w3.org/2000/svg" width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap="round" stroke-linejoin="round">
                                             <path d='M3 6h18'></path>
                                             <path d='M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6'></path>
                                             <path d='M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2'></path>
                                             <line x1='10' y1='11' x2='10' y2='17'></line>
                                             <line x1='14' y1='11' x2='14' y2='17'></line>
                                         </svg>
                                     </a>
                                 </div>
                             </div>
                         </div>
                     </div>
            <?php endforeach; ?>

            <div class="pagination">
                <div class="page-size">
                    <select class="page-size-select" id="page-size-select" onchange="window.location.href='?year=<?= $selected_year ?>&month=<?= $selected_month ?>&page=1&size='+this.value;">
                        <option value="10" <?= ($per_page == 10) ? 'selected' : '' ?>>10</option>
                        <option value="20" <?= ($per_page == 20) ? 'selected' : '' ?>>20</option>
                        <option value="50" <?= ($per_page == 50) ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= ($per_page == 100) ? 'selected' : '' ?>>100</option>
                    </select>
                    <label for="page-size-select">Show <?= $start_order + 1 ?> - <?= $end_order ?> of <?= $total_data ?> data</label>
                </div>
                <div class="pagination-controls">
                    <?php if ($current_page > 1): ?>
                        <a href="?year=<?= $selected_year ?>&month=<?= $selected_month ?>&page=<?= $current_page - 1 ?>&size=<?= $per_page ?>" class="page-number">&lt;</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?year=<?= $selected_year ?>&month=<?= $selected_month ?>&page=<?= $i ?>&size=<?= $per_page ?>" class="page-number <?= ($i == $current_page) ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <a href="?year=<?= $selected_year ?>&month=<?= $selected_month ?>&page=<?= $current_page + 1 ?>&size=<?= $per_page ?>" class="page-number">&gt;</a>
                    <?php endif; ?>
                </div>
            </div>
    </div>

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
                // In a real application, you would handle the navigation here
                // The links already point to the correct PHP files
            });
        });
    </script>
</body>
</html>
