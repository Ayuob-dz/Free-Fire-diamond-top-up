<?php
require_once 'includes/auth.php';
$user = $current_user;

$orders = $conn->query("SELECT o.*, p.name as package_name FROM orders o JOIN packages p ON o.package_id = p.id WHERE o.user_id = {$user['id']} ORDER BY o.created_at DESC");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلباتي</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>طلباتي</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>الباقة</th>
                    <th>ID اللاعب</th>
                    <th>المبلغ</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $order['id']; ?></td>
                    <td><?php echo $order['package_name']; ?></td>
                    <td><?php echo $order['player_id']; ?></td>
                    <td>$<?php echo $order['amount']; ?></td>
                    <td>
                        <?php if($order['status']=='pending'): ?>
                            <span class="badge bg-warning">قيد الانتظار</span>
                        <?php elseif($order['status']=='processing'): ?>
                            <span class="badge bg-info">قيد التنفيذ</span>
                        <?php elseif($order['status']=='completed'): ?>
                            <span class="badge bg-success">مكتمل</span>
                        <?php else: ?>
                            <span class="badge bg-danger">فشل</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $order['created_at']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="btn btn-secondary">رجوع</a>
    </div>
</body>
</html>
