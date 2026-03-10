<?php require_once 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>الصفحة الرئيسية</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .navbar { background: #1a1a2e; }
        .navbar-brand { color: #ff7b00 !important; font-weight: bold; }
        .hero { background: linear-gradient(135deg, #1a1a2e, #16213e); color: white; padding: 80px 0; text-align: center; }
        .btn-primary { background: #ff7b00; border: none; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">🔥 Fire Load</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a href="index.php" class="nav-link">الرئيسية</a></li>
                    <li class="nav-item"><a href="#packages" class="nav-link">الباقات</a></li>
                    <li class="nav-item"><a href="payment-methods.php" class="nav-link">طرق الدفع</a></li>
                    <li class="nav-item"><a href="faq.php" class="nav-link">الأسئلة الشائعة</a></li>
                </ul>
                <div>
                    <?php if (isLoggedIn()): ?>
                        <a href="dashboard.php" class="btn btn-outline-light">لوحة التحكم</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-light">تسجيل الدخول</a>
                        <a href="register.php" class="btn btn-primary">إنشاء حساب</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container">
            <h1>اشحن ألماس فري فاير بأفضل الأسعار</h1>
            <p class="lead">شحن فوري وآمن مع هدايا حصرية</p>
            <a href="#packages" class="btn btn-primary btn-lg">تصفح الباقات</a>
        </div>
    </section>

    <section id="packages" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">باقات الألماس</h2>
            <div class="row">
                <?php
                $packages = $conn->query("SELECT * FROM packages WHERE type='diamond' AND active=1 ORDER BY sort_order");
                while ($pkg = $packages->fetch_assoc()):
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <h3><?php echo $pkg['name']; ?></h3>
                            <p><?php echo $pkg['description']; ?></p>
                            <h4 class="text-warning">$<?php echo $pkg['price']; ?></h4>
                            <a href="<?php echo isLoggedIn() ? 'buy.php?package='.$pkg['id'] : 'register.php'; ?>" class="btn btn-primary">اشتر الآن</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    <!-- footer مشابه -->
</body>
</html>
