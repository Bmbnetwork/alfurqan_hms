<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../config/db.php';
include '../config/functions.php';
checkPharmacist();

$low_stock_drugs = $conn->query("SELECT * FROM drugs WHERE quantity_in_stock <= reorder_level ORDER BY quantity_in_stock ASC");
$expiring_drugs = $conn->query("SELECT * FROM drugs WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) ORDER BY expiry_date ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Low Stock Alerts | Alfurqan Clinic</title>
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
        .section { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .section h3 { color: #0f766e; margin-bottom: 20px; border-bottom: 2px solid #f0fdfa; padding-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #f0fdfa; font-size: 14px; }
        th { background: #f0fdfa; color: #0f766e; font-size: 13px; text-transform: uppercase; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; color: white; }
        .bg-low { background: #f59e0b; } .bg-expiry { background: #ef4444; }
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
            <a href="add_drug.php">➕ Add Drug</a>
            <a href="dispense.php">📤 Dispense</a>
            <a href="sales_records.php">📈 Sales Records</a>
            <a href="low_stock.php" class="active">⚠️ Low Stock</a>
            <a href="../logout.php" class="logout-btn">🚪 Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="header"><h1>⚠️ Inventory Alerts</h1></div>
        
        <div class="section">
            <h3> Low Stock Drugs (Below Reorder Level)</h3>
            <?php if ($low_stock_drugs->num_rows > 0): ?>
            <table>
                <thead><tr><th>Drug Name</th><th>Category</th><th>Current Stock</th><th>Reorder Level</th><th>Supplier</th></tr></thead>
                <tbody>
                    <?php while($d = $low_stock_drugs->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($d['drug_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($d['category']); ?></td>
                        <td style="color: #ef4444; font-weight: bold;"><?php echo $d['quantity_in_stock']; ?></td>
                        <td><?php echo $d['reorder_level']; ?></td>
                        <td><?php echo htmlspecialchars($d['supplier']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align: center; color: #6b7280; padding: 20px;">✅ All drugs are well stocked!</p>
            <?php endif; ?>
        </div>
        
        <div class="section">
            <h3> Expiring Soon (Within 30 Days)</h3>
            <?php if ($expiring_drugs->num_rows > 0): ?>
            <table>
                <thead><tr><th>Drug Name</th><th>Batch No</th><th>Expiry Date</th><th>Stock</th></tr></thead>
                <tbody>
                    <?php while($d = $expiring_drugs->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($d['drug_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($d['batch_number']); ?></td>
                        <td style="color: #ef4444; font-weight: bold;"><?php echo date('d M Y', strtotime($d['expiry_date'])); ?></td>
                        <td><?php echo $d['quantity_in_stock']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align: center; color: #6b7280; padding: 20px;">✅ No drugs expiring soon.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>