<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkAdmin();

$msg = "";
$msgType = "";

// Add User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        $msg = "❌ Username already exists!"; $msgType = "error";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed, $role);
        if ($stmt->execute()) {
            $msg = "✅ User added successfully!"; $msgType = "success";
            logActivity($conn, $_SESSION['user_id'], $_SESSION['username'], 'ADD_USER', "Added user: $username ($role)");
        } else { $msg = "❌ Error: " . $conn->error; $msgType = "error"; }
    }
}

// Delete User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $uid = $_POST['user_id'];
    if ($uid == $_SESSION['user_id']) { $msg = "❌ Cannot delete yourself!"; $msgType = "error"; }
    else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $uid);
        if ($stmt->execute()) { $msg = "✅ User deleted!"; $msgType = "success"; } 
        else { $msg = " Error: " . $conn->error; $msgType = "error"; }
    }
}

// Reset Password
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $uid = $_POST['user_id'];
    $new_pass = $_POST['new_password'];
    $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed, $uid);
    if ($stmt->execute()) { $msg = "✅ Password reset successfully!"; $msgType = "success"; }
}

$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users | Alfurqan Clinic</title>
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
        .form-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; }
        button { padding: 12px 25px; background: #003366; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .btn-sm { padding: 6px 12px; font-size: 12px; margin-right: 5px; }
        .btn-danger { background: #dc3545; } .btn-warning { background: #ffc107; color: #000; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e8f4f8; }
        th { background: #003366; color: white; font-size: 13px; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; }
        .badge-admin { background: #003366; } .badge-doctor { background: #28a745; }
        .badge-nurse { background: #17a2b8; } .badge-pharmacist { background: #e83e8c; }
        .badge-lab_technician { background: #6f42c1; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; } .alert-error { background: #f8d7da; color: #721c24; }
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 10% auto; padding: 30px; border-radius: 10px; width: 400px; }
        .close { float: right; font-size: 28px; cursor: pointer; }
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
        <div class="header"><h1>👥 Manage Users</h1></div>
        
        <?php if($msg != ""): ?><div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div><?php endif; ?>
        
        <div class="card">
            <h3>➕ Add New User</h3>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
                    <div class="form-group"><label>Password</label><input type="password" name="password" required minlength="6"></div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" required>
                            <option value="admin">Admin</option>
                            <option value="doctor">Doctor</option>
                            <option value="nurse">Nurse</option>
                            <option value="pharmacist">Pharmacist</option>
                            <option value="lab_technician">Lab Technician</option>
                        </select>
                    </div>
                </div>
                <button type="submit" name="add_user">Create User</button>
            </form>
        </div>
        
        <div class="card">
            <h3>📋 System Users</h3>
            <table>
                <thead><tr><th>ID</th><th>Username</th><th>Role</th><th>Created</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php while($u = $users->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                        <td><span class="badge badge-<?php echo $u['role']; ?>"><?php echo ucfirst(str_replace('_', ' ', $u['role'])); ?></span></td>
                        <td><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                        <td>
                            <button class="btn-sm btn-warning" onclick="openResetModal(<?php echo $u['id']; ?>, '<?php echo htmlspecialchars($u['username']); ?>')">🔑 Reset</button>
                            <?php if($u['id'] != $_SESSION['user_id']): ?>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this user?');">
                                <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                <button type="submit" name="delete_user" class="btn-sm btn-danger">🗑️ Delete</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div id="resetModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeResetModal()">&times;</span>
            <h3 style="color:#003366; margin-bottom:20px;">Reset Password</h3>
            <form method="POST">
                <input type="hidden" name="user_id" id="reset_uid">
                <p style="margin-bottom:15px;">Resetting password for: <strong id="reset_name"></strong></p>
                <div class="form-group"><label>New Password</label><input type="password" name="new_password" required minlength="6"></div>
                <button type="submit" name="reset_password">Reset Password</button>
            </form>
        </div>
    </div>

    <script>
        function openResetModal(id, name) {
            document.getElementById('reset_uid').value = id;
            document.getElementById('reset_name').textContent = name;
            document.getElementById('resetModal').style.display = 'block';
        }
        function closeResetModal() { document.getElementById('resetModal').style.display = 'none'; }
    </script>
</body>
</html>