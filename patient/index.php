<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../config/db.php';
include '../config/functions.php';

// Redirect if already logged in
if (isset($_SESSION['patient_id'])) {
    header("Location: dashboard.php");
    exit();
}

$msg = "";
$msgType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $msg = "❌ Please enter both email and password!";
        $msgType = "error";
    } else {
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
                
                header("Location: dashboard.php");
                exit();
            } else {
                $msg = "❌ Invalid password!";
                $msgType = "error";
            }
        } else {
            $msg = "❌ Email not registered!";
            $msgType = "error";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Login | Alfurqan Clinic</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .login-box { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 15px 50px rgba(0,0,0,0.3); width: 100%; max-width: 420px; }
        .logo-container { text-align: center; margin-bottom: 25px; }
        .logo-container img { width: 80px; height: 80px; object-fit: contain; }
        h2 { color: #4f46e5; text-align: center; margin-bottom: 30px; font-size: 24px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #555; font-weight: 600; font-size: 14px; }
        input { width: 100%; padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: 0.3s; }
        input:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        button { width: 100%; padding: 14px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        button:hover { transform: translateY(-2px); box-shadow: 0 5px 20px rgba(79, 70, 229, 0.4); }
        .alert { padding: 13px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
        .alert-error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #4f46e5; text-decoration: none; font-size: 14px; font-weight: 500; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo-container">
            <img src="../assets/logo.png" alt="Alfurqan Clinic">
        </div>
        <h2>Patient Portal Login</h2>
        
        <?php if($msg != ""): ?>
            <div class="alert alert-error"><?php echo $msg; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" name="login">Login to Portal</button>
        </form>
        
        <a href="../index.php" class="back-link">← Back to Main Login</a>
    </div>
</body>
</html>