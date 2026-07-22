<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkLabTechnician();

$status_filter = $_GET['status'] ?? '';
$priority_filter = $_GET['priority'] ?? '';

$query = "SELECT lr.*, lt.test_name, p.name as patient_name, u.username as doctor_name 
          FROM lab_requests lr 
          JOIN lab_tests lt ON lr.test_id = lt.id 
          JOIN patients p ON lr.patient_id = p.id 
          JOIN users u ON lr.doctor_id = u.id WHERE 1=1";
          
if ($status_filter) $query .= " AND lr.status = '$status_filter'";
if ($priority_filter) $query .= " AND lr.priority = '$priority_filter'";
$query .= " ORDER BY lr.request_date DESC";

$requests = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Requests | Alfurqan Clinic</title>
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
        .filter-bar { display: flex; gap: 15px; margin-bottom: 20px; }
        .filter-bar select { padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #f3e5f5; }
        th { background: #4a148c; color: white; font-size: 13px; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; }
        .bg-emergency { background: #c62828; } .bg-urgent { background: #f57c00; } .bg-routine { background: #1976d2; }
        .bg-pending { background: #f57c00; } .bg-completed { background: #2e7d32; }
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
        <div class="header"><h1>📋 All Test Requests</h1></div>
        
        <div class="card">
            <h3>Filter Requests</h3>
            <div class="filter-bar">
                <form method="GET" style="display:flex; gap:10px;">
                    <select name="status" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="Pending" <?php echo $status_filter=='Pending'?'selected':''; ?>>Pending</option>
                        <option value="Completed" <?php echo $status_filter=='Completed'?'selected':''; ?>>Completed</option>
                    </select>
                    <select name="priority" onchange="this.form.submit()">
                        <option value="">All Priorities</option>
                        <option value="Emergency" <?php echo $priority_filter=='Emergency'?'selected':''; ?>>Emergency</option>
                        <option value="Urgent" <?php echo $priority_filter=='Urgent'?'selected':''; ?>>Urgent</option>
                        <option value="Routine" <?php echo $priority_filter=='Routine'?'selected':''; ?>>Routine</option>
                    </select>
                </form>
            </div>
            
            <table>
                <thead><tr><th>ID</th><th>Patient</th><th>Test</th><th>Priority</th><th>Doctor</th><th>Date</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                    <?php while($r = $requests->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $r['id']; ?></td>
                        <td><?php echo htmlspecialchars($r['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($r['test_name']); ?></td>
                        <td><span class="badge bg-<?php echo strtolower($r['priority']); ?>"><?php echo $r['priority']; ?></span></td>
                        <td>Dr. <?php echo htmlspecialchars($r['doctor_name']); ?></td>
                        <td><?php echo date('d M Y', strtotime($r['request_date'])); ?></td>
                        <td><span class="badge bg-<?php echo strtolower($r['status']); ?>"><?php echo $r['status']; ?></span></td>
                        <td>
                            <?php if($r['status'] == 'Pending'): ?>
                                <a href="record_result.php?id=<?php echo $r['id']; ?>" class="btn-sm btn-primary">📝 Record</a>
                            <?php else: ?>
                                <a href="print_result.php?id=<?php echo $r['id']; ?>" target="_blank" class="btn-sm btn-success">🖨️ Print</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>