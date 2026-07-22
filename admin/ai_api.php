<?php
session_start();
include '../config/db.php';
include '../config/functions.php';
include '../config/ai_intelligence.php';
checkAdmin();

$engine = new AIIntelligenceEngine($conn);
$action = $_GET['action'] ?? '';

header('Content-Type: application/json');

switch ($action) {
    case 'get_kpis':
        echo json_encode($engine->getKPIs());
        break;
        
    case 'get_disease_trends':
        $period = $_GET['period'] ?? 'month';
        echo json_encode($engine->getTopDiseases($period));
        break;
        
    case 'get_risk_distribution':
        echo json_encode($engine->getPatientRiskDistribution());
        break;
        
    case 'get_department_performance':
        echo json_encode($engine->getDepartmentPerformance());
        break;
        
    case 'get_prescription_safety':
        echo json_encode($engine->getPrescriptionSafetyAnalytics());
        break;
        
    case 'get_forecast':
        $days = intval($_GET['days'] ?? 7);
        echo json_encode($engine->generateForecast($days));
        break;
        
    case 'generate_insights':
        echo json_encode($engine->generateDiseaseInsights());
        break;
        
    case 'get_executive_summary':
        echo json_encode($engine->generateExecutiveSummary());
        break;
        
    default:
        echo json_encode(['error' => 'Invalid action']);
}
?>