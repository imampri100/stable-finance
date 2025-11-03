<?php
function sidebarAdminLayout($index): void
{
    echo
    '<div class="sidebar">
        <div class="logo">Stable Finance</div>
        <ul class="nav-menu">
            <a href="../user_management.php" class="nav-item ' . ($index == 0 ? 'active' : '') . '">User Management</a>
        </ul>
    </div>';
}
?>
