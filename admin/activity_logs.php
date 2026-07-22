<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkAdmin();

$filter_action = $_GET['action'] ?? '';
$query = "SELECT * FROM activity_logs WHERE 1=1";
if ($filter_action) { $query .= " AND action = '$filter_action'"; }
$query .= " ORDER BY created_at DESC LIMIT 100";
$logs = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity Logs | Alfurqan Clinic</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; display: flex; }
        .sidebar { width: 260px; background: #003366; color: white; height: 100vh; position: fixed; }
        .sidebar-header { text-align: center; padding: 25px 20px; border-bottom: 1px solid #004080; background: #002244; }
        .sidebar-header img { width: 70px; height: 70px; margin-bottom: 10px; background: white; border-radius: 50%; padding: 5px; }
        .sidebar-header h2 { font-size: 18px; } .sidebar-header small { font-size: 11px; opacity: 0.8; }
        .sidebar-menu a { display: flex; align-items: center; gap: 15px; padding: 15px 25px; color: #b3cde0; text-decoration: none; border-bottom: 1px solid #004080; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #004080; color: white; padding-left: 30px; }
        .logout-btn { background: #cc0000 !important; justify-content: center; margin-top: 30px; }
        .main-content { margin-left: 260px; padding: 30px; width: calc(100% - 260px); }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e8f4f8; }
        .header h1 { color: #003366; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        .card h3 { color: #003366; margin-bottom: 20px; border-bottom: 2px solid #e8f4f8; padding-bottom: 15px; }
        .filter-bar { margin-bottom: 20px; }
        .filter-bar select { padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e8f4f8; font-size: 14px; }
        th { background: #003366; color: white; font-size: 13px; }
        tr:hover { background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/logo.png" alt="Logo">
            <h2>ALFURQAN CLINIC</h2><small>Admin Control Panel</small>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="register_patient.php">📝 Register Patient</a>
            <a href="manage_users.php">👥 Manage Users</a>
            <a href="appointments.php">📅 Appointments</a>
            <a href="activity_logs.php" class="active"> Activity Logs</a>
            <a href="billing.php">💰 Billing System</a>
            <a href="../logout.php" class="logout-btn">🚪 Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="header"><h1>📋 Activity Logs</h1></div>
        
        <div class="card">
            <h3>System Activity History</h3>
            <div class="filter-bar">
                <form method="GET" style="display:flex; gap:10px;">
                    <select name="action" onchange="this.form.submit()">
                        <option value="">All Actions</option>
                        <option value="LOGIN" <?php echo $filter_action=='LOGIN'?'selected':''; ?>>LOGIN</option>
                        <option value="LOGOUT" <?php echo $filter_action=='LOGOUT'?'selected':''; ?>>LOGOUT</option>
                        <option value="ADD_USER" <?php echo $filter_action=='ADD_USER'?'selected':''; ?>>ADD_USER</option>
                        <option value="REGISTER_PATIENT" <?php echo $filter_action=='REGISTER_PATIENT'?'selected':''; ?>>REGISTER_PATIENT</option>
                        <option value="CONFIRM_APPOINTMENT" <?php echo $filter_action=='CONFIRM_APPOINTMENT'?'selected':''; ?>>CONFIRM_APPOINTMENT</option>
                    </select>
                </form>
            </div>
            
            <table>
                <thead><tr><th>ID</th><th>User</th><th>Action</th><th>Description</th><th>IP Address</th><th>Date/Time</th></tr></thead>
                <tbody>
                    <?php while($log = $logs->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $log['id']; ?></td>
                        <td><?php echo htmlspecialchars($log['username']); ?></td>
                        <td><strong><?php echo $log['action']; ?></strong></td>
                        <td><?php echo htmlspecialchars($log['description']); ?></td>
                        <td><?php echo $log['ip_address']; ?></td>
                        <td><?php echo date('d M Y, g:i A', strtotime($log['created_at'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>