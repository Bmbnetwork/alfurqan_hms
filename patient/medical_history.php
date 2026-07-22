<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkPatient();

$patient_id = $_SESSION['patient_id'];

// Fetch Consultations
$consultations = $conn->query("SELECT c.*, u.username as doctor_name FROM consultations c JOIN users u ON c.doctor_id = u.id WHERE c.patient_id = $patient_id ORDER BY c.visit_date DESC");

// Fetch Lab Results
$lab_results = $conn->query("SELECT lr.*, lt.test_name, lres.result_value, lres.result_unit, lres.result_status 
                             FROM lab_requests lr 
                             JOIN lab_tests lt ON lr.test_id = lt.id 
                             LEFT JOIN lab_results lres ON lr.id = lres.request_id 
                             WHERE lr.patient_id = $patient_id AND lr.status = 'Completed' 
                             ORDER BY lr.request_date DESC");

// Fetch Prescriptions
$prescriptions = $conn->query("SELECT p.*, u.username as doctor_name FROM prescriptions p JOIN users u ON p.doctor_id = u.id WHERE p.patient_id = $patient_id ORDER BY p.prescribed_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical History | Alfurqan Clinic</title>
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
        .section { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .section h3 { color: #312e81; margin-bottom: 20px; border-bottom: 2px solid #f3f4f6; padding-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
        th { background: #f9fafb; color: #6b7280; font-size: 12px; text-transform: uppercase; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; color: white; font-weight: 600; }
        .bg-normal { background: #10b981; } .bg-abnormal { background: #f59e0b; } .bg-critical { background: #ef4444; }
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
        <div class="header"><h1>🏥 My Medical History</h1></div>
        
        <div class="section">
            <h3>🩺 Consultations</h3>
            <?php if ($consultations->num_rows > 0): ?>
            <table>
                <thead><tr><th>Date</th><th>Doctor</th><th>Symptoms</th><th>Diagnosis</th><th>Prescription</th></tr></thead>
                <tbody>
                    <?php while($c = $consultations->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($c['visit_date'])); ?></td>
                        <td>Dr. <?php echo htmlspecialchars($c['doctor_name']); ?></td>
                        <td><?php echo htmlspecialchars($c['symptoms']); ?></td>
                        <td><?php echo htmlspecialchars($c['diagnosis']); ?></td>
                        <td><?php echo htmlspecialchars($c['prescription']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="color: #6b7280; text-align: center; padding: 20px;">No consultation records found.</p>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h3>🔬 Laboratory Results</h3>
            <?php if ($lab_results->num_rows > 0): ?>
            <table>
                <thead><tr><th>Date</th><th>Test Name</th><th>Result</th><th>Status</th></tr></thead>
                <tbody>
                    <?php while($l = $lab_results->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($l['request_date'])); ?></td>
                        <td><?php echo htmlspecialchars($l['test_name']); ?></td>
                        <td><strong><?php echo htmlspecialchars($l['result_value'] ?? 'N/A'); ?></strong> <?php echo htmlspecialchars($l['result_unit'] ?? ''); ?></td>
                        <td><span class="badge bg-<?php echo strtolower($l['result_status'] ?? 'normal'); ?>"><?php echo ucfirst($l['result_status'] ?? 'Normal'); ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="color: #6b7280; text-align: center; padding: 20px;">No lab results found.</p>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h3>💊 Prescriptions</h3>
            <?php if ($prescriptions->num_rows > 0): ?>
            <table>
                <thead><tr><th>Date</th><th>Doctor</th><th>Drug</th><th>Dosage</th><th>Frequency</th><th>Instructions</th></tr></thead>
                <tbody>
                    <?php while($p = $prescriptions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($p['prescribed_date'])); ?></td>
                        <td>Dr. <?php echo htmlspecialchars($p['doctor_name']); ?></td>
                        <td><?php echo htmlspecialchars($p['drug_name']); ?></td>
                        <td><?php echo htmlspecialchars($p['dosage']); ?></td>
                        <td><?php echo htmlspecialchars($p['frequency']); ?></td>
                        <td><?php echo htmlspecialchars($p['instructions']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="color: #6b7280; text-align: center; padding: 20px;">No prescriptions found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>