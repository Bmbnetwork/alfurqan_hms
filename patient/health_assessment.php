<?php
session_start();
include '../config/db.php';
include '../config/functions.php';
include '../config/ai_engine.php';
checkPatient();

$patient_id = $_SESSION['patient_id'];
$msg = "";
$msgType = "";
$predictions = null;

// Get patient info
$stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

// Get all symptoms
$symptoms = $conn->query("SELECT * FROM symptoms ORDER BY symptom_category, symptom_name");

// Handle assessment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_assessment'])) {
    $selected_symptoms = $_POST['symptoms'] ?? [];
    $custom_symptoms = $_POST['custom_symptoms'] ?? '';
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $weight = $_POST['weight'];
    $medical_conditions = $_POST['medical_conditions'];
    $allergies = $_POST['allergies'];
    $current_medications = $_POST['current_medications'];
    
    // Combine selected and custom symptoms
    $all_symptoms = $selected_symptoms;
    if ($custom_symptoms) {
        $custom_list = array_map('trim', explode(',', $custom_symptoms));
        $all_symptoms = array_merge($all_symptoms, $custom_list);
    }
    
    // Run AI prediction
    $ai_engine = new AIEngine($conn);
    $predictions = $ai_engine->predictDiseases($all_symptoms, $age, $gender, $weight, $medical_conditions, $allergies);
    
    // Save to database
    if (!isset($predictions['error'])) {
        $assessment_data = [
            'symptoms_text' => implode(', ', $all_symptoms),
            'age' => $age,
            'gender' => $gender,
            'weight_kg' => $weight,
            'medical_conditions' => $medical_conditions,
            'allergies' => $allergies,
            'current_medications' => $current_medications,
            'predicted_diseases' => json_encode(array_column($predictions['predictions'], 'disease_name')),
            'confidence_scores' => json_encode(array_column($predictions['predictions'], 'confidence')),
            'risk_level' => $predictions['risk_level'],
            'recommended_department' => $predictions['recommended_department'],
            'priority_status' => $predictions['priority_status']
        ];
        
        if ($ai_engine->saveAssessment($patient_id, $assessment_data)) {
            $msg = "✅ Health assessment completed and saved to your medical record!";
            $msgType = "success";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Assessment | Alfurqan Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .assessment-card { background: white; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; margin-bottom: 20px; }
        .symptom-checkbox { margin: 5px; }
        .symptom-checkbox label { background: #e9ecef; padding: 8px 15px; border-radius: 20px; cursor: pointer; transition: 0.3s; }
        .symptom-checkbox input:checked + label { background: #0d6efd; color: white; }
        .prediction-card { border-left: 4px solid #0d6efd; padding: 15px; margin-bottom: 15px; background: #f8f9fa; border-radius: 8px; }
        .confidence-bar { background: #e9ecef; border-radius: 10px; overflow: hidden; height: 20px; }
        .confidence-fill { background: linear-gradient(90deg, #0d6efd, #0dcaf0); height: 100%; transition: width 0.5s; }
        .risk-badge { padding: 8px 15px; border-radius: 20px; font-weight: 600; }
        .risk-low { background: #d1e7dd; color: #0f5132; }
        .risk-medium { background: #fff3cd; color: #664d03; }
        .risk-high { background: #f8d7da; color: #842029; }
        .risk-critical { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="assessment-card">
            <h2 class="mb-4"> AI Health Assessment</h2>
            <p class="text-muted">Complete this assessment to receive AI-powered health insights</p>
            
            <?php if($msg): ?>
                <div class="alert alert-<?= $msgType ?>"><?= $msg ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <!-- Symptoms Selection -->
                <div class="mb-4">
                    <label class="form-label fw-bold">Select Your Symptoms</label>
                    <div class="row">
                        <?php 
                        $current_category = '';
                        while($symptom = $symptoms->fetch_assoc()): 
                            if ($current_category != $symptom['symptom_category']):
                                if ($current_category != '') echo '</div>';
                                $current_category = $symptom['symptom_category'];
                                echo '<div class="col-md-6 mb-3"><h6 class="text-primary">' . $current_category . '</h6>';
                            endif;
                        ?>
                            <div class="symptom-checkbox">
                                <input type="checkbox" name="symptoms[]" value="<?= htmlspecialchars($symptom['symptom_name']) ?>" id="symptom_<?= $symptom['id'] ?>">
                                <label for="symptom_<?= $symptom['id'] ?>"><?= htmlspecialchars($symptom['symptom_name']) ?></label>
                            </div>
                        <?php endwhile; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Other Symptoms (comma-separated)</label>
                    <input type="text" name="custom_symptoms" class="form-control" placeholder="e.g., fatigue, dizziness">
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Age</label>
                        <input type="number" name="age" class="form-control" value="<?= $patient['age'] ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Gender</label>
                        <select name="gender" class="form-select" required>
                            <option value="Male" <?= $patient['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $patient['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Weight (kg)</label>
                        <input type="number" step="0.1" name="weight" class="form-control" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Existing Medical Conditions</label>
                    <textarea name="medical_conditions" class="form-control" rows="2" placeholder="e.g., diabetes, hypertension"></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Allergies</label>
                    <textarea name="allergies" class="form-control" rows="2" placeholder="e.g., penicillin, peanuts"></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Current Medications</label>
                    <textarea name="current_medications" class="form-control" rows="2" placeholder="e.g., metformin, lisinopril"></textarea>
                </div>
                
                <button type="submit" name="submit_assessment" class="btn btn-primary btn-lg w-100">
                    🤖 Run AI Assessment
                </button>
            </form>
        </div>
        
        <!-- AI Predictions Results -->
        <?php if ($predictions && !isset($predictions['error'])): ?>
        <div class="assessment-card">
            <h3 class="mb-4">📊 AI Prediction Results</h3>
            
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="text-center">
                        <h6 class="text-muted">Risk Level</h6>
                        <span class="risk-badge risk-<?= strtolower($predictions['risk_level']) ?>">
                            <?= $predictions['risk_level'] ?>
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h6 class="text-muted">Priority Status</h6>
                        <span class="badge bg-<?= $predictions['priority_status'] == 'Critical' ? 'danger' : ($predictions['priority_status'] == 'Urgent' ? 'warning' : 'success') ?> fs-6">
                            <?= $predictions['priority_status'] ?>
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h6 class="text-muted">Recommended Department</h6>
                        <p class="fw-bold text-primary"><?= $predictions['recommended_department'] ?></p>
                    </div>
                </div>
            </div>
            
            <h5 class="mb-3">Possible Diseases:</h5>
            <?php foreach ($predictions['predictions'] as $index => $prediction): ?>
            <div class="prediction-card">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0"><?= $index + 1 ?>. <?= htmlspecialchars($prediction['disease_name']) ?></h6>
                    <span class="badge bg-<?= $prediction['severity_level'] == 'Critical' ? 'danger' : ($prediction['severity_level'] == 'High' ? 'warning' : 'info') ?>">
                        <?= $prediction['severity_level'] ?>
                    </span>
                </div>
                <div class="confidence-bar mb-2">
                    <div class="confidence-fill" style="width: <?= $prediction['confidence'] ?>%"></div>
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">Confidence: <strong><?= $prediction['confidence'] ?>%</strong></small>
                    <small class="text-muted">Matched: <?= $prediction['matched_symptoms'] ?>/<?= $prediction['total_symptoms'] ?> symptoms</small>
                </div>
                <p class="mt-2 mb-0 small"><?= htmlspecialchars($prediction['description']) ?></p>
            </div>
            <?php endforeach; ?>
            
            <div class="alert alert-info mt-4">
                <strong>⚠️ Disclaimer:</strong> This AI assessment is for informational purposes only and should not replace professional medical diagnosis. Please consult with a healthcare provider for proper medical advice.
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>