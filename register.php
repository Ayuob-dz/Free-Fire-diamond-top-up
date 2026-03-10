<?php
require_once 'includes/config.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $freefire_id = $conn->real_escape_string($_POST['freefire_id']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    
    // التحقق
    if ($password != $confirm) {
        $error = 'كلمة المرور غير متطابقة';
    } else {
        // التحقق من وجود البريد
        $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
        if ($check->num_rows > 0) {
            $error = 'البريد الإلكتروني مستخدم بالفعل';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, phone, freefire_id, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $username, $email, $phone, $freefire_id, $hashed);
            if ($stmt->execute()) {
                $success = 'تم إنشاء الحساب بنجاح، يمكنك تسجيل الدخول الآن';
                // إرسال إشعار للأدمن
                sendTelegramMessage("🆕 مستخدم جديد: $username ($email)");
            } else {
                $error = 'حدث خطأ، حاول مرة أخرى';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إنشاء حساب</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body { background: linear-gradient(135deg, #1a1a2e, #16213e); padding: 20px; }
        .register-card { background: white; border-radius: 20px; padding: 40px; max-width: 600px; margin: 20px auto; }
        .logo { text-align: center; font-size: 2rem; color: #ff7b00; margin-bottom: 20px; }
        .btn-primary { background: #ff7b00; border: none; }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="logo">🔥 Fire Load</div>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>الاسم الكامل</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>البريد الإلكتروني</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>رقم الهاتف</label>
                    <input type="tel" name="phone" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label>ID فري فاير (اختياري)</label>
                    <input type="text" name="freefire_id" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label>كلمة المرور</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>تأكيد كلمة المرور</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">إنشاء الحساب</button>
        </form>
        <div class="mt-3 text-center">
            لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a>
        </div>
    </div>
</body>
</html>
