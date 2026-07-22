<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../config/db.php';
include '../config/functions.php';
checkPharmacist();

$sales = $conn->query("SELECT ds.*, d.drug_name, p.name as patient_name, u.username as pharmacist_name 
                       FROM drug_sales ds 
                       JOIN drugs d ON ds.drug_id = d.id 
                       LEFT JOIN patients p ON ds.patient_id = p.id 
                       JOIN users u ON ds.pharmacist_id = u.id 
                       ORDER BY ds.sale_date DESC");

$total_revenue = $conn->query("SELECT SUM(total_amount) as total FROM drug_sales WHERE DATE(sale_date) = CURDATE()")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Records | Alfurqan Clinic</title>
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
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .card h3 { color: #0f766e; margin-bottom: 20px; border-bottom: 2px solid #f0fdfa; padding-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #f0fdfa; font-size: 14px; }
        th { background: #f0fdfa; color: #0f766e; font-size: 13px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="../assets/logo.png" alt="Logo">
            <h2>PHARMACY</h2><small>Alfurqan Clinic</small>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php"> Dashboard</a>
            <a href="inventory.php">💊 Inventory</a>
            <a href="add_drug.php">➕ Add Drug</a>
            <a href="dispense.php">📤 Dispense</a>
            <a href="sales_records.php" class="active"> Sales Records</a>
            <a href="low_stock.php">⚠️ Low Stock</a>
            <a href="../logout.php" class="logout-btn">🚪 Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="header"><h1>📈 Sales Records</h1></div>
        
        <div class="stats-grid">
            <div class="stat-card"><h3>Today's Revenue</h3><p>₦<?php echo number_format($total_revenue, 2); ?></p></div>
        </div>
        
        <div class="card">
            <h3>All Drug Sales</h3>
            <table>
                <thead><tr><th>Date/Time</th><th>Patient</th><th>Drug</th><th>Qty</th><th>Unit Price</th><th>Total</th><th>Pharmacist</th></tr></thead>
                <tbody>
                    <?php while($s = $sales->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d M Y, g:i A', strtotime($s['sale_date'])); ?></td>
                        <td><?php echo $s['patient_name'] ? htmlspecialchars($s['patient_name']) : 'Walk-in'; ?></td>
                        <td><?php echo htmlspecialchars($s['drug_name']); ?></td>
                        <td><?php echo $s['quantity_dispensed']; ?></td>
                        <td>₦<?php echo number_format($s['unit_price'], 2); ?></td>
                        <td><strong>₦<?php echo number_format($s['total_amount'], 2); ?></strong></td>
                        <td><?php echo htmlspecialchars($s['pharmacist_name']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>