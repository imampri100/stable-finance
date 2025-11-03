<?php
function headerLayout($page_title, $user) : void
{
    // get user initial
    $user_name = trim($user["name"] ?? "User");
    $name_parts = preg_split('/\s+/', $user_name);

    if (count($name_parts) > 1) {
        $user_initial = strtoupper(substr($name_parts[0], 0, 1) . substr(end($name_parts), 0, 1));
    } else {
        $user_initial = strtoupper(substr($user_name, 0, 2));
    }

    echo
    '<div class="header">
        <div class="page-title">' . $page_title . '</div>
        <div class="user-info">
            <div class="user-avatar">' . $user_initial . '</div>
            <div class="dropdown">
                <div class="dropdown-toggle" id="dropdown-toggle">
                    <span>' . $user["name"] . '</span>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 9l6 6 6-6"></path>
                    </svg>
                </div>
                <div class="dropdown-menu" id="dropdown-menu">
                    <a href="../profile.php" class="dropdown-item">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        Profile
                    </a>
                    <a href="../logout.php" class="dropdown-item">
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
    </div>';
}
?>

