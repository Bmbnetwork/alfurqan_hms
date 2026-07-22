<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkNurse();

$patients = $conn->query("SELECT * FROM patients ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Patients | Alfurqan Clinic</title>
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
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .card h3 { color: #004d40; margin-bottom: 20px; border-bottom: 2px solid #e0f2f1; padding-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e0f2f1; }
        th { background: #004d40; color: white; font-size: 13px; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; }
        .bg-stable { background: #2e7d32; } .bg-critical { background: #c62828; }
        .bg-recovering { background: #1976d2; } .bg-pending { background: #f57c00; }
        .bg-discharged { background: #616161; }
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
    <a href="my_patients.php">👥 My Patients</a>
    <a href="antenatal_assist.php">🤰 ANC Assist</a>
    <a href="view_patients.php">📋 All Patients</a>
    <a href="../logout.php" class="logout-btn">🚪 Logout</a>
</div>    </div>
    
    <div class="main-content">
        <div class="header"><h1>👥 All Registered Patients</h1></div>
        
        <div class="card">
            <h3>Patient Directory</h3>
            <table>
                <thead><tr><th>ID</th><th>Name</th><th>Age</th><th>Gender</th><th>Phone</th><th>Address</th><th>Status</th><th>Registered</th></tr></thead>
                <tbody>
                    <?php while($p = $patients->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $p['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                        <td><?php echo $p['age']; ?></td>
                        <td><?php echo $p['gender']; ?></td>
                        <td><?php echo $p['phone']; ?></td>
                        <td><?php echo htmlspecialchars(substr($p['address'], 0, 30)); ?>...</td>
                        <td><span class="badge bg-<?php echo strtolower($p['patient_status']); ?>"><?php echo $p['patient_status']; ?></span></td>
                        <td><?php echo date('d M Y', strtotime($p['reg_date'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>