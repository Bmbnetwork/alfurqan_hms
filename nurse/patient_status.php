<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkNurse();

$msg = ""; $msgType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $patient_id = $_POST['patient_id'];
    $status = $_POST['patient_status'];
    
    $stmt = $conn->prepare("UPDATE patients SET patient_status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $patient_id);
    
    if ($stmt->execute()) {
        $msg = "✅ Patient status updated successfully!"; $msgType = "success";
        logActivity($conn, $_SESSION['user_id'], $_SESSION['username'], 'UPDATE_PATIENT_STATUS', "Updated status for patient ID: $patient_id to $status");
    } else { $msg = "❌ Error: " . $conn->error; $msgType = "error"; }
}

$patients = $conn->query("SELECT * FROM patients ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Status | Alfurqan Clinic</title>
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
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; }
        .card h3 { color: #004d40; margin-bottom: 20px; border-bottom: 2px solid #e0f2f1; padding-bottom: 15px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr auto; gap: 15px; align-items: end; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; } label { display: block; margin-bottom: 5px; font-weight: 600; }
        select, input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        button { padding: 10px 20px; background: #004d40; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e0f2f1; }
        th { background: #004d40; color: white; font-size: 13px; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; }
        .bg-stable { background: #2e7d32; } .bg-critical { background: #c62828; }
        .bg-recovering { background: #1976d2; } .bg-pending { background: #f57c00; }
        .bg-discharged { background: #616161; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; } .alert-error { background: #f8d7da; color: #721c24; }
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
</div>
    </div>
    
    <div class="main-content">
        <div class="header"><h1>📈 Update Patient Status</h1></div>
        
        <?php if($msg != ""): ?><div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div><?php endif; ?>
        
        <div class="card">
            <h3>Change Patient Status</h3>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Select Patient</label>
                        <select name="patient_id" required>
                            <option value="">-- Choose Patient --</option>
                            <?php 
                            $patients->data_seek(0);
                            while($p = $patients->fetch_assoc()): ?>
                            <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?> (Current: <?php echo $p['patient_status']; ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>New Status</label>
                        <select name="patient_status" required>
                            <option value="Pending">Pending</option>
                            <option value="Stable">Stable</option>
                            <option value="Recovering">Recovering</option>
                            <option value="Critical">Critical</option>
                            <option value="Discharged">Discharged</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="update_status">Update Status</button>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h3>Current Patient Statuses</h3>
            <table>
                <thead><tr><th>ID</th><th>Name</th><th>Age</th><th>Gender</th><th>Phone</th><th>Current Status</th></tr></thead>
                <tbody>
                    <?php 
                    $patients->data_seek(0);
                    while($p = $patients->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $p['id']; ?></td>
                        <td><?php echo htmlspecialchars($p['name']); ?></td>
                        <td><?php echo $p['age']; ?></td>
                        <td><?php echo $p['gender']; ?></td>
                        <td><?php echo $p['phone']; ?></td>
                        <td><span class="badge bg-<?php echo strtolower($p['patient_status']); ?>"><?php echo $p['patient_status']; ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>