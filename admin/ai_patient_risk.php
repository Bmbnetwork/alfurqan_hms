<?php
session_start();
include '../config/db.php';
include '../config/functions.php';
include '../config/ai_intelligence.php';
checkAdmin();

$engine = new AIIntelligenceEngine($conn);
$risk_dist = $engine->getPatientRiskDistribution();

// Fetch high-risk patients for the table
$high_risk_query = "SELECT pa.*, p.name, p.phone FROM patient_assessments pa JOIN patients p ON pa.patient_id = p.id WHERE pa.risk_level IN ('High', 'Critical') ORDER BY pa.assessment_date DESC LIMIT 10";
$high_risk_patients = $conn->query($high_risk_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Risk Intelligence | AI Dashboard</title>
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
        .risk-badge { padding: 5px 10px; border-radius: 15px; font-size: 12px; font-weight: 600; color: white; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><h4><i class="fas fa-brain me-2"></i>AI Intelligence</h4><small class="text-muted">Hospital Command Center</small></div>
        <div class="sidebar-menu">
            <a href="ai_dashboard.php"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
            <a href="ai_disease_intelligence.php"><i class="fas fa-virus me-2"></i>Disease Intelligence</a>
            <a href="ai_patient_risk.php" class="active"><i class="fas fa-heartbeat me-2"></i>Patient Risk</a>
            <a href="ai_department_performance.php"><i class="fas fa-hospital me-2"></i>Department Analytics</a>
            <a href="ai_prescription_safety.php"><i class="fas fa-pills me-2"></i>Prescription Safety</a>
            <a href="ai_forecasting.php"><i class="fas fa-chart-area me-2"></i>Forecasting</a>
            <a href="ai_command_center.php"><i class="fas fa-satellite-dish me-2"></i>Command Center</a>
            <a href="ai_chat.php"><i class="fas fa-robot me-2"></i>Ask AI</a>
        </div>
    </div>

    <div class="main-content">
        <h2 class="mb-4">⚠️ Patient Risk Intelligence</h2>
        
        <div class="row">
            <div class="col-md-5">
                <div class="card-custom">
                    <h5 class="mb-3">Today's Risk Distribution</h5>
                    <canvas id="riskChart"></canvas>
                </div>
            </div>
            <div class="col-md-7">
                <div class="card-custom">
                    <h5 class="mb-3 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>High & Critical Risk Patients</h5>
                    <table class="table table-hover">
                        <thead><tr><th>Patient</th><th>Phone</th><th>Risk Level</th><th>Priority</th><th>Date</th></tr></thead>
                        <tbody>
                            <?php while($p = $high_risk_patients->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['name']) ?></td>
                                <td><?= $p['phone'] ?></td>
                                <td><span class="risk-badge bg-<?= $p['risk_level'] == 'Critical' ? 'danger' : 'warning' ?>"><?= $p['risk_level'] ?></span></td>
                                <td><?= $p['priority_status'] ?></td>
                                <td><?= date('d M Y', strtotime($p['assessment_date'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card-custom mt-3">
            <h5 class="mb-3">AI Risk Insight</h5>
            <div class="alert alert-info">
                <i class="fas fa-robot me-2"></i>
                <?php 
                $total = array_sum($risk_dist);
                $critical_high = ($risk_dist['Critical'] ?? 0) + ($risk_dist['High'] ?? 0);
                $percentage = $total > 0 ? round(($critical_high / $total) * 100, 1) : 0;
                echo "<strong>{$percentage}%</strong> of today's assessed patients were classified as High or Critical Risk. ";
                echo $percentage > 30 ? "Recommendation: Increase emergency response capacity and alert on-call specialists." : "Recommendation: Maintain standard monitoring protocols.";
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('riskChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?= json_encode(array_keys($risk_dist)) ?>,
                datasets: [{
                    data: <?= json_encode(array_values($risk_dist)) ?>,
                    backgroundColor: ['#dc3545', '#ffc107', '#0dcaf0', '#198754']
                }]
            },
            options: { responsive: true }
        });
    </script>
</body>
</html>