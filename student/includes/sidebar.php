<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="d-flex align-items-center">
            <i class="fas fa-graduation-cap fa-2x me-3"></i>
            <div class="sidebar-brand">
                <h5 class="mb-0">MAP System</h5>
                <small class="text-muted">Student Portal</small>
            </div>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-chevron-left"></i>
        </button>
    </div>
    
    <ul class="nav nav-pills flex-column mt-4">
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" 
               href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i>
                <span class="nav-text">Dashboard</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'submit-activity.php' ? 'active' : ''; ?>" 
               href="submit-activity.php">
                <i class="fas fa-plus-circle"></i>
                <span class="nav-text">Submit Activity</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'my-submissions.php' ? 'active' : ''; ?>" 
               href="my-submissions.php">
                <i class="fas fa-list-alt"></i>
                <span class="nav-text">My Submissions</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'transcript.php' ? 'active' : ''; ?>" 
               href="transcript.php">
                <i class="fas fa-file-pdf"></i>
                <span class="nav-text">Transcript</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : ''; ?>" 
               href="notifications.php">
                <i class="fas fa-bell"></i>
                <span class="nav-text">Notifications</span>
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>" 
               href="profile.php">
                <i class="fas fa-user"></i>
                <span class="nav-text">Profile</span>
            </a>
        </li>
    </ul>
    
    <div class="mt-auto p-3 border-top border-white-50">
        <div class="d-flex align-items-center mb-2">
            <div class="avatar-circle me-2">
                <i class="fas fa-user"></i>
            </div>
            <div class="flex-grow-1">
                <div class="text-white fw-semibold text-truncate"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                <small class="text-white-50">Student</small>
            </div>
        </div>
        <a href="../auth/logout.php" class="btn btn-outline-light btn-sm w-100">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
        </a>
    </div>
</nav>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.sidebar.collapsed .nav-text {
    display: none;
}

.sidebar.collapsed .sidebar-brand {
    display: none;
}

.sidebar.collapsed .sidebar-header {
    padding: 1rem 0.5rem;
    text-align: center;
}

.sidebar.collapsed .avatar-circle + div {
    display: none;
}

.sidebar.collapsed .btn {
    padding: 0.375rem;
}

.sidebar.collapsed .btn .me-2 {
    margin-right: 0 !important;
}

.sidebar.collapsed .mt-auto .d-flex {
    justify-content: center;
}
</style>