<?php
/**
 * AI Hospital Intelligence Engine
 * Alfurqan Clinic HMS - Advanced Analytics & Decision Support
 */

class AIIntelligenceEngine {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Get real-time KPI metrics
     */
    public function getKPIs() {
        $kpis = [];
        
        // Total Patients
        $result = $this->conn->query("SELECT COUNT(*) as total FROM patients");
        $kpis['total_patients'] = $result->fetch_assoc()['total'] ?? 0;
        
        // Active Patients (visited in last 30 days)
        $result = $this->conn->query("SELECT COUNT(DISTINCT patient_id) as total FROM consultations WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
        $kpis['active_patients'] = $result->fetch_assoc()['total'] ?? 0;
        
        // Today's Appointments
        $result = $this->conn->query("SELECT COUNT(*) as total FROM appointments WHERE DATE(appointment_date) = CURDATE()");
        $kpis['today_appointments'] = $result->fetch_assoc()['total'] ?? 0;
        
        // Today's Admissions (using consultations as proxy)
        $result = $this->conn->query("SELECT COUNT(*) as total FROM consultations WHERE DATE(visit_date) = CURDATE()");
        $kpis['today_admissions'] = $result->fetch_assoc()['total'] ?? 0;
        
        // Total Doctors
        $result = $this->conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'doctor'");
        $kpis['total_doctors'] = $result->fetch_assoc()['total'] ?? 0;
        
        // Prescriptions Issued
        $result = $this->conn->query("SELECT COUNT(*) as total FROM prescriptions");
        $kpis['prescriptions_issued'] = $result->fetch_assoc()['total'] ?? 0;
        
        // High-Risk Patients
        $result = $this->conn->query("SELECT COUNT(*) as total FROM patient_assessments WHERE risk_level IN ('High', 'Critical') AND DATE(assessment_date) = CURDATE()");
        $kpis['high_risk_patients'] = $result->fetch_assoc()['total'] ?? 0;
        
        // AI Alerts Generated
        $result = $this->conn->query("SELECT COUNT(*) as total FROM ai_insights WHERE DATE(generated_at) = CURDATE()");
        $kpis['ai_alerts'] = $result->fetch_assoc()['total'] ?? 0;
        
        // Drug Interactions Prevented
        $result = $this->conn->query("SELECT COUNT(*) as total FROM prescription_warnings WHERE severity IN ('Critical', 'Severe')");
        $kpis['interactions_prevented'] = $result->fetch_assoc()['total'] ?? 0;
        
        // Average Waiting Time (simulated based on appointment density)
        $avg_wait = round(($kpis['today_appointments'] / max($kpis['total_doctors'], 1)) * 15, 0);
        $kpis['avg_waiting_time'] = $avg_wait . ' mins';
        
        return $kpis;
    }
    
    /**
     * Get top diseases with statistics
     */
    public function getTopDiseases($period = 'month') {
        $date_filter = $this->getDateFilter($period);
        
        $query = "SELECT c.diagnosis, COUNT(*) as cases,
                 ROUND((COUNT(*) / (SELECT COUNT(*) FROM consultations WHERE diagnosis IS NOT NULL AND diagnosis != '' $date_filter)) * 100, 2) as percentage
                 FROM consultations c
                 WHERE c.diagnosis IS NOT NULL AND c.diagnosis != '' $date_filter
                 GROUP BY c.diagnosis
                 ORDER BY cases DESC
                 LIMIT 10";
        
        $result = $this->conn->query($query);
        $diseases = [];
        
        while ($row = $result->fetch_assoc()) {
            $diseases[] = [
                'name' => $row['diagnosis'],
                'cases' => $row['cases'],
                'percentage' => $row['percentage']
            ];
        }
        
        return $diseases;
    }
    
    /**
     * Get disease trend analysis
     */
    public function getDiseaseTrends($disease_name, $period = 'month') {
        $trends = [];
        
        // Current period
        $current_filter = $this->getDateFilter($period);
        $result = $this->conn->query("SELECT COUNT(*) as cases FROM consultations WHERE diagnosis = '$disease_name' $current_filter");
        $current_cases = $result->fetch_assoc()['cases'] ?? 0;
        
        // Previous period
        $previous_filter = $this->getPreviousDateFilter($period);
        $result = $this->conn->query("SELECT COUNT(*) as cases FROM consultations WHERE diagnosis = '$disease_name' $previous_filter");
        $previous_cases = $result->fetch_assoc()['cases'] ?? 0;
        
        // Calculate growth
        $growth = $previous_cases > 0 ? (($current_cases - $previous_cases) / $previous_cases) * 100 : 0;
        
        return [
            'current' => $current_cases,
            'previous' => $previous_cases,
            'growth' => round($growth, 2),
            'trend' => $growth > 0 ? 'increasing' : ($growth < 0 ? 'decreasing' : 'stable')
        ];
    }
    
    /**
     * Generate AI insights for diseases
     */
    public function generateDiseaseInsights() {
        $insights = [];
        $top_diseases = $this->getTopDiseases('month');
        
        foreach ($top_diseases as $disease) {
            $trend = $this->getDiseaseTrends($disease['name'], 'month');
            
            if ($trend['growth'] > 20) {
                $insights[] = [
                    'type' => 'Disease Trend',
                    'title' => "Rapid Growth: {$disease['name']}",
                    'content' => "{$disease['name']} cases increased by " . abs($trend['growth']) . "% compared to the previous month with {$disease['cases']} confirmed cases.",
                    'recommendation' => "Increase {$disease['name']} treatment resources and prepare additional medical staff.",
                    'confidence' => 85.00,
                    'severity' => 'High'
                ];
            }
            
            if ($disease['percentage'] > 30) {
                $insights[] = [
                    'type' => 'Disease Trend',
                    'title' => "Dominant Disease: {$disease['name']}",
                    'content' => "{$disease['name']} accounts for {$disease['percentage']}% of all diagnoses this month.",
                    'recommendation' => "Allocate additional resources for {$disease['name']} treatment and prevention.",
                    'confidence' => 90.00,
                    'severity' => 'Medium'
                ];
            }
        }
        
        return $insights;
    }
    
    /**
     * Get patient risk distribution
     */
    public function getPatientRiskDistribution() {
        $query = "SELECT risk_level, COUNT(*) as count
                 FROM patient_assessments
                 WHERE DATE(assessment_date) = CURDATE()
                 GROUP BY risk_level";
        
        $result = $this->conn->query($query);
        $distribution = ['Critical' => 0, 'High' => 0, 'Moderate' => 0, 'Low' => 0];
        
        while ($row = $result->fetch_assoc()) {
            $distribution[$row['risk_level']] = $row['count'];
        }
        
        return $distribution;
    }
    
        /**
     * Get department performance metrics
     */
    public function getDepartmentPerformance() {
        // FIX: The 'consultations' table doesn't have 'recommended_department'.
        // We will query 'patient_assessments' which DOES have it, 
        // and fallback to grouping by Doctor if no assessment data exists.
        
        $query = "SELECT recommended_department as name, COUNT(*) as consultations, 15 as avg_wait_time
                 FROM patient_assessments
                 WHERE assessment_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                 GROUP BY recommended_department
                 ORDER BY consultations DESC";
                 
        $result = $this->conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            $departments = [];
            while ($row = $result->fetch_assoc()) {
                $departments[] = [
                    'name' => $row['name'] ?? 'General Outpatient',
                    'consultations' => $row['consultations'],
                    'avg_wait_time' => $row['avg_wait_time']
                ];
            }
            return $departments;
        }
        
        // Fallback: Group by Doctor if no AI assessment data is available yet
        $fallback_query = "SELECT u.username as name, COUNT(*) as consultations, 15 as avg_wait_time
                          FROM consultations c
                          JOIN users u ON c.doctor_id = u.id
                          WHERE c.visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                          GROUP BY c.doctor_id
                          ORDER BY consultations DESC";
                          
        $fallback_result = $this->conn->query($fallback_query);
        $departments = [];
        
        if ($fallback_result) {
            while ($row = $fallback_result->fetch_assoc()) {
                $departments[] = [
                    'name' => $row['name'] . ' (Doctor)',
                    'consultations' => $row['consultations'],
                    'avg_wait_time' => $row['avg_wait_time']
                ];
            }
        }
        
        return $departments;
    }
    
    /**
     * Get prescription safety analytics
     */
    public function getPrescriptionSafetyAnalytics() {
        $query = "SELECT severity, COUNT(*) as count
                 FROM prescription_warnings
                 GROUP BY severity";
        
        $result = $this->conn->query($query);
        $analytics = ['Critical' => 0, 'Severe' => 0, 'Moderate' => 0, 'Low' => 0];
        
        while ($row = $result->fetch_assoc()) {
            $analytics[$row['severity']] = $row['count'];
        }
        
        // Get most common drug conflicts
        $conflict_query = "SELECT warning_message, COUNT(*) as count
                          FROM prescription_warnings
                          WHERE warning_type = 'Drug Interaction'
                          GROUP BY warning_message
                          ORDER BY count DESC
                          LIMIT 5";
        
        $conflict_result = $this->conn->query($conflict_query);
        $conflicts = [];
        
        while ($row = $conflict_result->fetch_assoc()) {
            $conflicts[] = $row;
        }
        
        return [
            'by_severity' => $analytics,
            'top_conflicts' => $conflicts,
            'total_alerts' => array_sum($analytics)
        ];
    }
    
    
             /**
     * Generate hospital forecast
     */
    public function generateForecast($days = 7) {
        $forecasts = [];
        
        // Get historical patient volume
        $historical_query = "SELECT DATE(visit_date) as date, COUNT(*) as patients
                            FROM consultations
                            WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                            GROUP BY DATE(visit_date)
                            ORDER BY date";
        
        $historical_result = $this->conn->query($historical_query);
        $historical_data = [];
        
        if ($historical_result) {
            while ($row = $historical_result->fetch_assoc()) {
                $historical_data[] = $row['patients'];
            }
        }
        
        // Simple moving average forecast
        $avg_patients = !empty($historical_data) ? array_sum($historical_data) / count($historical_data) : 0;
        $trend_factor = 1.17; // 17% growth factor (can be refined with more data)
        
        for ($i = 1; $i <= $days; $i++) {
            $forecast_date = date('Y-m-d', strtotime("+{$i} days"));
            $predicted = round($avg_patients * $trend_factor * (1 + ($i * 0.02)), 0);
            
            // FIX: Calculate confidence as a variable first
            $confidence = round(85 - ($i * 2), 0);
            
            $forecasts[] = [
                'date' => $forecast_date,
                'predicted_patients' => $predicted,
                'confidence' => $confidence
            ];
            
            // Store forecast in database
            $stmt = $this->conn->prepare("INSERT INTO hospital_forecasts (forecast_type, forecast_date, predicted_value, confidence, factors) VALUES (?, ?, ?, ?, ?)");
            
            if ($stmt) {
                // FIX: Extract literals and expressions into actual variables 
                // (PHP 8+ requires variables for bind_param references)
                $forecast_type = 'Patient Volume';
                $factors = "Based on 30-day historical average with " . round(($trend_factor - 1) * 100, 0) . "% growth trend";
                
                // Now bind the actual variables
                $stmt->bind_param("ssdis", $forecast_type, $forecast_date, $predicted, $confidence, $factors);
                $stmt->execute();
            }
        }
        
        return $forecasts;
    }
    
    /**
     * Process natural language query
     */
    public function processQuery($question, $user_id) {
        $start_time = microtime(true);
        
        // Store question
        $stmt = $this->conn->prepare("INSERT INTO ai_questions (user_id, question, intent, entities) VALUES (?, ?, ?, ?)");
        $intent = $this->detectIntent($question);
        $entities = json_encode($this->extractEntities($question));
        $stmt->bind_param("isss", $user_id, $question, $intent, $entities);
        $stmt->execute();
        $question_id = $this->conn->insert_id;
        
        // Generate response based on intent
        $response = $this->generateResponse($intent, $question);
        
        // Store response
        $end_time = microtime(true);
        $response_time = round(($end_time - $start_time) * 1000, 0);
        
        $stmt = $this->conn->prepare("INSERT INTO ai_responses (question_id, response, statistics, recommendation, confidence, response_time_ms) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssdi", $question_id, $response['answer'], $response['statistics'], $response['recommendation'], $response['confidence'], $response_time);
        $stmt->execute();
        
        return $response;
    }
    
    /**
     * Detect intent from question
     */
    private function detectIntent($question) {
        $question_lower = strtolower($question);
        
        if (strpos($question_lower, 'common disease') !== false || strpos($question_lower, 'most common') !== false) {
            return 'most_common_disease';
        } elseif (strpos($question_lower, 'department') !== false && strpos($question_lower, 'overload') !== false) {
            return 'department_overload';
        } elseif (strpos($question_lower, 'critical patient') !== false) {
            return 'critical_patients';
        } elseif (strpos($question_lower, 'medication interaction') !== false || strpos($question_lower, 'drug interaction') !== false) {
            return 'drug_interactions';
        } elseif (strpos($question_lower, 'increasing') !== false || strpos($question_lower, 'fastest growing') !== false) {
            return 'fastest_growing_disease';
        } elseif (strpos($question_lower, 'age group') !== false) {
            return 'age_group_analysis';
        } else {
            return 'general_query';
        }
    }
    
    /**
     * Extract entities from question
     */
    private function extractEntities($question) {
        $entities = [];
        
        // Extract time periods
        if (strpos($question, 'today') !== false) $entities['period'] = 'today';
        elseif (strpos($question, 'week') !== false) $entities['period'] = 'week';
        elseif (strpos($question, 'month') !== false) $entities['period'] = 'month';
        else $entities['period'] = 'month';
        
        return $entities;
    }
    
    /**
     * Generate AI response
     */
    private function generateResponse($intent, $question) {
        switch ($intent) {
            case 'most_common_disease':
                $top_diseases = $this->getTopDiseases('month');
                if (!empty($top_diseases)) {
                    $top = $top_diseases[0];
                    return [
                        'answer' => "{$top['name']} is the most common disease this month with {$top['cases']} confirmed cases, representing {$top['percentage']}% of all diagnoses.",
                        'statistics' => json_encode($top_diseases),
                        'recommendation' => "Ensure adequate stock of {$top['name']} medications and prepare additional staff for treatment.",
                        'confidence' => 92.00
                    ];
                }
                break;
                
            case 'critical_patients':
                $result = $this->conn->query("SELECT COUNT(*) as count FROM patient_assessments WHERE risk_level = 'Critical' AND DATE(assessment_date) = CURDATE()");
                $count = $result->fetch_assoc()['count'] ?? 0;
                return [
                    'answer' => "There are currently {$count} critical patients requiring immediate attention.",
                    'statistics' => json_encode(['critical_count' => $count]),
                    'recommendation' => $count > 5 ? "Activate emergency response protocol and allocate additional critical care resources." : "Continue monitoring critical patients with standard protocols.",
                    'confidence' => 95.00
                ];
                break;
                
            case 'fastest_growing_disease':
                $top_diseases = $this->getTopDiseases('month');
                $fastest_growing = null;
                $max_growth = 0;
                
                foreach ($top_diseases as $disease) {
                    $trend = $this->getDiseaseTrends($disease['name'], 'month');
                    if ($trend['growth'] > $max_growth) {
                        $max_growth = $trend['growth'];
                        $fastest_growing = $disease;
                    }
                }
                
                if ($fastest_growing) {
                    return [
                        'answer' => "{$fastest_growing['name']} is the fastest growing disease with a " . abs($max_growth) . "% increase compared to last month.",
                        'statistics' => json_encode($fastest_growing),
                        'recommendation' => "Investigate the cause of rapid growth and implement preventive measures.",
                        'confidence' => 88.00
                    ];
                }
                break;
                
            default:
                return [
                    'answer' => "I can help you analyze hospital data. Try asking about disease trends, patient risks, department performance, or drug interactions.",
                    'statistics' => '{}',
                    'recommendation' => "Use specific questions like 'What is the most common disease this month?' for detailed insights.",
                    'confidence' => 70.00
                ];
        }
    }
    
        /**
     * Generate executive summary
     */
    public function generateExecutiveSummary() {
        $today = date('Y-m-d');
        
        // FIX: Check if the table actually exists
        $table_check = $this->conn->query("SHOW TABLES LIKE 'executive_summaries'");
        if (!$table_check || $table_check->num_rows == 0) {
            return [
                'total_patients' => 0,
                'most_common_disease' => 'N/A',
                'fastest_growing_disease' => 'N/A',
                'critical_patients' => 0,
                'drug_interactions_prevented' => 0,
                'recommendation' => 'Database tables missing. Please import ai_dashboard.sql'
            ];
        }

        // Check if summary already exists for today
        $check = $this->conn->query("SELECT * FROM executive_summaries WHERE summary_date = '$today'");
        if ($check && $check->num_rows > 0) {
            return $check->fetch_assoc();
        }
        
        $kpis = $this->getKPIs();
        $top_diseases = $this->getTopDiseases('month');
        $most_common = !empty($top_diseases) ? $top_diseases[0]['name'] : 'N/A';
        
        // Find fastest growing
        $fastest_growing = 'N/A';
        $max_growth = 0;
        foreach ($top_diseases as $disease) {
            $trend = $this->getDiseaseTrends($disease['name'], 'month');
            if ($trend['growth'] > $max_growth) {
                $max_growth = $trend['growth'];
                $fastest_growing = $disease['name'];
            }
        }
        
        $critical_count = $this->conn->query("SELECT COUNT(*) as count FROM patient_assessments WHERE risk_level = 'Critical' AND DATE(assessment_date) = CURDATE()")->fetch_assoc()['count'] ?? 0;
        $interactions_prevented = $this->conn->query("SELECT COUNT(*) as count FROM prescription_warnings WHERE severity IN ('Critical', 'Severe')")->fetch_assoc()['count'] ?? 0;
        
        $recommendation = "Based on current data, {$most_common} is the primary health concern. Consider increasing resources for this condition and monitoring the growth of {$fastest_growing}.";
        
        $stmt = $this->conn->prepare("INSERT INTO executive_summaries (summary_date, total_patients, most_common_disease, fastest_growing_disease, critical_patients, drug_interactions_prevented, ai_recommendation) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sisiiis", $today, $kpis['total_patients'], $most_common, $fastest_growing, $critical_count, $interactions_prevented, $recommendation);
            $stmt->execute();
        }
        
        return [
            'id' => $this->conn->insert_id,
            'date' => $today,
            'total_patients' => $kpis['total_patients'],
            'most_common_disease' => $most_common,
            'fastest_growing_disease' => $fastest_growing,
            'critical_patients' => $critical_count,
            'drug_interactions_prevented' => $interactions_prevented,
            'recommendation' => $recommendation,
            'ai_recommendation' => $recommendation
        ];
    }
    
    /**
     * Get date filter for queries
     */
    private function getDateFilter($period) {
        switch ($period) {
            case 'today': return "AND DATE(visit_date) = CURDATE()";
            case 'week': return "AND visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            case 'month': return "AND visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            case 'year': return "AND visit_date >= DATE_SUB(CURDATE(), INTERVAL 365 DAY)";
            default: return "AND visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        }
    }
    
    /**
     * Get previous period filter
     */
    private function getPreviousDateFilter($period) {
        switch ($period) {
            case 'today': return "AND DATE(visit_date) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
            case 'week': return "AND visit_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 14 DAY) AND DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            case 'month': return "AND visit_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 60 DAY) AND DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            case 'year': return "AND visit_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 730 DAY) AND DATE_SUB(CURDATE(), INTERVAL 365 DAY)";
            default: return "AND visit_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 60 DAY) AND DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        }
    }
}
?>