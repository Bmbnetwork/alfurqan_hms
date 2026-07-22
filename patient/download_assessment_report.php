<?php
session_start();
include '../config/db.php';
include '../config/functions.php';
checkPatient();

$assessment_id = $_GET['id'] ?? 0;
$patient_id = $_SESSION['patient_id'];

$stmt = $conn->prepare("SELECT * FROM patient_assessments WHERE id = ? AND patient_id = ?");
$stmt->bind_param("ii", $assessment_id, $patient_id);
$stmt->execute();
$assessment = $stmt->get_result()->fetch_assoc();

if (!$assessment) {
    die('Assessment not found');
}

// Generate HTML report
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Health Assessment Report</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 40px; }
        .header { text-align: center; border-bottom: 3px solid #667eea; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #667eea; margin: 0; }
        .section { margin-bottom: 30px; }
        .section h3 { color: #333; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; }
        .risk-low { background: #d1e7dd; color: #0f5132; }
        .risk-medium { background: #fff3cd; color: #664d03; }
        .risk-high { background: #f8d7da; color: #842029; }
        .risk-critical { background: #dc3545; color: white; }
        .footer { text-align: center; margin-top: 50px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Alfurqan Clinic & Maternity Limited</h1>
        <h2>AI Health Assessment Report</h2>
        <p>Report ID: #' . $assessment_id . ' | Date: ' . date('d M Y, g:i A', strtotime($assessment['assessment_date'])) . '</p>
    </div>

    <div class="section">
        <h3>Patient Information</h3>
        <table>
            <tr><th>Name</th><td>' . htmlspecialchars($patient['name']) . '</td></tr>
            <tr><th>Age</th><td>' . $assessment['age'] . ' years</td></tr>
            <tr><th>Gender</th><td>' . $assessment['gender'] . '</td></tr>
            <tr><th>Weight</th><td>' . $assessment['weight_kg'] . ' kg</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>AI Assessment Results</h3>
        <table>
            <tr><th>Risk Level</th><td><span class="badge risk-' . strtolower($assessment['risk_level']) . '">' . $assessment['risk_level'] . '</span></td></tr>
            <tr><th>Priority Status</th><td>' . $assessment['priority_status'] . '</td></tr>
            <tr><th>Recommended Department</th><td>' . $assessment['recommended_department'] . '</td></tr>
        </table>
    </div>

    <div class="section">
        <h3>Symptoms Reported</h3>
        <p>' . htmlspecialchars($assessment['symptoms_text']) . '</p>
    </div>

    <div class="section">
        <h3>AI Disease Predictions</h3>
        <table>
            <tr><th>Disease</th><th>Confidence</th></tr>
';

$predicted_diseases = json_decode($assessment['predicted_diseases'], true);
$confidence_scores = json_decode($assessment['confidence_scores'], true);

if ($predicted_diseases) {
    foreach ($predicted_diseases as $index => $disease) {
        $html .= '<tr><td>' . ($index + 1) . '. ' . htmlspecialchars($disease) . '</td><td>' . ($confidence_scores[$index] ?? 0) . '%</td></tr>';
    }
}

$html .= '
        </table>
    </div>

    <div class="footer">
        <p><strong>Disclaimer:</strong> This AI assessment is for informational purposes only and should not replace professional medical diagnosis. Please consult with a healthcare provider for proper medical advice.</p>
        <p>Generated on ' . date('d M Y, g:i A') . ' | Alfurqan Clinic HMS</p>
    </div>
</body>
</html>
';

// Set headers for download
header('Content-Type: text/html');
header('Content-Disposition: attachment; filename="health_assessment_report_' . $assessment_id . '.html"');
echo $html;
?>