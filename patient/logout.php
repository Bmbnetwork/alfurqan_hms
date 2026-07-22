<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../config/db.php';
include '../config/functions.php';

// Log activity if possible
if (isset($_SESSION['patient_id'])) {
    logActivity($conn, $_SESSION['patient_user_id'] ?? null, $_SESSION['patient_email'] ?? 'Unknown', 'LOGOUT', 'Patient logged out successfully', 'patient');
}

// Destroy session
$_SESSION = array();
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}
session_destroy();

// Redirect to main login
header("Location: ../index.php?logout=success");
exit();
?>