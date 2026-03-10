<?php
require_once 'includes/auth.php';
$user = $current_user;

// جلب سجل المعاملات (الإيداعات والطلبات)
$deposits = $conn->query("SELECT * FROM deposits WHERE user_id = {$user['id']} ORDER BY created_at DESC");
$orders = $conn->query("SELECT o.*, p.name as package_name FROM orders o JOIN packages p ON o.package_id = p.id WHERE o.user_id = {$user['id']} ORDER BY o.created_at DESC");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>محفظتي</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar { background: #1a1a2e; min-height: 100vh; }
        .sidebar a { color: white; display: block; padding: 10px; }
        .sidebar a:hover { background: #ff7b00; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3 text-white">
                    <h3>🔥 Fire Load</h3>
                    <hr>
                    <a href="dashboard.php">لوحة التحكم</a>
                    <a href="profile.php">الملف الشخصي</a>
                    <a href="wallet.php" class="active">محفظتي</a>
                    <a href="buy.php">شراء ألماس</a>
                    <a href="orders.php">طلباتي</a>
                    <a href="logout.php">تسجيل الخروج</a>
                </div>
            </div>
            <div class="col-md-9 col-lg-10 p-4">
                <h2>محفظتي</h2>
                <div class="card mb-4">
                    <div class="card-body">
                        <h4>الرصيد الحالي: <span class="text-success">$<?php echo number_format($user['wallet_balance'], 2); ?></span></h4>
                        <a href="deposit.php" class="btn btn-primary">شحن المحفظة</a>
                    </div>
                </div>
                
                <h4>سجل الإيداعات</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>المبلغ</th>
                            <th>رقم العملية</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($dep = $deposits->fetch_assoc()): ?>
                        <tr>
                            <td>$<?php echo $dep['amount']; ?></td>
                            <td><?php echo $dep['transaction_id']; ?></td>
                            <td>
                                <?php if($dep['status']=='pending'): ?>
                                    <span class="badge bg-warning">قيد المراجعة</span>
                                <?php elseif($dep['status']=='approved'): ?>
                                    <span class="badge bg-success">تم القبول</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">مرفوض</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $dep['created_at']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <h4 class="mt-4">سجل عمليات الشراء</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>الباقة</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($ord = $orders->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $ord['package_name']; ?></td>
                            <td>$<?php echo $ord['amount']; ?></td>
                            <td>
                                <?php if($ord['status']=='pending'): ?>
                                    <span class="badge bg-warning">قيد الانتظار</span>
                                <?php elseif($ord['status']=='processing'): ?>
                                    <span class="badge bg-info">قيد التنفيذ</span>
                                <?php elseif($ord['status']=='completed'): ?>
                                    <span class="badge bg-success">مكتمل</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">فشل</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $ord['created_at']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
