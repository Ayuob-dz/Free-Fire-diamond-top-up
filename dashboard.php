<?php
require_once 'includes/auth.php';
$user = $current_user;

// إحصائيات سريعة
$orders_count = $conn->query("SELECT COUNT(*) as total FROM orders WHERE user_id = {$user['id']}")->fetch_assoc()['total'];
$wallet = $user['wallet_balance'];
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar { background: #1a1a2e; min-height: 100vh; color: white; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 10px; margin: 5px 0; border-radius: 5px; }
        .sidebar a:hover, .sidebar a.active { background: #ff7b00; }
        .content { padding: 20px; }
        .stat-card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h3 class="text-center">🔥 Fire Load</h3>
                    <hr>
                    <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> لوحة التحكم</a>
                    <a href="profile.php"><i class="fas fa-user"></i> الملف الشخصي</a>
                    <a href="wallet.php"><i class="fas fa-wallet"></i> محفظتي</a>
                    <a href="buy.php"><i class="fas fa-shopping-cart"></i> شراء ألماس</a>
                    <a href="orders.php"><i class="fas fa-history"></i> طلباتي</a>
                    <a href="payment-methods.php"><i class="fas fa-credit-card"></i> طرق الدفع</a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
                </div>
            </div>
            <!-- Main content -->
            <div class="col-md-9 col-lg-10 content">
                <h2>مرحباً، <?php echo $user['username']; ?></h2>
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h5>رصيد المحفظة</h5>
                            <h3>$<?php echo number_format($wallet, 2); ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h5>عدد الطلبات</h5>
                            <h3><?php echo $orders_count; ?></h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h5>آخر طلب</h5>
                            <h3>-</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
