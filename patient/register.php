<?php
session_start();
include '../config/db.php';

$msg = "";
$msgType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    if (empty($name) || empty($email) || empty($password)) {
        $errors[] = "All required fields must be filled!";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters!";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format!";
    }
    
    if (empty($errors)) {
        $check = $conn->prepare("SELECT id FROM patient_users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        
        if ($check->get_result()->num_rows > 0) {
            $errors[] = "Email already registered! Please login instead.";
        }
    }
    
    if (empty($errors)) {
        try {
            $conn->begin_transaction();
            
            $stmt = $conn->prepare("INSERT INTO patients (name, age, gender, phone, address) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sisss", $name, $age, $gender, $phone, $address);
            $stmt->execute();
            $patient_id = $conn->insert_id;
            
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO patient_users (patient_id, email, password, phone) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $patient_id, $email, $hashed_password, $phone);
            $stmt->execute();
            
            $conn->commit();
            
            $msg = "✅ Registration successful! Please login with your credentials.";
            $msgType = "success";
        } catch (Exception $e) {
            $conn->rollback();
            $msg = "❌ Registration failed: " . $e->getMessage();
            $msgType = "error";
        }
    } else {
        $msg = "❌ " . implode(" ", $errors);
        $msgType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration | Alfurqan Clinic</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #003366 0%, #0066cc 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .register-box { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 15px 50px rgba(0,0,0,0.3); width: 100%; max-width: 600px; }
        .logo-container { text-align: center; margin-bottom: 25px; }
        .logo-container img { width: 80px; }
        h2 { color: #003366; text-align: center; margin-bottom: 30px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #555; font-weight: 600; }
        input, select, textarea { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #003366; }
        button { width: 100%; padding: 14px; background: #003366; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; }
        button:hover { background: #002244; }
        .alert { padding: 13px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #003366; text-decoration: none; }
    </style>
</head>
<body>
    <div class="register-box">
        <div class="logo-container">
            <img src="../assets/logo.png" alt="Alfurqan Clinic">
        </div>
        <h2>Patient Registration</h2>
        
        <?php if($msg != ""): ?>
            <div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Age *</label>
                    <input type="number" name="age" required min="1" max="120">
                </div>
                <div class="form-group">
                    <label>Gender *</label>
                    <select name="gender" required>
                        <option value="">Select</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label>Phone Number *</label>
                <input type="text" name="phone" required>
            </div>
            
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" rows="2"></textarea>
            </div>
            
            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm_password" required>
                </div>
            </div>
            
            <button type="submit" name="register">Register</button>
        </form>
        
        <a href="../index.php" class="back-link">← Back to Home</a>
    </div>
</body>
</html>