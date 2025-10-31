<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Dashboard</title>
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
        
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">Stable Finance</div>
        <ul class="nav-menu">
            <a href="#" class="nav-item active">Dashboard</a>
            <a href="income_expense.php" class="nav-item">Income & Expense</a>
            <a href="planner.php" class="nav-item">Budget Planner</a>
            <a href="goals.php" class="nav-item">Saving Goals</a>
            <a href="manager.php" class="nav-item">Debt Manager</a>
            <a href="financial.php" class="nav-item">Financial Health</a>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <div class="dashboard-title">Dashboard</div>
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
                        <a href="#" class="dropdown-item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Profile
                        </a>
                        <a href="#" class="dropdown-item">
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
            <div class="stat-card">
                <div class="stat-title">Total Income</div>
                <div class="stat-value">Rp45.000.000</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Total Expense</div>
                <div class="stat-value">Rp31.500.000</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Current Balance</div>
                <div class="stat-value">Rp13.500.000</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Saving Progress</div>
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                    <span>70%</span>
                </div>
            </div>
        </div>
        
        <div class="chart-container">
            <div class="chart-card">
                <div class="chart-title">Income vs Expense</div>
                <div class="line-chart">
                    <div class="y-axis-labels">
                        <div>Rp50.000.000</div>
                        <div>Rp35.500.000</div>
                        <div>Rp20.500.000</div>
                        <div>Rp10.500.000</div>
                        <div>Rp0</div>
                    </div>
                    <div class="line-chart-grid">
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                        <div class="grid-line"></div>
                    </div>
                    <div class="line">
                        <svg width="100%" height="100%">
                            <polyline class="income-line" points="0,160 20,140 40,120 60,100 80,80 100,60 120,40 140,20 160,0"></polyline>
                            <polyline class="expense-line" points="0,160 20,150 40,140 60,130 80,120 100,100 120,80 140,60 160,40"></polyline>
                        </svg>
                    </div>
                    <div class="x-axis-labels">
                        <span>Jan</span>
                        <span>Feb</span>
                        <span>Mar</span>
                        <span>Apr</span>
                        <span>May</span>
                        <span>Jun</span>
                        <span>Jul</span>
                        <span>Aug</span>
                        <span>Sep</span>
                        <span>Oct</span>
                        <span>Nov</span>
                        <span>Dec</span>
                    </div>
                </div>
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #00d4ff;"></div>
                        <span>Income</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #4A90E2;"></div>
                        <span>Expense</span>
                    </div>
                </div>
            </div>
            
            <div class="chart-card">
                <div class="chart-title">Expense by Category</div>
                <div class="donut-chart">
                    <div class="donut-circle"></div>
                    <div class="donut-center">70%</div>
                </div>
                <div class="donut-legend">
                    <div class="donut-legend-item">
                        <div class="donut-legend-color" style="background-color: #4A90E2;"></div>
                        <span>Tuition</span>
                    </div>
                    <div class="donut-legend-item">
                        <div class="donut-legend-color" style="background-color: #6cd8a3;"></div>
                        <span>Food</span>
                    </div>
                    <div class="donut-legend-item">
                        <div class="donut-legend-color" style="background-color: #ff6b6b;"></div>
                        <span>Rent</span>
                    </div>
                    <div class="donut-legend-item">
                        <div class="donut-legend-color" style="background-color: #ffc107;"></div>
                        <span>Others</span>
                    </div>
                </div>
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
                // // In a real application, you would handle the navigation here
                // alert('Navigation would happen here in a real application.');
            });
        });
    </script>
</body>
</html>

