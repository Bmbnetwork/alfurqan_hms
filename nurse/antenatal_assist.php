<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkNurse();

$msg = ""; $msgType = "";
$nurse_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_anc'])) {
    $patient_id = $_POST['patient_id'];
    $gestation = $_POST['gestation_age_weeks'];
    $bp = $_POST['blood_pressure'];
    $weight = $_POST['weight_kg'];
    $fundal_height = $_POST['fundal_height'];
    $fetal_hr = $_POST['fetal_heart_rate'];
    $notes = $_POST['notes'];
    
    $stmt = $conn->prepare("INSERT INTO antenatal (patient_id, gestation_age_weeks, blood_pressure, weight_kg, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $patient_id, $gestation, $bp, $weight, $notes);
    
    if ($stmt->execute()) {
        $msg = "✅ ANC visit recorded successfully!"; $msgType = "success";
        logActivity($conn, $nurse_id, $_SESSION['username'], 'ANC_ASSIST', "Recorded ANC visit for patient ID: $patient_id (Gestation: ${gestation} weeks)");
    } else { $msg = "❌ Error: " . $conn->error; $msgType = "error"; }
}

// Only female patients
$female_patients = $conn->query("SELECT id, name, age FROM patients WHERE gender = 'Female' ORDER BY name ASC");

// Recent ANC records
$anc_records = $conn->query("SELECT a.*, p.name as patient_name, p.age 
                             FROM antenatal a 
                             JOIN patients p ON a.patient_id = p.id 
                             ORDER BY a.visit_date DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Antenatal Assist | Alfurqan Clinic</title>
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
        .info-box { background: #e8f5e9; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #2e7d32; }
        .info-box p { color: #1b5e20; margin: 0; font-size: 14px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #00796b; }
        button { padding: 12px 25px; background: #004d40; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        button:hover { background: #00695c; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e0f2f1; }
        th { background: #004d40; color: white; font-size: 13px; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; } .alert-error { background: #f8d7da; color: #721c24; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; }
        .bg-first { background: #2e7d32; } .bg-second { background: #1976d2; } .bg-third { background: #f57c00; }
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
            <a href="record_vitals.php">️ Record Vitals</a>
            <a href="patient_status.php">📈 Patient Status</a>
            <a href="my_patients.php">👥 My Patients</a>
            <a href="antenatal_assist.php" class="active">🤰 ANC Assist</a>
            <a href="view_patients.php"> All Patients</a>
            <a href="../logout.php" class="logout-btn">🚪 Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="header"><h1> Antenatal Care Assistance</h1></div>
        
        <?php if($msg != ""): ?><div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div><?php endif; ?>
        
        <div class="card">
            <h3>Record ANC Visit</h3>
            <div class="info-box">
                <p>ℹ️ <strong>Note:</strong> This form is for nurses to assist doctors in recording antenatal visits. All measurements will be saved to the patient's ANC record.</p>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label>Select Patient (Female) *</label>
                    <select name="patient_id" required>
                        <option value="">-- Choose Patient --</option>
                        <?php while($p = $female_patients->fetch_assoc()): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?> (Age: <?php echo $p['age']; ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Gestation Age (Weeks) *</label>
                        <input type="number" name="gestation_age_weeks" required min="1" max="42" placeholder="e.g., 24">
                    </div>
                    <div class="form-group">
                        <label>Blood Pressure *</label>
                        <input type="text" name="blood_pressure" required placeholder="e.g., 120/80">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Weight (kg) *</label>
                        <input type="number" step="0.1" name="weight_kg" required placeholder="e.g., 65.5">
                    </div>
                    <div class="form-group">
                        <label>Fundal Height (cm)</label>
                        <input type="number" step="0.1" name="fundal_height" placeholder="e.g., 26">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Fetal Heart Rate (bpm)</label>
                        <input type="number" name="fetal_heart_rate" placeholder="e.g., 140">
                    </div>
                    <div class="form-group">
                        <label>Next Visit Date</label>
                        <input type="date" name="next_visit">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Nursing Notes / Observations</label>
                    <textarea name="notes" rows="4" placeholder="e.g., Patient reports good fetal movement. No complaints. Advised to take iron supplements..."></textarea>
                </div>
                
                <button type="submit" name="save_anc">💾 Save ANC Record</button>
            </form>
        </div>
        
        <div class="card">
            <h3>Recent ANC Visits</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Age</th>
                        <th>Gestation</th>
                        <th>BP</th>
                        <th>Weight</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($anc_records->num_rows > 0): ?>
                        <?php while($a = $anc_records->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($a['visit_date'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($a['patient_name']); ?></strong></td>
                            <td><?php echo $a['age']; ?>y</td>
                            <td><?php echo $a['gestation_age_weeks']; ?> weeks</td>
                            <td><?php echo $a['blood_pressure']; ?></td>
                            <td><?php echo $a['weight_kg']; ?> kg</td>
                            <td><?php echo htmlspecialchars(substr($a['notes'] ?? '', 0, 40)); ?>...</td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #666; padding: 20px;">No ANC records yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>