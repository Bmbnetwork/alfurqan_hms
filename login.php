<?php
session_start();
include 'config/db.php';
include 'config/functions.php';

// Redirect if already logged in
if (isset($_SESSION['role'])) {
    switch($_SESSION['role']) {
        case 'admin': header("Location: admin/dashboard.php"); exit();
        case 'doctor': header("Location: doctor/dashboard.php"); exit();
        case 'nurse': header("Location: nurse/dashboard.php"); exit();
        case 'pharmacist': header("Location: pharmacist/dashboard.php"); exit();
        case 'lab_technician': header("Location: laboratory/dashboard.php"); exit();
    }
}

if (isset($_SESSION['patient_id'])) {
    header("Location: patient/dashboard.php");
    exit();
}

$msg = "";
$msgType = "";

if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $msg = "✅ You have been successfully logged out!";
    $msgType = "success";
}

// Staff Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['staff_login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            logActivity($conn, $user['id'], $user['username'], 'LOGIN', "User logged in successfully");
            
            switch($user['role']) {
                case 'admin': header("Location: admin/dashboard.php"); break;
                case 'doctor': header("Location: doctor/dashboard.php"); break;
                case 'nurse': header("Location: nurse/dashboard.php"); break;
                case 'pharmacist': header("Location: pharmacist/dashboard.php"); break;
                case 'lab_technician': header("Location: laboratory/dashboard.php"); break;
            }
            exit();
        } else {
            $msg = " Invalid password!";
            $msgType = "error";
        }
    } else {
        $msg = "❌ Username not found!";
        $msgType = "error";
    }
}

// Patient Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['patient_login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM patient_users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['patient_id'] = $user['patient_id'];
            $_SESSION['patient_email'] = $user['email'];
            $_SESSION['patient_user_id'] = $user['id'];
            header("Location: patient/dashboard.php");
            exit();
        } else {
            $msg = "❌ Invalid password!";
            $msgType = "error";
        }
    } else {
        $msg = "❌ Email not registered!";
        $msgType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Alfurqan Clinic</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #003366 0%, #0066cc 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .login-box { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 15px 50px rgba(0,0,0,0.3); width: 100%; max-width: 450px; }
        .logo-container { text-align: center; margin-bottom: 25px; }
        .logo-container img { width: 80px; }
        h2 { color: #003366; text-align: center; margin-bottom: 30px; }
        .tabs { display: flex; gap: 10px; margin-bottom: 25px; }
        .tab { flex: 1; padding: 12px; background: #f0f0f0; border: none; cursor: pointer; font-weight: 600; border-radius: 8px; }
        .tab.active { background: #003366; color: white; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #555; font-weight: 600; }
        input { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; }
        input:focus { outline: none; border-color: #003366; }
        button { width: 100%; padding: 14px; background: #003366; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; }
        button:hover { background: #002244; }
        .alert { padding: 13px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-error { background: #f8d7da; color: #721c24; }
        .form-section { display: none; }
        .form-section.active { display: block; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #003366; text-decoration: none; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo-container">
            <img src="assets/logo.png" alt="Alfurqan Clinic">
        </div>
        <h2>Login to Your Account</h2>
        
        <?php if($msg != ""): ?>
            <div class="alert alert-<?php echo $msgType; ?>"><?php echo $msg; ?></div>
        <?php endif; ?>
        
        <div class="tabs">
            <button class="tab active" onclick="showTab('staff')">Staff Login</button>
            <button class="tab" onclick="showTab('patient')">Patient Login</button>
        </div>
        
        <form method="POST" class="form-section active" id="staff-form">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="staff_login">Login as Staff</button>
        </form>
        
        <form method="POST" class="form-section" id="patient-form">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="patient_login">Login as Patient</button>
        </form>
        
        <a href="index.php" class="back-link">← Back to Home</a>
    </div>
    
    <script>
        function showTab(tab) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.form-section').forEach(f => f.classList.remove('active'));
            
            if (tab === 'staff') {
                document.querySelectorAll('.tab')[0].classList.add('active');
                document.getElementById('staff-form').classList.add('active');
            } else {
                document.querySelectorAll('.tab')[1].classList.add('active');
                document.getElementById('patient-form').classList.add('active');
            }
        }
    </script>
</body>
</html>