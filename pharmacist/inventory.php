<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../config/db.php';
include '../config/functions.php';
checkPharmacist();

$search = $_GET['search'] ?? '';
$query = "SELECT * FROM drugs WHERE drug_name LIKE ? OR category LIKE ? ORDER BY drug_name ASC";
$stmt = $conn->prepare($query);
$search_param = "%$search%";
$stmt->bind_param("ss", $search_param, $search_param);
$stmt->execute();
$drugs = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Drug Inventory | Alfurqan Clinic</title>
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
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .card h3 { color: #0f766e; margin-bottom: 20px; border-bottom: 2px solid #f0fdfa; padding-bottom: 15px; }
        .search-bar { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-bar input { flex: 1; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; }
        .search-bar button { padding: 10px 20px; background: #0f766e; color: white; border: none; border-radius: 6px; cursor: pointer; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #f0fdfa; font-size: 14px; }
        th { background: #f0fdfa; color: #0f766e; font-size: 13px; text-transform: uppercase; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; color: white; }
        .bg-ok { background: #10b981; } .bg-low { background: #f59e0b; } .bg-out { background: #ef4444; }
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
            <a href="inventory.php" class="active"> Inventory</a>
            <a href="add_drug.php">➕ Add Drug</a>
            <a href="dispense.php"> Dispense</a>
            <a href="sales_records.php">📈 Sales Records</a>
            <a href="low_stock.php">️ Low Stock</a>
            <a href="../logout.php" class="logout-btn"> Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="header"><h1>💊 Drug Inventory</h1></div>
        
        <div class="card">
            <h3>All Drugs in Stock</h3>
            <form method="GET" class="search-bar">
                <input type="text" name="search" placeholder="Search by drug name or category..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
            
            <table>
                <thead><tr><th>ID</th><th>Drug Name</th><th>Category</th><th>Strength</th><th>Stock</th><th>Price (₦)</th><th>Expiry</th><th>Status</th></tr></thead>
                <tbody>
                    <?php while($d = $drugs->fetch_assoc()): 
                        $status_class = 'bg-ok'; $status_text = 'In Stock';
                        if($d['quantity_in_stock'] == 0) { $status_class = 'bg-out'; $status_text = 'Out of Stock'; }
                        elseif($d['quantity_in_stock'] <= $d['reorder_level']) { $status_class = 'bg-low'; $status_text = 'Low Stock'; }
                    ?>
                    <tr>
                        <td>#<?php echo $d['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($d['drug_name']); ?></strong><br><small style="color:#666"><?php echo htmlspecialchars($d['generic_name'] ?? ''); ?></small></td>
                        <td><?php echo htmlspecialchars($d['category']); ?></td>
                        <td><?php echo htmlspecialchars($d['strength']); ?></td>
                        <td><?php echo $d['quantity_in_stock']; ?></td>
                        <td>₦<?php echo number_format($d['unit_price'], 2); ?></td>
                        <td><?php echo date('d M Y', strtotime($d['expiry_date'])); ?></td>
                        <td><span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>