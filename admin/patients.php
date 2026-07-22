<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkAdmin();

// Delete Patient
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_patient'])) {
    $pid = $_POST['patient_id'];
    $stmt = $conn->prepare("DELETE FROM patients WHERE id = ?");
    $stmt->bind_param("i", $pid);
    if ($stmt->execute()) { header("Location: patients.php"); exit(); }
}

$patients = $conn->query("SELECT * FROM patients ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patients | Alfurqan Clinic</title>
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
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e8f4f8; }
        th { background: #003366; color: white; font-size: 13px; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; }
        .bg-stable { background: #28a745; } .bg-critical { background: #dc3545; }
        .bg-recovering { background: #17a2b8; } .bg-pending { background: #ffc107; color: #000; }
        .bg-discharged { background: #6c757d; }
        .btn-sm { padding: 6px 12px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; color: white; text-decoration: none; display: inline-block; margin-right: 5px;}
        .btn-primary { background: #007bff; } .btn-danger { background: #dc3545; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/logo.png" alt="Logo">
            <h2>ALFURQAN CLINIC</h2><small>Admin Control Panel</small>
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
        <div class="header"><h1> Patient Records</h1></div>
        
        <div class="card">
            <h3>All Registered Patients</h3>
            <table>
                <thead><tr><th>ID</th><th>Name</th><th>Age</th><th>Gender</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php while($p = $patients->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $p['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                        <td><?php echo $p['age']; ?></td>
                        <td><?php echo $p['gender']; ?></td>
                        <td><?php echo $p['phone']; ?></td>
                        <td><span class="badge bg-<?php echo strtolower($p['patient_status']); ?>"><?php echo $p['patient_status']; ?></span></td>
                        <td>
                            <a href="edit_patient.php?id=<?php echo $p['id']; ?>" class="btn-sm btn-primary">✏️ Edit</a>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this patient?');">
                                <input type="hidden" name="patient_id" value="<?php echo $p['id']; ?>">
                                <button type="submit" name="delete_patient" class="btn-sm btn-danger">🗑️ Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>