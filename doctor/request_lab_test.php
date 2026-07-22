<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkDoctor();

$msg = ""; $msgType = "";
$doctor_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_lab'])) {
    $patient_id = $_POST['patient_id'];
    $test_id = $_POST['test_id'];
    $priority = $_POST['priority'];
    $notes = $_POST['clinical_notes'];
    
    $stmt = $conn->prepare("INSERT INTO lab_requests (patient_id, doctor_id, test_id, priority, clinical_notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $patient_id, $doctor_id, $test_id, $priority, $notes);
    
    if ($stmt->execute()) {
        $msg = "✅ Lab test requested successfully!"; $msgType = "success";
        logActivity($conn, $doctor_id, $_SESSION['username'], 'REQUEST_LAB_TEST', "Lab test requested for patient ID: $patient_id");
    } else { $msg = "❌ Error: " . $conn->error; $msgType = "error"; }
}

$patients = $conn->query("SELECT id, name FROM patients ORDER BY name ASC");
$lab_tests = $conn->query("SELECT * FROM lab_tests WHERE is_active = 1 ORDER BY category, test_name ASC");
$my_requests = $conn->query("SELECT lr.*, lt.test_name, p.name as patient_name FROM lab_requests lr JOIN lab_tests lt ON lr.test_id = lt.id JOIN patients p ON lr.patient_id = p.id WHERE lr.doctor_id = $doctor_id ORDER BY lr.request_date DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab Requests | Alfurqan Clinic</title>
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
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; }
        .card h3 { color: #1b5e20; margin-bottom: 20px; border-bottom: 2px solid #e8f5e9; padding-bottom: 15px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        button { padding: 12px 25px; background: #1b5e20; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e8f5e9; }
        th { background: #1b5e20; color: white; font-size: 13px; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; }
        .bg-pending { background: #f57c00; } .bg-completed { background: #2e7d32; } .bg-routine { background: #1976d2; } .bg-urgent { background: #f57c00; } .bg-emergency { background: #c62828; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; } .alert-error { background: #f8d7da; color: #721c24; }
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
        <div class="header"><h1>🔬 Request Laboratory Test</h1></div>
        
        <?php if($msg != ""): ?><div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div><?php endif; ?>
        
        <div class="card">
            <h3>New Lab Request</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Select Patient *</label>
                    <select name="patient_id" required>
                        <option value="">-- Choose Patient --</option>
                        <?php while($p = $patients->fetch_assoc()): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Select Test *</label>
                    <select name="test_id" required>
                        <option value="">-- Choose Test --</option>
                        <?php while($t = $lab_tests->fetch_assoc()): ?>
                        <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['test_name']); ?> (<?php echo htmlspecialchars($t['category']); ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Priority *</label>
                    <select name="priority" required>
                        <option value="Routine">Routine</option>
                        <option value="Urgent">Urgent</option>
                        <option value="Emergency">Emergency</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Clinical Notes</label>
                    <textarea name="clinical_notes" rows="3"></textarea>
                </div>
                <button type="submit" name="request_lab">🔬 Submit Request</button>
            </form>
        </div>
        
        <div class="card">
            <h3>My Lab Requests</h3>
            <table>
                <thead><tr><th>Date</th><th>Patient</th><th>Test</th><th>Priority</th><th>Status</th></tr></thead>
                <tbody>
                    <?php while($lr = $my_requests->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($lr['request_date'])); ?></td>
                        <td><?php echo htmlspecialchars($lr['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($lr['test_name']); ?></td>
                        <td><span class="badge bg-<?php echo strtolower($lr['priority']); ?>"><?php echo $lr['priority']; ?></span></td>
                        <td><span class="badge bg-<?php echo strtolower($lr['status']); ?>"><?php echo $lr['status']; ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>