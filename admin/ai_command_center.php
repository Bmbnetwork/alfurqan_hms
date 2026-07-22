<?php
session_start();
include '../config/db.php';
include '../config/functions.php';
include '../config/ai_intelligence.php';
checkAdmin();

$engine = new AIIntelligenceEngine($conn);
$kpis = $engine->getKPIs();
$risk_dist = $engine->getPatientRiskDistribution();
$summary = $engine->generateExecutiveSummary();
$departments = $engine->getDepartmentPerformance();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospital Command Center | AI Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #0f172a; color: white; font-family: 'Segoe UI', sans-serif; }
        .sidebar { background: #020617; color: white; min-height: 100vh; position: fixed; width: 260px; padding: 20px 0; border-right: 1px solid #1e293b; }
        .sidebar-header { padding: 20px; border-bottom: 1px solid #1e293b; margin-bottom: 20px; }
        .sidebar-header h4 { color: #00d4ff; font-weight: 700; margin: 0; }
        .sidebar-menu a { display: block; padding: 12px 20px; color: #94a3b8; text-decoration: none; transition: 0.3s; border-left: 3px solid transparent; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(0,212,255,0.1); color: #00d4ff; border-left-color: #00d4ff; }
        .main-content { margin-left: 260px; padding: 30px; }
        .command-card { background: #1e293b; border-radius: 12px; padding: 20px; border: 1px solid #334155; margin-bottom: 20px; }
        .command-card h5 { color: #00d4ff; border-bottom: 1px solid #334155; padding-bottom: 10px; margin-bottom: 15px; }
        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 20px; }
        .kpi-box { background: #0f172a; padding: 20px; border-radius: 10px; text-align: center; border-left: 4px solid #00d4ff; }
        .kpi-box.critical { border-left-color: #ef4444; }
        .kpi-box.warning { border-left-color: #f59e0b; }
        .kpi-box.success { border-left-color: #10b981; }
        .kpi-value { font-size: 28px; font-weight: bold; margin: 10px 0; }
        .kpi-label { color: #94a3b8; font-size: 13px; }
        .live-indicator { display: inline-block; width: 10px; height: 10px; background: #10b981; border-radius: 50%; animation: pulse 1.5s infinite; margin-right: 8px; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.4; } 100% { opacity: 1; } }
        .dept-load-bar { background: #334155; height: 8px; border-radius: 4px; overflow: hidden; margin-top: 5px; }
        .dept-load-fill { background: linear-gradient(90deg, #00d4ff, #0077b6); height: 100%; }
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
            <a href="ai_forecasting.php"><i class="fas fa-chart-area me-2"></i>Forecasting</a>
            <a href="ai_command_center.php" class="active"><i class="fas fa-satellite-dish me-2"></i>Command Center</a>
            <a href="ai_chat.php"><i class="fas fa-robot me-2"></i>Ask AI</a>
        </div>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><span class="live-indicator"></span>Hospital Command Center</h2>
            <span class="text-muted">Last Updated: <?= date('H:i:s') ?></span>
        </div>
        
        <!-- Live KPI Grid -->
        <div class="kpi-grid">
            <div class="kpi-box">
                <div class="kpi-label">Total Patients</div>
                <div class="kpi-value"><?= number_format($kpis['total_patients']) ?></div>
            </div>
            <div class="kpi-box critical">
                <div class="kpi-label">Critical Patients</div>
                <div class="kpi-value text-danger"><?= $risk_dist['Critical'] ?? 0 ?></div>
            </div>
            <div class="kpi-box warning">
                <div class="kpi-label">High Risk Patients</div>
                <div class="kpi-value text-warning"><?= $risk_dist['High'] ?? 0 ?></div>
            </div>
            <div class="kpi-box success">
                <div class="kpi-label">Interactions Prevented</div>
                <div class="kpi-value text-success"><?= $kpis['interactions_prevented'] ?></div>
            </div>
        </div>

        <div class="row">
            <!-- Department Load -->
            <div class="col-md-6">
                <div class="command-card">
                    <h5><i class="fas fa-hospital me-2"></i>Department Load</h5>
                    <?php 
                    $max_consultations = !empty($departments) ? max(array_column($departments, 'consultations')) : 1;
                    foreach($departments as $dept): 
                        $load_percentage = ($dept['consultations'] / $max_consultations) * 100;
                    ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span><?= htmlspecialchars($dept['name']) ?></span>
                            <span class="text-muted"><?= $dept['consultations'] ?> cases</span>
                        </div>
                        <div class="dept-load-bar">
                            <div class="dept-load-fill" style="width: <?= $load_percentage ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Daily AI Recommendations -->
            <div class="col-md-6">
                <div class="command-card">
                    <h5><i class="fas fa-robot me-2"></i>Daily AI Recommendations</h5>
                    <div class="alert" style="background: #0f172a; border-left: 4px solid #00d4ff; color: #e2e8f0;">
                        <strong>Executive Summary (<?= date('d M Y') ?>):</strong><br><br>
                        Most Common Disease: <strong class="text-info"><?= $summary['most_common_disease'] ?></strong><br>
                        Fastest Growing: <strong class="text-warning"><?= $summary['fastest_growing_disease'] ?></strong><br>
                        Critical Patients: <strong class="text-danger"><?= $summary['critical_patients'] ?></strong><br><br>
                        <hr style="border-color: #334155;">
<strong>AI Directive:</strong> 
<?= $summary['ai_recommendation'] ?? $summary['recommendation'] ?? 'No AI recommendation generated yet. Please run patient assessments.' ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resource Utilization -->
        <div class="command-card">
            <h5><i class="fas fa-server me-2"></i>System & Resource Utilization</h5>
            <div class="row text-center">
                <div class="col-md-3">
                    <h3 class="text-info"><?= $kpis['today_appointments'] ?></h3>
                    <small class="text-muted">Today's Appointments</small>
                </div>
                <div class="col-md-3">
                    <h3 class="text-success"><?= $kpis['today_admissions'] ?></h3>
                    <small class="text-muted">Today's Admissions</small>
                </div>
                <div class="col-md-3">
                    <h3 class="text-warning"><?= $kpis['avg_waiting_time'] ?></h3>
                    <small class="text-muted">Avg Waiting Time</small>
                </div>
                <div class="col-md-3">
                    <h3 class="text-danger"><?= $kpis['ai_alerts'] ?></h3>
                    <small class="text-muted">AI Alerts Generated</small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>