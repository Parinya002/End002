<?php
session_start();

// ตรวจสอบการส่งข้อมูลจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // เชื่อมต่อฐานข้อมูล
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sports_db";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // รับค่าจากฟอร์ม
    $admin_username = $_POST['username'];
    $admin_password = $_POST['password'];

    // ค้นหาผู้ใช้จากฐานข้อมูล
    $sql = "SELECT * FROM admins WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $admin_username, $admin_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // ถ้าพบผู้ใช้ให้เริ่ม session และไปยังหน้าแดชบอร์ด
        $_SESSION['admin_id'] = $admin_username;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        // ถ้าไม่พบผู้ใช้
        $error_message = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h2>เข้าสู่ระบบ</h2>
    </header>
    <?php
    if (isset($error_message)) {
        echo "<p style='color:red;'>$error_message</p>";
    }
    ?>
    <form method="POST" action="admin_login.php">
        <label for="username">ชื่อผู้ใช้:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">รหัสผ่าน:</label>
<input type="password" id="password" name="password" required>
<button type="button" id="togglePassword">แสดง</button><br>

<script>
    const passwordField = document.getElementById("password");
    const togglePassword = document.getElementById("togglePassword");

    togglePassword.addEventListener("click", function() {
        const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
        passwordField.setAttribute("type", type);
        this.textContent = type === "password" ? "แสดง" : "ซ่อน";
    });
</script>
        <button type="submit">เข้าสู่ระบบ</button>
    </form>
</body>
</html>
