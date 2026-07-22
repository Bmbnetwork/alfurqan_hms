<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkNurse();

$nurse_id = $_SESSION['user_id'];

// Get all patients with their latest vitals
$patients_query = "SELECT p.*, 
                   (SELECT v.temperature FROM vitals v WHERE v.patient_id = p.id ORDER BY v.recorded_at DESC LIMIT 1) as latest_temp,
                   (SELECT v.blood_pressure FROM vitals v WHERE v.patient_id = p.id ORDER BY v.recorded_at DESC LIMIT 1) as latest_bp,
                   (SELECT v.pulse_rate FROM vitals v WHERE v.patient_id = p.id ORDER BY v.recorded_at DESC LIMIT 1) as latest_pulse,
                   (SELECT v.recorded_at FROM vitals v WHERE v.patient_id = p.id ORDER BY v.recorded_at DESC LIMIT 1) as last_vitals_time
                   FROM patients p 
                   ORDER BY p.id DESC";

$patients = $conn->query($patients_query);

// Statistics
$total_patients = $conn->query("SELECT COUNT(*) as total FROM patients")->fetch_assoc()['total'] ?? 0;
$critical_count = $conn->query("SELECT COUNT(*) as total FROM patients WHERE patient_status = 'Critical'")->fetch_assoc()['total'] ?? 0;
$stable_count = $conn->query("SELECT COUNT(*) as total FROM patients WHERE patient_status = 'Stable'")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Patients | Alfurqan Clinic</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #e0f2f1; display: flex; }
        .sidebar { width: 260px; background: #004d40; color: white; height: 100vh; position: fixed; }
        .sidebar-header { text-align: center; padding: 25px 20px; border-bottom: 1px solid #00695c; background: #00332a; }
        .sidebar-header img { width: 70px; height: 70px; margin-bottom: 10px; background: white; border-radius: 50%; padding: 5px; }
        .sidebar-header h2 { font-size: 18px; } .sidebar-header small { font-size: 11px; opacity: 0.8; }
        .sidebar-menu a { display: flex; align-items: center; gap: 15px; padding: 15px 25px; color: #b2dfdb; text-decoration: none; border-bottom: 1px solid #00695c; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #00695c; color: white; padding-left: 30px; }
        .logout-btn { background: #c62828 !important; justify-content: center; margin-top: 30px; }
        .main-content { margin-left: 260px; padding: 30px; width: calc(100% - 260px); }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #b2dfdb; }
        .header h1 { color: #004d40; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-left: 5px solid #00796b; }
        .stat-card h3 { color: #666; font-size: 14px; margin-bottom: 10px; }
        .stat-card p { color: #004d40; font-size: 32px; font-weight: bold; }
        .stat-card.critical { border-left-color: #c62828; } .stat-card.critical p { color: #c62828; }
        .stat-card.stable { border-left-color: #2e7d32; } .stat-card.stable p { color: #2e7d32; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; }
        .card h3 { color: #004d40; margin-bottom: 20px; border-bottom: 2px solid #e0f2f1; padding-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e0f2f1; }
        th { background: #004d40; color: white; font-size: 13px; }
        tr:hover { background: #f5f5f5; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; font-weight: 600; }
        .bg-stable { background: #2e7d32; } .bg-critical { background: #c62828; }
        .bg-recovering { background: #1976d2; } .bg-pending { background: #f57c00; }
        .bg-discharged { background: #616161; }
        .vitals-info { font-size: 12px; color: #666; }
        .vitals-info strong { color: #004d40; }
        .btn-sm { padding: 6px 12px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; color: white; text-decoration: none; display: inline-block; }
        .btn-primary { background: #00796b; } .btn-primary:hover { background: #004d40; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/logo.png" alt="Logo">
            <h2>NURSE PORTAL</h2><small>Alfurqan Clinic</small>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="record_vitals.php">🌡️ Record Vitals</a>
            <a href="patient_status.php">📈 Patient Status</a>
            <a href="my_patients.php" class="active">👥 My Patients</a>
            <a href="antenatal_assist.php"> ANC Assist</a>
            <a href="view_patients.php"> All Patients</a>
            <a href="../logout.php" class="logout-btn"> Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>👥 My Patients</h1>
            <span>Nurse: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card"><h3>Total Patients</h3><p><?php echo $total_patients; ?></p></div>
            <div class="stat-card stable"><h3>Stable</h3><p><?php echo $stable_count; ?></p></div>
            <div class="stat-card critical"><h3>Critical</h3><p><?php echo $critical_count; ?></p></div>
        </div>
        
        <div class="card">
            <h3>Patient List with Latest Vitals</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient Name</th>
                        <th>Age/Gender</th>
                        <th>Phone</th>
                        <th>Latest Vitals</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($p = $patients->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $p['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                        <td><?php echo $p['age']; ?>y / <?php echo $p['gender']; ?></td>
                        <td><?php echo $p['phone']; ?></td>
                        <td>
                            <?php if($p['latest_temp']): ?>
                                <div class="vitals-info">
                                    <strong>Temp:</strong> <?php echo $p['latest_temp']; ?>°C<br>
                                    <strong>BP:</strong> <?php echo $p['latest_bp']; ?><br>
                                    <strong>Pulse:</strong> <?php echo $p['latest_pulse']; ?> bpm<br>
                                    <small>Last: <?php echo date('d M Y, g:i A', strtotime($p['last_vitals_time'])); ?></small>
                                </div>
                            <?php else: ?>
                                <span style="color: #999; font-size: 12px;">No vitals recorded</span>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-<?php echo strtolower($p['patient_status']); ?>"><?php echo $p['patient_status']; ?></span></td>
                        <td>
                            <a href="record_vitals.php?patient_id=<?php echo $p['id']; ?>" class="btn-sm btn-primary">🌡️ Add Vitals</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>