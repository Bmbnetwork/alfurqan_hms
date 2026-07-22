<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/db.php';
include '../config/functions.php';

// Security check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lab_technician') { 
    die("Access Denied");
}

$request_id = $_GET['id'] ?? 0;

$stmt = $conn->prepare("SELECT lr.*, lt.test_name, lt.category, lt.sample_type, 
                        p.name as patient_name, p.age, p.gender, p.phone, p.address,
                        u.username as doctor_name,
                        lres.result_value, lres.result_unit, lres.reference_min, lres.reference_max, 
                        lres.result_status, lres.notes as result_notes, lres.result_date,
                        tech.username as technician_name
                        FROM lab_requests lr 
                        JOIN lab_tests lt ON lr.test_id = lt.id 
                        JOIN patients p ON lr.patient_id = p.id 
                        JOIN users u ON lr.doctor_id = u.id 
                        LEFT JOIN lab_results lres ON lr.id = lres.request_id 
                        LEFT JOIN users tech ON lres.technician_id = tech.id 
                        WHERE lr.id = ?");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$lab = $stmt->get_result()->fetch_assoc();

if (!$lab) { die("Report not found."); }

$report_id = 'LAB-' . date('Y') . '-' . str_pad($lab['id'], 6, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lab Report - <?php echo $report_id; ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f5f5f5; padding: 20px; }
        .print-container { max-width: 800px; margin: 0 auto; background: white; padding: 40px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 3px solid #4a148c; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { width: 80px; height: 80px; margin-bottom: 15px; }
        .clinic-name { font-size: 28px; color: #4a148c; font-weight: 700; margin-bottom: 5px; }
        .clinic-address { color: #666; font-size: 14px; margin-bottom: 5px; }
        .report-title { background: #4a148c; color: white; padding: 15px; text-align: center; font-size: 24px; font-weight: 600; margin: 20px 0; border-radius: 8px; }
        .report-info { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; background: #f3e5f5; padding: 20px; border-radius: 8px; border-left: 4px solid #4a148c; }
        .info-group { margin-bottom: 15px; }
        .info-group label { display: block; color: #666; font-size: 12px; text-transform: uppercase; margin-bottom: 5px; }
        .info-group .value { color: #333; font-size: 16px; font-weight: 600; }
        .patient-section { background: #e8f5e9; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #2e7d32; }
        .patient-section h3 { color: #2e7d32; margin-bottom: 15px; font-size: 18px; }
        .result-section { margin-bottom: 30px; }
        .result-section h3 { color: #4a148c; margin-bottom: 15px; font-size: 18px; border-bottom: 2px solid #4a148c; padding-bottom: 10px; }
        .result-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .result-table th, .result-table td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e0e0e0; }
        .result-table th { background: #f3e5f5; color: #4a148c; font-weight: 600; font-size: 13px; text-transform: uppercase; }
        .status-normal { color: #2e7d32; font-weight: 600; }
        .status-abnormal { color: #f57c00; font-weight: 600; }
        .status-critical { color: #c62828; font-weight: 600; }
        .notes-section { background: #fff3cd; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #ffc107; }
        .notes-section h4 { color: #856404; margin-bottom: 10px; }
        .notes-section p { color: #856404; line-height: 1.6; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 2px solid #e0e0e0; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        .signature-box { text-align: center; padding: 20px; }
        .signature-line { border-top: 2px solid #333; margin-top: 40px; padding-top: 10px; }
        .signature-name { font-weight: 600; color: #333; }
        .signature-title { color: #666; font-size: 14px; }
        .disclaimer { margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 8px; font-size: 12px; color: #666; text-align: center; line-height: 1.6; }
        .print-button { position: fixed; top: 20px; right: 20px; padding: 15px 30px; background: #4a148c; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; box-shadow: 0 4px 15px rgba(74, 20, 140, 0.4); z-index: 1000; }
        .back-button { position: fixed; top: 20px; left: 20px; padding: 15px 30px; background: #6c757d; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; z-index: 1000; }
        @media print { body { background: white; padding: 0; } .print-container { box-shadow: none; padding: 20px; } .print-button, .back-button { display: none !important; } }
    </style>
</head>
<body>
    <a href="javascript:window.history.back();" class="back-button">← Back</a>
    <button onclick="window.print()" class="print-button">🖨️ Print Report</button>

    <div class="print-container">
        <div class="header">
            <img src="../assets/logo.png" alt="Alfurqan Clinic" class="logo">
            <div class="clinic-name">ALFURQAN CLINIC & MATERNITY LIMITED</div>
            <div class="clinic-address">Bauchi, Nigeria</div>
            <div class="clinic-contact"> 0913-781-4650 | ✉️ info@alfurqanclinic.com</div>
        </div>
        
        <div class="report-title"> LABORATORY TEST REPORT</div>
        
        <div class="report-info">
            <div>
                <div class="info-group"><label>Report ID</label><div class="value"><?php echo $report_id; ?></div></div>
                <div class="info-group"><label>Request ID</label><div class="value">#<?php echo $lab['id']; ?></div></div>
                <div class="info-group"><label>Test Name</label><div class="value"><?php echo htmlspecialchars($lab['test_name']); ?></div></div>
                <div class="info-group"><label>Category</label><div class="value"><?php echo htmlspecialchars($lab['category']); ?></div></div>
            </div>
            <div>
                <div class="info-group"><label>Request Date</label><div class="value"><?php echo date('d M Y, g:i A', strtotime($lab['request_date'])); ?></div></div>
                <div class="info-group"><label>Result Date</label><div class="value"><?php echo $lab['result_date'] ? date('d M Y, g:i A', strtotime($lab['result_date'])) : 'Pending'; ?></div></div>
                <div class="info-group"><label>Priority</label><div class="value"><?php echo $lab['priority']; ?></div></div>
                <div class="info-group"><label>Sample Type</label><div class="value"><?php echo htmlspecialchars($lab['sample_type']); ?></div></div>
            </div>
        </div>
        
        <div class="patient-section">
            <h3>👤 Patient Information</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="info-group"><label>Full Name</label><div class="value"><?php echo htmlspecialchars($lab['patient_name']); ?></div></div>
                <div class="info-group"><label>Age / Gender</label><div class="value"><?php echo $lab['age']; ?> years / <?php echo $lab['gender']; ?></div></div>
                <div class="info-group"><label>Phone Number</label><div class="value"><?php echo $lab['phone']; ?></div></div>
                <div class="info-group"><label>Address</label><div class="value"><?php echo htmlspecialchars($lab['address']); ?></div></div>
            </div>
        </div>
        
        <div class="result-section">
            <h3>📊 Test Result</h3>
            <table class="result-table">
                <thead>
                    <tr><th>Test Parameter</th><th>Result</th><th>Unit</th><th>Reference Range</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($lab['test_name']); ?></strong></td>
                        <td style="font-size: 18px; font-weight: 600;"><?php echo htmlspecialchars($lab['result_value'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($lab['result_unit'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($lab['reference_min'] ?? ''); ?> - <?php echo htmlspecialchars($lab['reference_max'] ?? ''); ?></td>
                        <td>
                            <?php
                            $statusClass = 'status-normal'; $statusIcon = '✅';
                            if($lab['result_status'] == 'Abnormal') { $statusClass = 'status-abnormal'; $statusIcon = '⚠️'; }
                            if($lab['result_status'] == 'Critical') { $statusClass = 'status-critical'; $statusIcon = '🚨'; }
                            ?>
                            <span class="<?php echo $statusClass; ?>"><?php echo $statusIcon; ?> <?php echo $lab['result_status'] ?? 'Pending'; ?></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <?php if (!empty($lab['result_notes']) || !empty($lab['clinical_notes'])): ?>
        <div class="notes-section">
            <h4>📝 Notes & Comments</h4>
            <?php if (!empty($lab['clinical_notes'])): ?><p><strong>Clinical Notes:</strong> <?php echo nl2br(htmlspecialchars($lab['clinical_notes'])); ?></p><?php endif; ?>
            <?php if (!empty($lab['result_notes'])): ?><p style="margin-top: 10px;"><strong>Lab Notes:</strong> <?php echo nl2br(htmlspecialchars($lab['result_notes'])); ?></p><?php endif; ?>
        </div>
        <?php endif; ?>
        
        <div class="footer">
            <div class="signature-box">
                <div class="signature-line">
                    <div class="signature-name">Dr. <?php echo htmlspecialchars($lab['doctor_name']); ?></div>
                    <div class="signature-title">Requesting Physician</div>
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    <div class="signature-name"><?php echo htmlspecialchars($lab['technician_name'] ?? 'Lab Technician'); ?></div>
                    <div class="signature-title">Laboratory Technician</div>
                </div>
            </div>
        </div>
        
        <div class="disclaimer">
            <strong>⚠️ DISCLAIMER:</strong> This report is for the use of the requesting physician and patient only. Results should be interpreted in conjunction with clinical findings.
            <br><br>
            <em>Generated on <?php echo date('d M Y, g:i A'); ?> | Report ID: <?php echo $report_id; ?></em>
        </div>
    </div>
</body>
</html>