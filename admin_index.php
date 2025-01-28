<?php
session_start(); // เริ่มต้น session

// ตรวจสอบว่าผู้ใช้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sports_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// กำหนดตัวแปรประเภทกีฬา (ถ้ามีการเลือก)
$sport = isset($_POST['sports']) ? filter_var($_POST['sports'], FILTER_SANITIZE_STRING) : '';

// สร้างคำสั่ง SQL สำหรับดึงข้อมูล
if ($sport) {
    $sql = "SELECT id, match_name_1, match_name_2, CONCAT(score_team_1, ' - ', score_team_2) AS score, sports FROM results WHERE sports = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $sport);
} else {
    $sql = "SELECT id, match_name_1, match_name_2, CONCAT(score_team_1, ' - ', score_team_2) AS score, sports FROM results";
    $stmt = $conn->prepare($sql);
}

// ประมวลผลคำสั่ง SQL
$stmt->execute();
$result = $stmt->get_result();

// ปิด statement แต่ยังไม่ปิด connection (ยังใช้ต่อได้)
$stmt->close();
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
        <div class="container">
            <div>
                <!-- ลิงก์ออกจากระบบ -->
                <a href="admin_logout.php" style="display: inline-block; padding: 10px 20px; margin-top: 20px; background-color:rgb(255, 0, 0); color: white; text-decoration: none; border-radius: 5px;">ออกจากระบบ</a> |
                <a href="admin_dashboard.php" style="display: inline-block; padding: 10px 20px; margin-top: 20px; background-color:rgb(43, 163, 65); color: white; text-decoration: none; border-radius: 5px;">แดชบอร์ด</a>
            </div>
        </div>
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
        <div class="button-container">
            <a href="add_match.php" class="button-link">เพิ่มข้อมูลการแข่งขัน</a>
        </div>
    </div>

    <footer>
        <p>รายงานผลการแข่งขันกีฬา</p>
    </footer>
</body>
</html>
