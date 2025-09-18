<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'map_management');

// Create connection
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Initialize session
session_start();

// Helper functions
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn($user_type = null) {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
        return false;
    }
    if ($user_type && $_SESSION['user_type'] !== $user_type) {
        return false;
    }
    return true;
}
?>