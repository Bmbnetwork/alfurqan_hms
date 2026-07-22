<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkLabTechnician();

$tech_id = $_SESSION['user_id'];

// Statistics
$total_requests = $conn->query("SELECT COUNT(*) as total FROM lab_requests")->fetch_assoc()['total'] ?? 0;
$pending_tests = $conn->query("SELECT COUNT(*) as total FROM lab_requests WHERE status='Pending'")->fetch_assoc()['total'] ?? 0;
$completed_today = $conn->query("SELECT COUNT(*) as total FROM lab_requests WHERE status='Completed' AND DATE(request_date) = CURDATE()")->fetch_assoc()['total'] ?? 0;
$urgent_pending = $conn->query("SELECT COUNT(*) as total FROM lab_requests WHERE status='Pending' AND priority IN ('Urgent', 'Emergency')")->fetch_assoc()['total'] ?? 0;

// Pending Requests (Priority Ordered)
$pending_requests = $conn->query("SELECT lr.*, lt.test_name, lt.sample_type, p.name as patient_name, p.phone, u.username as doctor_name 
                                  FROM lab_requests lr 
                                  JOIN lab_tests lt ON lr.test_id = lt.id 
                                  JOIN patients p ON lr.patient_id = p.id 
                                  JOIN users u ON lr.doctor_id = u.id 
                                  WHERE lr.status = 'Pending' 
                                  ORDER BY 
                                      CASE lr.priority 
                                          WHEN 'Emergency' THEN 1 
                                          WHEN 'Urgent' THEN 2 
                                          ELSE 3 
                                      END, 
                                  lr.request_date ASC 
                                  LIMIT 10");

// Completed Today
$completed_today_list = $conn->query("SELECT lr.*, lt.test_name, p.name as patient_name, lres.result_status 
                                      FROM lab_requests lr 
                                      JOIN lab_tests lt ON lr.test_id = lt.id 
                                      JOIN patients p ON lr.patient_id = p.id 
                                      LEFT JOIN lab_results lres ON lr.id = lres.request_id 
                                      WHERE lr.status = 'Completed' AND DATE(lr.request_date) = CURDATE() 
                                      ORDER BY lr.request_date DESC 
                                      LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laboratory Dashboard | Alfurqan Clinic</title>
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
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-left: 5px solid #4a148c; }
        .stat-card h3 { color: #666; font-size: 14px; margin-bottom: 10px; }
        .stat-card p { color: #4a148c; font-size: 32px; font-weight: bold; }
        .stat-card.urgent { border-left-color: #c62828; } .stat-card.urgent p { color: #c62828; }
        .stat-card.completed { border-left-color: #2e7d32; } .stat-card.completed p { color: #2e7d32; }
        .section { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; }
        .section h3 { color: #4a148c; margin-bottom: 20px; border-bottom: 2px solid #f3e5f5; padding-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #f3e5f5; }
        th { background: #4a148c; color: white; font-size: 13px; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; }
        .bg-emergency { background: #c62828; } .bg-urgent { background: #f57c00; } .bg-routine { background: #1976d2; }
        .bg-pending { background: #f57c00; } .bg-completed { background: #2e7d32; }
        .bg-normal { background: #2e7d32; } .bg-abnormal { background: #f57c00; } .bg-critical { background: #c62828; }
        .btn-sm { padding: 6px 12px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; color: white; text-decoration: none; display: inline-block; }
        .btn-primary { background: #4a148c; } .btn-success { background: #2e7d32; }
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
        <div class="header">
            <h1>🔬 Laboratory Dashboard</h1>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card"><h3>Total Requests</h3><p><?php echo $total_requests; ?></p></div>
            <div class="stat-card"><h3>Pending Tests</h3><p><?php echo $pending_tests; ?></p></div>
            <div class="stat-card completed"><h3>Completed Today</h3><p><?php echo $completed_today; ?></p></div>
            <div class="stat-card urgent"><h3>Urgent/Emergency</h3><p><?php echo $urgent_pending; ?></p></div>
        </div>
        
        <div class="section">
            <h3>⏳ Pending Test Requests (Priority Order)</h3>
            <?php if ($pending_requests->num_rows > 0): ?>
            <table>
                <thead><tr><th>ID</th><th>Patient</th><th>Test</th><th>Sample</th><th>Priority</th><th>Doctor</th><th>Action</th></tr></thead>
                <tbody>
                    <?php while($req = $pending_requests->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $req['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($req['patient_name']); ?></strong><br><small><?php echo $req['phone']; ?></small></td>
                        <td><?php echo htmlspecialchars($req['test_name']); ?></td>
                        <td><?php echo htmlspecialchars($req['sample_type']); ?></td>
                        <td><span class="badge bg-<?php echo strtolower($req['priority']); ?>"><?php echo $req['priority']; ?></span></td>
                        <td>Dr. <?php echo htmlspecialchars($req['doctor_name']); ?></td>
                        <td><a href="record_result.php?id=<?php echo $req['id']; ?>" class="btn-sm btn-primary">📝 Record</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align: center; color: #666; padding: 20px;">✅ No pending test requests!</p>
            <?php endif; ?>
        </div>

        <div class="section">
            <h3>✅ Completed Today</h3>
            <?php if ($completed_today_list->num_rows > 0): ?>
            <table>
                <thead><tr><th>Patient</th><th>Test</th><th>Result Status</th><th>Completed</th><th>Action</th></tr></thead>
                <tbody>
                    <?php while($test = $completed_today_list->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($test['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($test['test_name']); ?></td>
                        <td><span class="badge bg-<?php echo strtolower($test['result_status'] ?? 'normal'); ?>"><?php echo ucfirst($test['result_status'] ?? 'Normal'); ?></span></td>
                        <td><?php echo date('g:i A', strtotime($test['request_date'])); ?></td>
                        <td><a href="print_result.php?id=<?php echo $test['id']; ?>" target="_blank" class="btn-sm btn-success">🖨️ Print</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align: center; color: #666; padding: 20px;">No tests completed today yet.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>