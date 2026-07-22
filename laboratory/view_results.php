<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkLabTechnician();

$results = $conn->query("SELECT lr.*, lt.test_name, p.name as patient_name, lres.result_value, lres.result_unit, lres.result_status, lres.result_date 
                         FROM lab_requests lr 
                         JOIN lab_tests lt ON lr.test_id = lt.id 
                         JOIN patients p ON lr.patient_id = p.id 
                         JOIN lab_results lres ON lr.id = lres.request_id 
                         WHERE lr.status = 'Completed' 
                         ORDER BY lres.result_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Results | Alfurqan Clinic</title>
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
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .card h3 { color: #4a148c; margin-bottom: 20px; border-bottom: 2px solid #f3e5f5; padding-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #f3e5f5; }
        th { background: #4a148c; color: white; font-size: 13px; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; }
        .bg-normal { background: #2e7d32; } .bg-abnormal { background: #f57c00; } .bg-critical { background: #c62828; }
        .btn-sm { padding: 6px 12px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; color: white; text-decoration: none; display: inline-block; }
        .btn-success { background: #2e7d32; }
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
        <div class="header"><h1>📈 Completed Lab Results</h1></div>
        
        <div class="card">
            <h3>All Recorded Results</h3>
            <table>
                <thead><tr><th>ID</th><th>Patient</th><th>Test</th><th>Result</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
                <tbody>
                    <?php while($r = $results->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $r['id']; ?></td>
                        <td><?php echo htmlspecialchars($r['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($r['test_name']); ?></td>
                        <td><strong><?php echo htmlspecialchars($r['result_value']); ?></strong> <?php echo htmlspecialchars($r['result_unit']); ?></td>
                        <td><span class="badge bg-<?php echo strtolower($r['result_status']); ?>"><?php echo $r['result_status']; ?></span></td>
                        <td><?php echo date('d M Y, g:i A', strtotime($r['result_date'])); ?></td>
                        <td><a href="print_result.php?id=<?php echo $r['id']; ?>" target="_blank" class="btn-sm btn-success">️ Print</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>