<?php
require_once '../config/database.php';

if (!isLoggedIn('student')) {
    redirect('../index.php');
}

$conn = getConnection();
$prn = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = sanitize($_POST['category']);
    $activity_id = sanitize($_POST['activity_type']);
    $level = sanitize($_POST['level']);
    $activity_date = sanitize($_POST['activity_date']);
    $remarks = sanitize($_POST['remarks']);
    
    // File uploads
    $certificate_path = '';
    $proof_path = '';
    
    $upload_dir = '../uploads/activities/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Handle certificate upload
    if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] === 0) {
        $certificate_name = time() . '_cert_' . basename($_FILES['certificate']['name']);
        $certificate_path = $upload_dir . $certificate_name;
        
        if (move_uploaded_file($_FILES['certificate']['tmp_name'], $certificate_path)) {
            $certificate_path = 'uploads/activities/' . $certificate_name;
        } else {
            $error = "Failed to upload certificate";
        }
    }
    
    // Handle proof upload
    if (isset($_FILES['proof']) && $_FILES['proof']['error'] === 0) {
        $proof_name = time() . '_proof_' . basename($_FILES['proof']['name']);
        $proof_path = $upload_dir . $proof_name;
        
        if (move_uploaded_file($_FILES['proof']['tmp_name'], $proof_path)) {
            $proof_path = 'uploads/activities/' . $proof_name;
        }
    }
    
    if (empty($error)) {
        // Get activity name
        $stmt = $conn->prepare("SELECT activity_name FROM activities_master WHERE id = ?");
        $stmt->bind_param("i", $activity_id);
        $stmt->execute();
        $activity_name = $stmt->get_result()->fetch_assoc()['activity_name'];
        
        // Insert activity submission
        $stmt = $conn->prepare("
            INSERT INTO activities (prn, category, activity_type, level, certificate, proof_document, date, remarks) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssssss", $prn, $category, $activity_name, $level, $certificate_path, $proof_path, $activity_date, $remarks);
        
        if ($stmt->execute()) {
            $message = "Activity submitted successfully! It will be reviewed by your coordinator.";
        } else {
            $error = "Failed to submit activity. Please try again.";
        }
    }
}

// Get categories and activities
$categories = $conn->query("SELECT * FROM categories ORDER BY id")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Activity - MAP Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">Submit New Activity</h1>
                <p class="text-muted mb-0">Upload your certificates and activity proof</p>
            </div>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Activity Details</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" id="activityForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Category *</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>">
                                            Category <?php echo $cat['id']; ?>: <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="activity_type" class="form-label">Activity Type *</label>
                                    <select class="form-select" id="activity_type" name="activity_type" required disabled>
                                        <option value="">Select Category First</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="level" class="form-label">Level</label>
                                    <select class="form-select" id="level" name="level">
                                        <option value="">Select Level (if applicable)</option>
                                        <option value="College">College</option>
                                        <option value="District">District</option>
                                        <option value="State">State</option>
                                        <option value="National">National</option>
                                        <option value="International">International</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="activity_date" class="form-label">Activity Date *</label>
                                    <input type="date" class="form-control" id="activity_date" name="activity_date" 
                                           max="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="certificate" class="form-label">Certificate/Document *</label>
                                <div class="file-upload-area" onclick="document.getElementById('certificate').click()">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <h6>Click to upload certificate</h6>
                                    <p class="text-muted">PDF, JPG, PNG files only (Max: 5MB)</p>
                                </div>
                                <input type="file" class="form-control d-none" id="certificate" name="certificate" 
                                       accept=".pdf,.jpg,.jpeg,.png" required>
                                <div id="certificatePreview" class="mt-2"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="proof" class="form-label">Additional Proof *</label>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Required:</strong> Please upload one of the following as proof:
                                    <ul class="mb-0 mt-2">
                                        <li>Geotag/Location photo during the event</li>
                                        <li>Group photo with participants</li>
                                        <li>Event banner/backdrop photo</li>
                                        <li>Other relevant proof document</li>
                                    </ul>
                                </div>
                                <div class="file-upload-area" onclick="document.getElementById('proof').click()">
                                    <i class="fas fa-camera"></i>
                                    <h6>Click to upload proof</h6>
                                    <p class="text-muted">JPG, PNG files only (Max: 5MB)</p>
                                </div>
                                <input type="file" class="form-control d-none" id="proof" name="proof" 
                                       accept=".jpg,.jpeg,.png" required>
                                <div id="proofPreview" class="mt-2"></div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="3" 
                                          placeholder="Any additional information about the activity..."></textarea>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Activity
                                </button>
                                <a href="dashboard.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Submission Guidelines</h5>
                    </div>
                    <div class="card-body">
                        <div class="guideline-item mb-3">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-certificate text-primary"></i>
                                </div>
                                <div class="ms-3">
                                    <h6>Valid Certificate</h6>
                                    <p class="text-muted small mb-0">Upload original certificates or official documents only.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="guideline-item mb-3">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-success"></i>
                                </div>
                                <div class="ms-3">
                                    <h6>Proof Required</h6>
                                    <p class="text-muted small mb-0">Additional proof like geotag, event photos, or group pictures is mandatory.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="guideline-item mb-3">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-clock text-warning"></i>
                                </div>
                                <div class="ms-3">
                                    <h6>Review Process</h6>
                                    <p class="text-muted small mb-0">Submissions will be reviewed by coordinators within 3-5 working days.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="guideline-item">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-file-alt text-info"></i>
                                </div>
                                <div class="ms-3">
                                    <h6>File Formats</h6>
                                    <p class="text-muted small mb-0">Certificates: PDF, JPG, PNG<br>Proof: JPG, PNG only</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Points Information</h5>
                    </div>
                    <div class="card-body" id="pointsInfo">
                        <p class="text-muted">Select an activity to see point allocation details.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/dashboard.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category');
            const activitySelect = document.getElementById('activity_type');
            const levelSelect = document.getElementById('level');
            const pointsInfo = document.getElementById('pointsInfo');
            
            // Handle category change
            categorySelect.addEventListener('change', function() {
                const categoryId = this.value;
                
                if (categoryId) {
                    // Fetch activities for selected category
                    fetch(`../api/get-activities.php?category=${categoryId}`)
                        .then(response => response.json())
                        .then(data => {
                            activitySelect.innerHTML = '<option value="">Select Activity Type</option>';
                            activitySelect.disabled = false;
                            
                            data.activities.forEach(activity => {
                                const option = document.createElement('option');
                                option.value = activity.id;
                                option.textContent = activity.activity_name;
                                option.dataset.pointsType = activity.points_type;
                                option.dataset.minPoints = activity.min_points;
                                option.dataset.maxPoints = activity.max_points;
                                activitySelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching activities:', error);
                            activitySelect.innerHTML = '<option value="">Error loading activities</option>';
                        });
                } else {
                    activitySelect.innerHTML = '<option value="">Select Category First</option>';
                    activitySelect.disabled = true;
                    pointsInfo.innerHTML = '<p class="text-muted">Select an activity to see point allocation details.</p>';
                }
            });
            
            // Handle activity type change
            activitySelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const pointsType = selectedOption.dataset.pointsType;
                const minPoints = selectedOption.dataset.minPoints;
                const maxPoints = selectedOption.dataset.maxPoints;
                
                if (pointsType === 'Level') {
                    levelSelect.disabled = false;
                    levelSelect.required = true;
                    
                    // Fetch level points
                    fetch(`../api/get-activity-levels.php?activity_id=${this.value}`)
                        .then(response => response.json())
                        .then(data => {
                            let pointsHtml = '<h6>Points by Level:</h6><ul class="list-unstyled">';
                            data.levels.forEach(level => {
                                pointsHtml += `<li><span class="badge bg-light text-dark me-2">${level.level}</span>${level.points} points</li>`;
                            });
                            pointsHtml += '</ul>';
                            pointsInfo.innerHTML = pointsHtml;
                        });
                } else if (pointsType === 'Fixed') {
                    levelSelect.disabled = true;
                    levelSelect.required = false;
                    levelSelect.value = '';
                    
                    pointsInfo.innerHTML = `
                        <h6>Fixed Points:</h6>
                        <p class="mb-0">
                            <span class="badge bg-primary">${minPoints || maxPoints} points</span>
                        </p>
                    `;
                }
            });
            
            // File upload handlers
            document.getElementById('certificate').addEventListener('change', function(e) {
                handleFilePreview(e.target, 'certificatePreview');
            });
            
            document.getElementById('proof').addEventListener('change', function(e) {
                handleFilePreview(e.target, 'proofPreview');
            });
            
            function handleFilePreview(input, previewId) {
                const preview = document.getElementById(previewId);
                const file = input.files[0];
                
                if (file) {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    
                    preview.innerHTML = `
                        <div class="alert alert-success">
                            <i class="fas fa-file me-2"></i>
                            <strong>${fileName}</strong> (${fileSize} MB)
                            <button type="button" class="btn-close float-end" onclick="clearFile('${input.id}', '${previewId}')"></button>
                        </div>
                    `;
                }
            }
            
            window.clearFile = function(inputId, previewId) {
                document.getElementById(inputId).value = '';
                document.getElementById(previewId).innerHTML = '';
            };
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>