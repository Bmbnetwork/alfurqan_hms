<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'config/db.php';
include 'config/functions.php';

$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? 'Unknown';
$user_role = $_SESSION['role'] ?? 'unknown';
$patient_id = $_SESSION['patient_id'] ?? null;
$patient_email = $_SESSION['patient_email'] ?? 'Unknown';

if ($patient_id) {
    $log_username = $patient_email;
    $log_type = 'PATIENT';
} else {
    $log_username = $username;
    $log_type = strtoupper($user_role);
}

if (isset($conn)) {
    logActivity($conn, $user_id, $log_username, 'LOGOUT', "$log_type user logged out successfully", $patient_id ? 'patient' : 'staff');
}

$_SESSION = array();

if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

session_destroy();

header("Location: index.php?logout=success");
exit();
?>