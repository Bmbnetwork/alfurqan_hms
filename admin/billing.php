<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkAdmin();

$msg = ""; $msgType = "";

// Add Bill
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_bill'])) {
    $patient_id = $_POST['patient_id'];
    $amount = $_POST['amount'];
    $desc = $_POST['description'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("INSERT INTO billing (patient_id, amount, description, status, created_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("idssi", $patient_id, $amount, $desc, $status, $_SESSION['user_id']);
    if ($stmt->execute()) { 
        $msg = "✅ Bill created successfully!"; $msgType = "success"; 
        logActivity($conn, $_SESSION['user_id'], $_SESSION['username'], 'ADD_BILL', "Created bill of ₦$amount for patient ID: $patient_id");
    } else { $msg = " Error: " . $conn->error; $msgType = "error"; }
}

// Quick Status Toggle
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $bill_id = $_POST['bill_id'];
    $new_status = $_POST['new_status'];
    $stmt = $conn->prepare("UPDATE billing SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $bill_id);
    $stmt->execute();
}

$total_revenue = $conn->query("SELECT SUM(amount) as total FROM billing WHERE status='Paid'")->fetch_assoc()['total'] ?? 0;
$pending_amount = $conn->query("SELECT SUM(amount) as total FROM billing WHERE status='Pending'")->fetch_assoc()['total'] ?? 0;
$bills = $conn->query("SELECT b.*, p.name as patient_name FROM billing b JOIN patients p ON b.patient_id = p.id ORDER BY b.bill_date DESC");
$patients = $conn->query("SELECT id, name FROM patients");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billing System | Alfurqan Clinic</title>
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
        .stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); border-left: 5px solid #003366; }
        .stat-card h3 { color: #666; font-size: 14px; margin-bottom: 10px; }
        .stat-card p { color: #003366; font-size: 28px; font-weight: bold; }
        .stat-card.pending { border-left-color: #ffc107; } .stat-card.pending p { color: #ffc107; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; }
        .card h3 { color: #003366; margin-bottom: 20px; border-bottom: 2px solid #e8f4f8; padding-bottom: 15px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap: 15px; align-items: end; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; } label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        button { padding: 10px 20px; background: #003366; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e8f4f8; }
        th { background: #003366; color: white; font-size: 13px; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; }
        .bg-paid { background: #28a745; } .bg-pending { background: #ffc107; color: #000; }
        .btn-sm { padding: 6px 12px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; color: white; text-decoration: none; display: inline-block; margin-right: 5px;}
        .btn-success { background: #28a745; } .btn-warning { background: #ffc107; color: #000; } .btn-primary { background: #007bff; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; } .alert-error { background: #f8d7da; color: #721c24; }
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
    <a href="../logout.php" class="logout-btn">🚪 Logout</a>
</div>
    </div>
    
    <div class="main-content">
        <div class="header"><h1>💰 Billing System</h1></div>
        
        <?php if($msg != ""): ?><div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div><?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card"><h3>Total Revenue (Paid)</h3><p>₦<?php echo number_format($total_revenue, 2); ?></p></div>
            <div class="stat-card pending"><h3>Pending Amount</h3><p>₦<?php echo number_format($pending_amount, 2); ?></p></div>
        </div>
        
        <div class="card">
            <h3>➕ Create New Bill</h3>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Patient</label>
                        <select name="patient_id" required>
                            <option value="">Select Patient</option>
                            <?php while($p = $patients->fetch_assoc()): ?>
                            <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Amount (₦)</label><input type="number" name="amount" step="0.01" required></div>
                    <div class="form-group"><label>Description</label><input type="text" name="description" required></div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="Pending">Pending</option>
                            <option value="Paid">Paid</option>
                        </select>
                    </div>
                    <div class="form-group"><button type="submit" name="add_bill">Create Bill</button></div>
                </div>
            </form>
        </div>
        
        <div class="card">
            <h3>📋 Billing History</h3>
            <table>
                <thead><tr><th>ID</th><th>Patient</th><th>Description</th><th>Amount</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php while($b = $bills->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $b['id']; ?></td>
                        <td><?php echo htmlspecialchars($b['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($b['description']); ?></td>
                        <td><strong>₦<?php echo number_format($b['amount'], 2); ?></strong></td>
                        <td><span class="badge bg-<?php echo strtolower($b['status']); ?>"><?php echo $b['status']; ?></span></td>
                        <td><?php echo date('d M Y', strtotime($b['bill_date'])); ?></td>
                        <td>
                            <a href="edit_bill.php?id=<?php echo $b['id']; ?>" class="btn-sm btn-primary">✏️ Edit</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="bill_id" value="<?php echo $b['id']; ?>">
                                <?php if($b['status'] == 'Pending'): ?>
                                <input type="hidden" name="new_status" value="Paid">
                                <button type="submit" name="update_status" class="btn-sm btn-success">Mark Paid</button>
                                <?php else: ?>
                                <input type="hidden" name="new_status" value="Pending">
                                <button type="submit" name="update_status" class="btn-sm btn-warning">Mark Pending</button>
                                <?php endif; ?>
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