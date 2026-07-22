<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkPatient();

$msg = ""; $msgType = "";
$patient_id = $_SESSION['patient_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book'])) {
    $doctor_id = !empty($_POST['doctor_id']) ? $_POST['doctor_id'] : NULL;
    $apt_date = $_POST['appointment_date'];
    $apt_time = $_POST['appointment_time'];
    $reason = $_POST['reason'];
    
    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("iisss", $patient_id, $doctor_id, $apt_date, $apt_time, $reason);
    
    if ($stmt->execute()) {
        $msg = "✅ Appointment request submitted! Admin will confirm shortly."; $msgType = "success";
    } else { $msg = "❌ Error: " . $conn->error; $msgType = "error"; }
}

$doctors = $conn->query("SELECT id, username FROM users WHERE role = 'doctor'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Appointment | Alfurqan Clinic</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f3f4f6; display: flex; }
        .sidebar { width: 260px; background: #312e81; color: white; height: 100vh; position: fixed; }
        .sidebar-header { text-align: center; padding: 25px 20px; border-bottom: 1px solid #4338ca; background: #1e1b4b; }
        .sidebar-header img { width: 70px; height: 70px; margin-bottom: 10px; background: white; border-radius: 50%; padding: 5px; }
        .sidebar-header h2 { font-size: 18px; } .sidebar-header small { font-size: 11px; opacity: 0.8; }
        .sidebar-menu a { display: flex; align-items: center; gap: 15px; padding: 15px 25px; color: #c7d2fe; text-decoration: none; border-bottom: 1px solid #4338ca; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #4338ca; color: white; padding-left: 30px; }
        .logout-btn { background: #dc2626 !important; justify-content: center; margin-top: 30px; }
        .main-content { margin-left: 260px; padding: 30px; width: calc(100% - 260px); }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e5e7eb; }
        .header h1 { color: #312e81; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 700px; }
        .card h3 { color: #312e81; margin-bottom: 20px; border-bottom: 2px solid #f3f4f6; padding-bottom: 15px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #374151; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #4f46e5; }
        button { padding: 12px 25px; background: #4f46e5; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        button:hover { background: #4338ca; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-success { background: #d1fae5; color: #065f46; } .alert-error { background: #fee2e2; color: #991b1b; }
        .info-box { background: #eff6ff; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #3b82f6; }
        .info-box p { color: #1e40af; font-size: 14px; margin: 0; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/logo.png" alt="Logo">
            <h2>PATIENT PORTAL</h2><small>Alfurqan Clinic</small>
        </div>
        <div class="sidebar-menu">
    <a href="dashboard.php">📊 Dashboard</a>
    <a href="symptoms_dashboard.php" class="active">🏥 Health Assessment</a>
    <a href="health_assessment.php"> New Assessment</a>
    <a href="book_appointment.php">📅 Book Appointment</a>
    <a href="my_appointments.php">📋 My Appointments</a>
    <a href="medical_history.php">🏥 Medical History</a>
    <a href="profile.php">👤 My Profile</a>
    <a href="logout.php" class="logout-btn"> Logout</a>
</div>
    </div>
    
    <div class="main-content">
        <div class="header"><h1>📅 Book an Appointment</h1></div>
        
        <?php if($msg != ""): ?><div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div><?php endif; ?>
        
        <div class="card">
            <h3>Request Appointment</h3>
            <div class="info-box">
                <p>ℹ️ Your request will be reviewed by our admin team. You will receive a confirmation once approved.</p>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label>Preferred Doctor (Optional)</label>
                    <select name="doctor_id">
                        <option value="">Any Available Doctor</option>
                        <?php while($d = $doctors->fetch_assoc()): ?>
                        <option value="<?php echo $d['id']; ?>">Dr. <?php echo htmlspecialchars($d['username']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Preferred Date *</label>
                    <input type="date" name="appointment_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label>Preferred Time *</label>
                    <select name="appointment_time" required>
                        <option value="">Select Time</option>
                        <option value="08:00:00">8:00 AM</option>
                        <option value="09:00:00">9:00 AM</option>
                        <option value="10:00:00">10:00 AM</option>
                        <option value="11:00:00">11:00 AM</option>
                        <option value="14:00:00">2:00 PM</option>
                        <option value="15:00:00">3:00 PM</option>
                        <option value="16:00:00">4:00 PM</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Reason for Visit *</label>
                    <textarea name="reason" rows="4" required placeholder="Describe your symptoms or reason..."></textarea>
                </div>
                <button type="submit" name="book">📅 Submit Request</button>
            </form>
        </div>
    </div>
</body>
</html>