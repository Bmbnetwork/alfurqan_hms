<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkDoctor();

$doctor_id = $_SESSION['user_id'];

// Fetch completed lab requests and their results for this doctor
$stmt = $conn->prepare("SELECT 
                            lr.id AS request_id, 
                            lr.request_date, 
                            lr.priority, 
                            lr.clinical_notes,
                            lt.test_name, 
                            lt.category,
                            p.name AS patient_name, 
                            p.age, 
                            p.gender,
                            lres.result_value, 
                            lres.result_unit, 
                            lres.reference_min,
                            lres.reference_max,
                            lres.result_status, 
                            lres.result_date,
                            lres.notes AS lab_notes,
                            u.username AS technician_name
                        FROM lab_requests lr
                        JOIN lab_tests lt ON lr.test_id = lt.id
                        JOIN patients p ON lr.patient_id = p.id
                        LEFT JOIN lab_results lres ON lr.id = lres.request_id
                        LEFT JOIN users u ON lres.technician_id = u.id
                        WHERE lr.doctor_id = ? AND lr.status = 'Completed'
                        ORDER BY lres.result_date DESC");

$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$results = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Lab Results | Alfurqan Clinic</title>
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
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 20px; }
        .card h3 { color: #1b5e20; margin-bottom: 15px; border-bottom: 2px solid #e8f5e9; padding-bottom: 10px; }
        
        /* Result Card Styling */
        .result-card { border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; margin-bottom: 20px; transition: 0.3s; }
        .result-card:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .result-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed #ccc; }
        .patient-info { font-weight: 600; color: #333; }
        .test-info { color: #666; font-size: 14px; }
        
        .result-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px; }
        .result-item { background: #f8f9fa; padding: 10px; border-radius: 6px; }
        .result-item label { display: block; font-size: 12px; color: #666; margin-bottom: 4px; }
        .result-item .value { font-size: 16px; font-weight: 600; color: #1b5e20; }
        
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; font-weight: 600; }
        .bg-normal { background: #2e7d32; } .bg-abnormal { background: #f57c00; } .bg-critical { background: #c62828; }
        .bg-emergency { background: #c62828; } .bg-urgent { background: #f57c00; } .bg-routine { background: #1976d2; }
        
        .btn-print { padding: 8px 15px; background: #1b5e20; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 13px; }
        .btn-print:hover { background: #144a18; }

        /* Print Styles */
        @media print {
            .sidebar, .header, .btn-print, .no-print { display: none !important; }
            .main-content { margin-left: 0; padding: 0; width: 100%; }
            .result-card { break-inside: avoid; border: 1px solid #000; }
            body { background: white; }
        }
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
            <a href="view_lab_results.php" class="active">📈 View Lab Results</a>
            <a href="prescribe_drug.php">💊 Prescriptions</a>
            <a href="../logout.php" class="logout-btn">🚪 Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>📈 Completed Lab Results</h1>
            <button class="btn-print no-print" onclick="window.print()">🖨️ Print All Results</button>
        </div>
        
        <?php if ($results->num_rows > 0): ?>
            <?php while($row = $results->fetch_assoc()): ?>
            <div class="result-card">
                <div class="result-header">
                    <div>
                        <div class="patient-info">👤 <?php echo htmlspecialchars($row['patient_name']); ?> 
                            <small style="font-weight: normal; color: #666;">(<?php echo $row['age']; ?>y, <?php echo $row['gender']; ?>)</small>
                        </div>
                        <div class="test-info"> <?php echo htmlspecialchars($row['test_name']); ?> 
                            <span class="badge bg-routine" style="margin-left: 10px;"><?php echo $row['priority']; ?></span>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <small style="color: #666;">Reported: <?php echo date('d M Y, g:i A', strtotime($row['result_date'])); ?></small><br>
                        <span class="badge bg-<?php echo strtolower($row['result_status'] ?? 'normal'); ?>">
                            <?php echo ucfirst($row['result_status'] ?? 'Normal'); ?>
                        </span>
                    </div>
                </div>
                
                <div class="result-grid">
                    <div class="result-item">
                        <label>Result Value</label>
                        <div class="value"><?php echo htmlspecialchars($row['result_value'] ?? 'N/A'); ?> 
                            <small><?php echo htmlspecialchars($row['result_unit'] ?? ''); ?></small>
                        </div>
                    </div>
                    <div class="result-item">
                        <label>Reference Range</label>
                        <div class="value" style="font-size: 14px; color: #555;">
                            <?php echo htmlspecialchars($row['reference_min'] ?? ''); ?> - <?php echo htmlspecialchars($row['reference_max'] ?? ''); ?>
                        </div>
                    </div>
                    <div class="result-item">
                        <label>Processed By</label>
                        <div class="value" style="font-size: 14px; color: #555;">
                            <?php echo htmlspecialchars($row['technician_name'] ?? 'Lab Tech'); ?>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($row['lab_notes'])): ?>
                <div style="background: #fff3cd; padding: 10px; border-radius: 6px; border-left: 4px solid #ffc107; margin-top: 10px;">
                    <strong> Lab Notes:</strong> <?php echo nl2br(htmlspecialchars($row['lab_notes'])); ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($row['clinical_notes'])): ?>
                <div style="background: #e3f2fd; padding: 10px; border-radius: 6px; border-left: 4px solid #2196f3; margin-top: 10px;">
                    <strong>🩺 Clinical Notes:</strong> <?php echo nl2br(htmlspecialchars($row['clinical_notes'])); ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="card" style="text-align: center; padding: 50px;">
                <h3 style="color: #666; border: none;">No Completed Lab Results Yet</h3>
                <p style="color: #999;">Results for lab tests you requested will appear here once the laboratory completes them.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>