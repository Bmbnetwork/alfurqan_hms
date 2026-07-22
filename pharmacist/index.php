<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../config/db.php';
include '../config/functions.php';

if (isset($_SESSION['role']) && $_SESSION['role'] === 'pharmacist') {
    header("Location: dashboard.php");
    exit();
}

$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = 'pharmacist'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = 'pharmacist';
            logActivity($conn, $user['id'], $user['username'], 'LOGIN', "Pharmacist logged in");
            header("Location: dashboard.php");
            exit();
        } else {
            $msg = "❌ Invalid password!";
        }
    } else {
        $msg = "❌ User not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pharmacist Login | Alfurqan Clinic</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #0f766e 0%, #14b8a6 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; }
        .login-box { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 15px 50px rgba(0,0,0,0.3); width: 100%; max-width: 400px; }
        .logo-container { text-align: center; margin-bottom: 20px; }
        .logo-container img { width: 80px; }
        h2 { color: #0f766e; text-align: center; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; color: #555; font-weight: 600; }
        input { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; }
        input:focus { outline: none; border-color: #0f766e; }
        button { width: 100%; padding: 14px; background: #0f766e; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; }
        button:hover { background: #0d5e58; }
        .alert { padding: 12px; background: #f8d7da; color: #721c24; border-radius: 6px; margin-bottom: 20px; text-align: center; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #0f766e; text-decoration: none; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="logo-container"><img src="../assets/logo.png" alt="Logo"></div>
        <h2>Pharmacy Portal</h2>
        <?php if($msg): ?><div class="alert"><?php echo $msg; ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
            <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
            <button type="submit" name="login">Login</button>
        </form>
        <a href="../index.php" class="back-link">← Back to Main Login</a>
    </div>
</body>
</html>