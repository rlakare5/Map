<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../index.php');
}

$username = sanitize($_POST['username']);
$password = $_POST['password'];
$user_type = sanitize($_POST['user_type']);

if (empty($username) || empty($password) || empty($user_type)) {
    $_SESSION['error'] = "Please fill in all fields";
    redirect('../index.php');
}

$conn = getConnection();
$user = null;
$table = '';
$id_field = '';

// Determine table and ID field based on user type
switch ($user_type) {
    case 'student':
        $table = 'students';
        $id_field = 'prn';
        break;
    case 'coordinator':
        $table = 'coordinators';
        $id_field = 'coordinator_id';
        break;
    case 'hod':
        $table = 'hods';
        $id_field = 'hod_id';
        break;
    case 'admin':
        $table = 'admins';
        $id_field = 'admin_id';
        break;
    default:
        $_SESSION['error'] = "Invalid user type";
        redirect('../index.php');
}

// Fetch user from database
$stmt = $conn->prepare("SELECT * FROM $table WHERE $id_field = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    
    // Verify password
    if (password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user[$id_field];
        $_SESSION['user_type'] = $user_type;
        $_SESSION['user_name'] = ($user_type === 'student') 
            ? $user['first_name'] . ' ' . $user['last_name']
            : $user['name'];
        
        if ($user_type === 'student') {
            $_SESSION['student_data'] = $user;
        }
        
        // Redirect to appropriate dashboard
        switch ($user_type) {
            case 'student':
                redirect('../student/dashboard.php');
                break;
            case 'coordinator':
                redirect('../coordinator/dashboard.php');
                break;
            case 'hod':
                redirect('../hod/dashboard.php');
                break;
            case 'admin':
                redirect('../admin/dashboard.php');
                break;
        }
    } else {
        $_SESSION['error'] = "Invalid password";
    }
} else {
    $_SESSION['error'] = "User not found";
}

$conn->close();
redirect('../index.php');
?>