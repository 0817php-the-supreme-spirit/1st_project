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

			// 달에 대한 결과 값
			$date_sum = db_user_salary_date_sum($conn);
			if($date_sum  === false) {
				throw new Exception("DB Error : db_user_salary_date_sum");
			}

			// 일에 대한 결과 값
			$date_sum_day = db_user_salary_date_day($conn);
			if($date_sum  === false) {
				throw new Exception("DB Error : db_user_salary_date_day");
			}

			$arr_param = [
				"date1" => $date
				,"date2" => $date
			];
		
			// 데이터 베이스에서 유저의 사용 금액을 조회하는 함수
			$user_data = db_select_user_table_all($conn, $arr_param);
			if($user_data === false) {
				throw new Exception("DB Error : select_user_table");
			}
	
			require_once(ROOT."php/amount.php");
		}
		else {
			// 통계 페이지에는 post가 사용되지 않음
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
		<link rel="stylesheet" href="/1st_project/src/css/total/style.css">
		<link rel="stylesheet" href="/1st_project/src/css/side/style.css">
		<title>아껴봐요 절약의 숲 통계 페이지</title>
	</head>

	<body>

		<main>
			<div class="header">
				<a href="/1st_project/src/php/main.php"><h1>: 아껴봐요 절약의 숲</h1></a>
			</div>

			<<div class="side-left">
				<div class="side-left-box">
					<form action="/1st_project/src/php/list.php" method="post">
							<!-- <input class="date-box" type="date" required value={props.date} onChange={props.changeHandler}> -->
							<label class="date-label">
								<input type="hidden" name="date" value="<?php echo $date; ?>">
								<input class="date-box" type="date" id="date" name="date" value="<?php echo $date; ?>">
								<button class="date-btn" type="sibmit"><img src="/1st_project/src/img/date.png" alt=""></button>
							</label>

					<div class="side-left-line-1"></div>

					<a href="/1st_project/src/php/list.php"><div class="side-left-page side-left-off"><p>오늘의 지출</p></div></a>
					<a href="/1st_project/src/php/insert.php/?date=<?php echo $date; ?>"><div class="side-left-page side-left-off"><p>지출 작성부</p></div></a>
					<a href="/1st_project/src/php/total.php"><div class="side-left-page side-left-on"><p>지출 통계서</p></div></a>

					<div class="side-left-line-2"></div>

					<div class="category-box"></div>
					</form>

				</div>
			</div>

			<div class="content">
				<div class="content-box">
					<table class="content-table">
						<tr>
							<td class="content-date-box content-td-color">날 짜</td>
							<td class="content-amount-box content-td-color">사용 벨 (달)</td>
							<td class="content-monthly-box content-td-color">남은 벨 (달)</td>
						</tr>
					</table>
					<?php foreach($date_sum as $val) { ?>
						<details>
							<summary>
								<?php if($val["total_amount"] <= $val["monthly_salary"]) {?>
									<div class="content-date-box content-td-margin content-box-success-color"><?php echo $val["create_month"] ?></div>
									<div class="content-amount-box content-td-margin content-box-success-color"><?php echo number_format($val["total_amount"]) ?>원</div>
									<div class="content-monthly-box content-box-success-color"><?php echo number_format($val["monthly_salary"] - $val["total_amount"])?>원</div>
								<?php } else { 	?>
									<div class="content-date-box content-td-margin content-box-failure-color"><?php echo $val["create_month"] ?></div>
									<div class="content-amount-box content-td-margin content-box-failure-color"><?php echo number_format($val["total_amount"]) ?>원</div>
									<div class="content-monthly-box content-box-failure-color"><?php echo number_format($val["monthly_salary"] - $val["total_amount"])?>원</div>
								<?php }	?>
								<div class="content-day-bar"></div>
							</summary>
							<table class="content-table-day">
								<tr>
									<td class="content-date-box-day content-td-color-day">날 짜</td>
									<td class="content-amount-box-day content-td-color-day">사용 벨 (일)</td>
									<td class="content-monthly-box-day content-td-color-day">남은 벨 (일)</td>
								</tr>	
							</table>
								<?php foreach($date_sum_day as $sum) { ?>
									<?php if($sum["create_month"] == $val["create_month"]) { ?>
										<?php if($sum["amount_used_sum"] <= $sum["daily_salary"]) {?>
											<div class="background-box">
												<div class="content-date-summary-box content-td-margin content-box-success-color"><?php echo $sum["create_date"]; ?></div>
												<div class="content-amount-summary-box content-td-margin content-box-success-color"><?php echo number_format($sum["amount_used_sum"]); ?></div>
												<div class="content-monthly-summary-box content-box-success-color"><?php echo number_format($sum["daily_salary"] - $sum["amount_used_sum"]); ?></div>
											</div>
										<?php } else { 	?>
											<div class="background-box">
												<div class="content-date-summary-box content-td-margin content-box-failure-color"><?php echo $sum["create_date"]; ?></div>
												<div class="content-amount-summary-box content-td-margin content-box-failure-color"><?php echo number_format($sum["amount_used_sum"]); ?></div>
												<div class="content-monthly-summary-box content-box-failure-color"><?php echo number_format($sum["daily_salary"] - $sum["amount_used_sum"]); ?></div>
											</div>
										<?php }	?>
									<?php } ?>
								<?php } ?>
								<div class="content-day-bar"></div>
						</details>
					<?php } ?>
				</div>
			</div>

			<?php require_once(ROOT."php/side.php") ?>
		</main>
		
	</body>
</html>