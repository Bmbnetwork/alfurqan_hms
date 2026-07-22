<?php
session_start();
include '../config/db.php';
include '../config/functions.php';
include '../config/ai_intelligence.php';
checkAdmin();

$engine = new AIIntelligenceEngine($conn);
$kpis = $engine->getKPIs();
$top_diseases = $engine->getTopDiseases('month');
$risk_distribution = $engine->getPatientRiskDistribution();
$department_performance = $engine->getDepartmentPerformance();
$prescription_safety = $engine->getPrescriptionSafetyAnalytics();
$forecast = $engine->generateForecast(7);
$executive_summary = $engine->generateExecutiveSummary();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Hospital Intelligence Dashboard | Alfurqan Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0d6efd;
            --success: #198754;
            --warning: #ffc107;
            --danger: #dc3545;
            --dark: #212529;
            --light: #f8f9fa;
        }
        
        body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, sans-serif; }
        
        .sidebar {
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            min-height: 100vh;
            position: fixed;
            width: 260px;
            padding: 20px 0;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .sidebar-header h4 {
            color: #00d4ff;
            font-weight: 700;
            margin: 0;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(0,212,255,0.1);
            color: #00d4ff;
            border-left-color: #00d4ff;
        }
        
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }
        
        .kpi-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: 0.3s;
            border-left: 4px solid var(--primary);
        }
        
        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .kpi-card.success { border-left-color: var(--success); }
        .kpi-card.warning { border-left-color: var(--warning); }
        .kpi-card.danger { border-left-color: var(--danger); }
        
        .kpi-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }
        
        .kpi-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark);
        }
        
        .kpi-label {
            color: #6c757d;
            font-size: 14px;
        }
        
        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .insight-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
        }
        
        .insight-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .insight-card.success {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .chat-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            height: 500px;
            display: flex;
            flex-direction: column;
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
        }
        
        .chat-message {
            margin-bottom: 15px;
            padding: 12px;
            border-radius: 12px;
            max-width: 80%;
        }
        
        .chat-message.user {
            background: #0d6efd;
            color: white;
            margin-left: auto;
        }
        
        .chat-message.ai {
            background: #f0f2f5;
            color: var(--dark);
        }
        
        .chat-input {
            display: flex;
            gap: 10px;
            padding-top: 10px;
            border-top: 1px solid #e9ecef;
        }
        
        .dark-mode {
            background: #1a1a2e;
            color: white;
        }
        
        .dark-mode .kpi-card,
        .dark-mode .chart-container,
        .dark-mode .chat-container {
            background: #16213e;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-brain me-2"></i>AI Intelligence</h4>
            <small class="text-muted">Hospital Command Center</small>
        </div>
        <div class="sidebar-menu">
            <a href="ai_dashboard.php" class="active"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
            <a href="ai_disease_intelligence.php"><i class="fas fa-virus me-2"></i>Disease Intelligence</a>
            <a href="ai_patient_risk.php"><i class="fas fa-heartbeat me-2"></i>Patient Risk</a>
            <a href="ai_department_performance.php"><i class="fas fa-hospital me-2"></i>Department Analytics</a>
            <a href="ai_prescription_safety.php"><i class="fas fa-pills me-2"></i>Prescription Safety</a>
            <a href="ai_forecasting.php"><i class="fas fa-chart-area me-2"></i>Forecasting</a>
            <a href="ai_command_center.php"><i class="fas fa-satellite-dish me-2"></i>Command Center</a>
            <a href="ai_chat.php"><i class="fas fa-robot me-2"></i>Ask AI</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0">AI Hospital Intelligence Dashboard</h2>
                <p class="text-muted">Real-time analytics and predictive insights</p>
            </div>
            <div>
                <button class="btn btn-outline-primary me-2" onclick="toggleDarkMode()">
                    <i class="fas fa-moon"></i> Dark Mode
                </button>
                <button class="btn btn-primary" onclick="refreshData()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="kpi-card">
                    <div class="kpi-icon text-primary"><i class="fas fa-users"></i></div>
                    <div class="kpi-value"><?= number_format($kpis['total_patients']) ?></div>
                    <div class="kpi-label">Total Patients</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="kpi-card success">
                    <div class="kpi-icon text-success"><i class="fas fa-user-check"></i></div>
                    <div class="kpi-value"><?= number_format($kpis['active_patients']) ?></div>
                    <div class="kpi-label">Active Patients</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="kpi-card">
                    <div class="kpi-icon text-info"><i class="fas fa-calendar-check"></i></div>
                    <div class="kpi-value"><?= $kpis['today_appointments'] ?></div>
                    <div class="kpi-label">Today's Appointments</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="kpi-card warning">
                    <div class="kpi-icon text-warning"><i class="fas fa-procedures"></i></div>
                    <div class="kpi-value"><?= $kpis['today_admissions'] ?></div>
                    <div class="kpi-label">Today's Admissions</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="kpi-card danger">
                    <div class="kpi-icon text-danger"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="kpi-value"><?= $kpis['high_risk_patients'] ?></div>
                    <div class="kpi-label">High-Risk Patients</div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="kpi-card success">
                    <div class="kpi-icon text-success"><i class="fas fa-shield-alt"></i></div>
                    <div class="kpi-value"><?= $kpis['interactions_prevented'] ?></div>
                    <div class="kpi-label">Interactions Prevented</div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 -->
        <div class="row">
            <div class="col-md-8">
                <div class="chart-container">
                    <h5 class="mb-3">Disease Trend Analysis</h5>
                    <canvas id="diseaseTrendChart"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="chart-container">
                    <h5 class="mb-3">Patient Risk Distribution</h5>
                    <canvas id="riskDistributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- AI Insights -->
        <div class="row mt-4">
            <div class="col-md-12">
                <h4 class="mb-3"><i class="fas fa-lightbulb text-warning me-2"></i>AI-Generated Insights</h4>
                <?php
                $insights = $engine->generateDiseaseInsights();
                foreach ($insights as $insight):
                ?>
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
        </div>

                <!-- Executive Summary -->
        <div class="chart-container mt-4">
            <h5 class="mb-3"><i class="fas fa-file-alt text-primary me-2"></i>Today's Executive Summary</h5>
            <div class="row">
                <div class="col-md-3">
                    <strong>Total Patients:</strong> <?= number_format($executive_summary['total_patients'] ?? 0) ?>
                </div>
                <div class="col-md-3">
                    <strong>Most Common Disease:</strong> <?= $executive_summary['most_common_disease'] ?? 'N/A' ?>
                </div>
                <div class="col-md-3">
                    <strong>Critical Patients:</strong> <?= $executive_summary['critical_patients'] ?? 0 ?>
                </div>
                <div class="col-md-3">
                    <strong>Interactions Prevented:</strong> <?= $executive_summary['drug_interactions_prevented'] ?? 0 ?>
                </div>
            </div>
            <div class="mt-3 p-3 bg-light rounded">
                <strong>AI Recommendation:</strong> 
                <?= $executive_summary['ai_recommendation'] ?? $executive_summary['recommendation'] ?? 'No data available yet. Run AI assessments to generate insights.' ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Disease Trend Chart
        const diseaseCtx = document.getElementById('diseaseTrendChart').getContext('2d');
        new Chart(diseaseCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($top_diseases, 'name')) ?>,
                datasets: [{
                    label: 'Cases',
                    data: <?= json_encode(array_column($top_diseases, 'cases')) ?>,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                }
            }
        });

        // Risk Distribution Chart
        const riskCtx = document.getElementById('riskDistributionChart').getContext('2d');
        new Chart(riskCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(<?= json_encode($risk_distribution) ?>),
                datasets: [{
                    data: Object.values(<?= json_encode($risk_distribution) ?>),
                    backgroundColor: ['#dc3545', '#ffc107', '#0dcaf0', '#198754']
                }]
            },
            options: {
                responsive: true
            }
        });

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }

        function refreshData() {
            Swal.fire({
                title: 'Refreshing Data...',
                timer: 1500,
                timerProgressBar: true,
                didOpen: () => { Swal.showLoading(); }
            }).then(() => {
                location.reload();
            });
        }

        // Auto-refresh every 5 minutes
        setInterval(() => {
            // Silently refresh KPI data via AJAX
            fetch('ai_api.php?action=get_kpis')
                .then(response => response.json())
                .then(data => {
                    // Update KPI cards dynamically
                    console.log('KPIs updated:', data);
                });
        }, 300000);
    </script>
</body>
</html>