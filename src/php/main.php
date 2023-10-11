<?php 
	define("ROOT",$_SERVER["DOCUMENT_ROOT"]."/1st_project/src/");
	require_once(ROOT."lib/lib_db.php");
	define("ERROR_MSG_PARAM", "해당 값을 찾을 수 없습니다.");

	$conn = null;
	$http_method = $_SERVER["REQUEST_METHOD"];
	$arr_err_msg = []; // 에러 메세지 저장용
?>

<!DOCTYPE html>
<html lang="ko">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="/1st_project/src/css/main/style.css">
		<title>Document</title>
	</head>

	<body>
		<div class="input-box">
			<div class="input-box-int">
				<form action="/1st_project/src/php/list.php" method="">
					<input type="number" placeholder="한달 급여를 입력해주세요">

					<div class="start-btn">
						<button>START</button>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>