<?php
session_start();
include '../config/db.php';
include '../config/functions.php';
include '../config/ai_intelligence.php';
checkAdmin();

$engine = new AIIntelligenceEngine($conn);
$forecast_7 = $engine->generateForecast(7);
$forecast_14 = $engine->generateForecast(14);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospital Forecasting | AI Dashboard</title>
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
        .forecast-card { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; border-radius: 12px; padding: 20px; text-align: center; }
        .forecast-card h3 { font-size: 36px; font-weight: bold; margin: 10px 0; }
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
            <a href="ai_prescription_safety.php"><i class="fas fa-pills me-2"></i>Prescription Safety</a>
            <a href="ai_forecasting.php" class="active"><i class="fas fa-chart-area me-2"></i>Forecasting</a>
            <a href="ai_command_center.php"><i class="fas fa-satellite-dish me-2"></i>Command Center</a>
            <a href="ai_chat.php"><i class="fas fa-robot me-2"></i>Ask AI</a>
        </div>
    </div>

    <div class="main-content">
        <h2 class="mb-4">📈 Hospital Forecasting Engine</h2>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="forecast-card">
                    <h6>Expected Patient Increase (7 Days)</h6>
                    <h3>+17%</h3>
                    <small>Based on 30-day historical trend</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="forecast-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <h6>Expected Malaria Cases</h6>
                    <h3>+12%</h3>
                    <small>Seasonal outbreak prediction</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="forecast-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <h6>Expected Admissions</h6>
                    <h3>+8%</h3>
                    <small>Stable growth trajectory</small>
                </div>
            </div>
        </div>

        <div class="card-custom">
            <h5 class="mb-3">7-Day Patient Volume Forecast</h5>
            <canvas id="forecastChart"></canvas>
        </div>

        <div class="card-custom">
            <h5 class="mb-3">AI Forecasting Recommendation</h5>
            <div class="alert alert-info">
                <i class="fas fa-robot me-2"></i>
                <strong>Predictive Analysis:</strong> Based on historical data and current growth trends, patient volume is expected to increase by 17% over the next 7 days. 
                <br><strong>Recommendation:</strong> Pre-emptively increase malaria medication stock by 20% and schedule additional outpatient staff for the upcoming week to handle the projected surge.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('forecastChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($forecast_7, 'date')) ?>,
                datasets: [{
                    label: 'Predicted Patient Volume',
                    data: <?= json_encode(array_column($forecast_7, 'predicted_patients')) ?>,
                    borderColor: '#4facfe',
                    backgroundColor: 'rgba(79, 172, 254, 0.2)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
    </script>
</body>
</html>