<?php
	define("ROOT",$_SERVER["DOCUMENT_ROOT"]."/1st_project/src/");
	require_once(ROOT."lib/lib_db.php");
	define("ERROR_MSG_PARAM", "해당 값을 찾을 수 없습니다.");

	$conn = null;
	$http_method = $_SERVER["REQUEST_METHOD"];
	$arr_err_msg = []; // 에러 메세지 저장용

	try {
		if(!db_conn($conn))
		{
			//강제 예외 발생 : DB Instance
			throw new Exception("DB Error : PDO Instance");
		}
		if($http_method === "GET") {
			$date = isset($_GET["date"]) ? trim($_GET["date"]) : "";
			$date = date('Y-m-d');

			if($date === "") {
                $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "date");
            }

			if(count($arr_err_msg) === 0) {
				$arr_param = [
					"date" => $date
				];
				$result = db_select($conn, $arr_param);

				if(!$result)
				{
					throw new Exception("DB Error : SELECT boards");
				}
			}
		}
		else {
			$date = isset($_POST["date"]) ? trim($_POST["date"]) : date('Y-m-d');

			$life = isset($_POST["life"]) ? trim($_POST["life"]) : "";
			$activity = isset($_POST["activity"]) ? trim($_POST["activity"]) : "";
			$stupid = isset($_POST["stupid"]) ? trim($_POST["stupid"]) : "";

			$category = [];

			if($date === "") {
                $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "date");
            }

			if(isset($_POST["category"])) {
				$category = $_POST["category"];
			}

			if(count($arr_err_msg) === 0) {
				$arr_param = [
					"date" => $date
					,"category" => $category
				];

				$result = db_select_search($conn, $arr_param);
				
				if($result === false) {
					throw new Exception("DB Error : select_search");
				}
				else if(count($result) === 0) {
					$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "date");
					// throw new Exception("DB Error : select_date");
				}
			}
		}


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
		<title>아껴봐요 절약의 숲 리스트 페이지</title>
	</head>

	<body>

		<main>
			<div class="header">
				<a href="/1st_project/src/php/list.php"><h1>: 아껴봐요 절약의 숲</h1></a>
			</div>

			<div class="side-left">
				<div class="side-left-box">
					<form action="/1st_project/src/php/list.php/?date=<?php echo $date; ?>" method="post">
							<!-- <input class="date-box" type="date" required value={props.date} onChange={props.changeHandler}> -->
							<label class="date-label">
								<input class="date-box" type="date" id="date" name="date" value="<?php echo $date; ?>">
								<button class="date-btn" type="sibmit"><img src="/1st_project/src/img/date.png" alt=""></button>
							</label>

					<div class="side-left-line-1"></div>

					<a href=""><div class="side-left-page side-left-on"><p>오늘의 지출</p></div></a>
					<a href=""><div class="side-left-page side-left-off"><p>지출 작성부</p></div></a>
					<a href=""><div class="side-left-page side-left-off"><p>지출 통계서</p></div></a>

					<div class="side-left-line-2"></div>

						<div class="category-all-box">
							<input type="radio" name="category" id="category1" value="">
							<!-- <label for="category1" class="category-box">전체 비용</label> -->
							<button class="category-box category-box-select" id="category" name="category">카테고리 선택</button>
						</div>
				
						<div class="category-all-box">
							<input type="radio" name="category" id="category2" value='life'>
							<!-- <label for="category2" class="category-box">생활 비용</label> -->
							<button class="category-box" id="category" name="category" value="life">생활 비용</button>
						</div>
				
						<div class="category-all-box">
							<input type="radio" name="category" id="category3" value='activity'>
							<!-- <label for="category3" class="category-box">활동 비용</label> -->
							<button class="category-box" id="category" name="category" value="activity">활동 비용</button>
						</div>
				
						<div class="category-all-box">
							<input type="radio" name="category" id="category4" value='stupid'>
							<!-- <label for="category4" class="category-box" >멍청 비용</label> -->
							<button class="category-box" id="category" name="category" value="stupid">멍청 비용</button>
						</div>
					</form>

				</div>
			</div>

			<div class="content">
				<div class="content-box">
						<?php foreach($arr_err_msg as $val) { ?>
							<div class="error-box">
							<p class="err_msg"><?php echo $val; ?></p>
							</div>
						<?php } ?>

					<?php 
					if(!$arr_err_msg) { ?>
						<table class="content-table">
							<tr>
								<td class="content-categort-box content-td-color">분류</td>
								<td class="content-title-box content-td-color">제목</td>
								<td class="content-amount-box content-td-color">사용 금액</td>
							</tr>
					<?php } ?>
						
						<?php 
							foreach($result as $item) {
						?>
						<tr>
							<td class="content-categort-box">
								<?php if($item["category_name"] == 'life') { ?>
									<img class="gap" src="/1st_project/src/img/life.png"> 
								<?php } else if($item["category_name"] == 'activity') { ?>
									<img class="gap" src="/1st_project/src/img/activity.png">
								<?php }  else { ?>
									<img src="/1st_project/src/img/stupid.png">
								<?php } ?>
							</td>
							<td class="content-title-box"><a href="/1st_project/src/php/detail.php/?id=<?php echo $item["id"]; ?>"><?php echo $item["title"]?></a></td>
							<td class="content-amount-box"><?php echo $item["amount_used"], "원"; ?></td>
						</tr>
						<?php 
							}
						?>
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