<?php
session_start(); // เริ่มต้น session

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['admin_id'])) {
    // ถ้ายังไม่ได้เข้าสู่ระบบ ให้เปลี่ยนเส้นทางไปยังหน้า login
    header("Location: admin_login.php");
    exit();
}

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";  // ชื่อผู้ใช้ฐานข้อมูล
$password = "";  // รหัสผ่านฐานข้อมูล
$dbname = "sports_db";   // ชื่อฐานข้อมูล

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// กำหนดตัวแปรประเภทกีฬา (ถ้ามีการเลือก)
$sport = isset($_POST['sports']) ? $_POST['sports'] : '';

// สร้างคำสั่ง SQL สำหรับดึงข้อมูล
if ($sport) {
    // กรองข้อมูลตามประเภทกีฬา
    $sql = "SELECT id, match_name_1, match_name_2, score, sports FROM results WHERE sports = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sport);
} else {
    // ดึงข้อมูลทั้งหมดถ้าไม่ได้เลือกประเภทกีฬา
    $sql = "SELECT id, match_name_1, match_name_2, score, sports FROM results";
    $stmt = $conn->prepare($sql);
}

// ประมวลผลคำสั่ง SQL
$stmt->execute();
$result = $stmt->get_result();

// ปิดการเชื่อมต่อฐานข้อมูล
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลการแข่งขันทั้งหมด</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>ข้อมูลการแข่งขันกีฬา</h1>
        <!-- ลิงก์ออกจากระบบ -->
        <a href="admin_logout.php" style="text-decoration: none; color: red;">ออกจากระบบ</a> |
        <!-- ลิงก์ไปยังแดชบอร์ด -->
        <a href="admin_dashboard.php" style="text-decoration: none; color: green;">แดชบอร์ด</a>
    </header>

    <div class="container">
        <!-- ฟอร์มสำหรับเลือกประเภทกีฬา -->
        <form method="POST" action="admin_index.php">
            <label for="sports">เลือกประเภทกีฬา:</label>
            <select id="sports" name="sports">
                <option value="">ทั้งหมด</option>
                <option value="football" <?php echo ($sport == 'football') ? 'selected' : ''; ?>>ฟุตบอล</option>
                <option value="basketball" <?php echo ($sport == 'basketball') ? 'selected' : ''; ?>>บาสเกตบอล</option>
                <option value="volleyball" <?php echo ($sport == 'volleyball') ? 'selected' : ''; ?>>วอลเลย์บอล</option>
                <option value="tennis" <?php echo ($sport == 'tennis') ? 'selected' : ''; ?>>เทนนิส</option>
            </select>
            <button type="submit">แสดงผล</button>
        </form>

        <!-- แสดงผลข้อมูลในรูปแบบตาราง -->
        <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>ชื่อทีม 1</th>
                    <th>ชื่อทีม 2</th>
                    <th>คะแนน</th>
                    <th>ประเภทกีฬา</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['match_name_1']); ?></td>
                            <td><?php echo htmlspecialchars($row['match_name_2']); ?></td>
                            <td><?php echo htmlspecialchars($row['score']); ?></td>
                            <td><?php echo htmlspecialchars($row['sports']); ?></td>
                            <td>
                                <a href="edit_match.php?id=<?php echo $row['id']; ?>" style="color: blue;">แก้ไข</a> |
                                <a href="delete_match.php?id=<?php echo $row['id']; ?>" style="color: red;" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้?');">ลบ</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">ไม่มีข้อมูลการแข่งขัน</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- ปุ่มเพิ่มข้อมูล -->
        <div style="margin-top: 20px; text-align: center;">
            <a href="add_match.php" style="display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">เพิ่มข้อมูลการแข่งขัน</a>
        </div>
    </div>

    <footer>
        <p>รายงานผลการแข่งขันกีฬา</p>
    </footer>
</body>
</html>
