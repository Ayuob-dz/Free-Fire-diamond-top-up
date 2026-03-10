<?php
// هذا الملف يتم تضمينه في الصفحات التي تتطلب تسجيل دخول
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// تحديث آخر نشاط
$_SESSION['last_activity'] = time();

// جلب بيانات المستخدم الحالي
$current_user = getCurrentUser();
?>
