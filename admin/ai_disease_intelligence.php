<?php
session_start();
include '../config/db.php';
include '../config/functions.php';
include '../config/ai_intelligence.php';
checkAdmin();

$engine = new AIIntelligenceEngine($conn);
$top_diseases = $engine->getTopDiseases('month');
$insights = $engine->generateDiseaseInsights();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Disease Intelligence | AI Dashboard</title>
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
        .insight-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; padding: 20px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><h4><i class="fas fa-brain me-2"></i>AI Intelligence</h4><small class="text-muted">Hospital Command Center</small></div>
        <div class="sidebar-menu">
            <a href="ai_dashboard.php"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
            <a href="ai_disease_intelligence.php" class="active"><i class="fas fa-virus me-2"></i>Disease Intelligence</a>
            <a href="ai_patient_risk.php"><i class="fas fa-heartbeat me-2"></i>Patient Risk</a>
            <a href="ai_department_performance.php"><i class="fas fa-hospital me-2"></i>Department Analytics</a>
            <a href="ai_prescription_safety.php"><i class="fas fa-pills me-2"></i>Prescription Safety</a>
            <a href="ai_forecasting.php"><i class="fas fa-chart-area me-2"></i>Forecasting</a>
            <a href="ai_command_center.php"><i class="fas fa-satellite-dish me-2"></i>Command Center</a>
            <a href="ai_chat.php"><i class="fas fa-robot me-2"></i>Ask AI</a>
        </div>
    </div>

    <div class="main-content">
        <h2 class="mb-4">🦠 AI Disease Intelligence</h2>
        
        <div class="row">
            <div class="col-md-8">
                <div class="card-custom">
                    <h5 class="mb-3">Top Diseases This Month</h5>
                    <canvas id="diseaseChart"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-custom">
                    <h5 class="mb-3">Disease Distribution</h5>
                    <table class="table table-sm">
                        <thead><tr><th>Disease</th><th>Cases</th><th>%</th></tr></thead>
                        <tbody>
                            <?php foreach($top_diseases as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['name']) ?></td>
                                <td><?= $d['cases'] ?></td>
                                <td><?= $d['percentage'] ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <h4 class="mt-4 mb-3"><i class="fas fa-lightbulb text-warning me-2"></i>AI Disease Insights</h4>
        <?php foreach($insights as $insight): ?>
        <div class="insight-card">
            <h6><i class="fas fa-robot me-2"></i><?= $insight['title'] ?></h6>
            <p class="mb-2"><?= $insight['content'] ?></p>
            <small><strong>Recommendation:</strong> <?= $insight['recommendation'] ?></small>
            <div class="mt-2">
                <span class="badge bg-light text-dark">Confidence: <?= $insight['confidence'] ?>%</span>
                <span class="badge bg-<?= $insight['severity'] == 'High' ? 'danger' : 'warning' ?>"><?= $insight['severity'] ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('diseaseChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($top_diseases, 'name')) ?>,
                datasets: [{
                    label: 'Number of Cases',
                    data: <?= json_encode(array_column($top_diseases, 'cases')) ?>,
                    backgroundColor: 'rgba(102, 126, 234, 0.7)',
                    borderColor: '#667eea',
                    borderWidth: 1
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    </script>
</body>
</html>