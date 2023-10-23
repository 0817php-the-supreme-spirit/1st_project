<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selectedCategory = $_POST["category"];

    $servername = "localhost";
    $username = "root";
    $password = "php504";
    $dbname = "test";

    // 데이터베이스 연결
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 연결 확인
    if ($conn->connect_error) {
        die("데이터베이스 연결 실패: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM categorytest WHERE Category = '$selectedCategory'";

    $result = $conn->query($sql);

    // 쿼리 결과 확인
    if ($result === false) {
        die("쿼리 실행 실패: " . $conn->error);
    }

    // 연결 종료
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>카테고리 목록</title>
</head>
<body>
    <h1>카테고리 목록</h1>

    <form method="POST" action="process.php">
        <label for="category">카테고리 선택:</label>
        <select id="category" name="category">
            <option value="C">C</option>
            <option value="L">L</option>
            <option value="F">F</option>
            <option value="S">S</option>
            <!-- 다른 카테고리 옵션들 추가 -->
        </select>
        <input type="submit" value="검색">
    </form>

    <?php
        // 결과 처리
        if ($result->num_rows > 0) {
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                echo "<li>ID: " . $row["ID"] . " - Category: " . $row["Category"] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "해당 카테고리가 없습니다.";
        }
    ?>
</body>
</html>