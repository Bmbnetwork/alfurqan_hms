<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']) && isset($_SESSION['role']);
}

function isPatientLoggedIn() {
    return isset($_SESSION['patient_id']) && isset($_SESSION['patient_email']);
}

function checkAdmin() {
    if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
        header("Location: ../index.php");
        exit();
    }
}

function checkDoctor() {
    if (!isLoggedIn() || $_SESSION['role'] !== 'doctor') {
        header("Location: ../index.php");
        exit();
    }
}

function checkNurse() {
    if (!isLoggedIn() || $_SESSION['role'] !== 'nurse') {
        header("Location: ../index.php");
        exit();
    }
}

function checkPharmacist() {
    if (!isLoggedIn() || $_SESSION['role'] !== 'pharmacist') {
        header("Location: ../index.php");
        exit();
    }
}

function checkLabTechnician() {
    if (!isLoggedIn() || $_SESSION['role'] !== 'lab_technician') {
        header("Location: ../index.php");
        exit();
    }
}

function checkPatient() {
    if (!isPatientLoggedIn()) {
        header("Location: ../index.php");
        exit();
    }
}

function logActivity($conn, $user_id, $username, $action, $description, $user_type = 'staff') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    
    $table_check = $conn->query("SHOW TABLES LIKE 'activity_logs'");
    if ($table_check->num_rows == 0) {
        return false;
    }
    
    $log_user_id = ($user_type === 'patient') ? NULL : $user_id;
    
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, username, action, description, ip_address) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("issss", $log_user_id, $username, $action, $description, $ip);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    return false;
}

function getRoleDisplayName($role) {
    $roles = [
        'admin' => 'Administrator',
        'doctor' => 'Doctor',
        'nurse' => 'Nurse',
        'pharmacist' => 'Pharmacist',
        'lab_technician' => 'Lab Technician'
    ];
    return $roles[$role] ?? ucfirst($role);
}
?>