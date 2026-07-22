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
    echo json_encode(['error' => 'Assessment not found']);
    exit();
}

$html = '
<div class="row">
    <div class="col-md-6">
        <h6 class="fw-bold">Patient Information</h6>
        <table class="table table-sm">
            <tr><td><strong>Age:</strong></td><td>' . $assessment['age'] . ' years</td></tr>
            <tr><td><strong>Gender:</strong></td><td>' . $assessment['gender'] . '</td></tr>
            <tr><td><strong>Weight:</strong></td><td>' . $assessment['weight_kg'] . ' kg</td></tr>
            <tr><td><strong>Date:</strong></td><td>' . date('d M Y, g:i A', strtotime($assessment['assessment_date'])) . '</td></tr>
        </table>
    </div>
    <div class="col-md-6">
        <h6 class="fw-bold">AI Assessment Results</h6>
        <table class="table table-sm">
            <tr><td><strong>Risk Level:</strong></td><td><span class="badge bg-' . ($assessment['risk_level'] == 'Critical' ? 'danger' : ($assessment['risk_level'] == 'High' ? 'warning' : 'success')) . '">' . $assessment['risk_level'] . '</span></td></tr>
            <tr><td><strong>Priority:</strong></td><td>' . $assessment['priority_status'] . '</td></tr>
            <tr><td><strong>Department:</strong></td><td>' . $assessment['recommended_department'] . '</td></tr>
        </table>
    </div>
</div>

<h6 class="fw-bold mt-4">Symptoms Reported</h6>
<div class="mb-3">
    ' . implode(' ', array_map(function($s) { return '<span class="badge bg-secondary me-1">' . htmlspecialchars($s) . '</span>'; }, explode(', ', $assessment['symptoms_text']))) . '
</div>

<h6 class="fw-bold">AI Disease Predictions</h6>
';

$predicted_diseases = json_decode($assessment['predicted_diseases'], true);
$confidence_scores = json_decode($assessment['confidence_scores'], true);

if ($predicted_diseases) {
    foreach ($predicted_diseases as $index => $disease) {
        $html .= '
        <div class="border rounded p-3 mb-2">
            <div class="d-flex justify-content-between">
                <strong>' . ($index + 1) . '. ' . htmlspecialchars($disease) . '</strong>
                <span class="text-primary fw-bold">' . ($confidence_scores[$index] ?? 0) . '%</span>
            </div>
            <div class="progress mt-2" style="height: 8px;">
                <div class="progress-bar" style="width: ' . ($confidence_scores[$index] ?? 0) . '%"></div>
            </div>
        </div>
        ';
    }
}

if ($assessment['medical_conditions']) {
    $html .= '<h6 class="fw-bold mt-4">Medical Conditions</h6><p>' . htmlspecialchars($assessment['medical_conditions']) . '</p>';
}

if ($assessment['allergies']) {
    $html .= '<h6 class="fw-bold">Allergies</h6><p>' . htmlspecialchars($assessment['allergies']) . '</p>';
}

if ($assessment['current_medications']) {
    $html .= '<h6 class="fw-bold">Current Medications</h6><p>' . htmlspecialchars($assessment['current_medications']) . '</p>';
}

if ($assessment['doctor_notes']) {
    $html .= '<h6 class="fw-bold mt-4">Doctor\'s Notes</h6><div class="alert alert-info">' . nl2br(htmlspecialchars($assessment['doctor_notes'])) . '</div>';
}

echo json_encode(['html' => $html]);
?>