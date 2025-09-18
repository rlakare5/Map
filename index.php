<?php
require_once 'config/database.php';

// Redirect if already logged in
if (isLoggedIn()) {
    switch($_SESSION['user_type']) {
        case 'student':
            redirect('student/dashboard.php');
            break;
        case 'coordinator':
            redirect('coordinator/dashboard.php');
            break;
        case 'hod':
            redirect('hod/dashboard.php');
            break;
        case 'admin':
            redirect('admin/dashboard.php');
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanjivani University - MAP Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="login-page">
    <div class="container-fluid">
        <div class="row min-vh-100">
            <!-- Left side - Branding -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center bg-primary-gradient">
                <div class="text-center text-white">
                    <div class="university-logo mb-4">
                        <i class="fas fa-graduation-cap fa-5x mb-3"></i>
                        <h1 class="display-4 fw-bold">Sanjivani University</h1>
                        <p class="lead">Multi-Activity Program Management System</p>
                    </div>
                    <div class="features-list">
                        <div class="feature-item mb-3">
                            <i class="fas fa-trophy me-2"></i>
                            <span>Track Your Achievements</span>
                        </div>
                        <div class="feature-item mb-3">
                            <i class="fas fa-chart-line me-2"></i>
                            <span>Monitor Your Progress</span>
                        </div>
                        <div class="feature-item mb-3">
                            <i class="fas fa-certificate me-2"></i>
                            <span>Upload Certificates</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right side - Login Forms -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center">
                <div class="login-container">
                    <div class="card shadow-lg border-0">
                        <div class="card-header bg-white border-0 text-center py-4">
                            <h3 class="fw-bold text-primary mb-0">Welcome Back</h3>
                            <p class="text-muted">Please sign in to your account</p>
                        </div>
                        <div class="card-body p-5">
                            <!-- User Type Selection -->
                            <div class="mb-4">
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="userType" id="student" value="student" checked>
                                    <label class="btn btn-outline-primary" for="student">
                                        <i class="fas fa-user-graduate me-2"></i>Student
                                    </label>
                                    
                                    <input type="radio" class="btn-check" name="userType" id="coordinator" value="coordinator">
                                    <label class="btn btn-outline-primary" for="coordinator">
                                        <i class="fas fa-user-tie me-2"></i>Coordinator
                                    </label>
                                    
                                    <input type="radio" class="btn-check" name="userType" id="hod" value="hod">
                                    <label class="btn btn-outline-primary" for="hod">
                                        <i class="fas fa-user-cog me-2"></i>HoD
                                    </label>
                                    
                                    <input type="radio" class="btn-check" name="userType" id="admin" value="admin">
                                    <label class="btn btn-outline-primary" for="admin">
                                        <i class="fas fa-user-shield me-2"></i>Admin
                                    </label>
                                </div>
                            </div>

                            <form id="loginForm" action="auth/login.php" method="POST">
                                <div class="mb-3">
                                    <label for="username" class="form-label" id="usernameLabel">PRN Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-user"></i>
                                        </span>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-lock"></i>
                                        </span>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <input type="hidden" name="user_type" id="userTypeInput" value="student">

                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">
                                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/login.js"></script>
</body>
</html>