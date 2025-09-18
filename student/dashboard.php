<?php
require_once '../config/database.php';

if (!isLoggedIn('student')) {
    redirect('../index.php');
}

$conn = getConnection();
$prn = $_SESSION['user_id'];

// Get student data and programme rules
$stmt = $conn->prepare("
    SELECT s.*, pr.* 
    FROM students s 
    LEFT JOIN programme_rules pr ON s.programme = pr.programme AND s.admission_year = pr.admission_year 
    WHERE s.prn = ?
");
$stmt->bind_param("s", $prn);
$stmt->execute();
$student_data = $stmt->get_result()->fetch_assoc();

// Get activity stats
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_submissions,
        SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved_count,
        SUM(CASE WHEN status = 'Approved' THEN points ELSE 0 END) as total_points,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_count
    FROM activities 
    WHERE prn = ?
");
$stmt->bind_param("s", $prn);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// Get category-wise points
$stmt = $conn->prepare("
    SELECT 
        category,
        SUM(CASE WHEN status = 'Approved' THEN points ELSE 0 END) as earned_points
    FROM activities 
    WHERE prn = ? 
    GROUP BY category
");
$stmt->bind_param("s", $prn);
$stmt->execute();
$category_points = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$category_data = [
    'A' => ['name' => 'Technical Skills', 'required' => $student_data['technical'] ?? 0, 'earned' => 0],
    'B' => ['name' => 'Sports & Cultural', 'required' => $student_data['sports_cultural'] ?? 0, 'earned' => 0],
    'C' => ['name' => 'Community Outreach', 'required' => $student_data['community_outreach'] ?? 0, 'earned' => 0],
    'D' => ['name' => 'Innovation', 'required' => $student_data['innovation'] ?? 0, 'earned' => 0],
    'E' => ['name' => 'Leadership', 'required' => $student_data['leadership'] ?? 0, 'earned' => 0]
];

foreach ($category_points as $cat) {
    if (isset($category_data[$cat['category']])) {
        $category_data[$cat['category']]['earned'] = $cat['earned_points'];
    }
}

$total_required = $student_data['total_points'] ?? 0;
$total_earned = $stats['total_points'] ?? 0;
$progress_percentage = $total_required > 0 ? round(($total_earned / $total_required) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - MAP Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
                <p class="text-muted mb-0">Track your MAP progress and submit activities</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary mobile-menu-toggle d-md-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon primary">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="ms-3">
                            <div class="stats-number"><?php echo $total_earned; ?></div>
                            <div class="stats-label">Total Points Earned</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="ms-3">
                            <div class="stats-number"><?php echo $stats['approved_count']; ?></div>
                            <div class="stats-label">Approved Activities</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon warning">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="ms-3">
                            <div class="stats-number"><?php echo $stats['pending_count']; ?></div>
                            <div class="stats-label">Pending Review</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon danger">
                            <i class="fas fa-target"></i>
                        </div>
                        <div class="ms-3">
                            <div class="stats-number"><?php echo $total_required; ?></div>
                            <div class="stats-label">Required Points</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Overview -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Overall Progress</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-semibold">MAP Completion</span>
                            <span class="text-muted"><?php echo $total_earned; ?> / <?php echo $total_required; ?> points</span>
                        </div>
                        <div class="progress mb-3" style="height: 12px;">
                            <div class="progress-bar bg-primary" style="width: <?php echo $progress_percentage; ?>%"></div>
                        </div>
                        <div class="text-center">
                            <span class="h4 text-primary"><?php echo $progress_percentage; ?>%</span>
                            <span class="text-muted"> Complete</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="submit-activity.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Submit New Activity
                            </a>
                            <a href="my-submissions.php" class="btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>View Submissions
                            </a>
                            <a href="transcript.php" class="btn btn-outline-success">
                                <i class="fas fa-download me-2"></i>Download Transcript
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category-wise Breakdown -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Category-wise Progress</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($category_data as $cat_id => $cat_info): ?>
                    <div class="col-lg-6 mb-3">
                        <div class="p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="mb-0">Category <?php echo $cat_id; ?>: <?php echo $cat_info['name']; ?></h6>
                                <span class="badge bg-<?php echo $cat_info['earned'] >= $cat_info['required'] ? 'success' : 'warning'; ?>">
                                    <?php echo $cat_info['earned']; ?> / <?php echo $cat_info['required']; ?>
                                </span>
                            </div>
                            <?php 
                                $cat_progress = $cat_info['required'] > 0 ? round(($cat_info['earned'] / $cat_info['required']) * 100) : 0;
                                $cat_progress = min($cat_progress, 100); // Cap at 100%
                            ?>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-<?php echo $cat_info['earned'] >= $cat_info['required'] ? 'success' : 'primary'; ?>" 
                                     style="width: <?php echo $cat_progress; ?>%"></div>
                            </div>
                            <small class="text-muted"><?php echo $cat_progress; ?>% complete</small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Submissions</h5>
                <a href="my-submissions.php" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <?php
                $stmt = $conn->prepare("
                    SELECT * FROM activities 
                    WHERE prn = ? 
                    ORDER BY created_at DESC 
                    LIMIT 5
                ");
                $stmt->bind_param("s", $prn);
                $stmt->execute();
                $recent_activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                ?>
                
                <?php if (empty($recent_activities)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h6 class="text-muted">No activities submitted yet</h6>
                    <p class="text-muted">Start by submitting your first activity!</p>
                    <a href="submit-activity.php" class="btn btn-primary">Submit Activity</a>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Activity</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_activities as $activity): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($activity['activity_type']); ?></td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        Category <?php echo $activity['category']; ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($activity['date'])); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $activity['status'] === 'Approved' ? 'success' : 
                                            ($activity['status'] === 'Rejected' ? 'danger' : 'warning'); 
                                    ?>">
                                        <?php echo $activity['status']; ?>
                                    </span>
                                </td>
                                <td><?php echo $activity['points']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>

<?php $conn->close(); ?>