<?php
session_start();
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
    $stmt = $conn->prepare("SELECT match_name_1, match_name_2, score_team_1, score_team_2, sports FROM results WHERE id = ?");
    $stmt->bind_param("i", $match_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // ตรวจสอบว่ามีข้อมูลการแข่งขัน
    if ($result->num_rows > 0) {
        $match = $result->fetch_assoc();
    } else {
        die("ไม่พบข้อมูลการแข่งขันนี้");
    }
    $stmt->close();
} else {
    die("ไม่มี match_id ที่จะทำการแก้ไข");
}

// ตรวจสอบว่าได้รับการส่งฟอร์ม (POST) หรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // รับค่าจากฟอร์มและกรองข้อมูล
    $match_name_1 = htmlspecialchars($_POST['match_name_1']);
    $match_name_2 = htmlspecialchars($_POST['match_name_2']);
    $score_team_1 = filter_var($_POST['score_team_1'], FILTER_VALIDATE_INT);
    $score_team_2 = filter_var($_POST['score_team_2'], FILTER_VALIDATE_INT);
    $sports = htmlspecialchars($_POST['sports']);

    // อัพเดตข้อมูลการแข่งขันในฐานข้อมูล
    $stmt = $conn->prepare("UPDATE results SET match_name_1 = ?, match_name_2 = ?, score_team_1 = ?, score_team_2 = ?, sports = ? WHERE id = ?");
    $stmt->bind_param("ssiisi", $match_name_1, $match_name_2, $score_team_1, $score_team_2, $sports, $match_id);
    $stmt->execute();

    // ตรวจสอบว่าอัพเดตข้อมูลสำเร็จหรือไม่
    if ($stmt->affected_rows > 0) {
        echo "<p style='color: green;'>การแก้ไขข้อมูลสำเร็จ!</p>";
    } else {
        echo "<p style='color: red;'>ไม่พบการเปลี่ยนแปลงที่ต้องการบันทึก!</p>";
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
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>แก้ไขข้อมูลการแข่งขันกีฬา</h1>
    </header>

    <div class="container">
        <div class="button-container">
            <a href="admin_index.php" class="button-link">กลับไปหน้าหลัก</a>
        </div>

        <form method="POST" action="">
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
            <div>
                <label for="match_name_1">ชื่อทีม 1:</label>
                <input type="text" id="match_name_1" name="match_name_1" value="<?php echo htmlspecialchars($match['match_name_1']); ?>" required>
            </div>
            <div>
                <label for="score_team_1">คะแนนทีม 1:</label>
                <input type="text" id="score_team_1" name="score_team_1" value="<?php echo htmlspecialchars($match['score_team_1']); ?>" required>
            </div>
            <div>
                <label for="match_name_2">ชื่อทีม 2:</label>
                <input type="text" id="match_name_2" name="match_name_2" value="<?php echo htmlspecialchars($match['match_name_2']); ?>" required>
            </div>
            <div>
                <label for="score_team_2">คะแนนทีม 2:</label>
                <input type="text" id="score_team_2" name="score_team_2" value="<?php echo htmlspecialchars($match['score_team_2']); ?>" required>
            </div>
            <button type="submit">บันทึกการแก้ไข</button>
        </form>
    </div>

    <footer>
        <p>รายงานผลการแข่งขันกีฬา</p>
    </footer>
</body>
</html>