<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../config/db.php';
include '../config/functions.php';
checkPharmacist();

$msg = ""; $msgType = "";
$pharmacist_id = $_SESSION['user_id'];

// Handle Dispensing Logic
if (isset($_GET['rx_id'])) {
    $rx_id = intval($_GET['rx_id']);
    
    // Get prescription details
    $rx_stmt = $conn->prepare("SELECT * FROM prescriptions WHERE id = ? AND status = 'Pending'");
    $rx_stmt->bind_param("i", $rx_id);
    $rx_stmt->execute();
    $rx = $rx_stmt->get_result()->fetch_assoc();
    
    if ($rx) {
        // Try to find drug in inventory by name
        $drug_name_clean = $rx['drug_name'];
        $drug_check = $conn->prepare("SELECT * FROM drugs WHERE drug_name = ?");
        $drug_check->bind_param("s", $drug_name_clean);
        $drug_check->execute();
        $drug = $drug_check->get_result()->fetch_assoc();
        
        if ($drug) {
            if ($drug['quantity_in_stock'] >= $rx['quantity']) {
                // Deduct stock
                $new_qty = $drug['quantity_in_stock'] - $rx['quantity'];
                $conn->query("UPDATE drugs SET quantity_in_stock = $new_qty WHERE id = {$drug['id']}");
                
                // Record Sale
                $total_amount = $drug['unit_price'] * $rx['quantity'];
                $sale_stmt = $conn->prepare("INSERT INTO drug_sales (patient_id, prescription_id, drug_id, quantity_dispensed, unit_price, total_amount, pharmacist_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $sale_stmt->bind_param("iiiiidi", $rx['patient_id'], $rx_id, $drug['id'], $rx['quantity'], $drug['unit_price'], $total_amount, $pharmacist_id);
                $sale_stmt->execute();
                
                // Update Prescription Status
                $conn->query("UPDATE prescriptions SET status = 'Dispensed' WHERE id = $rx_id");
                
                $msg = "✅ Successfully dispensed {$rx['quantity']}x {$drug['drug_name']} to patient!"; $msgType = "success";
                logActivity($conn, $pharmacist_id, $_SESSION['username'], 'DISPENSE_DRUG', "Dispensed {$rx['drug_name']} for Rx #$rx_id");
            } else {
                $msg = "❌ Insufficient stock! Available: {$drug['quantity_in_stock']}, Required: {$rx['quantity']}"; $msgType = "error";
            }
        } else {
            $msg = "⚠️ Drug '{$rx['drug_name']}' not found in inventory. Please add it first or dispense manually."; $msgType = "error";
        }
    } else {
        $msg = " Prescription not found or already processed."; $msgType = "error";
    }
}

// Get Pending Prescriptions
$pending_rx = $conn->query("SELECT p.*, pt.name as patient_name FROM prescriptions p JOIN patients pt ON p.patient_id = pt.id WHERE p.status = 'Pending' ORDER BY p.prescribed_date ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dispense Drugs | Alfurqan Clinic</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0fdfa; display: flex; }
        .sidebar { width: 260px; background: #0f766e; color: white; height: 100vh; position: fixed; }
        .sidebar-header { text-align: center; padding: 25px 20px; border-bottom: 1px solid #14b8a6; background: #0d5e58; }
        .sidebar-header img { width: 70px; margin-bottom: 10px; background: white; border-radius: 50%; padding: 5px; }
        .sidebar-header h2 { font-size: 18px; } .sidebar-header small { font-size: 11px; opacity: 0.8; }
        .sidebar-menu a { display: flex; align-items: center; gap: 15px; padding: 15px 25px; color: #ccfbf1; text-decoration: none; border-bottom: 1px solid #14b8a6; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #14b8a6; color: white; padding-left: 30px; }
        .logout-btn { background: #dc2626 !important; justify-content: center; margin-top: 30px; }
        .main-content { margin-left: 260px; padding: 30px; width: calc(100% - 260px); }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #ccfbf1; }
        .header h1 { color: #0f766e; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .card h3 { color: #0f766e; margin-bottom: 20px; border-bottom: 2px solid #f0fdfa; padding-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #f0fdfa; }
        th { background: #f0fdfa; color: #0f766e; font-size: 13px; text-transform: uppercase; }
        .btn { padding: 8px 15px; background: #0f766e; color: white; text-decoration: none; border-radius: 4px; font-size: 13px; border: none; cursor: pointer; }
        .btn:hover { background: #0d5e58; }
        .alert { padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .alert-success { background: #d1fae5; color: #065f46; } .alert-error { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/logo.png" alt="Logo">
            <h2>PHARMACY</h2><small>Alfurqan Clinic</small>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="inventory.php">💊 Inventory</a>
            <a href="add_drug.php"> Add Drug</a>
            <a href="dispense.php" class="active">📤 Dispense</a>
            <a href="sales_records.php">📈 Sales Records</a>
            <a href="low_stock.php">⚠️ Low Stock</a>
            <a href="../logout.php" class="logout-btn">🚪 Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="header"><h1> Dispense Medication</h1></div>
        
        <?php if($msg): ?><div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div><?php endif; ?>
        
        <div class="card">
            <h3>Pending Prescriptions (Click Dispense to Process)</h3>
            <?php if ($pending_rx->num_rows > 0): ?>
            <table>
                <thead><tr><th>Rx ID</th><th>Patient</th><th>Drug</th><th>Dosage</th><th>Qty</th><th>Date</th><th>Action</th></tr></thead>
                <tbody>
                    <?php while($rx = $pending_rx->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $rx['id']; ?></td>
                        <td><?php echo htmlspecialchars($rx['patient_name']); ?></td>
                        <td><strong><?php echo htmlspecialchars($rx['drug_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($rx['dosage']); ?></td>
                        <td><?php echo $rx['quantity']; ?></td>
                        <td><?php echo date('d M Y', strtotime($rx['prescribed_date'])); ?></td>
                        <td>
                            <a href="dispense.php?rx_id=<?php echo $rx['id']; ?>" class="btn" onclick="return confirm('Dispense this medication? Stock will be deducted.');">📤 Dispense</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align: center; color: #6b7280; padding: 20px;">✅ No pending prescriptions to dispense.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>