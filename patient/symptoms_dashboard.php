<?php
session_start();
include '../config/db.php';
include '../config/functions.php';
checkPatient();

$patient_id = $_SESSION['patient_id'];

// Get patient info
$stmt = $conn->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

// Get assessment statistics
$total_assessments = $conn->query("SELECT COUNT(*) as total FROM patient_assessments WHERE patient_id = $patient_id")->fetch_assoc()['total'] ?? 0;
$high_risk_count = $conn->query("SELECT COUNT(*) as total FROM patient_assessments WHERE patient_id = $patient_id AND risk_level IN ('High', 'Critical')")->fetch_assoc()['total'] ?? 0;
$recent_assessment = $conn->query("SELECT * FROM patient_assessments WHERE patient_id = $patient_id ORDER BY assessment_date DESC LIMIT 1")->fetch_assoc();

// Get all assessments
$assessments = $conn->query("SELECT * FROM patient_assessments WHERE patient_id = $patient_id ORDER BY assessment_date DESC");

// Get most common symptoms
$common_symptoms_query = "SELECT symptoms_text, COUNT(*) as count FROM patient_assessments WHERE patient_id = $patient_id GROUP BY symptoms_text ORDER BY count DESC LIMIT 5";
$common_symptoms = $conn->query($common_symptoms_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Health Dashboard | Alfurqan Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .dashboard-card { background: white; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); padding: 25px; margin-bottom: 20px; }
        .stat-box { text-align: center; padding: 20px; border-radius: 10px; }
        .stat-box.primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .stat-box.success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: white; }
        .stat-box.warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
        .stat-box.danger { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; }
        .stat-box h3 { font-size: 36px; font-weight: bold; margin-bottom: 5px; }
        .stat-box p { margin: 0; font-size: 14px; opacity: 0.9; }
        .assessment-item { border-left: 4px solid #667eea; padding: 15px; margin-bottom: 15px; background: #f8f9fa; border-radius: 8px; transition: 0.3s; }
        .assessment-item:hover { transform: translateX(5px); box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .risk-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .risk-low { background: #d1e7dd; color: #0f5132; }
        .risk-medium { background: #fff3cd; color: #664d03; }
        .risk-high { background: #f8d7da; color: #842029; }
        .risk-critical { background: #dc3545; color: white; }
        .symptom-tag { display: inline-block; background: #e9ecef; padding: 5px 12px; border-radius: 15px; margin: 3px; font-size: 13px; }
        .disease-prediction { background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px; border: 1px solid #e9ecef; }
        .confidence-bar { background: #e9ecef; border-radius: 10px; overflow: hidden; height: 8px; margin-top: 8px; }
        .confidence-fill { background: linear-gradient(90deg, #667eea, #764ba2); height: 100%; transition: width 0.5s; }
        .timeline-item { position: relative; padding-left: 30px; margin-bottom: 20px; }
        .timeline-item::before { content: ''; position: absolute; left: 0; top: 5px; width: 12px; height: 12px; border-radius: 50%; background: #667eea; }
        .timeline-item::after { content: ''; position: absolute; left: 5px; top: 20px; width: 2px; height: calc(100% - 15px); background: #e9ecef; }
        .timeline-item:last-child::after { display: none; }
        .btn-new-assessment { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 12px 30px; border-radius: 25px; font-weight: 600; }
        .btn-new-assessment:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4); color: white; }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Header -->
        <div class="dashboard-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-2">🏥 My Health Dashboard</h2>
                    <p class="text-muted mb-0">AI-Powered Health Assessment & Symptom Tracking</p>
                </div>
                <a href="health_assessment.php" class="btn btn-new-assessment">
                    <i class="fas fa-plus-circle me-2"></i>New Assessment
                </a>
            </div>
        </div>

        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-box primary">
                    <h3><?= $total_assessments ?></h3>
                    <p>Total Assessments</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box success">
                    <h3><?= $total_assessments - $high_risk_count ?></h3>
                    <p>Normal Risk</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box warning">
                    <h3><?= $high_risk_count ?></h3>
                    <p>High Risk Alerts</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-box danger">
                    <h3><?= $recent_assessment ? date('d M', strtotime($recent_assessment['assessment_date'])) : 'N/A' ?></h3>
                    <p>Last Assessment</p>
                </div>
            </div>
        </div>

        <!-- Current Health Status -->
        <?php if ($recent_assessment): ?>
        <div class="dashboard-card">
            <h4 class="mb-4"><i class="fas fa-heartbeat text-danger me-2"></i>Current Health Status</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="text-center p-3 bg-light rounded">
                        <h6 class="text-muted mb-2">Risk Level</h6>
                        <span class="risk-badge risk-<?= strtolower($recent_assessment['risk_level']) ?> fs-6">
                            <?= $recent_assessment['risk_level'] ?>
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 bg-light rounded">
                        <h6 class="text-muted mb-2">Priority Status</h6>
                        <span class="badge bg-<?= $recent_assessment['priority_status'] == 'Critical' ? 'danger' : ($recent_assessment['priority_status'] == 'Urgent' ? 'warning' : 'success') ?> fs-6">
                            <?= $recent_assessment['priority_status'] ?>
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center p-3 bg-light rounded">
                        <h6 class="text-muted mb-2">Recommended Department</h6>
                        <p class="fw-bold text-primary mb-0"><?= $recent_assessment['recommended_department'] ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Recent Assessments -->
            <div class="col-md-8">
                <div class="dashboard-card">
                    <h4 class="mb-4"><i class="fas fa-history text-primary me-2"></i>Assessment History</h4>
                    
                    <?php if ($assessments->num_rows > 0): ?>
                        <?php while($assessment = $assessments->fetch_assoc()): ?>
                        <div class="assessment-item">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1"><?= date('d M Y, g:i A', strtotime($assessment['assessment_date'])) ?></h6>
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i><?= $assessment['age'] ?> years | 
                                        <i class="fas fa-venus-mars me-1"></i><?= $assessment['gender'] ?> |
                                        <i class="fas fa-weight me-1"></i><?= $assessment['weight_kg'] ?> kg
                                    </small>
                                </div>
                                <span class="risk-badge risk-<?= strtolower($assessment['risk_level']) ?>">
                                    <?= $assessment['risk_level'] ?> Risk
                                </span>
                            </div>
                            
                            <!-- Symptoms -->
                            <div class="mb-3">
                                <small class="text-muted fw-bold">Symptoms Reported:</small>
                                <div class="mt-1">
                                    <?php 
                                    $symptoms_list = explode(', ', $assessment['symptoms_text']);
                                    foreach(array_slice($symptoms_list, 0, 8) as $symptom): 
                                    ?>
                                    <span class="symptom-tag"><?= htmlspecialchars($symptom) ?></span>
                                    <?php endforeach; ?>
                                    <?php if(count($symptoms_list) > 8): ?>
                                    <span class="symptom-tag">+<?= count($symptoms_list) - 8 ?> more</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- AI Predictions -->
                            <?php 
                            $predicted_diseases = json_decode($assessment['predicted_diseases'], true);
                            $confidence_scores = json_decode($assessment['confidence_scores'], true);
                            ?>
                            <?php if ($predicted_diseases && !empty($predicted_diseases)): ?>
                            <div>
                                <small class="text-muted fw-bold">AI Predictions:</small>
                                <?php foreach(array_slice($predicted_diseases, 0, 3) as $index => $disease): ?>
                                <div class="disease-prediction mt-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <strong><?= $index + 1 ?>. <?= htmlspecialchars($disease) ?></strong>
                                        <span class="text-primary fw-bold"><?= $confidence_scores[$index] ?? 0 ?>%</span>
                                    </div>
                                    <div class="confidence-bar">
                                        <div class="confidence-fill" style="width: <?= $confidence_scores[$index] ?? 0 ?>%"></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Actions -->
                            <div class="mt-3 pt-3 border-top">
                                <button class="btn btn-sm btn-outline-primary me-2" onclick="viewAssessment(<?= $assessment['id'] ?>)">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </button>
                                <button class="btn btn-sm btn-outline-success" onclick="downloadReport(<?= $assessment['id'] ?>)">
                                    <i class="fas fa-download me-1"></i>Download Report
                                </button>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Assessments Yet</h5>
                            <p class="text-muted">Complete your first health assessment to get AI-powered insights</p>
                            <a href="health_assessment.php" class="btn btn-primary">Start Assessment</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Quick Actions -->
                <div class="dashboard-card">
                    <h5 class="mb-3"><i class="fas fa-bolt text-warning me-2"></i>Quick Actions</h5>
                    <a href="health_assessment.php" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-stethoscope me-2"></i>New Assessment
                    </a>
                    <a href="medical_history.php" class="btn btn-outline-secondary w-100 mb-2">
                        <i class="fas fa-file-medical me-2"></i>Medical History
                    </a>
                    <a href="book_appointment.php" class="btn btn-outline-success w-100">
                        <i class="fas fa-calendar-check me-2"></i>Book Appointment
                    </a>
                </div>

                <!-- Common Symptoms -->
                <div class="dashboard-card">
                    <h5 class="mb-3"><i class="fas fa-chart-bar text-info me-2"></i>Your Common Symptoms</h5>
                    <?php if ($common_symptoms->num_rows > 0): ?>
                        <?php while($symptom = $common_symptoms->fetch_assoc()): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="symptom-tag"><?= htmlspecialchars($symptom['symptoms_text']) ?></span>
                            <span class="badge bg-primary"><?= $symptom['count'] ?>x</span>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted small">No symptom data available yet</p>
                    <?php endif; ?>
                </div>

                <!-- Health Tips -->
                <div class="dashboard-card">
                    <h5 class="mb-3"><i class="fas fa-lightbulb text-success me-2"></i>Health Tips</h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Regular Check-ups:</strong> Schedule routine health assessments every 3-6 months for early detection.
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>High Risk Alert:</strong> If you experience severe symptoms, seek immediate medical attention.
                    </div>
                </div>

                <!-- Doctor's Notes -->
                <?php if ($recent_assessment && $recent_assessment['doctor_notes']): ?>
                <div class="dashboard-card">
                    <h5 class="mb-3"><i class="fas fa-user-md text-primary me-2"></i>Doctor's Notes</h5>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0"><?= nl2br(htmlspecialchars($recent_assessment['doctor_notes'])) ?></p>
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-clock me-1"></i>
                            <?php echo $recent_assessment['doctor_reviewed'] ? 'Reviewed by Doctor' : 'Pending Review'; ?>
                        </small>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Assessment Detail Modal -->
    <div class="modal fade" id="assessmentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assessment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="assessmentDetails">
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function viewAssessment(id) {
            // Load assessment details via AJAX
            fetch('get_assessment_details.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('assessmentDetails').innerHTML = data.html;
                    new bootstrap.Modal(document.getElementById('assessmentModal')).show();
                })
                .catch(error => {
                    Swal.fire('Error', 'Failed to load assessment details', 'error');
                });
        }

        function downloadReport(id) {
            Swal.fire({
                title: 'Download Report',
                text: 'Your assessment report will be downloaded as PDF',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Download',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'download_assessment_report.php?id=' + id;
                }
            });
        }

        // Auto-refresh every 5 minutes
        setTimeout(() => {
            location.reload();
        }, 300000);
    </script>
</body>
</html>