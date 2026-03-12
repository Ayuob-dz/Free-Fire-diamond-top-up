-- لا نقوم بإنشاء قاعدة البيانات، بل نستخدم الموجودة مسبقاً
-- تم حذف السطر CREATE DATABASE و USE

-- جدول المستخدمين
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    freefire_id VARCHAR(50),
    wallet_balance DECIMAL(10,2) DEFAULT 0.00,
    role ENUM('user','admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- جدول طلبات الإيداع (شحن المحفظة)
CREATE TABLE IF NOT EXISTS deposits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    transaction_id VARCHAR(255),
    screenshot VARCHAR(255),
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- جدول الباقات (المنتجات)
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    type ENUM('diamond','pass','membership') NOT NULL,
    diamonds INT DEFAULT 0,
    price DECIMAL(10,2) NOT NULL,
    cost DECIMAL(10,2), -- سعر التكلفة (للأدمن)
    duration VARCHAR(20), -- weekly, monthly للعضويات
    image VARCHAR(255),
    sort_order INT DEFAULT 0,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول طلبات الشراء (الألماس)
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    package_id INT NOT NULL,
    player_id VARCHAR(50) NOT NULL, -- ID فري فاير
    amount DECIMAL(10,2) NOT NULL, -- المبلغ المخصوم
    status ENUM('pending','processing','completed','failed') DEFAULT 'pending',
    automation_status TEXT, -- تفاصيل من المحاكي
    completed_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (package_id) REFERENCES packages(id)
);

-- جدول بطاقات الفيزا (للأتمتة)
CREATE TABLE IF NOT EXISTS cards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    card_number VARCHAR(255) NOT NULL, -- مشفر
    expiry VARCHAR(10) NOT NULL,
    cvv VARCHAR(255) NOT NULL, -- مشفر
    balance DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('active','blocked','expired') DEFAULT 'active',
    usage_count INT DEFAULT 0,
    last_used DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول سجل النظام (logs)
CREATE TABLE IF NOT EXISTS system_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    log_type VARCHAR(50),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول محادثات التليجرام (اختياري لربط البوت)
CREATE TABLE IF NOT EXISTS telegram_chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chat_id BIGINT NOT NULL,
    user_id INT, -- يمكن ربطه بحساب الموقع
    step VARCHAR(50),
    temp_data TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- إدخال بعض الباقات الافتراضية (إذا لم تكن موجودة)
INSERT IGNORE INTO packages (name, description, type, diamonds, price, cost, sort_order) VALUES
('100 ألماسة', '100 ألماسة + 10 كيلوغا', 'diamond', 100, 1.50, 1.00, 1),
('210 ألماسة', '210 ألماسة + 21 غلاف', 'diamond', 210, 3.00, 2.00, 2),
('530 ألماسة', '530 ألماسة + 53 غلاف', 'diamond', 530, 7.50, 5.00, 3),
('1080 ألماسة', '1,080 ألماسة + 108 غلاف', 'diamond', 1080, 15.00, 10.00, 4),
('2200 ألماسة', '2,200 ألماسة + 220 غلاف', 'diamond', 2200, 30.00, 20.00, 5),
('Booyah Pass', 'تصريح بوابة', 'pass', 0, 4.50, 3.00, 6),
('عضوية أسبوعية', 'اشتراك أسبوعي', 'membership', 0, 3.00, 2.00, 7),
('عضوية شهرية', 'اشتراك شهري', 'membership', 0, 15.00, 10.00, 8);
