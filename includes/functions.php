<?php
/**
 * التحقق من وجود جلسة صالحة
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * إعادة التوجيه إذا لم يكن مسجل دخول
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * إعادة التوجيه إذا كان مسجل دخول بالفعل
 */
function requireGuest() {
    if (isLoggedIn()) {
        header('Location: dashboard.php');
        exit;
    }
}

/**
 * جلب بيانات المستخدم الحالي
 */
function getCurrentUser() {
    global $conn;
    if (!isLoggedIn()) return null;
    $user_id = $_SESSION['user_id'];
    $result = $conn->query("SELECT * FROM users WHERE id = '$user_id'");
    return $result->fetch_assoc();
}

/**
 * تحديث رصيد المحفظة
 */
function updateWalletBalance($user_id, $new_balance) {
    global $conn;
    $new_balance = floatval($new_balance);
    $stmt = $conn->prepare("UPDATE users SET wallet_balance = ? WHERE id = ?");
    $stmt->bind_param("di", $new_balance, $user_id);
    return $stmt->execute();
}

/**
 * إضافة رصيد إلى المحفظة
 */
function addToWallet($user_id, $amount) {
    global $conn;
    $user = $conn->query("SELECT wallet_balance FROM users WHERE id = '$user_id'")->fetch_assoc();
    $new_balance = $user['wallet_balance'] + $amount;
    return updateWalletBalance($user_id, $new_balance);
}

/**
 * خصم رصيد من المحفظة
 */
function deductFromWallet($user_id, $amount) {
    global $conn;
    $user = $conn->query("SELECT wallet_balance FROM users WHERE id = '$user_id'")->fetch_assoc();
    if ($user['wallet_balance'] < $amount) return false;
    $new_balance = $user['wallet_balance'] - $amount;
    return updateWalletBalance($user_id, $new_balance);
}

/**
 * إرسال رسالة إلى تليجرام (تنبيه للأدمن)
 */
function sendTelegramMessage($message, $chat_id = null) {
    if (!$chat_id) $chat_id = TELEGRAM_CHAT_ID_ADMIN;
    $token = TELEGRAM_BOT_TOKEN;
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}

/**
 * التحقق من أن المستخدم أدمن
 */
function isAdmin() {
    $user = getCurrentUser();
    return $user && $user['role'] == 'admin';
}

/**
 * رفع صورة (للإثباتات)
 */
function uploadImage($file, $target_dir = 'assets/uploads/') {
    $target_file = $target_dir . time() . '_' . basename($file['name']);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // التحقق من أن الملف صورة
    $check = getimagesize($file['tmp_name']);
    if ($check === false) return false;
    
    // التحقق من الحجم (5MB كحد أقصى)
    if ($file['size'] > 5000000) return false;
    
    // السماح ببعض الصيغ
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) return false;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $target_file;
    }
    return false;
}
?>
