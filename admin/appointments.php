<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkAdmin();

$msg = ""; $msgType = "";

// Confirm Appointment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_appointment'])) {
    $apt_id = $_POST['appointment_id'];
    $doc_id = $_POST['doctor_id'];
    $notes = $_POST['notes'];
    
    $stmt = $conn->prepare("UPDATE appointments SET status='Confirmed', doctor_id=?, confirmed_by=?, confirmed_at=NOW(), notes=? WHERE id=?");
    $stmt->bind_param("iisi", $doc_id, $_SESSION['user_id'], $notes, $apt_id);
    if ($stmt->execute()) { $msg = "✅ Appointment confirmed!"; $msgType = "success"; }
}

// Cancel Appointment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_appointment'])) {
    $apt_id = $_POST['appointment_id'];
    $stmt = $conn->prepare("UPDATE appointments SET status='Cancelled' WHERE id=?");
    $stmt->bind_param("i", $apt_id);
    if ($stmt->execute()) { $msg = " Appointment cancelled!"; $msgType = "error"; }
}

// Fetch Appointments
$status_filter = $_GET['status'] ?? '';
$query = "SELECT a.*, p.name as patient_name, p.phone, u.username as doctor_name 
          FROM appointments a 
          JOIN patients p ON a.patient_id = p.id 
          LEFT JOIN users u ON a.doctor_id = u.id WHERE 1=1";
if ($status_filter) { $query .= " AND a.status = '$status_filter'"; }
$query .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$appointments = $conn->query($query);

$doctors = $conn->query("SELECT id, username FROM users WHERE role='doctor'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointments | Alfurqan Clinic</title>
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
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; }
        .card h3 { color: #003366; margin-bottom: 20px; border-bottom: 2px solid #e8f4f8; padding-bottom: 15px; }
        .filter-bar { display: flex; gap: 15px; margin-bottom: 20px; }
        .filter-bar select { padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e8f4f8; }
        th { background: #003366; color: white; font-size: 13px; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; }
        .bg-pending { background: #ffc107; color: #000; } .bg-confirmed { background: #28a745; }
        .bg-cancelled { background: #dc3545; } .bg-completed { background: #17a2b8; }
        .btn-sm { padding: 6px 12px; font-size: 12px; margin-right: 5px; border: none; border-radius: 4px; cursor: pointer; color: white; }
        .btn-success { background: #28a745; } .btn-danger { background: #dc3545; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; } .alert-error { background: #f8d7da; color: #721c24; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 10% auto; padding: 30px; border-radius: 10px; width: 400px; }
        .close { float: right; font-size: 28px; cursor: pointer; }
        .form-group { margin-bottom: 15px; } label { display: block; margin-bottom: 5px; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        button { padding: 10px 20px; background: #003366; color: white; border: none; border-radius: 6px; cursor: pointer; }
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
            <a href="appointments.php" class="active"> Appointments</a>
            <a href="activity_logs.php">📋 Activity Logs</a>
            <a href="billing.php">💰 Billing System</a>
            <a href="../logout.php" class="logout-btn">🚪 Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="header"><h1>📅 Appointment Management</h1></div>
        
        <?php if($msg != ""): ?><div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div><?php endif; ?>
        
        <div class="card">
            <h3>All Appointments</h3>
            <div class="filter-bar">
                <form method="GET" style="display:flex; gap:10px;">
                    <select name="status" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="Pending" <?php echo $status_filter=='Pending'?'selected':''; ?>>Pending</option>
                        <option value="Confirmed" <?php echo $status_filter=='Confirmed'?'selected':''; ?>>Confirmed</option>
                        <option value="Cancelled" <?php echo $status_filter=='Cancelled'?'selected':''; ?>>Cancelled</option>
                        <option value="Completed" <?php echo $status_filter=='Completed'?'selected':''; ?>>Completed</option>
                    </select>
                </form>
            </div>
            
            <table>
                <thead><tr><th>ID</th><th>Patient</th><th>Date & Time</th><th>Doctor</th><th>Reason</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php while($a = $appointments->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $a['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($a['patient_name']); ?></strong><br><small><?php echo $a['phone']; ?></small></td>
                        <td><?php echo date('d M Y', strtotime($a['appointment_date'])); ?><br><small><?php echo date('g:i A', strtotime($a['appointment_time'])); ?></small></td>
                        <td><?php echo $a['doctor_name'] ? 'Dr. '.$a['doctor_name'] : '<span style="color:#999">Unassigned</span>'; ?></td>
                        <td><?php echo htmlspecialchars(substr($a['reason'], 0, 30)); ?>...</td>
                        <td><span class="badge bg-<?php echo strtolower($a['status']); ?>"><?php echo $a['status']; ?></span></td>
                        <td>
                            <?php if($a['status'] == 'Pending'): ?>
                            <button class="btn-sm btn-success" onclick="openConfirmModal(<?php echo $a['id']; ?>)">✅ Confirm</button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Cancel this appointment?');">
                                <input type="hidden" name="appointment_id" value="<?php echo $a['id']; ?>">
                                <button type="submit" name="cancel_appointment" class="btn-sm btn-danger">❌ Cancel</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeConfirmModal()">&times;</span>
            <h3 style="color:#003366; margin-bottom:20px;">Confirm Appointment</h3>
            <form method="POST">
                <input type="hidden" name="appointment_id" id="confirm_apt_id">
                <div class="form-group">
                    <label>Assign Doctor</label>
                    <select name="doctor_id" required>
                        <option value="">Select Doctor</option>
                        <?php while($d = $doctors->fetch_assoc()): ?>
                        <option value="<?php echo $d['id']; ?>">Dr. <?php echo htmlspecialchars($d['username']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group"><label>Notes</label><textarea name="notes" rows="3"></textarea></div>
                <button type="submit" name="confirm_appointment">Confirm Appointment</button>
            </form>
        </div>
    </div>

    <script>
        function openConfirmModal(id) { document.getElementById('confirm_apt_id').value = id; document.getElementById('confirmModal').style.display = 'block'; }
        function closeConfirmModal() { document.getElementById('confirmModal').style.display = 'none'; }
    </script>
</body>
</html>