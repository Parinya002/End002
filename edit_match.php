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

// ตรวจสอบว่าได้รับ match_id มาจาก URL หรือไม่
$match_id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;

if ($match_id) {
    // ดึงข้อมูลการแข่งขันจากฐานข้อมูลโดยใช้ match_id
    $stmt = $conn->prepare("SELECT match_name_1, match_name_2, score, sports FROM results WHERE id = ?");
    $stmt->bind_param("i", $match_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // ตรวจสอบว่ามีข้อมูลการแข่งขัน
    if ($result->num_rows > 0) {
        $match = $result->fetch_assoc();
    } else {
        die("ไม่พบข้อมูลการแข่งขันนี้");
    }
} else {
    die("ไม่มี match_id ที่จะทำการแก้ไข");
}

// ตรวจสอบว่าได้รับการส่งฟอร์ม (POST) หรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์ม
    $match_name_1 = $_POST['match_name_1'];
    $match_name_2 = $_POST['match_name_2'];
    $score = $_POST['score'];
    $sports = $_POST['sports'];

    // อัพเดตข้อมูลการแข่งขันในฐานข้อมูล
    $stmt = $conn->prepare("UPDATE results SET match_name_1 = ?, match_name_2 = ?, score = ?, sports = ? WHERE id = ?");
    $stmt->bind_param("sssssi", $match_name_1, $match_name_2, $score, $sports, $winner, $match_id);
    $stmt->execute();

    // ตรวจสอบว่าอัพเดตข้อมูลสำเร็จหรือไม่
    if ($stmt->affected_rows > 0) {
        echo "<p>การแก้ไขข้อมูลสำเร็จ!</p>";
    } else {
        echo "<p>ไม่พบการเปลี่ยนแปลงที่ต้องการบันทึก!</p>";
    }

    $stmt->close();
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลการแข่งขันกีฬา</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>แก้ไขข้อมูลการแข่งขันกีฬา</h1>
    </header>

    <div class="container">
        <form method="POST" action="">
            <label for="match_name_1">ชื่อทีม 1:</label>
            <input type="text" id="match_name_1" name="match_name_1" value="<?php echo isset($match['match_name_1']) ? htmlspecialchars($match['match_name_1']) : ''; ?>" required>

            <label for="match_name_2">ชื่อทีม 2:</label>
            <input type="text" id="match_name_2" name="match_name_2" value="<?php echo isset($match['match_name_2']) ? htmlspecialchars($match['match_name_2']) : ''; ?>" required>

            <label for="score">คะแนน:</label>
            <input type="text" id="score" name="score" value="<?php echo isset($match['score']) ? htmlspecialchars($match['score']) : ''; ?>" required>

            <label for="sports">ประเภทกีฬา:</label>
            <select id="sports" name="sports" required>
                <option value="football" <?php echo ($match['sports'] == 'football') ? 'selected' : ''; ?>>ฟุตบอล</option>
                <option value="basketball" <?php echo ($match['sports'] == 'basketball') ? 'selected' : ''; ?>>บาสเกตบอล</option>
                <option value="volleyball" <?php echo ($match['sports'] == 'volleyball') ? 'selected' : ''; ?>>วอลเลย์บอล</option>
                <option value="tennis" <?php echo ($match['sports'] == 'tennis') ? 'selected' : ''; ?>>เทนนิส</option>
            </select>

            <button type="submit">บันทึกการแก้ไข</button>
        </form>
        <div>
        <a href="admin_index.php" style="display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">กลับไปหน้าหลัก</a>
        </div>
    </div>

    <footer>
        <p>รายงานผลการแข่งขันกีฬา</p>
    </footer>

</body>
</html>
