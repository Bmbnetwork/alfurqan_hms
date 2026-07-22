<?php
session_start();
include '../config/db.php';
include '../config/functions.php';
checkAdmin();

// Get AI analytics
$total_assessments = $conn->query("SELECT COUNT(*) as total FROM patient_assessments")->fetch_assoc()['total'] ?? 0;
$total_warnings = $conn->query("SELECT COUNT(*) as total FROM prescription_warnings")->fetch_assoc()['total'] ?? 0;
$prevented_errors = $conn->query("SELECT COUNT(*) as total FROM prescription_warnings WHERE severity IN ('Critical', 'Severe')")->fetch_assoc()['total'] ?? 0;

// Most predicted diseases
$top_diseases = $conn->query("SELECT predicted_diseases, COUNT(*) as count FROM patient_assessments GROUP BY predicted_diseases ORDER BY count DESC LIMIT 5");

// High-risk patients
$high_risk = $conn->query("SELECT pa.*, p.name as patient_name FROM patient_assessments pa JOIN patients p ON pa.patient_id = p.id WHERE pa.risk_level IN ('High', 'Critical') ORDER BY pa.assessment_date DESC LIMIT 10");

// Common drug interactions
$common_interactions = $conn->query("SELECT pw.warning_message, COUNT(*) as count FROM prescription_warnings pw WHERE pw.warning_type = 'Drug Interaction' GROUP BY pw.warning_message ORDER BY count DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Analytics | Alfurqan Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .stat-card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stat-card h3 { font-size: 32px; font-weight: bold; margin-bottom: 10px; }
        .stat-card.primary h3 { color: #0d6efd; }
        .stat-card.success h3 { color: #198754; }
        .stat-card.warning h3 { color: #ffc107; }
        .stat-card.danger h3 { color: #dc3545; }
    </style>
</head>
<body>
    <div class="container py-4">
        <h2 class="mb-4">🤖 AI System Analytics</h2>
        
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card primary">
                    <h3><?= $total_assessments ?></h3>
                    <p class="text-muted mb-0">Total Assessments</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <h3><?= $total_warnings ?></h3>
                    <p class="text-muted mb-0">Total Warnings</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card danger">
                    <h3><?= $prevented_errors ?></h3>
                    <p class="text-muted mb-0">Errors Prevented</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <h3><?= $total_assessments > 0 ? round(($prevented_errors / max($total_warnings, 1)) * 100, 1) : 0 ?>%</h3>
                    <p class="text-muted mb-0">Safety Rate</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Most Predicted Diseases</h5>
                    </div>
                    <div class="card-body">
                        <?php while($disease = $top_diseases->fetch_assoc()): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?= htmlspecialchars($disease['predicted_diseases']) ?></span>
                            <span class="badge bg-primary"><?= $disease['count'] ?></span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Common Drug Interactions</h5>
                    </div>
                    <div class="card-body">
                        <?php while($interaction = $common_interactions->fetch_assoc()): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><?= htmlspecialchars($interaction['warning_message']) ?></span>
                            <span class="badge bg-warning"><?= $interaction['count'] ?></span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">High-Risk Patients</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Risk Level</th>
                            <th>Priority</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($patient = $high_risk->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($patient['patient_name']) ?></td>
                            <td><span class="badge bg-<?= $patient['risk_level'] == 'Critical' ? 'danger' : 'warning' ?>"><?= $patient['risk_level'] ?></span></td>
                            <td><?= $patient['priority_status'] ?></td>
                            <td><?= date('d M Y', strtotime($patient['assessment_date'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>