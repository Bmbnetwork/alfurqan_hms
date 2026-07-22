<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkAdmin();

$msg = "";
$msgType = "";

// Get Patient ID
$patient_id = $_GET['id'] ?? 0;

// Fetch Patient Data
$stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

if (!$patient) {
    header("Location: patients.php");
    exit();
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_patient'])) {
    $name = trim($_POST['name']);
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $status = $_POST['patient_status'];
    
    $stmt = $conn->prepare("UPDATE patients SET name=?, age=?, gender=?, phone=?, address=?, allergies=?, patient_status=? WHERE id=?");
$allergies = trim($_POST['allergies']);
$stmt->bind_param("sisssssi", $name, $age, $gender, $phone, $address, $allergies, $status, $patient_id);
    
    if ($stmt->execute()) {
        $msg = "✅ Patient updated successfully!";
        $msgType = "success";
        logActivity($conn, $_SESSION['user_id'], $_SESSION['username'], 'EDIT_PATIENT', "Updated patient: $name");
        // Refresh data
        $stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $patient = $stmt->get_result()->fetch_assoc();
    } else {
        $msg = "❌ Error: " . $conn->error;
        $msgType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Patient | Alfurqan Clinic</title>
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
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); max-width: 800px; }
        .card h3 { color: #003366; margin-bottom: 20px; border-bottom: 2px solid #e8f4f8; padding-bottom: 15px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        button { padding: 12px 25px; background: #003366; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        .btn-secondary { background: #6c757d; margin-left: 10px; text-decoration: none; display: inline-block; padding: 12px 25px; border-radius: 6px; color: white;}
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
    <a href="patients.php"> Patients</a>
    <a href="../logout.php" class="logout-btn">🚪 Logout</a>
</div>
    </div>
    
    <div class="main-content">
        <div class="header"><h1>️ Edit Patient Information</h1></div>
        
        <?php if($msg != ""): ?>
            <div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <h3>Patient Details (ID: #<?php echo $patient['id']; ?>)</h3>
            <form method="POST">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($patient['name']); ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Age *</label>
                        <input type="number" name="age" value="<?php echo $patient['age']; ?>" required min="1" max="120">
                    </div>
                    <div class="form-group">
                        <label>Gender *</label>
                        <select name="gender" required>
                            <option value="Male" <?php echo $patient['gender']=='Male'?'selected':''; ?>>Male</option>
                            <option value="Female" <?php echo $patient['gender']=='Female'?'selected':''; ?>>Female</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($patient['phone']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" rows="3"><?php echo htmlspecialchars($patient['address']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Patient Status</label>
                    <select name="patient_status">
                        <option value="Pending" <?php echo $patient['patient_status']=='Pending'?'selected':''; ?>>Pending</option>
                        <option value="Stable" <?php echo $patient['patient_status']=='Stable'?'selected':''; ?>>Stable</option>
                        <option value="Recovering" <?php echo $patient['patient_status']=='Recovering'?'selected':''; ?>>Recovering</option>
                        <option value="Critical" <?php echo $patient['patient_status']=='Critical'?'selected':''; ?>>Critical</option>
                        <option value="Discharged" <?php echo $patient['patient_status']=='Discharged'?'selected':''; ?>>Discharged</option>
                    </select>
                </div>
                <div class="form-group">
    <label>Allergies (Comma separated)</label>
    <textarea name="allergies" rows="2" placeholder="e.g., Penicillin, Peanuts, Latex, Sulfa"><?php echo htmlspecialchars($patient['allergies'] ?? ''); ?></textarea>
    <small style="color: #666;">Separate multiple allergies with commas.</small>
</div>
                <button type="submit" name="update_patient">💾 Update Patient</button>
                <a href="patients.php" class="btn-secondary">← Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>