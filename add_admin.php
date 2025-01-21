<?php
// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sports_db";

try {
    $conn = new mysqli($servername, $username, $password, $dbname);

    // ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // ตรวจสอบการส่งข้อมูล POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $admin_username = $_POST['username'];
        $admin_password = $_POST['password'];

        // ตรวจสอบว่าได้กรอกข้อมูลหรือไม่
        if (empty($admin_username) || empty($admin_password)) {
            echo "กรุณากรอกชื่อผู้ใช้และรหัสผ่าน!";
        } else {
            // แฮชรหัสผ่านก่อนบันทึก
            $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

            // สั่งเพิ่มข้อมูล Admin ใหม่
            $sql = "INSERT INTO admins (username, password) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $admin_username, $hashed_password);

            if ($stmt->execute()) {
                echo "<p style='color: green;'>ผู้ดูแลระบบถูกเพิ่มสำเร็จ!</p>";
            } else {
                echo "<p style='color: red;'>เกิดข้อผิดพลาดในการเพิ่มผู้ดูแลระบบ: " . $stmt->error . "</p>";
            }

            $stmt->close();
        }
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: " . $e->getMessage() . "</p>";
} finally {
    // ปิดการเชื่อมต่อฐานข้อมูล
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มผู้ดูแลระบบ</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>เพิ่มผู้ดูแลระบบใหม่</h1>
        <form method="POST" action="add_admin.php">
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


            <button type="submit">เพิ่มผู้ดูแลระบบ</button>
        </form>
</body>
</html>
