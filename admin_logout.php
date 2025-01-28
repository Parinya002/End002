<?php
session_start();
session_unset();  // ลบข้อมูล session ทั้งหมด
session_destroy(); // ทำลาย session

header("Location: admin_login.php");  // เปลี่ยนเส้นทางไปหน้า login
exit();
?>
