<?php
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

// กำหนดตัวแปรเพื่อเก็บข้อความแจ้งเตือน
$successMessage = "";
$errorMessage = "";

// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์มหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับข้อมูลจากฟอร์ม
    $match_name_1 = $_POST['match_name_1'];
    $match_name_2 = $_POST['match_name_2'];
    $score = $_POST['score'];
    $sports = $_POST['sports'];

    // ตรวจสอบข้อมูลให้แน่ใจว่าไม่ว่าง
    if (empty($match_name_1) || empty($match_name_2) || empty($score) || empty($sports)) {
        $errorMessage = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } else {
        // เตรียมคำสั่ง SQL สำหรับการเพิ่มข้อมูล
        $stmt = $conn->prepare("INSERT INTO results (match_name_1, match_name_2, score, sports) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $match_name_1, $match_name_2, $score, $sports);

        // ประมวลผลคำสั่ง SQL
        if ($stmt->execute()) {
            $successMessage = "ข้อมูลการแข่งขันถูกเพิ่มเรียบร้อยแล้ว!";
        } else {
            $errorMessage = "เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . $stmt->error;
        }

        // ปิด statement
        $stmt->close();
    }
}

// ปิดการเชื่อมต่อ
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มข้อมูลการแข่งขันกีฬา</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>เพิ่มข้อมูลการแข่งขันกีฬา</h1>
    </header>

    <div class="container">
        <!-- แสดงผลข้อความแจ้งเตือน -->
        <?php if ($successMessage): ?>
            <p style="color: green;"><?php echo $successMessage; ?></p>
        <?php elseif ($errorMessage): ?>
            <p style="color: red;"><?php echo $errorMessage; ?></p>
        <?php endif; ?>

        <!-- ฟอร์มกรอกข้อมูล -->
        <form method="POST" action="">
            <div>
                <label for="match_name_1">ชื่อทีม 1:</label>
                <input type="text" id="match_name_1" name="match_name_1" required>
            </div>
            <div>
                <label for="match_name_2">ชื่อทีม 2:</label>
                <input type="text" id="match_name_2" name="match_name_2" required>
            </div>
            <div>
                <label for="score">คะแนน:</label>
                <input type="text" id="score" name="score" required>
            </div>
            <div>
                <label for="sports">ประเภทกีฬา:</label>
                <select id="sports" name="sports" required>
                    <option value="">-- เลือกประเภทกีฬา --</option>
                    <option value="football">ฟุตบอล</option>
                    <option value="basketball">บาสเกตบอล</option>
                    <option value="volleyball">วอลเลย์บอล</option>
                    <option value="tennis">เทนนิส</option>
                </select>
            </div>
            <div>
                <button type="submit">เพิ่มข้อมูล</button>
            </div>
        </form>

        <!-- ปุ่มกลับหน้า index.php -->
        <div>
            <a href="admin_index.php" style="display: inline-block; padding: 10px 20px; margin-top: 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;">
                กลับสู่หน้าแรก
            </a>
        </div>
    </div>

    <footer>
        <p>รายงานผลการแข่งขันกีฬา</p>
    </footer>
</body>
</html>
