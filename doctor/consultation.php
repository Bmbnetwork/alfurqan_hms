<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkDoctor();

$msg = ""; $msgType = "";
$doctor_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_consultation'])) {
    $patient_id = $_POST['patient_id'];
    $symptoms = $_POST['symptoms'];
    $diagnosis = $_POST['diagnosis'];
    $prescription = $_POST['prescription'];
    
    $stmt = $conn->prepare("INSERT INTO consultations (patient_id, doctor_id, symptoms, diagnosis, prescription) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $patient_id, $doctor_id, $symptoms, $diagnosis, $prescription);
    
    if ($stmt->execute()) {
        $msg = "✅ Consultation recorded successfully!"; $msgType = "success";
        logActivity($conn, $doctor_id, $_SESSION['username'], 'ADD_CONSULTATION', "Consultation for patient ID: $patient_id");
    } else { $msg = "❌ Error: " . $conn->error; $msgType = "error"; }
}

$patients = $conn->query("SELECT id, name FROM patients ORDER BY name ASC");
$consultations = $conn->query("SELECT c.*, p.name as patient_name FROM consultations c JOIN patients p ON c.patient_id = p.id WHERE c.doctor_id = $doctor_id ORDER BY c.visit_date DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Consultation | Alfurqan Clinic</title>
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
        <div class="header"><h1>🩺 New Consultation</h1></div>
        
        <?php if($msg != ""): ?><div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div><?php endif; ?>
        
        <div class="card">
            <h3>Record Patient Consultation</h3>
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
                    <label>Symptoms *</label>
                    <textarea name="symptoms" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label>Diagnosis *</label>
                    <textarea name="diagnosis" rows="2" required></textarea>
                </div>
                <div class="form-group">
                    <label>Prescription / Treatment Plan</label>
                    <textarea name="prescription" rows="3"></textarea>
                </div>
                <button type="submit" name="save_consultation">💾 Save Consultation</button>
            </form>
        </div>
        
        <div class="card">
            <h3>Recent Consultations</h3>
            <table>
                <thead><tr><th>Date</th><th>Patient</th><th>Diagnosis</th><th>Prescription</th></tr></thead>
                <tbody>
                    <?php while($c = $consultations->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($c['visit_date'])); ?></td>
                        <td><?php echo htmlspecialchars($c['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($c['diagnosis']); ?></td>
                        <td><?php echo htmlspecialchars(substr($c['prescription'], 0, 30)); ?>...</td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>