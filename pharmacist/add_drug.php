<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../config/db.php';
include '../config/functions.php';
checkPharmacist();

$msg = ""; $msgType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_drug'])) {
    $name = $_POST['drug_name'];
    $generic = $_POST['generic_name'];
    $category = $_POST['category'];
    $form = $_POST['dosage_form'];
    $strength = $_POST['strength'];
    $qty = $_POST['quantity'];
    $reorder = $_POST['reorder_level'];
    $price = $_POST['unit_price'];
    $supplier = $_POST['supplier'];
    $batch = $_POST['batch_number'];
    $expiry = $_POST['expiry_date'];
    
    $stmt = $conn->prepare("INSERT INTO drugs (drug_name, generic_name, category, dosage_form, strength, quantity_in_stock, reorder_level, unit_price, supplier, batch_number, expiry_date, added_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiiidssi", $name, $generic, $category, $form, $strength, $qty, $reorder, $price, $supplier, $batch, $expiry, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        $msg = "✅ Drug added successfully!"; $msgType = "success";
        logActivity($conn, $_SESSION['user_id'], $_SESSION['username'], 'ADD_DRUG', "Added drug: $name");
    } else { $msg = "❌ Error: " . $conn->error; $msgType = "error"; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Drug | Alfurqan Clinic</title>
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
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); max-width: 800px; }
        .card h3 { color: #0f766e; margin-bottom: 20px; border-bottom: 2px solid #f0fdfa; padding-bottom: 15px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #374151; font-weight: 600; }
        input, select { width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 6px; }
        button { padding: 12px 25px; background: #0f766e; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }
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
            <a href="inventory.php"> Inventory</a>
            <a href="add_drug.php" class="active">➕ Add Drug</a>
            <a href="dispense.php">📤 Dispense</a>
            <a href="sales_records.php">📈 Sales Records</a>
            <a href="low_stock.php">⚠️ Low Stock</a>
            <a href="../logout.php" class="logout-btn">🚪 Logout</a>
        </div>
    </div>
    
    <div class="main-content">
        <div class="header"><h1>➕ Add New Drug</h1></div>
        
        <?php if($msg): ?><div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div><?php endif; ?>
        
        <div class="card">
            <h3>Drug Details</h3>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group"><label>Drug Name *</label><input type="text" name="drug_name" required></div>
                    <div class="form-group"><label>Generic Name</label><input type="text" name="generic_name"></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Category *</label>
                        <select name="category" required>
                            <option value="Antibiotics">Antibiotics</option>
                            <option value="Analgesics">Analgesics</option>
                            <option value="Antimalarials">Antimalarials</option>
                            <option value="Vitamins">Vitamins</option>
                            <option value="Antihypertensives">Antihypertensives</option>
                            <option value="Antidiabetics">Antidiabetics</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Dosage Form *</label>
                        <select name="dosage_form" required>
                            <option value="Tablet">Tablet</option>
                            <option value="Capsule">Capsule</option>
                            <option value="Syrup">Syrup</option>
                            <option value="Injection">Injection</option>
                            <option value="Cream">Cream</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Strength *</label><input type="text" name="strength" placeholder="e.g., 500mg" required></div>
                    <div class="form-group"><label>Unit Price (₦) *</label><input type="number" step="0.01" name="unit_price" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Quantity in Stock *</label><input type="number" name="quantity" required></div>
                    <div class="form-group"><label>Reorder Level *</label><input type="number" name="reorder_level" value="10" required></div>
                </div>
                <div class="form-row">
                    <div class="form-group"><label>Batch Number</label><input type="text" name="batch_number"></div>
                    <div class="form-group"><label>Expiry Date *</label><input type="date" name="expiry_date" required></div>
                </div>
                <div class="form-group"><label>Supplier</label><input type="text" name="supplier"></div>
                <button type="submit" name="add_drug">💾 Add Drug to Inventory</button>
            </form>
        </div>
    </div>
</body>
</html>