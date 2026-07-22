<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkDoctor();

$doctor_id = $_SESSION['user_id'];

// Statistics
$total_patients = $conn->query("SELECT COUNT(DISTINCT patient_id) as total FROM consultations WHERE doctor_id = $doctor_id")->fetch_assoc()['total'] ?? 0;
$total_consultations = $conn->query("SELECT COUNT(*) as total FROM consultations WHERE doctor_id = $doctor_id")->fetch_assoc()['total'] ?? 0;
$pending_lab = $conn->query("SELECT COUNT(*) as total FROM lab_requests WHERE doctor_id = $doctor_id AND status = 'Pending'")->fetch_assoc()['total'] ?? 0;
$total_prescriptions = $conn->query("SELECT COUNT(*) as total FROM prescriptions WHERE doctor_id = $doctor_id")->fetch_assoc()['total'] ?? 0;

// Recent Consultations
$recent_consultations = $conn->query("SELECT c.*, p.name as patient_name FROM consultations c JOIN patients p ON c.patient_id = p.id WHERE c.doctor_id = $doctor_id ORDER BY c.visit_date DESC LIMIT 5");

// Today's Appointments
$today_apts = $conn->query("SELECT a.*, p.name as patient_name, p.phone FROM appointments a JOIN patients p ON a.patient_id = p.id WHERE a.doctor_id = $doctor_id AND DATE(a.appointment_date) = CURDATE() ORDER BY a.appointment_time ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Dashboard | Alfurqan Clinic</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #e8f5e9; display: flex; }
        .sidebar { width: 260px; background: #1b5e20; color: white; height: 100vh; position: fixed; }
        .sidebar-header { text-align: center; padding: 25px 20px; border-bottom: 1px solid #2e7d32; background: #144a18; }
        .sidebar-header img { width: 70px; height: 70px; margin-bottom: 10px; background: white; border-radius: 50%; padding: 5px; }
        .sidebar-header h2 { font-size: 18px; } .sidebar-header small { font-size: 11px; opacity: 0.8; }
        .sidebar-menu a { display: flex; align-items: center; gap: 15px; padding: 15px 25px; color: #c8e6c9; text-decoration: none; border-bottom: 1px solid #2e7d32; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #2e7d32; color: white; padding-left: 30px; }
        .logout-btn { background: #c62828 !important; justify-content: center; margin-top: 30px; }
        .main-content { margin-left: 260px; padding: 30px; width: calc(100% - 260px); }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #c8e6c9; }
        .header h1 { color: #1b5e20; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-left: 5px solid #2e7d32; }
        .stat-card h3 { color: #666; font-size: 14px; margin-bottom: 10px; }
        .stat-card p { color: #1b5e20; font-size: 32px; font-weight: bold; }
        .section { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; }
        .section h3 { color: #1b5e20; margin-bottom: 20px; border-bottom: 2px solid #e8f5e9; padding-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e8f5e9; }
        th { background: #1b5e20; color: white; font-size: 13px; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; }
        .bg-confirmed { background: #2e7d32; } .bg-pending { background: #f57c00; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/logo.png" alt="Logo">
            <h2>DOCTOR PORTAL</h2><small>Alfurqan Clinic</small>
        </div>
        <div class="sidebar-menu">
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="consultation.php">🩺 Consultation</a>
    <a href="antenatal.php">🤰 Antenatal Care</a>
    <a href="request_lab_test.php">🔬 Lab Requests</a>
    <a href="view_lab_results.php">📈 View Lab Results</a> <!-- ADD THIS LINE -->
    <a href="prescribe_drug.php">💊 Prescriptions</a>
    <a href="../logout.php" class="logout-btn">🚪 Logout</a>
</div>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1> Dashboard Overview</h1>
            <span>Welcome, Dr. <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card"><h3>Total Patients</h3><p><?php echo $total_patients; ?></p></div>
            <div class="stat-card"><h3>Consultations</h3><p><?php echo $total_consultations; ?></p></div>
            <div class="stat-card"><h3>Pending Lab Results</h3><p><?php echo $pending_lab; ?></p></div>
            <div class="stat-card"><h3>Prescriptions</h3><p><?php echo $total_prescriptions; ?></p></div>
        </div>
        
        <div class="section">
            <h3>📅 Today's Appointments</h3>
            <?php if ($today_apts->num_rows > 0): ?>
            <table>
                <thead><tr><th>Time</th><th>Patient</th><th>Phone</th><th>Reason</th><th>Status</th></tr></thead>
                <tbody>
                    <?php while($apt = $today_apts->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('g:i A', strtotime($apt['appointment_time'])); ?></td>
                        <td><?php echo htmlspecialchars($apt['patient_name']); ?></td>
                        <td><?php echo $apt['phone']; ?></td>
                        <td><?php echo htmlspecialchars(substr($apt['reason'], 0, 30)); ?>...</td>
                        <td><span class="badge bg-<?php echo strtolower($apt['status']); ?>"><?php echo $apt['status']; ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="color: #666; text-align: center; padding: 20px;">No appointments scheduled for today.</p>
            <?php endif; ?>
        </div>

        <div class="section">
            <h3>🩺 Recent Consultations</h3>
            <?php if ($recent_consultations->num_rows > 0): ?>
            <table>
                <thead><tr><th>Date</th><th>Patient</th><th>Diagnosis</th></tr></thead>
                <tbody>
                    <?php while($c = $recent_consultations->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($c['visit_date'])); ?></td>
                        <td><?php echo htmlspecialchars($c['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($c['diagnosis']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="color: #666; text-align: center; padding: 20px;">No recent consultations.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>