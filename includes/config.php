<?php
session_start();

// إعدادات قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'fire_load');

// الاتصال بقاعدة البيانات
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// إعدادات الموقع
define('SITE_NAME', 'Fire Load');
define('SITE_URL', 'http://localhost/project');

// إعدادات تليجرام (البوت)
define('TELEGRAM_BOT_TOKEN', '7423907926:AAHdcrw76o6XH54nvGUk1IO7RGQ6j7BCFYY');
define('TELEGRAM_CHAT_ID_ADMIN', '7130722086'); // معرف الأدمن

// دالة لتسجيل الأخطاء
function log_error($message) {
    global $conn;
    $message = $conn->real_escape_string($message);
    $conn->query("INSERT INTO system_logs (log_type, message) VALUES ('error', '$message')");
}

// تضمين الدوال المساعدة
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
?>
