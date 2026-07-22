<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../config/db.php';
include '../config/functions.php';
checkPharmacist();

$pharmacist_id = $_SESSION['user_id'];

// Stats
$total_drugs = $conn->query("SELECT COUNT(*) as total FROM drugs")->fetch_assoc()['total'] ?? 0;
$low_stock = $conn->query("SELECT COUNT(*) as total FROM drugs WHERE quantity_in_stock <= reorder_level")->fetch_assoc()['total'] ?? 0;
$expiring_soon = $conn->query("SELECT COUNT(*) as total FROM drugs WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)")->fetch_assoc()['total'] ?? 0;
$pending_rx = $conn->query("SELECT COUNT(*) as total FROM prescriptions WHERE status = 'Pending'")->fetch_assoc()['total'] ?? 0;

// Pending Prescriptions
$pending_prescriptions = $conn->query("SELECT p.*, pt.name as patient_name, u.username as doctor_name 
                                       FROM prescriptions p 
                                       JOIN patients pt ON p.patient_id = pt.id 
                                       JOIN users u ON p.doctor_id = u.id 
                                       WHERE p.status = 'Pending' ORDER BY p.prescribed_date DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pharmacy Dashboard | Alfurqan Clinic</title>
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
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 5px solid #0f766e; }
        .stat-card h3 { color: #6b7280; font-size: 14px; margin-bottom: 10px; }
        .stat-card p { color: #0f766e; font-size: 32px; font-weight: bold; }
        .stat-card.warning { border-left-color: #f59e0b; } .stat-card.warning p { color: #f59e0b; }
        .stat-card.danger { border-left-color: #ef4444; } .stat-card.danger p { color: #ef4444; }
        .section { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .section h3 { color: #0f766e; margin-bottom: 20px; border-bottom: 2px solid #f0fdfa; padding-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #f0fdfa; }
        th { background: #f0fdfa; color: #0f766e; font-size: 13px; text-transform: uppercase; }
        .btn { padding: 6px 12px; background: #0f766e; color: white; text-decoration: none; border-radius: 4px; font-size: 12px; }
        .btn:hover { background: #0d5e58; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/logo.png" alt="Logo">
            <h2>PHARMACY</h2><small>Alfurqan Clinic</small>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="active">📊 Dashboard</a>
            <a href="inventory.php"> Inventory</a>
            <a href="add_drug.php">➕ Add Drug</a>
            <a href="dispense.php">📤 Dispense</a>
            <a href="sales_records.php">📈 Sales Records</a>
            <a href="low_stock.php">⚠️ Low Stock</a>
            <a href="../logout.php" class="logout-btn">🚪 Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>📊 Pharmacy Dashboard</h1>
            <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card"><h3>Total Drugs</h3><p><?php echo $total_drugs; ?></p></div>
            <div class="stat-card warning"><h3>Low Stock</h3><p><?php echo $low_stock; ?></p></div>
            <div class="stat-card danger"><h3>Expiring Soon</h3><p><?php echo $expiring_soon; ?></p></div>
            <div class="stat-card"><h3>Pending Prescriptions</h3><p><?php echo $pending_rx; ?></p></div>
        </div>
        
        <div class="section">
            <h3>📋 Pending Prescriptions from Doctors</h3>
            <?php if ($pending_prescriptions->num_rows > 0): ?>
            <table>
                <thead><tr><th>Date</th><th>Patient</th><th>Drug</th><th>Dosage</th><th>Qty</th><th>Doctor</th><th>Action</th></tr></thead>
                <tbody>
                    <?php while($rx = $pending_prescriptions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($rx['prescribed_date'])); ?></td>
                        <td><?php echo htmlspecialchars($rx['patient_name']); ?></td>
                        <td><?php echo htmlspecialchars($rx['drug_name']); ?></td>
                        <td><?php echo htmlspecialchars($rx['dosage']); ?></td>
                        <td><?php echo $rx['quantity']; ?></td>
                        <td>Dr. <?php echo htmlspecialchars($rx['doctor_name']); ?></td>
                        <td><a href="dispense.php?rx_id=<?php echo $rx['id']; ?>" class="btn">📤 Dispense</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align: center; color: #6b7280; padding: 20px;">✅ No pending prescriptions.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>