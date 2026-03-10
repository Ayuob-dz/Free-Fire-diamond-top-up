<?php
require_once 'includes/auth.php';
$user = $current_user;

// جلب الباقات المتاحة
$packages = $conn->query("SELECT * FROM packages WHERE active=1 ORDER BY sort_order");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['package_id'])) {
    $package_id = intval($_POST['package_id']);
    $player_id = $conn->real_escape_string($_POST['player_id']);
    
    // جلب بيانات الباقة
    $pkg = $conn->query("SELECT * FROM packages WHERE id = $package_id")->fetch_assoc();
    if (!$pkg) {
        $error = "الباقة غير موجودة";
    } elseif ($user['wallet_balance'] < $pkg['price']) {
        $error = "رصيدك غير كافٍ، يرجى شحن المحفظة";
    } else {
        // خصم الرصيد وإنشاء الطلب
        $conn->begin_transaction();
        try {
            // خصم الرصيد
            $new_balance = $user['wallet_balance'] - $pkg['price'];
            $conn->query("UPDATE users SET wallet_balance = $new_balance WHERE id = {$user['id']}");
            
            // إنشاء الطلب
            $stmt = $conn->prepare("INSERT INTO orders (user_id, package_id, player_id, amount, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->bind_param("iisd", $user['id'], $package_id, $player_id, $pkg['price']);
            $stmt->execute();
            $order_id = $conn->insert_id;
            
            $conn->commit();
            
            // إرسال إشعار للأدمن
            sendTelegramMessage("🛒 طلب جديد رقم $order_id من {$user['username']} - {$pkg['name']}");
            
            $success = "تم إنشاء الطلب بنجاح. سيتم شحن حسابك خلال دقائق.";
        } catch (Exception $e) {
            $conn->rollback();
            $error = "حدث خطأ، حاول مرة أخرى.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>شراء ألماس</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>شراء ألماس</h2>
        <div class="row">
            <div class="col-md-8">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                
                <div class="row">
                    <?php while($pkg = $packages->fetch_assoc()): ?>
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5><?php echo $pkg['name']; ?></h5>
                                <p><?php echo $pkg['description']; ?></p>
                                <h4 class="text-warning">$<?php echo $pkg['price']; ?></h4>
                                <button class="btn btn-primary" onclick="showBuyModal(<?php echo $pkg['id']; ?>, '<?php echo $pkg['name']; ?>', <?php echo $pkg['price']; ?>)">شراء</button>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>رصيدك الحالي</h5>
                        <h3>$<?php echo number_format($user['wallet_balance'], 2); ?></h3>
                        <a href="wallet.php" class="btn btn-sm btn-primary">شحن المحفظة</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for confirmation -->
    <div class="modal fade" id="buyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تأكيد الشراء</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="package_id" id="modalPackageId">
                        <p>الباقة: <strong id="modalPackageName"></strong></p>
                        <p>السعر: <strong id="modalPrice"></strong> $</p>
                        <div class="mb-3">
                            <label>ID فري فاير الخاص بك</label>
                            <input type="text" name="player_id" class="form-control" value="<?php echo $user['freefire_id']; ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">تأكيد الشراء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showBuyModal(id, name, price) {
            document.getElementById('modalPackageId').value = id;
            document.getElementById('modalPackageName').innerText = name;
            document.getElementById('modalPrice').innerText = price;
            new bootstrap.Modal(document.getElementById('buyModal')).show();
        }
    </script>
</body>
</html>
