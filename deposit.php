<?php
require_once 'includes/auth.php';
$user = $current_user;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = floatval($_POST['amount']);
    $transaction_id = $conn->real_escape_string($_POST['transaction_id']);
    
    // رفع الصورة
    if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] == 0) {
        $upload = uploadImage($_FILES['screenshot']);
        if ($upload) {
            $stmt = $conn->prepare("INSERT INTO deposits (user_id, amount, transaction_id, screenshot, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->bind_param("idss", $user['id'], $amount, $transaction_id, $upload);
            if ($stmt->execute()) {
                // إرسال إشعار للأدمن عبر تليجرام
                $msg = "💰 طلب إيداع جديد\n";
                $msg .= "المستخدم: {$user['username']}\n";
                $msg .= "المبلغ: $ $amount\n";
                $msg .= "رقم العملية: $transaction_id\n";
                sendTelegramMessage($msg);
                $success = "تم إرسال طلب الإيداع بنجاح، سيتم مراجعته قريباً.";
            } else {
                $error = "حدث خطأ في حفظ الطلب.";
            }
        } else {
            $error = "فشل رفع الصورة. تأكد من أنها صورة صالحة (jpg, png) وأقل من 5MB.";
        }
    } else {
        $error = "يرجى اختيار صورة للعملية.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>شحن المحفظة</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>شحن المحفظة عبر Binance</h2>
        <div class="card p-4">
            <p>حساب Binance الثابت: <strong>123456789</strong> <button class="btn btn-sm btn-secondary" onclick="navigator.clipboard.writeText('123456789')">نسخ</button></p>
            <p>يرجى تحويل المبلغ المطلوب إلى هذا الحساب ثم تعبئة النموذج أدناه مع إرفاق لقطة شاشة للعملية.</p>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>المبلغ ($)</label>
                    <input type="number" step="0.01" name="amount" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>رقم العملية (Transaction ID)</label>
                    <input type="text" name="transaction_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>لقطة شاشة للعملية</label>
                    <input type="file" name="screenshot" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary">إرسال الطلب</button>
                <a href="wallet.php" class="btn btn-secondary">رجوع</a>
            </form>
        </div>
    </div>
</body>
</html>
