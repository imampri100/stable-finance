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
    <div class="sidebar">
        <div class="logo">App Name / Logo</div>
        <ul class="nav-menu">
            <a href="dashboard.php" class="nav-item">Dashboard</a>
            <a href="#" class="nav-item active">Income & Expense</a>
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
        
        <div class="stats-container">
            <!-- Total Income: SELECT SUM(amount) FROM transactions WHERE type = 'income' -->
            <div class="stat-card">
                <div class="stat-title">Total Income</div>
                <div class="stat-value">Rp50.000.000</div>
            </div>
            
            <!-- Total Expense: SELECT SUM(amount) FROM transactions WHERE type = 'expense' -->
            <div class="stat-card">
                <div class="stat-title">Total Expense</div>
                <div class="stat-value">Rp10.000.000</div>
            </div>
            
            <!-- Total Balance: Total Income - Total Expense -->
            <div class="stat-card">
                <div class="stat-title">Total Balance</div>
                <div class="stat-value">Rp40.000.000</div>
            </div>
        </div>
        
        <div class="section-title">Income and Expense History</div>
        
        <div class="controls">
            <div class="filter-group">
                <div class="filter-label">Filter:</div>
                <select class="filter-select">
                    <option>Month</option>
                    <option>Week</option>
                    <option>Year</option>
                </select>
                <select class="filter-select">
                    <option>All Types</option>
                    <option>Income</option>
                    <option>Expense</option>
                </select>
                <select class="filter-select">
                    <option>All Categories</option>
                    <option>Groceries</option>
                    <option>Tuition</option>
                    <option>Rent</option>
                    <option>Others</option>
                </select>
            </div>
            
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Search Here...">
                <div class="search-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>
            </div>
            
            <button class="add-button">+ Add Data</button>
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
            
            <!-- 
            <?php
                // PHP code to fetch data from the database and echo table rows
                // $conn = new mysqli($servername, $username, $password, $dbname);
                // $sql = "SELECT date, type, category, description, nominal FROM transactions ORDER BY date DESC";
                // $result = $conn->query($sql);
                // while($row = $result->fetch_assoc()) {
                //     echo "<div class='table-row'>
                //             <div class='table-cell'>{$row['date']}</div>
                //             <div class='table-cell'>{$row['type']}</div>
                //             <div class='table-cell'>{$row['category']}</div>
                //             <div class='table-cell'>{$row['description']}</div>
                //             <div class='table-cell'>Rp {$row['nominal']}</div>
                //             <div class='table-cell'>
                //                 <div class='action-icons'>
                //                     <div class='action-icon'>
                //                         <svg width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'>
                //                             <eye width='16' height='16' fill='none' stroke='currentColor' stroke-width='2'>
                //                                 <path d='M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z'></path>
                //                                 <circle cx='12' cy='12' r='3'></circle>
                //                             </eye>
                //                         </svg>
                //                     </div>
                //                     <div class='action-icon'>
                //                         <svg width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'>
                //                             <path d='M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7'></path>
                //                             <path d='M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 4a2.121 2.121 0 0 1-3-3L14.5 6.5a2.121 2.121 0 0 1 3 3z'></path>
                //                         </svg>
                //                     </div>
                //                     <div class='action-icon'>
                //                         <svg width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'>
                //                             <path d='M3 6h18'></path>
                //                             <path d='M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6'></path>
                //                             <path d='M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2'></path>
                //                             <line x1='10' y1='11' x2='10' y2='17'></line>
                //                             <line x1='14' y1='11' x2='14' y2='17'></line>
                //                         </svg>
                //                     </div>
                //                 </div>
                //             </div>
                //         </div>";
                // }
            ?>
            -->
            
            <div class="table-row">
                <div class="table-cell">29 Sep 2025</div>
                <div class="table-cell">Budget</div>
                <div class="table-cell">Groceries</div>
                <div class="table-cell">Monthly Shopping</div>
                <div class="table-cell">Rp 1.000.000</div>
                <div class="table-cell">
                    <div class="action-icons">
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <eye width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </eye>
                            </svg>
                        </div>
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 4a2.121 2.121 0 0 1-3-3L14.5 6.5a2.121 2.121 0 0 1 3 3z"></path>
                            </svg>
                        </div>
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-row">
                <div class="table-cell">29 Sep 2025</div>
                <div class="table-cell">Loan</div>
                <div class="table-cell">Car Installment</div>
                <div class="table-cell">Pay Car Installment</div>
                <div class="table-cell">Rp 2.000.000</div>
                <div class="table-cell">
                    <div class="action-icons">
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <eye width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </eye>
                            </svg>
                        </div>
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 4a2.121 2.121 0 0 1-3-3L14.5 6.5a2.121 2.121 0 0 1 3 3z"></path>
                            </svg>
                        </div>
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-row">
                <div class="table-cell">29 Sep 2025</div>
                <div class="table-cell">Budget</div>
                <div class="table-cell">Others</div>
                <div class="table-cell">Buy Snacks</div>
                <div class="table-cell">Rp 1.000.000</div>
                <div class="table-cell">
                    <div class="action-icons">
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <eye width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </eye>
                            </svg>
                        </div>
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 4a2.121 2.121 0 0 1-3-3L14.5 6.5a2.121 2.121 0 0 1 3 3z"></path>
                            </svg>
                        </div>
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-row">
                <div class="table-cell">29 Sep 2025</div>
                <div class="table-cell">Saving</div>
                <div class="table-cell">Tuition Fee</div>
                <div class="table-cell">Pay Tuition Fee</div>
                <div class="table-cell">Rp 5.000.000</div>
                <div class="table-cell">
                    <div class="action-icons">
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <eye width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </eye>
                            </svg>
                        </div>
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 4a2.121 2.121 0 0 1-3-3L14.5 6.5a2.121 2.121 0 0 1 3 3z"></path>
                            </svg>
                        </div>
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-row">
                <div class="table-cell">28 Sep 2025</div>
                <div class="table-cell">Income</div>
                <div class="table-cell">Income</div>
                <div class="table-cell">Main Salary</div>
                <div class="table-cell">Rp 10.000.000</div>
                <div class="table-cell">
                    <div class="action-icons">
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <eye width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </eye>
                            </svg>
                        </div>
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 4a2.121 2.121 0 0 1-3-3L14.5 6.5a2.121 2.121 0 0 1 3 3z"></path>
                            </svg>
                        </div>
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-row">
                <div class="table-cell">28 Sep 2025</div>
                <div class="table-cell">Income</div>
                <div class="table-cell">Income</div>
                <div class="table-cell">Freelance Salary</div>
                <div class="table-cell">Rp 3.000.000</div>
                <div class="table-cell">
                    <div class="action-icons">
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <eye width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </eye>
                            </svg>
                        </div>
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 4a2.121 2.121 0 0 1-3-3L14.5 6.5a2.121 2.121 0 0 1 3 3z"></path>
                            </svg>
                        </div>
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-row">
                <div class="table-cell">28 Sep 2025</div>
                <div class="table-cell">Income</div>
                <div class="table-cell">Income</div>
                <div class="table-cell">Refund From John Doe</div>
                <div class="table-cell">Rp 3.000.000</div>
                <div class="table-cell">
                    <div class="action-icons">
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <eye width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </eye>
                            </svg>
                        </div>
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 4a2.121 2.121 0 0 1-3-3L14.5 6.5a2.121 2.121 0 0 1 3 3z"></path>
                            </svg>
                        </div>
                        <div class="action-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                <line x1="14" y1="11" x2="14" y2="17"></line>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="pagination">
            <div class="page-size">
                <select class="page-size-select">
                    <option>10</option>
                    <option>20</option>
                    <option>50</option>
                    <option>100</option>
                </select>
                <span>Show 1 - 10 from 8 data</span>
            </div>
            <div class="pagination-controls">
                <div class="page-number">&lt;</div>
                <div class="page-number active">1</div>
                <div class="page-number">2</div>
                <div class="page-number">3</div>
                <div class="page-number">...</div>
                <div class="page-number">11</div>
                <div class="page-number">&gt;</div>
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

