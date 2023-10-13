<?php
	define("ROOT",$_SERVER["DOCUMENT_ROOT"]."/1st_project/src/");
	require_once(ROOT."lib/lib_db.php");
	define("ERROR_MSG_PARAM", "%s 값을 찾을 수 없습니다.");

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
			$date = isset($_GET["date"]) ? trim($_GET["date"]) : date('Y-m-d');
			$id = isset($_GET["id"]) ? $_GET["id"] : "";

			if($date === "") {
                $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "date1");
            }
			

			if(count($arr_err_msg) === 0) {
				$arr_param = [
					"date" => $date
				];
				$result = db_select($conn, $arr_param);

				if(!$result) {
					$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "data");
				}

				$arr_param = [
					"date" => $date
				];
		
				$amount_used = db_select_amount_used($conn, $arr_param);
				if($amount_used === false) {
					throw new Exception("DB Error : select_user_table");
				}
				$amount_used = $amount_used[0];

			}
		}
		else {
			$date = isset($_POST["date"]) ? trim($_POST["date"]) : date('Y-m-d');

			$life = isset($_POST["life"]) ? trim($_POST["life"]) : "";
			$activity = isset($_POST["activity"]) ? trim($_POST["activity"]) : "";
			$stupid = isset($_POST["stupid"]) ? trim($_POST["stupid"]) : "";

			$category = [];

			if($date === "") {
                $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "date3");
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
					$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "date4");
					// throw new Exception("DB Error : select_date");
				}
				
				$arr_param = [
					"date" => $date
				];
		
				$amount_used = db_select_amount_used($conn, $arr_param);
				if($amount_used === false) {
					throw new Exception("DB Error : select_user_table");
				}
				$amount_used = isset($amount_used) ? $amount_used : "지출 없음";
				
				$amount_used = $amount_used[0];
				
			}
		}
		$user_data = db_select_user_table($conn);
		if($user_data === false) {
			throw new Exception("DB Error : select_user_table");
		}

		$user_days = $user_data[0];

		$user_days_percent = $user_days["daily_salary"];

		$amount_used_percent = $amount_used["amount_used"];

		$percent = ($amount_used_percent / $user_days_percent) * 100;

		$percent = (int)$percent;

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
					<form action="/1st_project/src/php/list.php" method="post">
							<!-- <input class="date-box" type="date" required value={props.date} onChange={props.changeHandler}> -->
							<label class="date-label">
								<input type="hidden" name="date" value="<?php echo $date; ?>">
								<input class="date-box" type="date" id="date" name="date" value="<?php echo $date; ?>">
								<button class="date-btn" type="sibmit"><img src="/1st_project/src/img/date.png" alt=""></button>
							</label>

					<div class="side-left-line-1"></div>

					<a href=""><div class="side-left-page side-left-on"><p>오늘의 지출</p></div></a>
					<a href="/1st_project/src/php/insert.php/?date=<?php echo $date; ?>"><div class="side-left-page side-left-off"><p>지출 작성부</p></div></a>
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
							<td class="content-title-box content-title-box-hover"><a href="/1st_project/src/php/datail.php/?id=<?php echo $item["id"]; ?>&date=<?php echo $date;?>"><?php echo $item["title"]?></a></td>
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
					
					<div class="side-right-top">
						<?php if($percent >= 0 && $percent < 80) { ?>
							<p class="success">성 공!</p>
						<?php } else if($percent >= 80 && $percent < 99) { ?>
							<p class="danger">위 험!</p>
						<?php } else { ?>
							<p class="failure">실 패!</p>
						<?php } ?>
					</div>
					<div class="side-right-character">
						<?php if($percent >= 0 && $percent < 20) { ?>
							<div class="side-right-character-1"></div>
						<?php } else if($percent >= 20 && $percent < 40) { ?>
							<div class="side-right-character-2"></div>
						<?php } else if($percent >= 40 && $percent < 60) { ?>
							<div class="side-right-character-3"></div>
						<?php } else if($percent >= 60 && $percent < 80) { ?>
							<div class="side-right-character-4"></div>
						<?php } else if($percent >= 80 && $percent < 100) { ?>
							<div class="side-right-character-5"></div>
						<?php } else if($percent > 100) { ?>
							<div class="side-right-character-6"></div>
						<?php } ?>
					</div>
					<div class="side-right-bottom">
						<p>소비한 벨</p>
						<progress id="progress" value="<?php echo $amount_used["amount_used"]; ?>" min="0" max="<?php echo $user_days["daily_salary"]; ?>"></progress>
						<div class="side-right-user">
							<p class="small">사용 벨 : <?php if($amount_used["amount_used"] == 0) { echo 0; } else { echo $amount_used["amount_used"]; }?>원</p>
							<p class="small p_gpa">남은 벨 : <?php echo $user_days["daily_salary"] - $amount_used["amount_used"]; ?>원</p>
							<div class="bar"></div>
							<p class="small p_gpa all">전체 벨 : <?php echo $user_days["daily_salary"]; ?>원</p>
						</div>
					</div>

				</div>
			</div>
		</main>
		
	</body>
</html>