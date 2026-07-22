<?php
session_start();
include '../config/db.php';
include '../config/functions.php';
include '../config/ai_intelligence.php';
checkAdmin();

$engine = new AIIntelligenceEngine($conn);
$safety_data = $engine->getPrescriptionSafetyAnalytics();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescription Safety | AI Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .sidebar { background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%); color: white; min-height: 100vh; position: fixed; width: 260px; padding: 20px 0; }
        .sidebar-header { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar-header h4 { color: #00d4ff; font-weight: 700; margin: 0; }
        .sidebar-menu a { display: block; padding: 12px 20px; color: rgba(255,255,255,0.7); text-decoration: none; transition: 0.3s; border-left: 3px solid transparent; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(0,212,255,0.1); color: #00d4ff; border-left-color: #00d4ff; }
        .main-content { margin-left: 260px; padding: 30px; }
        .card-custom { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); margin-bottom: 20px; }
        .stat-box { text-align: center; padding: 20px; border-radius: 10px; color: white; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><h4><i class="fas fa-brain me-2"></i>AI Intelligence</h4><small class="text-muted">Hospital Command Center</small></div>
        <div class="sidebar-menu">
            <a href="ai_dashboard.php"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
            <a href="ai_disease_intelligence.php"><i class="fas fa-virus me-2"></i>Disease Intelligence</a>
            <a href="ai_patient_risk.php"><i class="fas fa-heartbeat me-2"></i>Patient Risk</a>
            <a href="ai_department_performance.php"><i class="fas fa-hospital me-2"></i>Department Analytics</a>
            <a href="ai_prescription_safety.php" class="active"><i class="fas fa-pills me-2"></i>Prescription Safety</a>
            <a href="ai_forecasting.php"><i class="fas fa-chart-area me-2"></i>Forecasting</a>
            <a href="ai_command_center.php"><i class="fas fa-satellite-dish me-2"></i>Command Center</a>
            <a href="ai_chat.php"><i class="fas fa-robot me-2"></i>Ask AI</a>
        </div>
    </div>

    <div class="main-content">
        <h2 class="mb-4"> Prescription Safety Analytics</h2>
        
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-box bg-primary"><h3><?= $safety_data['total_alerts'] ?></h3><p>Total Alerts</p></div>
            </div>
            <div class="col-md-3">
                <div class="stat-box bg-danger"><h3><?= $safety_data['by_severity']['Critical'] + $safety_data['by_severity']['Severe'] ?></h3><p>High Risk Alerts</p></div>
            </div>
            <div class="col-md-3">
                <div class="stat-box bg-warning text-dark"><h3><?= $safety_data['by_severity']['Moderate'] ?></h3><p>Moderate Risk</p></div>
            </div>
            <div class="col-md-3">
                <div class="stat-box bg-success"><h3><?= $safety_data['by_severity']['Low'] ?></h3><p>Low Risk</p></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card-custom">
                    <h5 class="mb-3">Alert Severity Breakdown</h5>
                    <canvas id="alertChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-custom">
                    <h5 class="mb-3 text-danger"><i class="fas fa-exclamation-circle me-2"></i>Most Common Drug Conflicts</h5>
                    <table class="table table-sm">
                        <thead><tr><th>Conflict</th><th>Occurrences</th></tr></thead>
                        <tbody>
                            <?php foreach($safety_data['top_conflicts'] as $conflict): ?>
                            <tr>
                                <td><?= htmlspecialchars($conflict['warning_message']) ?></td>
                                <td><span class="badge bg-danger"><?= $conflict['count'] ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($safety_data['top_conflicts'])): ?>
                            <tr><td colspan="2" class="text-center text-muted">No drug conflicts recorded yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card-custom">
            <h5 class="mb-3">AI Prescription Insight</h5>
            <div class="alert alert-warning">
                <i class="fas fa-robot me-2"></i>
                <?php 
                $total = $safety_data['total_alerts'];
                $high_risk = $safety_data['by_severity']['Critical'] + $safety_data['by_severity']['Severe'];
                $percentage = $total > 0 ? round(($high_risk / $total) * 100, 1) : 0;
                echo "<strong>{$percentage}%</strong> of all prescription alerts are classified as High Risk (Critical/Severe). ";
                echo $percentage > 40 ? "Recommendation: Provide additional prescribing guidance and mandatory review for high-risk medications." : "Recommendation: Continue current prescription safety protocols.";
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('alertChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Critical', 'Severe', 'Moderate', 'Low'],
                datasets: [{
                    label: 'Number of Alerts',
                    data: [<?= $safety_data['by_severity']['Critical'] ?>, <?= $safety_data['by_severity']['Severe'] ?>, <?= $safety_data['by_severity']['Moderate'] ?>, <?= $safety_data['by_severity']['Low'] ?>],
                    backgroundColor: ['#dc3545', '#fd7e14', '#ffc107', '#198754']
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    </script>
</body>
</html>