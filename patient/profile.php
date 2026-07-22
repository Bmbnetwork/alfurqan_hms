<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkPatient();

$patient_id = $_SESSION['patient_id'];

$stmt = $conn->prepare("SELECT p.*, pu.email FROM patients p JOIN patient_users pu ON p.id = pu.patient_id WHERE p.id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile | Alfurqan Clinic</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f3f4f6; display: flex; }
        .sidebar { width: 260px; background: #312e81; color: white; height: 100vh; position: fixed; }
        .sidebar-header { text-align: center; padding: 25px 20px; border-bottom: 1px solid #4338ca; background: #1e1b4b; }
        .sidebar-header img { width: 70px; height: 70px; margin-bottom: 10px; background: white; border-radius: 50%; padding: 5px; }
        .sidebar-header h2 { font-size: 18px; } .sidebar-header small { font-size: 11px; opacity: 0.8; }
        .sidebar-menu a { display: flex; align-items: center; gap: 15px; padding: 15px 25px; color: #c7d2fe; text-decoration: none; border-bottom: 1px solid #4338ca; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #4338ca; color: white; padding-left: 30px; }
        .logout-btn { background: #dc2626 !important; justify-content: center; margin-top: 30px; }
        .main-content { margin-left: 260px; padding: 30px; width: calc(100% - 260px); }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e5e7eb; }
        .header h1 { color: #312e81; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 800px; }
        .card h3 { color: #312e81; margin-bottom: 20px; border-bottom: 2px solid #f3f4f6; padding-bottom: 15px; }
        .profile-header { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #f3f4f6; }
        .avatar { width: 80px; height: 80px; background: #4f46e5; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: bold; }
        .profile-info h2 { color: #1f2937; margin-bottom: 5px; }
        .profile-info p { color: #6b7280; font-size: 14px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .info-item { background: #f9fafb; padding: 15px; border-radius: 8px; border-left: 4px solid #4f46e5; }
        .info-item label { display: block; font-size: 12px; color: #6b7280; text-transform: uppercase; margin-bottom: 5px; }
        .info-item .value { font-size: 16px; color: #1f2937; font-weight: 600; }
        .alert-box { background: #eff6ff; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #3b82f6; }
        .alert-box p { color: #1e40af; font-size: 14px; margin: 0; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/logo.png" alt="Logo">
            <h2>PATIENT PORTAL</h2><small>Alfurqan Clinic</small>
        </div>
        <div class="sidebar-menu">
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="symptoms_dashboard.php" class="active">🏥 Health Assessment</a>
    <a href="health_assessment.php"> New Assessment</a>
    <a href="book_appointment.php">📅 Book Appointment</a>
    <a href="my_appointments.php">📋 My Appointments</a>
    <a href="medical_history.php">🏥 Medical History</a>
    <a href="profile.php">👤 My Profile</a>
    <a href="logout.php" class="logout-btn"> Logout</a>
</div>
    </div>
    
    <div class="main-content">
        <div class="header"><h1>👤 My Profile</h1></div>
        
        <div class="card">
            <div class="alert-box">
                <p>ℹ️ <strong>Note:</strong> Your profile information is managed by our admin team. To update your details, please contact the clinic front desk.</p>
            </div>
            
            <div class="profile-header">
                <div class="avatar"><?php echo strtoupper(substr($patient['name'], 0, 1)); ?></div>
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($patient['name']); ?></h2>
                    <p>Patient ID: #<?php echo $patient['id']; ?> | Registered: <?php echo date('d M Y', strtotime($patient['reg_date'])); ?></p>
                </div>
            </div>
            
            <div class="info-grid">
                <div class="info-item"><label>Email Address</label><div class="value"><?php echo htmlspecialchars($patient['email']); ?></div></div>
                <div class="info-item"><label>Phone Number</label><div class="value"><?php echo htmlspecialchars($patient['phone']); ?></div></div>
                <div class="info-item"><label>Age</label><div class="value"><?php echo $patient['age']; ?> years</div></div>
                <div class="info-item"><label>Gender</label><div class="value"><?php echo $patient['gender']; ?></div></div>
                <div class="info-item" style="grid-column: 1 / -1;"><label>Address</label><div class="value"><?php echo htmlspecialchars($patient['address'] ?? 'Not provided'); ?></div></div>
                <div class="info-item"><label>Patient Status</label><div class="value"><?php echo $patient['patient_status']; ?></div></div>
            </div>
        </div>
    </div>
</body>
</html>