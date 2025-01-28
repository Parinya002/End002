<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ดผู้ดูแล</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header>
    <h1>ยินดีต้อนรับสู่แดชบอร์ดผู้ดูแล</h1>
</header>
<div class="success-message">
    <y>คุณได้เข้าสู่ระบบสำเร็จแล้ว</y>
</div>
<div class="button-container">
    <a href="add_admin.php" class="button-link">เพิ่มผู้ดูแล</a>
    <a href="admin_index.php" class="button-link">ดูข้อมูลการแข่งขัน</a>
    <a href="admin_logout.php" class="button-link">ออกจากระบบ</a>
</div>
</body>
</html>
