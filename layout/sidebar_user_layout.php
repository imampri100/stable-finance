<?php
function sidebarUserLayout($index): void
{
    echo
    '<div class="sidebar">
        <div class="logo">Stable Finance</div>
        <ul class="nav-menu">
            <a href="../income_expense.php" class="nav-item ' . ($index == 1 ? 'active' : '') . '">Income & Expense</a>
            <a href="../planner.php" class="nav-item ' . ($index == 2 ? 'active' : '') . '">Budget Planner</a>
        </ul>
    </div>';
}

//function sidebarUserLayout($index): void
//{
//    echo
//    '<div class="sidebar">
//        <div class="logo">Stable Finance</div>
//        <ul class="nav-menu">
//            <a href="../dashboard.php" class="nav-item ' . ($index == 0 ? 'active' : '') . '">Dashboard</a>
//            <a href="../income_expense.php" class="nav-item ' . ($index == 1 ? 'active' : '') . '">Income & Expense</a>
//            <a href="../planner.php" class="nav-item ' . ($index == 2 ? 'active' : '') . '">Budget Planner</a>
//            <a href="../goals.php" class="nav-item ' . ($index == 3 ? 'active' : '') . '">Saving Goals</a>
//            <a href="../manager.php" class="nav-item ' . ($index == 4 ? 'active' : '') . '">Debt Manager</a>
//            <a href="../financial.php" class="nav-item ' . ($index == 5 ? 'active' : '') . '">Financial Health</a>
//        </ul>
//    </div>';
//}
?>
