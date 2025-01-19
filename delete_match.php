<?php
// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";  // ใส่ชื่อผู้ใช้ฐานข้อมูล
$password = "";  // ใส่รหัสผ่านฐานข้อมูล
$dbname = "sports_db";   // ใส่ชื่อฐานข้อมูล

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ลบข้อมูล
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // คำสั่ง SQL ลบข้อมูล
    $sql = "DELETE FROM results WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id); // i = integer

    if ($stmt->execute()) {
        $successMessage = "ข้อมูลถูกลบเรียบร้อยแล้ว!";
    } else {
        $errorMessage = "เกิดข้อผิดพลาดในการลบข้อมูล: " . $stmt->error;
    }

    $stmt->close();
}

// ดึงข้อมูลทั้งหมดจากฐานข้อมูล
$sql = "SELECT * FROM results";
$result = $conn->query($sql);

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลบข้อมูลการแข่งขันกีฬา</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>ลบข้อมูลการแข่งขันกีฬา</h1>
    </header>

    <div class="container">
        <!-- แสดงผลข้อความแจ้งเตือน -->
        <?php if (isset($successMessage)): ?>
            <p style="color: green;"><?php echo $successMessage; ?></p>
        <?php elseif (isset($errorMessage)): ?>
            <p style="color: red;"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <!-- ตารางแสดงข้อมูลการแข่งขัน -->
        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>ชื่อทีม</th>
                    <th>คะแนน</th>
                    <th>ประเภทกีฬา</th>
                    <th>ลบ</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['match_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['score']); ?></td>
                            <td><?php echo htmlspecialchars($row['sports']); ?></td>
                            <td>
                                <!-- ปุ่มลบ -->
                                <a href="delete_match.php?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้?');">
                                    ลบ
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">ไม่มีข้อมูล</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- ปุ่มกลับไปที่หน้าหลัก -->
        <div>
            <a href="index.php" style="display: inline-block; padding: 10px 20px; margin-top: 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">
                กลับสู่หน้าแรก
            </a>
        </div>
    </div>

    <footer>
        <p>รายงานผลการแข่งขันกีฬา</p>
    </footer>

</body>
</html>
