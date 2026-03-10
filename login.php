<?php
require_once 'includes/config.php';

// إذا كان مسجل دخول بالفعل، يذهب للوحة التحكم
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    
    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            if ($remember) {
                setcookie('user_id', $user['id'], time() + (86400 * 30), '/'); // 30 يوم
            }
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'كلمة المرور غير صحيحة';
        }
    } else {
        $error = 'البريد الإلكتروني غير مسجل';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a1a2e, #16213e); display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { background: white; border-radius: 20px; padding: 40px; width: 100%; max-width: 400px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .logo { text-align: center; font-size: 2rem; font-weight: bold; color: #ff7b00; margin-bottom: 20px; }
        .btn-primary { background: #ff7b00; border: none; }
        .btn-primary:hover { background: #e06b00; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">🔥 Fire Load</div>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">كلمة المرور</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">تذكرني</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">تسجيل الدخول</button>
        </form>
        <div class="mt-3 text-center">
            ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a>
        </div>
    </div>
</body>
</html>
