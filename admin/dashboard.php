<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkAdmin();

$admin_id = $_SESSION['user_id'];

$total_patients = $conn->query("SELECT COUNT(*) as total FROM patients")->fetch_assoc()['total'] ?? 0;
$total_revenue = $conn->query("SELECT SUM(amount) as total FROM billing WHERE status='Paid'")->fetch_assoc()['total'] ?? 0;
$pending_bills = $conn->query("SELECT COUNT(*) as total FROM billing WHERE status='Pending'")->fetch_assoc()['total'] ?? 0;
$total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'] ?? 0;
$pending_appointments = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE status='Pending'")->fetch_assoc()['total'] ?? 0;
$today_appointments = $conn->query("SELECT COUNT(*) as total FROM appointments WHERE DATE(appointment_date) = CURDATE()")->fetch_assoc()['total'] ?? 0;

$recent_patients = $conn->query("SELECT * FROM patients ORDER BY id DESC LIMIT 5");
$recent_activities = $conn->query("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 5");

$pending_apts_query = "SELECT a.*, p.name as patient_name, p.phone 
                       FROM appointments a 
                       JOIN patients p ON a.patient_id = p.id 
                       WHERE a.status = 'Pending' 
                       ORDER BY a.created_at DESC 
                       LIMIT 5";
$pending_apts = $conn->query($pending_apts_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Alfurqan Clinic</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; display: flex; }
        .sidebar { width: 260px; background: #003366; color: white; height: 100vh; position: fixed; }
        .sidebar-header { text-align: center; padding: 25px 20px; border-bottom: 1px solid #004080; background: #002244; }
        .sidebar-header img { width: 70px; height: 70px; margin-bottom: 10px; background: white; border-radius: 50%; padding: 5px; }
        .sidebar-header h2 { font-size: 18px; font-weight: 700; }
        .sidebar-header small { font-size: 11px; opacity: 0.8; }
        .sidebar-menu a { display: flex; align-items: center; gap: 15px; padding: 15px 25px; color: #b3cde0; text-decoration: none; border-bottom: 1px solid #004080; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #004080; color: white; padding-left: 30px; }
        .logout-btn { background: #cc0000 !important; justify-content: center; margin-top: 30px; }
        .main-content { margin-left: 260px; padding: 30px; width: calc(100% - 260px); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e8f4f8; }
        .header h1 { color: #003366; font-size: 28px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-left: 5px solid #003366; }
        .stat-card h3 { color: #666; font-size: 14px; margin-bottom: 10px; }
        .stat-card p { color: #003366; font-size: 32px; font-weight: bold; }
        .section { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; }
        .section h3 { color: #003366; margin-bottom: 20px; border-bottom: 2px solid #e8f4f8; padding-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e8f4f8; }
        th { background: #003366; color: white; font-weight: 600; font-size: 13px; }
        tr:hover { background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/logo.png" alt="Logo">
            <h2>ALFURQAN CLINIC</h2>
            <small>Admin Control Panel</small>
        </div>
        <div class="sidebar-menu">
    <a href="dashboard.php" class="active">📊 Dashboard</a>
    
    <!-- NEW AI DASHBOARD LINK -->
    <a href="ai_dashboard.php" style="background: linear-gradient(90deg, #667eea 0%, #764ba2 100%); color: white; font-weight: bold;">
         AI Intelligence
    </a>
    
    <a href="register_patient.php">📝 Register Patient</a>
    <a href="manage_users.php">👥 Manage Users</a>
    <a href="appointments.php">📅 Appointments</a>
    <a href="activity_logs.php">📋 Activity Logs</a>
    <a href="billing.php">💰 Billing System</a>
    <a href="patients.php"> Patients</a>
    <a href="../logout.php" class="logout-btn">🚪 Logout</a>
</div>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>📊 Dashboard Overview</h1>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card"><h3>Total Patients</h3><p><?php echo $total_patients; ?></p></div>
            <div class="stat-card"><h3>Total Revenue</h3><p>₦<?php echo number_format($total_revenue, 2); ?></p></div>
            <div class="stat-card"><h3>Pending Bills</h3><p><?php echo $pending_bills; ?></p></div>
            <div class="stat-card"><h3>Total Staff</h3><p><?php echo $total_users; ?></p></div>
            <div class="stat-card"><h3>Pending Appts</h3><p><?php echo $pending_appointments; ?></p></div>
            <div class="stat-card"><h3>Today's Appts</h3><p><?php echo $today_appointments; ?></p></div>
        </div>
        
        <div class="section">
            <h3>⏳ Pending Appointment Approvals</h3>
            <?php if ($pending_apts && $pending_apts->num_rows > 0): ?>
            <table>
                <thead><tr><th>ID</th><th>Patient</th><th>Date</th><th>Reason</th><th>Action</th></tr></thead>
                <tbody>
                    <?php while($apt = $pending_apts->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $apt['id']; ?></td>
                        <td><?php echo htmlspecialchars($apt['patient_name']); ?></td>
                        <td><?php echo date('d M Y', strtotime($apt['appointment_date'])); ?></td>
                        <td><?php echo htmlspecialchars(substr($apt['reason'], 0, 40)); ?>...</td>
                        <td><a href="appointments.php" style="padding: 6px 12px; background: #003366; color: white; text-decoration: none; border-radius: 5px;">📋 Review</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align: center; color: #999; padding: 30px;">✅ No pending appointments!</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>