<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkLabTechnician();

$priority_filter = $_GET['priority'] ?? '';

$query = "SELECT lr.*, lt.test_name, lt.sample_type, p.name as patient_name, p.phone, u.username as doctor_name 
          FROM lab_requests lr 
          JOIN lab_tests lt ON lr.test_id = lt.id 
          JOIN patients p ON lr.patient_id = p.id 
          JOIN users u ON lr.doctor_id = u.id 
          WHERE lr.status IN ('Pending', 'In Progress')";
          
if ($priority_filter) {
    $query .= " AND lr.priority = '$priority_filter'";
}

$query .= " ORDER BY 
            CASE lr.priority 
                WHEN 'Emergency' THEN 1 
                WHEN 'Urgent' THEN 2 
                ELSE 3 
            END, 
            lr.request_date ASC";

$pending_tests = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pending Tests | Alfurqan Clinic</title>
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
        .filter-bar { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .filter-btn { padding: 8px 16px; border: 2px solid #4a148c; background: white; color: #4a148c; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 13px; transition: 0.3s; }
        .filter-btn:hover, .filter-btn.active { background: #4a148c; color: white; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #f3e5f5; }
        th { background: #4a148c; color: white; font-size: 13px; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; font-weight: 600; }
        .bg-emergency { background: #c62828; } .bg-urgent { background: #f57c00; } .bg-routine { background: #1976d2; }
        .btn-sm { padding: 8px 14px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; color: white; text-decoration: none; display: inline-block; font-weight: 600; }
        .btn-primary { background: #4a148c; } .btn-primary:hover { background: #6a1b9a; }
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
        <div class="header"><h1> Pending & In-Progress Tests</h1></div>
        
        <div class="card">
            <h3>Filter by Priority</h3>
            <div class="filter-bar">
                <a href="pending_tests.php" class="filter-btn <?php echo !$priority_filter ? 'active' : ''; ?>">All</a>
                <a href="pending_tests.php?priority=Emergency" class="filter-btn <?php echo $priority_filter=='Emergency'?'active':''; ?>">🚨 Emergency</a>
                <a href="pending_tests.php?priority=Urgent" class="filter-btn <?php echo $priority_filter=='Urgent'?'active':''; ?>">⚠️ Urgent</a>
                <a href="pending_tests.php?priority=Routine" class="filter-btn <?php echo $priority_filter=='Routine'?'active':''; ?>">📋 Routine</a>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Test Name</th>
                        <th>Sample Type</th>
                        <th>Priority</th>
                        <th>Requested By</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pending_tests->num_rows > 0): ?>
                        <?php while($t = $pending_tests->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $t['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($t['patient_name']); ?></strong><br>
                                <small style="color:#666;"><?php echo $t['phone']; ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($t['test_name']); ?></td>
                            <td><?php echo htmlspecialchars($t['sample_type']); ?></td>
                            <td><span class="badge bg-<?php echo strtolower($t['priority']); ?>"><?php echo $t['priority']; ?></span></td>
                            <td>Dr. <?php echo htmlspecialchars($t['doctor_name']); ?></td>
                            <td><?php echo date('d M Y, g:i A', strtotime($t['request_date'])); ?></td>
                            <td>
                                <a href="record_result.php?id=<?php echo $t['id']; ?>" class="btn-sm btn-primary">📝 Record Result</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; color: #666; padding: 30px;">✅ No pending tests found. Great job!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>