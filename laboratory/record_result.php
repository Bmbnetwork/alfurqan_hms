<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkLabTechnician();

$msg = ""; $msgType = "";
$tech_id = $_SESSION['user_id'];
$request_id = $_GET['id'] ?? 0;

// Fetch Request Details
$stmt = $conn->prepare("SELECT lr.*, lt.test_name, lt.sample_type, lt.reference_range, p.name as patient_name, p.age, p.gender, u.username as doctor_name 
                        FROM lab_requests lr 
                        JOIN lab_tests lt ON lr.test_id = lt.id 
                        JOIN patients p ON lr.patient_id = p.id 
                        JOIN users u ON lr.doctor_id = u.id 
                        WHERE lr.id = ?");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$request = $stmt->get_result()->fetch_assoc();

if (!$request) { header("Location: test_requests.php"); exit(); }

// Check existing result
$existing = $conn->query("SELECT * FROM lab_results WHERE request_id = $request_id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_result'])) {
    $result_value = $_POST['result_value'];
    $result_unit = $_POST['result_unit'];
    $ref_min = $_POST['reference_min'];
    $ref_max = $_POST['reference_max'];
    $result_status = $_POST['result_status'];
    $notes = $_POST['notes'];
    
    if ($existing) {
        $stmt = $conn->prepare("UPDATE lab_results SET result_value=?, result_unit=?, reference_min=?, reference_max=?, result_status=?, notes=?, technician_id=? WHERE request_id=?");
        $stmt->bind_param("ssssssii", $result_value, $result_unit, $ref_min, $ref_max, $result_status, $notes, $tech_id, $request_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO lab_results (request_id, result_value, result_unit, reference_min, reference_max, result_status, notes, technician_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssi", $request_id, $result_value, $result_unit, $ref_min, $ref_max, $result_status, $notes, $tech_id);
    }
    
    if ($stmt->execute()) {
        // Update request status
        $conn->query("UPDATE lab_requests SET status='Completed' WHERE id=$request_id");
        $msg = "✅ Result saved successfully!"; $msgType = "success";
        logActivity($conn, $tech_id, $_SESSION['username'], 'RECORD_LAB_RESULT', "Recorded result for request ID: $request_id");
        
        // Refresh existing
        $existing = $conn->query("SELECT * FROM lab_results WHERE request_id = $request_id")->fetch_assoc();
    } else { $msg = "❌ Error: " . $conn->error; $msgType = "error"; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Record Result | Alfurqan Clinic</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f8f9ff; display: flex; }
        .sidebar { width: 260px; background: #4a148c; color: white; height: 100vh; position: fixed; }
        .sidebar-header { text-align: center; padding: 25px 20px; border-bottom: 1px solid #6a1b9a; background: #38006b; }
        .sidebar-header img { width: 70px; height: 70px; margin-bottom: 10px; background: white; border-radius: 50%; padding: 5px; }
        .sidebar-header h2 { font-size: 18px; } .sidebar-header small { font-size: 11px; opacity: 0.8; }
        .sidebar-menu a { display: flex; align-items: center; gap: 15px; padding: 15px 25px; color: #e1bee7; text-decoration: none; border-bottom: 1px solid #6a1b9a; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #6a1b9a; color: white; padding-left: 30px; }
        .logout-btn { background: #c62828 !important; justify-content: center; margin-top: 30px; }
        .main-content { margin-left: 260px; padding: 30px; width: calc(100% - 260px); }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e8eaf6; }
        .header h1 { color: #4a148c; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; }
        .card h3 { color: #4a148c; margin-bottom: 20px; border-bottom: 2px solid #f3e5f5; padding-bottom: 15px; }
        .info-box { background: #f3e5f5; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #4a148c; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .info-item label { display: block; font-size: 12px; color: #666; margin-bottom: 5px; }
        .info-item .val { font-weight: 600; color: #333; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        button { padding: 12px 25px; background: #4a148c; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .btn-print { background: #2e7d32; text-decoration: none; display: inline-block; padding: 12px 25px; border-radius: 6px; color: white; margin-left: 10px;}
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; } .alert-error { background: #f8d7da; color: #721c24; }
        .status-options { display: flex; gap: 10px; }
        .status-options label { flex: 1; padding: 10px; border: 2px solid #ddd; border-radius: 6px; text-align: center; cursor: pointer; }
        .status-options input { display: none; }
        .status-options input:checked + span { font-weight: bold; }
        .status-options label:has(input:checked) { border-color: #4a148c; background: #f3e5f5; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/logo.png" alt="Logo">
            <h2>LABORATORY</h2><small>Alfurqan Clinic</small>
        </div>
        <div class="sidebar-menu">
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="test_requests.php">📋 Test Requests</a>
    <a href="pending_tests.php">⏳ Pending Tests</a>
    <a href="view_results.php">📈 View Results</a>
    <a href="../logout.php" class="logout-btn">🚪 Logout</a>
</div>
    </div>
    
    <div class="main-content">
        <div class="header"><h1>📝 Record Test Result</h1></div>
        
        <?php if($msg != ""): ?><div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div><?php endif; ?>
        
        <div class="card">
            <h3>Request Information</h3>
            <div class="info-box">
                <div class="info-grid">
                    <div class="info-item"><label>Patient</label><div class="val"><?php echo htmlspecialchars($request['patient_name']); ?> (<?php echo $request['age']; ?>y, <?php echo $request['gender']; ?>)</div></div>
                    <div class="info-item"><label>Test</label><div class="val"><?php echo htmlspecialchars($request['test_name']); ?> (<?php echo htmlspecialchars($request['sample_type']); ?>)</div></div>
                    <div class="info-item"><label>Requested By</label><div class="val">Dr. <?php echo htmlspecialchars($request['doctor_name']); ?></div></div>
                    <div class="info-item"><label>Date</label><div class="val"><?php echo date('d M Y, g:i A', strtotime($request['request_date'])); ?></div></div>
                </div>
            </div>
            
            <h3>Enter Results</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Result Value *</label>
                    <input type="text" name="result_value" value="<?php echo htmlspecialchars($existing['result_value'] ?? ''); ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Unit</label><input type="text" name="result_unit" value="<?php echo htmlspecialchars($existing['result_unit'] ?? ''); ?>"></div>
                    <div class="form-group"><label>Reference Min</label><input type="text" name="reference_min" value="<?php echo htmlspecialchars($existing['reference_min'] ?? ''); ?>"></div>
                    <div class="form-group"><label>Reference Max</label><input type="text" name="reference_max" value="<?php echo htmlspecialchars($existing['reference_max'] ?? ''); ?>"></div>
                </div>
                <div class="form-group">
                    <label>Result Status *</label>
                    <div class="status-options">
                        <label><input type="radio" name="result_status" value="Normal" <?php echo ($existing['result_status'] ?? '')=='Normal'?'checked':''; ?> required><span style="color:#2e7d32">✅ Normal</span></label>
                        <label><input type="radio" name="result_status" value="Abnormal" <?php echo ($existing['result_status'] ?? '')=='Abnormal'?'checked':''; ?>><span style="color:#f57c00">⚠️ Abnormal</span></label>
                        <label><input type="radio" name="result_status" value="Critical" <?php echo ($existing['result_status'] ?? '')=='Critical'?'checked':''; ?>><span style="color:#c62828">🚨 Critical</span></label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Notes / Comments</label>
                    <textarea name="notes" rows="3"><?php echo htmlspecialchars($existing['notes'] ?? ''); ?></textarea>
                </div>
                <button type="submit" name="save_result">💾 Save Result</button>
                <?php if($existing): ?>
                    <a href="print_result.php?id=<?php echo $request_id; ?>" target="_blank" class="btn-print">🖨️ Print Report</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>