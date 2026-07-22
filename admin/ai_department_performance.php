<?php
session_start();
include '../config/db.php';
include '../config/functions.php';
include '../config/ai_intelligence.php';
checkAdmin();

$engine = new AIIntelligenceEngine($conn);
$departments = $engine->getDepartmentPerformance();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Department Performance | AI Dashboard</title>
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
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><h4><i class="fas fa-brain me-2"></i>AI Intelligence</h4><small class="text-muted">Hospital Command Center</small></div>
        <div class="sidebar-menu">
            <a href="ai_dashboard.php"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
            <a href="ai_disease_intelligence.php"><i class="fas fa-virus me-2"></i>Disease Intelligence</a>
            <a href="ai_patient_risk.php"><i class="fas fa-heartbeat me-2"></i>Patient Risk</a>
            <a href="ai_department_performance.php" class="active"><i class="fas fa-hospital me-2"></i>Department Analytics</a>
            <a href="ai_prescription_safety.php"><i class="fas fa-pills me-2"></i>Prescription Safety</a>
            <a href="ai_forecasting.php"><i class="fas fa-chart-area me-2"></i>Forecasting</a>
            <a href="ai_command_center.php"><i class="fas fa-satellite-dish me-2"></i>Command Center</a>
            <a href="ai_chat.php"><i class="fas fa-robot me-2"></i>Ask AI</a>
        </div>
    </div>

    <div class="main-content">
        <h2 class="mb-4">🏥 Department Performance Analytics</h2>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card-custom">
                    <h5 class="mb-3">Department Workload Distribution</h5>
                    <canvas id="deptChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-custom">
                    <h5 class="mb-3">Department Rankings (Last 30 Days)</h5>
                    <table class="table table-striped">
                        <thead><tr><th>Rank</th><th>Department</th><th>Consultations</th><th>Avg Wait (mins)</th></tr></thead>
                        <tbody>
                            <?php foreach($departments as $index => $dept): ?>
                            <tr>
                                <td><strong>#<?= $index + 1 ?></strong></td>
                                <td><?= htmlspecialchars($dept['name']) ?></td>
                                <td><?= $dept['consultations'] ?></td>
                                <td><?= $dept['avg_wait_time'] ?> mins</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card-custom">
            <h5 class="mb-3">AI Department Insight</h5>
            <div class="alert alert-success">
                <i class="fas fa-robot me-2"></i>
                <?php if(!empty($departments)): 
                    $top = $departments[0];
                    $total = array_sum(array_column($departments, 'consultations'));
                    $percentage = $total > 0 ? round(($top['consultations'] / $total) * 100, 1) : 0;
                ?>
                    The <strong><?= htmlspecialchars($top['name']) ?></strong> handled <strong><?= $percentage ?>%</strong> of all consultations this month (<?= $top['consultations'] ?> cases). 
                    <?php if($top['avg_wait_time'] > 20): ?>
                        <br><strong>Recommendation:</strong> Allocate additional medical personnel during peak hours to reduce the <?= $top['avg_wait_time'] ?> minute average wait time.
                    <?php else: ?>
                        <br><strong>Recommendation:</strong> Maintain current staffing levels as wait times are optimal.
                    <?php endif; ?>
                <?php else: echo "Not enough data to generate department insights yet."; endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('deptChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($departments, 'name')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($departments, 'consultations')) ?>,
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#20c997']
                }]
            },
            options: { responsive: true }
        });
    </script>
</body>
</html>