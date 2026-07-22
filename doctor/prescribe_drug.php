<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';
checkDoctor();

$msg = ""; 
$msgType = "";
$doctor_id = $_SESSION['user_id'];
$allergy_warning_msg = "";

// Fetch all patients WITH their allergies for the dropdown
$patients = $conn->query("SELECT id, name, allergies FROM patients ORDER BY name ASC");

// Handle Prescription Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['prescribe'])) {
    $patient_id = intval($_POST['patient_id']);
    $drug_ids = $_POST['drug_ids'] ?? [];
    $dosages = $_POST['dosages'] ?? [];
    $frequencies = $_POST['frequencies'] ?? [];
    $durations = $_POST['durations'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $instructions = $_POST['instructions'];
    
    // 1. Fetch Patient Allergies
    $p_stmt = $conn->prepare("SELECT allergies FROM patients WHERE id = ?");
    $p_stmt->bind_param("i", $patient_id);
    $p_stmt->execute();
    $patient_allergies_raw = $p_stmt->get_result()->fetch_assoc()['allergies'] ?? '';
    $patient_allergies = strtolower($patient_allergies_raw);
    $allergy_list = array_filter(array_map('trim', explode(',', $patient_allergies)));
    
    // 2. Check Prescribed Drugs against Allergies (Backend Monitoring)
    foreach ($drug_ids as $index => $drug_id) {
        $drug_id = intval($drug_id);
        $d_stmt = $conn->prepare("SELECT drug_name, category FROM drugs WHERE id = ?");
        $d_stmt->bind_param("i", $drug_id);
        $d_stmt->execute();
        $drug = $d_stmt->get_result()->fetch_assoc();
        
        if ($drug) {
            $drug_name_lower = strtolower($drug['drug_name']);
            $drug_cat_lower = strtolower($drug['category']);
            
            foreach ($allergy_list as $allergy) {
                if (!empty($allergy) && (stripos($drug_name_lower, $allergy) !== false || stripos($drug_cat_lower, $allergy) !== false)) {
                    $allergy_warning_msg .= "🚨 <strong>CRITICAL ALLERGY ALERT:</strong> Patient is allergic to <strong>{$allergy}</strong>. You are prescribing <strong>{$drug['drug_name']}</strong>.<br>";
                }
            }
        }
    }

    // 3. Save Prescription ONLY if no allergy conflicts
    if (empty($allergy_warning_msg) && !empty($drug_ids)) {
        $success = true;
        foreach ($drug_ids as $index => $drug_id) {
            $drug_id = intval($drug_id);
            $drug = $conn->query("SELECT drug_name FROM drugs WHERE id = $drug_id")->fetch_assoc();
            
            $dosage = $dosages[$index] ?? '';
            $frequency = $frequencies[$index] ?? '';
            $duration = $durations[$index] ?? '';
            $quantity = intval($quantities[$index] ?? 0);
            
            $stmt = $conn->prepare("INSERT INTO prescriptions (patient_id, doctor_id, drug_id, drug_name, dosage, frequency, duration, quantity, instructions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iiissssis", $patient_id, $doctor_id, $drug_id, $drug['drug_name'], $dosage, $frequency, $duration, $quantity, $instructions);
            
            if (!$stmt->execute()) {
                $success = false;
                break;
            }
        }
        
        if ($success) {
            $msg = "✅ Prescription created successfully!";
            $msgType = "success";
            logActivity($conn, $doctor_id, $_SESSION['username'], 'PRESCRIBE_DRUG', "Prescribed medications for patient ID: $patient_id");
        } else {
            $msg = " Error creating prescription!";
            $msgType = "error";
        }
    } elseif (!empty($allergy_warning_msg)) {
        $msg = "❌ Prescription Blocked due to Allergy Conflict!";
        $msgType = "error";
    }
}

// Fetch recent prescriptions for the table
$prescriptions = $conn->query("SELECT p.*, pt.name as patient_name FROM prescriptions p JOIN patients pt ON p.patient_id = pt.id WHERE p.doctor_id = $doctor_id ORDER BY p.prescribed_date DESC LIMIT 10");

// Reset patient pointer for the form dropdown
$patients->data_seek(0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescriptions | Alfurqan Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #e8f5e9; display: flex; }
        .sidebar { width: 260px; background: #1b5e20; color: white; height: 100vh; position: fixed; }
        .sidebar-header { text-align: center; padding: 25px 20px; border-bottom: 1px solid #2e7d32; background: #144a18; }
        .sidebar-header img { width: 70px; height: 70px; margin-bottom: 10px; background: white; border-radius: 50%; padding: 5px; }
        .sidebar-header h2 { font-size: 18px; } .sidebar-header small { font-size: 11px; opacity: 0.8; }
        .sidebar-menu a { display: flex; align-items: center; gap: 15px; padding: 15px 25px; color: #c8e6c9; text-decoration: none; border-bottom: 1px solid #2e7d32; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #2e7d32; color: white; padding-left: 30px; }
        .logout-btn { background: #c62828 !important; justify-content: center; margin-top: 30px; }
        .main-content { margin-left: 260px; padding: 30px; width: calc(100% - 260px); }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #c8e6c9; }
        .header h1 { color: #1b5e20; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 25px; }
        .card h3 { color: #1b5e20; margin-bottom: 20px; border-bottom: 2px solid #e8f5e9; padding-bottom: 15px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #333; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        button { padding: 12px 25px; background: #1b5e20; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
        button:hover { background: #144a18; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #e8f5e9; }
        th { background: #1b5e20; color: white; font-size: 13px; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; color: white; }
        .bg-pending { background: #f57c00; } .bg-dispensed { background: #2e7d32; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; } .alert-error { background: #f8d7da; color: #721c24; }
        
        /* Allergy Alert Box */
        .allergy-alert { 
            background: #fff3cd; color: #856404; padding: 15px; border-radius: 6px; 
            border-left: 5px solid #ffc107; margin-bottom: 20px; display: none; 
            font-weight: 600; align-items: center; gap: 10px;
        }
        .allergy-alert i { font-size: 20px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/logo.png" alt="Logo">
            <h2>DOCTOR PORTAL</h2><small>Alfurqan Clinic</small>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="consultation.php">🩺 Consultation</a>
            <a href="antenatal.php">🤰 Antenatal Care</a>
            <a href="request_lab_test.php"> Lab Requests</a>
            <a href="view_lab_results.php">📈 View Lab Results</a>
            <a href="prescribe_drug.php" class="active">💊 Prescriptions</a>
            <a href="../logout.php" class="logout-btn">🚪 Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="header"><h1>💊 Prescribe Medication</h1></div>
        
        <?php if($msg != ""): ?><div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div><?php endif; ?>
        
        <div class="card">
            <h3>New Prescription</h3>
            <form method="POST" id="prescriptionForm">
                <div class="form-group">
                    <label>Select Patient *</label>
                    <select name="patient_id" id="patientSelect" required onchange="showPatientAllergies()">
                        <option value="">-- Choose Patient --</option>
                        <?php while($p = $patients->fetch_assoc()): 
                            $allergies_data = htmlspecialchars($p['allergies'] ?? '');
                        ?>
                        <option value="<?php echo $p['id']; ?>" data-allergies="<?php echo $allergies_data; ?>">
                            <?php echo htmlspecialchars($p['name']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Dynamic Allergy Alert Box -->
                <div id="allergyAlertBox" class="allergy-alert">
                    <i>️</i> 
                    <span>Patient Allergies: <strong id="allergyText" style="color: #d32f2f;"></strong></span>
                </div>
                
                <div id="drugContainer">
                    <div class="drug-row form-row">
                        <div class="form-group">
                            <label>Drug Name *</label>
                            <input type="text" name="drug_names[]" class="form-control" placeholder="e.g., Paracetamol" required>
                        </div>
                        <div class="form-group">
                            <label>Dosage *</label>
                            <input type="text" name="dosages[]" placeholder="e.g., 500mg" required>
                        </div>
                    </div>
                    <div class="drug-row form-row">
                        <div class="form-group">
                            <label>Frequency *</label>
                            <input type="text" name="frequencies[]" placeholder="e.g., Twice daily" required>
                        </div>
                        <div class="form-group">
                            <label>Duration</label>
                            <input type="text" name="durations[]" placeholder="e.g., 5 days">
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn btn-secondary" style="background:#6c757d; margin-bottom:20px;" onclick="addDrugRow()">+ Add Another Drug</button>
                
                <div class="form-group">
                    <label>Instructions / Notes</label>
                    <textarea name="instructions" rows="3"></textarea>
                </div>
                <button type="submit" name="prescribe">💊 Create Prescription</button>
            </form>
        </div>
        
        <div class="card">
            <h3>My Recent Prescriptions</h3>
            <table>
                <thead><tr><th>Date</th><th>Patient</th><th>Drug</th><th>Dosage</th><th>Frequency</th><th>Status</th></tr></thead>
                <tbody>
                    <?php while($rx = $prescriptions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($rx['prescribed_date'])); ?></td>
                        <td><?php echo htmlspecialchars($rx['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($rx['drug_name']); ?></td>
                        <td><?php echo htmlspecialchars($rx['dosage']); ?></td>
                        <td><?php echo htmlspecialchars($rx['frequency']); ?></td>
                        <td><span class="badge bg-<?php echo strtolower($rx['status']); ?>"><?php echo $rx['status']; ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // 1. Show Allergies when patient is selected
        function showPatientAllergies() {
            const select = document.getElementById('patientSelect');
            const selectedOption = select.options[select.selectedIndex];
            const allergies = selectedOption.getAttribute('data-allergies');
            const alertBox = document.getElementById('allergyAlertBox');
            const allergyText = document.getElementById('allergyText');

            if (allergies && allergies.trim() !== "") {
                allergyText.textContent = allergies;
                alertBox.style.display = 'flex';
            } else {
                alertBox.style.display = 'none';
            }
        }

        // 2. Add more drug rows dynamically
        let drugCount = 1;
        function addDrugRow() {
            drugCount++;
            const container = document.getElementById('drugContainer');
            const newRow = document.createElement('div');
            newRow.className = 'drug-row form-row';
            newRow.style.borderTop = '1px solid #eee';
            newRow.style.paddingTop = '15px';
            newRow.style.marginTop = '10px';
            newRow.innerHTML = `
                <div class="form-group">
                    <label>Drug Name ${drugCount} *</label>
                    <input type="text" name="drug_names[]" class="form-control" placeholder="e.g., Amoxicillin" required>
                </div>
                <div class="form-group">
                    <label>Dosage *</label>
                    <input type="text" name="dosages[]" placeholder="e.g., 500mg" required>
                </div>
                <div class="form-group">
                    <label>Frequency *</label>
                    <input type="text" name="frequencies[]" placeholder="e.g., 3 times daily" required>
                </div>
                <div class="form-group">
                    <label>Duration</label>
                    <input type="text" name="durations[]" placeholder="e.g., 7 days">
                </div>
            `;
            container.appendChild(newRow);
        }

        // 3. Block submission if backend detected an allergy conflict
        <?php if (!empty($allergy_warning_msg)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Prescription Blocked!',
            html: `<?php echo $allergy_warning_msg; ?><br><br>Please select an alternative medication.`,
            confirmButtonText: 'Understood',
            confirmButtonColor: '#d33'
        });
        <?php endif; ?>
    </script>
</body>
</html>