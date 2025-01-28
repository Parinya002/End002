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

// กำหนดค่าตัวแปรค้นหา
$searchMatchName = "";
$searchSports = "";

// ตรวจสอบว่าได้ทำการค้นหาหรือกรองข้อมูลหรือไม่
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['search_match_name'])) {
        $searchMatchName = $_GET['search_match_name'];
    }
    if (isset($_GET['search_sports'])) {
        $searchSports = $_GET['search_sports'];
    }
}

// สร้างเงื่อนไขการค้นหา
$sql = "SELECT match_name_1, match_name_2, score_team_1, score_team_2, sports FROM results WHERE 1";

if (!empty($searchMatchName)) {
    $sql .= " AND (match_name_1 LIKE ? OR match_name_2 LIKE ?)";
}

if (!empty($searchSports)) {
    $sql .= " AND sports = ?";
}

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

// Binding parameters
if (!empty($searchMatchName) && !empty($searchSports)) {
    $searchMatchNameLike = "%" . $searchMatchName . "%";
    $stmt->bind_param("sss", $searchMatchNameLike, $searchMatchNameLike, $searchSports);
} elseif (!empty($searchMatchName)) {
    $searchMatchNameLike = "%" . $searchMatchName . "%";
    $stmt->bind_param("ss", $searchMatchNameLike, $searchMatchNameLike);
} elseif (!empty($searchSports)) {
    $stmt->bind_param("s", $searchSports);
}

$stmt->execute();
$result = $stmt->get_result();

// ตรวจสอบว่ามีข้อมูลหรือไม่
if ($result) {
    $matches = $result->fetch_all(MYSQLI_ASSOC);  // เก็บผลลัพธ์ในตัวแปร $matches
} else {
    $matches = [];  // ถ้าไม่มีข้อมูล
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();

// ฟังก์ชั่นคำนวณทีมชนะ
function getWinner($score_team_1, $score_team_2) {
    if ($score_team_1 > $score_team_2) {
        return 'team1'; // ทีม 1 ชนะ
    } elseif ($score_team_2 > $score_team_1) {
        return 'team2'; // ทีม 2 ชนะ
    } else {
        return 'draw'; // หากคะแนนเท่ากัน
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลการแข่งขันกีฬา</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div>
        <header>
            <h1>ข้อมูลการแข่งขันกีฬา</h1>
        </header>
    </div>

    <div class="container">
        <!-- ฟอร์มค้นหาหรือกรองข้อมูล -->
        <form method="GET" action="">
            <label for="search_match_name">ชื่อทีม:</label>
            <input type="text" id="search_match_name" name="search_match_name" value="<?php echo htmlspecialchars($searchMatchName); ?>">

            <label for="search_sports">ประเภทกีฬา:</label>
            <select id="search_sports" name="search_sports">
                <option value="">-- เลือกประเภทกีฬา --</option>
                <option value="football" <?php echo ($searchSports == 'football') ? 'selected' : ''; ?>>ฟุตบอล</option>
                <option value="basketball" <?php echo ($searchSports == 'basketball') ? 'selected' : ''; ?>>บาสเกตบอล</option>
                <option value="volleyball" <?php echo ($searchSports == 'volleyball') ? 'selected' : ''; ?>>วอลเลย์บอล</option>
                <option value="tennis" <?php echo ($searchSports == 'tennis') ? 'selected' : ''; ?>>เทนนิส</option>
            </select>

            <button type="submit">ค้นหา</button>
        </form>

        <!-- ตรวจสอบว่า $matches มีข้อมูลหรือไม่ -->
        <?php if (!empty($matches)): ?>
            <table border="1" cellpadding="10" style="margin-top: 20px; width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th>ชื่อทีม 1</th>
                        <th>ชื่อทีม 2</th>
                        <th>คะแนนทีม 1</th>
                        <th>คะแนนทีม 2</th>
                        <th>ประเภทกีฬา</th>
                        <th>ทีมชนะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matches as $match): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($match['match_name_1']); ?></td>
                            <td><?php echo htmlspecialchars($match['match_name_2']); ?></td>
                            <td><?php echo htmlspecialchars($match['score_team_1']); ?></td>
                            <td><?php echo htmlspecialchars($match['score_team_2']); ?></td>
                            <td><?php echo htmlspecialchars($match['sports']); ?></td>
                            <td>
                                <?php 
                                    // แสดงทีมชนะเป็นตัวหนา
                                    $winner = getWinner($match['score_team_1'], $match['score_team_2']);
                                    if ($winner == 'team1') {
                                        echo "<strong>" . htmlspecialchars($match['match_name_1']) . "</strong>";
                                    } elseif ($winner == 'team2') {
                                        echo "<strong>" . htmlspecialchars($match['match_name_2']) . "</strong>";
                                    } else {
                                        echo "<strong>เสมอ</strong>";
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>ไม่มีข้อมูลการแข่งขันที่ตรงกับเงื่อนไขการค้นหา</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>รายงานผลการแข่งขันกีฬา</p>
    </footer>
</body>
</html>
