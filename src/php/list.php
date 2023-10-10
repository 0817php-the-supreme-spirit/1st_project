<?php
	define("ROOT",$_SERVER["DOCUMENT_ROOT"]."/1st_project/src/");
	require_once(ROOT."lib/lib_db.php");
	$conn = null;
	$http_method = $_SERVER["REQUEST_METHOD"];

	try {
		if(!db_conn($conn))
		{
			//강제 예외 발생 : DB Instance
			throw new Exception("DB Error : PDO Instance");
		}

		$result = db_select($conn);
		if(!$result)
		{
			throw new Exception("DB Error : SELECT boards");
		}

		var_dump($result);


	}
	catch(Exception $e) {
		echo $e->getMessage(); // 예외발생 메세지 출력
		exit; // 처리 종료
	}
	finally {
		db_destroy_conn($conn); // DB 파기
	}

?>


<!DOCTYPE html>
<html lang="ko">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="/1st_project/src/css/list/style.css">
		<title>Document</title>
	</head>

	<body>

		<main>
			<div class="header">
				<a href=""><h1>: 아껴봐요 절약의 숲</h1></a>
			</div>

			<div class="side-left">
				<div class="side-left-box">
					<form action="list.html/?date=" method="post">
						<table>
							<!-- <input class="date-box" type="date" required value={props.date} onChange={props.changeHandler}> -->
							<input class="date-box" type="date">
						</table>
					</form>

					<div class="side-left-line-1"></div>

					<a href=""><div class="side-left-page side-left-on"><p>오늘의 지출</p></div></a>
					<a href=""><div class="side-left-page side-left-off"><p>지출 작성부</p></div></a>
					<a href=""><div class="side-left-page side-left-off"><p>지출 통계서</p></div></a>

					<div class="side-left-line-2"></div>

					<form action="" method="post">
						<input type="radio" name="category" id="category1">
						<label for="category1" class="category-box">전체 비용</label>
				
						<input type="radio" name="category" id="category2">
						<label for="category2" class="category-box">생활 비용</label>
				
						<input type="radio" name="category" id="category3">
						<label for="category3" class="category-box">활동 비용</label>
				
						<input type="radio" name="category" id="category4">
						<label for="category4" class="category-box">멍청 비용</label>
					</form>

				</div>
			</div>

			<div class="content">
				<div class="content-box">
					<table class="content-table">
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
						<tr>
							<td class="content-categort-box">그림</td>
							<td class="content-title-box">제목</td>
							<td class="content-amount-box">금액</td>
						</tr>
					</table>
				</div>
			</div>

			<div class="side-right">
				<div class="side-right-box">
					
					<div class="side-right-top"><p>성 공!</p></div>
					<div class="side-right-character"></div>
					<div class="side-right-bottom">
						<p>소비한 벨</p>
						<meter value="15" min="0" max="100" optimum="15" id="meter"></meter>
						<p>사용 금액 / 전체 금액</p>
					</div>

				</div>
			</div>
		</main>
		
	</body>
</html>