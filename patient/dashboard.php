<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkPatient();

$patient_id = $_SESSION['patient_id'];

// Statistics
$total_apts = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE patient_id = $patient_id")->fetch_assoc()['total'] ?? 0;
$pending_apts = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE patient_id = $patient_id AND status = 'Pending'")->fetch_assoc()['total'] ?? 0;
$upcoming_apts = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE patient_id = $patient_id AND appointment_date >= CURDATE() AND status = 'Confirmed'")->fetch_assoc()['total'] ?? 0;

// Upcoming Appointments
$upcoming_list = $conn->query("SELECT a.*, u.username as doctor_name 
                               FROM appointments a 
                               LEFT JOIN users u ON a.doctor_id = u.id 
                               WHERE a.patient_id = $patient_id AND a.appointment_date >= CURDATE() AND a.status = 'Confirmed' 
                               ORDER BY a.appointment_date ASC, a.appointment_time ASC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Dashboard | Alfurqan Clinic</title>
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
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #4f46e5; }
        .stat-card h3 { color: #6b7280; font-size: 14px; margin-bottom: 10px; }
        .stat-card p { color: #312e81; font-size: 32px; font-weight: bold; }
        .stat-card.pending { border-left-color: #f59e0b; } .stat-card.pending p { color: #f59e0b; }
        .stat-card.upcoming { border-left-color: #10b981; } .stat-card.upcoming p { color: #10b981; }
        .section { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .section h3 { color: #312e81; margin-bottom: 20px; border-bottom: 2px solid #f3f4f6; padding-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #f3f4f6; }
        th { background: #f9fafb; color: #6b7280; font-size: 13px; text-transform: uppercase; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; font-weight: 600; }
        .bg-pending { background: #f59e0b; } .bg-confirmed { background: #10b981; } .bg-cancelled { background: #ef4444; }
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
        <div class="header">
            <h1>📊 Welcome Back!</h1>
            <span>Hello, Patient</span>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card"><h3>Total Appointments</h3><p><?php echo $total_apts; ?></p></div>
            <div class="stat-card pending"><h3>Pending Approval</h3><p><?php echo $pending_apts; ?></p></div>
            <div class="stat-card upcoming"><h3>Upcoming Visits</h3><p><?php echo $upcoming_apts; ?></p></div>
        </div>
        
        <div class="section">
            <h3>📅 Upcoming Appointments</h3>
            <?php if ($upcoming_list->num_rows > 0): ?>
            <table>
                <thead><tr><th>Date</th><th>Time</th><th>Doctor</th><th>Reason</th><th>Status</th></tr></thead>
                <tbody>
                    <?php while($apt = $upcoming_list->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($apt['appointment_date'])); ?></td>
                        <td><?php echo date('g:i A', strtotime($apt['appointment_time'])); ?></td>
                        <td><?php echo $apt['doctor_name'] ? 'Dr. ' . htmlspecialchars($apt['doctor_name']) : 'To be assigned'; ?></td>
                        <td><?php echo htmlspecialchars(substr($apt['reason'], 0, 40)); ?>...</td>
                        <td><span class="badge bg-<?php echo strtolower($apt['status']); ?>"><?php echo $apt['status']; ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align: center; color: #6b7280; padding: 20px;">No upcoming appointments. <a href="book_appointment.php" style="color: #4f46e5;">Book one now!</a></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>