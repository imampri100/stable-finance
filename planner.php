<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Planner</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            display: flex;
            height: 100vh;
            background-color: #f5f7fa;
        }
        
        .sidebar {
            width: 250px;
            background-color: #6c9bd8;
            color: white;
            padding: 20px 0;
            display: flex;
            flex-direction: column;
        }
        
        .logo {
            padding: 20px 30px;
            font-weight: bold;
            font-size: 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 30px;
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
        
        .filter-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
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
            grid-template-columns: 1fr 2fr 2fr 3fr;
            font-weight: bold;
        }
        
        .table-row {
            display: grid;
            grid-template-columns: 1fr 2fr 2fr 3fr;
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
        
        .progress-bar {
            height: 8px;
            background-color: #ddd;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 5px;
        }
        
        .progress-fill {
            height: 100%;
            background-color: #4A90E2;
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
            margin-top: 20px;
            float: right;
        }
        
        .add-button:hover {
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
            
            .filter-container {
                flex-direction: column;
            }
            
            .stats-container {
                flex-direction: column;
            }
            
            .table-header,
            .table-row {
                grid-template-columns: 1fr 1fr 1fr;
            }
            
            .table-cell:nth-child(4) {
                display: none;
            }
            
            .add-button {
                width: 100%;
                float: none;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">Stable Finance</div>
        <ul class="nav-menu">
            <a href="dashboard.php" class="nav-item">Dashboard</a>
            <a href="income_expense.php" class="nav-item">Income & Expense</a>
            <a href="#" class="nav-item active">Budget Planner</a>
            <a href="goals.php" class="nav-item">Saving Goals</a>
            <a href="manager.php" class="nav-item">Debt Manager</a>
            <a href="financial.php" class="nav-item">Financial Health</a>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <div class="page-title">Budget Planner</div>
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
        
        <div class="filter-container">
            <div class="filter-group">
                <div class="filter-label">Tahun</div>
                <select class="filter-select">
                    <option>2025</option>
                    <option>2024</option>
                    <option>2023</option>
                </select>
            </div>
            <div class="filter-group">
                <div class="filter-label">Bulan</div>
                <select class="filter-select">
                    <option>September</option>
                    <option>August</option>
                    <option>July</option>
                    <option>June</option>
                    <option>May</option>
                    <option>April</option>
                    <option>March</option>
                    <option>February</option>
                    <option>January</option>
                </select>
            </div>
        </div>
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-title">Total Budget</div>
                <div class="stat-value">Rp 10.000.000</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Realization</div>
                <div class="stat-value">Rp 5.000.000</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Budget Left</div>
                <div class="stat-value">Rp 5.000.000</div>
            </div>
        </div>
        
        <div class="section-title">Detail Budget</div>
        
        <div class="table-container">
            <div class="table-header">
                <div>No</div>
                <div>Name</div>
                <div>Ratio</div>
                <div></div>
            </div>
            
            <div class="table-row">
                <div class="table-cell">1</div>
                <div class="table-cell">Transportation</div>
                <div class="table-cell">Rp 200.000 / Rp 500.000</div>
                <div class="table-cell">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 40%;"></div>
                    </div>
                </div>
            </div>
            
            <div class="table-row">
                <div class="table-cell">2</div>
                <div class="table-cell">Entertainment</div>
                <div class="table-cell">Rp 500.000 / Rp 400.000</div>
                <div class="table-cell">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 100%;"></div>
                    </div>
                </div>
            </div>
            
            <div class="table-row">
                <div class="table-cell">3</div>
                <div class="table-cell">Groceries</div>
                <div class="table-cell">Rp 200.000 / Rp 500.000</div>
                <div class="table-cell">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 40%;"></div>
                    </div>
                </div>
            </div>
            
            <div class="table-row">
                <div class="table-cell">4</div>
                <div class="table-cell">Others</div>
                <div class="table-cell">Rp 200.000 / Rp 500.000</div>
                <div class="table-cell">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 40%;"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <button class="add-button">+ Add Data</button>
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

