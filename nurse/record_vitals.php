<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkNurse();

$msg = ""; $msgType = "";
$nurse_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['record_vitals'])) {
    $patient_id = $_POST['patient_id'];
    $temp = $_POST['temperature'];
    $bp = $_POST['blood_pressure'];
    $pulse = $_POST['pulse_rate'];
    $resp = $_POST['respiratory_rate'];
    $weight = $_POST['weight_kg'];
    $spo2 = $_POST['oxygen_saturation'];
    $notes = $_POST['notes'];
    
    $stmt = $conn->prepare("INSERT INTO vitals (patient_id, nurse_id, temperature, blood_pressure, pulse_rate, respiratory_rate, weight_kg, oxygen_saturation, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iidddiids", $patient_id, $nurse_id, $temp, $bp, $pulse, $resp, $weight, $spo2, $notes);
    
    if ($stmt->execute()) {
        $msg = "✅ Vitals recorded successfully!"; $msgType = "success";
        logActivity($conn, $nurse_id, $_SESSION['username'], 'RECORD_VITALS', "Recorded vitals for patient ID: $patient_id");
    } else { $msg = "❌ Error: " . $conn->error; $msgType = "error"; }
}

$patients = $conn->query("SELECT id, name FROM patients ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Record Vitals | Alfurqan Clinic</title>
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
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); max-width: 800px; }
        .card h3 { color: #004d40; margin-bottom: 20px; border-bottom: 2px solid #e0f2f1; padding-bottom: 15px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        button { padding: 12px 25px; background: #004d40; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
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
        <div class="header"><h1>🌡️ Record Patient Vitals</h1></div>
        
        <?php if($msg != ""): ?><div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div><?php endif; ?>
        
        <div class="card">
            <h3>Enter Vital Signs</h3>
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
                <div class="form-row">
                    <div class="form-group"><label>Temperature (°C) *</label><input type="number" step="0.1" name="temperature" required></div>
                    <div class="form-group"><label>Blood Pressure (e.g., 120/80) *</label><input type="text" name="blood_pressure" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Pulse Rate (bpm) *</label><input type="number" name="pulse_rate" required></div>
                    <div class="form-group"><label>Respiratory Rate (/min)</label><input type="number" name="respiratory_rate"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Weight (kg)</label><input type="number" step="0.1" name="weight_kg"></div>
                    <div class="form-group"><label>Oxygen Saturation (SpO2 %)</label><input type="number" name="oxygen_saturation" min="0" max="100"></div>
                </div>
                <div class="form-group">
                    <label>Nursing Notes / Observations</label>
                    <textarea name="notes" rows="3"></textarea>
                </div>
                <button type="submit" name="record_vitals">💾 Save Vitals</button>
            </form>
        </div>
    </div>
</body>
</html>