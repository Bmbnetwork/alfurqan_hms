<?php
/**
 * AI Engine for Disease Prediction and Drug Interaction Checking
 * Alfurqan Clinic HMS - FIXED VERSION
 */

class AIEngine {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Predict diseases based on symptoms
     */
    public function predictDiseases($symptoms, $age, $gender, $weight, $medical_conditions, $allergies) {
        if (empty($symptoms)) {
            return ['error' => 'No symptoms provided'];
        }
        
        // Get symptom IDs
        $symptom_ids = [];
        foreach ($symptoms as $symptom_name) {
            $symptom_name = trim($symptom_name);
            if (empty($symptom_name)) continue;
            
            $stmt = $this->conn->prepare("SELECT id FROM symptoms WHERE symptom_name = ?");
            $stmt->bind_param("s", $symptom_name);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $symptom_ids[] = $row['id'];
            }
        }
        
        if (empty($symptom_ids)) {
            return ['error' => 'No valid symptoms found in database'];
        }
        
        // Get all diseases with their symptoms (only diseases that have mappings)
        $diseases = [];
        $disease_query = "SELECT d.id, d.disease_name, d.severity_level, d.recommended_department, d.description,
                         GROUP_CONCAT(dsm.symptom_id) as symptom_ids,
                         SUM(dsm.weight) as total_weight
                         FROM diseases d
                         INNER JOIN disease_symptom_mapping dsm ON d.id = dsm.disease_id
                         GROUP BY d.id
                         HAVING symptom_ids IS NOT NULL";
        
        $result = $this->conn->query($disease_query);
        
        if (!$result) {
            return ['error' => 'Database query failed: ' . $this->conn->error];
        }
        
        while ($disease = $result->fetch_assoc()) {
            // FIX: Check if symptom_ids is not null before exploding
            if (empty($disease['symptom_ids'])) {
                continue; // Skip diseases with no symptom mappings
            }
            
            $disease_symptom_ids = array_map('intval', explode(',', $disease['symptom_ids']));
            $matched_symptoms = array_intersect($symptom_ids, $disease_symptom_ids);
            $match_count = count($matched_symptoms);
            
            if ($match_count > 0) {
                // Calculate confidence score
                $total_disease_symptoms = count($disease_symptom_ids);
                $confidence = ($match_count / $total_disease_symptoms) * 100;
                
                // Apply severity weight adjustment
                $severity_multiplier = 1.0;
                if ($disease['severity_level'] == 'Critical') $severity_multiplier = 1.3;
                elseif ($disease['severity_level'] == 'High') $severity_multiplier = 1.2;
                elseif ($disease['severity_level'] == 'Medium') $severity_multiplier = 1.0;
                else $severity_multiplier = 0.9;
                
                $adjusted_confidence = $confidence * $severity_multiplier;
                
                // Adjust for age and gender factors
                $age_factor = $this->calculateAgeFactor($disease['id'], $age, $gender);
                $final_confidence = $adjusted_confidence * $age_factor;
                
                $diseases[] = [
                    'disease_id' => $disease['id'],
                    'disease_name' => $disease['disease_name'],
                    'confidence' => round(min($final_confidence, 100), 2),
                    'severity_level' => $disease['severity_level'],
                    'recommended_department' => $disease['recommended_department'],
                    'description' => $disease['description'],
                    'matched_symptoms' => $match_count,
                    'total_symptoms' => $total_disease_symptoms
                ];
            }
        }
        
        // Sort by confidence
        usort($diseases, function($a, $b) {
            return $b['confidence'] <=> $a['confidence'];
        });
        
        // Determine risk level
        $risk_level = $this->calculateRiskLevel($diseases, $age, $medical_conditions);
        
        // Determine priority
        $priority = $this->calculatePriority($diseases, $risk_level);
        
        // Get top recommendation
        $top_disease = !empty($diseases) ? $diseases[0] : null;
        
        return [
            'predictions' => array_slice($diseases, 0, 5),
            'risk_level' => $risk_level,
            'priority_status' => $priority,
            'recommended_department' => $top_disease ? $top_disease['recommended_department'] : 'General Outpatient',
            'total_symptoms_analyzed' => count($symptom_ids)
        ];
    }
    
    /**
     * Calculate age factor for disease prediction
     */
    private function calculateAgeFactor($disease_id, $age, $gender) {
        $factor = 1.0;
        
        if ($age < 18 && in_array($disease_id, [1, 2, 3])) {
            $factor = 0.8;
        } elseif ($age > 60 && in_array($disease_id, [10, 11])) {
            $factor = 1.3;
        }
        
        return $factor;
    }
    
    /**
     * Calculate overall risk level
     */
    private function calculateRiskLevel($diseases, $age, $medical_conditions) {
        if (empty($diseases)) return 'Low';
        
        $top_confidence = $diseases[0]['confidence'];
        $top_severity = $diseases[0]['severity_level'];
        
        $high_risk_conditions = ['diabetes', 'hypertension', 'heart disease', 'cancer'];
        $has_high_risk = false;
        
        if ($medical_conditions) {
            foreach ($high_risk_conditions as $condition) {
                if (stripos($medical_conditions, $condition) !== false) {
                    $has_high_risk = true;
                    break;
                }
            }
        }
        
        if ($top_severity == 'Critical' || ($top_confidence > 80 && $has_high_risk)) {
            return 'Critical';
        } elseif ($top_severity == 'High' || $top_confidence > 70) {
            return 'High';
        } elseif ($top_confidence > 50) {
            return 'Medium';
        } else {
            return 'Low';
        }
    }
    
    /**
     * Calculate priority status
     */
    private function calculatePriority($diseases, $risk_level) {
        if ($risk_level == 'Critical') return 'Critical';
        if ($risk_level == 'High') return 'Urgent';
        return 'Normal';
    }
    
        /**
     * Check drug interactions
     */
    public function checkDrugInteractions($drug_ids, $patient_id) {
        $warnings = [];
        
        if (empty($drug_ids)) {
            return $warnings;
        }
        
        // FIX: Safely get patient allergies without crashing if column is missing
        $patient_allergies = '';
        $patient_query = $this->conn->query("SELECT * FROM patients WHERE id = " . intval($patient_id));
        
        if ($patient_query && $patient_query->num_rows > 0) {
            $patient = $patient_query->fetch_assoc();
            // Check if the column actually exists in the result array
            if (isset($patient['allergies'])) {
                $patient_allergies = $patient['allergies'];
            }
        }
        
        // Get patient's current medications
        $current_meds_result = $this->conn->query("SELECT drug_name FROM prescriptions WHERE patient_id = " . intval($patient_id) . " AND status = 'Active'");
        $current_meds = $current_meds_result ? $current_meds_result->fetch_all(MYSQLI_ASSOC) : [];
        
        
        // Check drug-to-drug interactions
        for ($i = 0; $i < count($drug_ids); $i++) {
            for ($j = $i + 1; $j < count($drug_ids); $j++) {
                $drug_id_1 = intval($drug_ids[$i]);
                $drug_id_2 = intval($drug_ids[$j]);
                
                $stmt = $this->conn->prepare("SELECT di.*, d1.drug_name as drug1_name, d2.drug_name as drug2_name
                                             FROM drug_interactions di
                                             JOIN drugs d1 ON di.drug_id_1 = d1.id
                                             JOIN drugs d2 ON di.drug_id_2 = d2.id
                                             WHERE (di.drug_id_1 = ? AND di.drug_id_2 = ?)
                                             OR (di.drug_id_1 = ? AND di.drug_id_2 = ?)");
                $stmt->bind_param("iiii", $drug_id_1, $drug_id_2, $drug_id_2, $drug_id_1);
                $stmt->execute();
                $result = $stmt->get_result();
                
                while ($interaction = $result->fetch_assoc()) {
                    $warnings[] = [
                        'type' => 'Drug Interaction',
                        'severity' => $interaction['interaction_severity'],
                        'message' => "Interaction between {$interaction['drug1_name']} and {$interaction['drug2_name']}",
                        'description' => $interaction['interaction_description'],
                        'recommendation' => $interaction['recommendation']
                    ];
                }
            }
        }
        
        // Check drug allergies
        foreach ($drug_ids as $drug_id) {
            $drug_id = intval($drug_id);
            $stmt = $this->conn->prepare("SELECT da.allergy_name, da.severity, d.drug_name
                                         FROM drug_allergies da
                                         JOIN drugs d ON da.drug_id = d.id
                                         WHERE da.drug_id = ?");
            $stmt->bind_param("i", $drug_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($allergy = $result->fetch_assoc()) {
                if (!empty($patient_allergies) && stripos($patient_allergies, $allergy['allergy_name']) !== false) {
                    $warnings[] = [
                        'type' => 'Allergy',
                        'severity' => 'Critical',
                        'message' => "Patient is allergic to {$allergy['drug_name']}",
                        'description' => "Allergy: {$allergy['allergy_name']}",
                        'recommendation' => 'Use alternative medication immediately'
                    ];
                }
            }
        }
        
        // Check for duplicate medications
        $drug_names = [];
        foreach ($drug_ids as $drug_id) {
            $drug_id = intval($drug_id);
            $drug = $this->conn->query("SELECT drug_name FROM drugs WHERE id = $drug_id")->fetch_assoc();
            if ($drug) {
                $drug_names[] = $drug['drug_name'];
            }
        }
        
        $duplicates = array_filter(array_count_values($drug_names), function($count) {
            return $count > 1;
        });
        
        foreach ($duplicates as $drug_name => $count) {
            $warnings[] = [
                'type' => 'Duplicate',
                'severity' => 'Medium',
                'message' => "Duplicate medication: $drug_name",
                'description' => "This medication appears $count times in the prescription",
                'recommendation' => 'Remove duplicate entries'
            ];
        }
        
        // Sort warnings by severity
        $severity_order = ['Critical' => 0, 'Severe' => 1, 'High' => 2, 'Moderate' => 3, 'Medium' => 4, 'Mild' => 5, 'Low' => 6];
        usort($warnings, function($a, $b) use ($severity_order) {
            $a_sev = $severity_order[$a['severity']] ?? 99;
            $b_sev = $severity_order[$b['severity']] ?? 99;
            return $a_sev <=> $b_sev;
        });
        
        return $warnings;
    }
    
        /**
     * Save assessment to database
     */
    public function saveAssessment($patient_id, $assessment_data) {
        // FIX: Extract array values into actual variables first 
        // (PHP 8+ requires variables for bind_param references)
        $symptoms_text = $assessment_data['symptoms_text'] ?? '';
        $age = (int)($assessment_data['age'] ?? 0);
        $gender = $assessment_data['gender'] ?? 'Male';
        $weight_kg = $assessment_data['weight_kg'] ?? 0;
        $medical_conditions = $assessment_data['medical_conditions'] ?? '';
        $allergies = $assessment_data['allergies'] ?? '';
        $current_medications = $assessment_data['current_medications'] ?? '';
        $predicted_diseases = $assessment_data['predicted_diseases'] ?? '';
        $confidence_scores = $assessment_data['confidence_scores'] ?? '';
        $risk_level = $assessment_data['risk_level'] ?? 'Low';
        $recommended_department = $assessment_data['recommended_department'] ?? 'General Outpatient';
        $priority_status = $assessment_data['priority_status'] ?? 'Normal';

        $stmt = $this->conn->prepare("INSERT INTO patient_assessments 
            (patient_id, symptoms_text, age, gender, weight_kg, medical_conditions, allergies, 
             current_medications, predicted_diseases, confidence_scores, risk_level, 
             recommended_department, priority_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            return false;
        }

        // Now bind the actual variables
        $stmt->bind_param(
            "isissssssssss",
            $patient_id,
            $symptoms_text,
            $age,
            $gender,
            $weight_kg,
            $medical_conditions,
            $allergies,
            $current_medications,
            $predicted_diseases,
            $confidence_scores,
            $risk_level,
            $recommended_department,
            $priority_status
        );
        
        return $stmt->execute();
    }
    
        /**
     * Save prescription warning
     */
    public function saveWarning($prescription_id, $patient_id, $doctor_id, $warning) {
        // FIX: Extract values into variables
        $warning_type = $warning['type'] ?? 'Unknown';
        $severity = $warning['severity'] ?? 'Medium';
        $warning_message = $warning['message'] ?? '';
        $recommendation = $warning['recommendation'] ?? '';

        $stmt = $this->conn->prepare("INSERT INTO prescription_warnings 
            (prescription_id, patient_id, doctor_id, warning_type, severity, warning_message, recommendation) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
            
        if (!$stmt) {
            return false;
        }

        // Bind the actual variables
        $stmt->bind_param(
            "iisssss",
            $prescription_id,
            $patient_id,
            $doctor_id,
            $warning_type,
            $severity,
            $warning_message,
            $recommendation
        );
        
        return $stmt->execute();
    }
}
?>