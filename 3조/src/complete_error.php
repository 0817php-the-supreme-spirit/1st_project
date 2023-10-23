<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/project1/src/"); //웹 서버
define("FILE_HEADER", ROOT."html/header.html");
define("FILE_STATUS", ROOT."status.php");
define("FILE_CHALLENGE", ROOT."challenge_bar.php");
require_once(ROOT. "lib/com_lib.php"); // DB 라이브러리
require_once(ROOT. "lib/bar_lib.php"); // DB 라이브러리

?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>에러 페이지</title>
    <link rel="stylesheet" href="/project1/src/css/error.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Black+Han+Sans&family=Nanum+Pen+Script&family=Noto+Sans+KR:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/project1/src/css/header.css">
    <link rel="stylesheet" href="/project1/src/css/status.css">
    <link rel="stylesheet" href="/project1/src/css/challenge_bar.css">
</head>
<body>
    <?php
    require_once(FILE_HEADER);
	require_once(FILE_STATUS);
    ?>
    <section class="section-in">
    <p class="err_msg"><?php echo "No completed item" ?></p>
    </section>
    <?php
    require_once(FILE_CHALLENGE);
?>
</body>
</html>