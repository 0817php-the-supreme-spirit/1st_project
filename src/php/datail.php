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
			$date = isset($_GET["date"]) ? trim($_GET["date"]) : date('Y-m-d');
			$id = isset($_GET["id"]) ? $_GET["id"] : "";

			if($id === "" ) {
				$arr_err_msg[] = "Parameter Error : id";
			}
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
				$arr_param = [
					"id" => $id
				];
		
				$result = db_select_id($conn, $arr_param);

				if(!(count($result) === 1)) {
					throw new Exception("DB Error : Select id Count");
				}
		
				else if($result === false) {
					throw new Exception("DB Error : Select id");
				}
		
				
				$item = $result[0];

				$arr_param = [
					"date" => $date
				];
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

				$arr_param = [
					"date" => $date
				];
		
			}
		}

		require_once(ROOT."php/amount.php");

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
		<link rel="stylesheet" href="/1st_project/src/css/detail/style.css">
		<title>아껴봐요 절약의 숲 상세 페이지</title>
	</head>

	<body>

		<main>
			<div class="header">
				<a href="/1st_project/src/php/main.php"><h1>: 아껴봐요 절약의 숲</h1></a>
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
					<a href="/1st_project/src/php/insert.php"><div class="side-left-page side-left-off"><p>지출 작성부</p></div></a>
					<a href="/1st_project/src/php/total.php/?date=<?php echo $date; ?>"><div class="side-left-page side-left-off"><p>지출 통계서</p></div></a>

					<div class="side-left-line-2"></div>

					<div class="category-box"></div>
					</form>

				</div>
			</div>

			<div class="content">
				<div class="content-box">
					<?php 
						foreach($result as $item) {
					?>
						<div class="content-date-box">
							<span><?php echo $item["create_date"]; ?></span>
						</div>
						<div class="content-category-box">
							<span><?php if($item["category_name"] == 'life') { ?>
									<p>생활 비용</p>
								<?php } else if($item["category_name"] == 'activity') { ?>
									<p>활동 비용</p>
								<?php }  else { ?>
									<p>멍청 비용</p>
								<?php } ?></span>
						</div>

						<div class="content-title-box">
							<div class="content-title-box-1">
								<p>제목</p>
							</div>

							<div class="content-title-box-2">
								<p><?php echo $item["title"]; ?></p>
							</div>
						</div>

						<div class="content-memo-box">
							<div class="content-memo-box-1">
								<p>메모</p>
							</div>

							<div class="content-memo-box-2">
								<p><?php if($item["memo"] == 0) {
									echo "메모를 하지 않았어요";
									}
									else {
									echo $item["memo"]; 
									} ?></p>
							</div>
						</div>

						<div class="content-value-box">
							<div class="content-user-box">
								<div class="content-user-amount">
									<p>일일 사용 금액 : <?php echo number_format($item["amount_used"]); ?>원</p>
								</div>
		
								<div class="content-user-remaining">
									<p>일일 남은 금액 : <?php echo number_format($user_days["daily_salary"] - $amount_used["amount_used"]); ?>원</p>
								</div>
							</div>

							<div class="content-phrases-box">
								<?php if($percent_days >= 0 && $percent_days < 20) {?>
									<p class="content-phrases-box-color">잘하고</p>
									<p class="content-phrases-box-color">있어</p>
								<?php } else if($percent_days >= 20 && $percent_days < 40) { ?>
									<p class="content-phrases-box-color">아직은</p>
									<p class="content-phrases-box-color">괜찮아</p>
								<?php } else if($percent_days >= 40 && $percent_days < 60) { ?>
									<p class="content-phrases-box-color">소비 액수가</p>
									<p class="content-phrases-box-color">좀 큰대?</p>
								<?php } else if($percent_days >= 60 && $percent_days < 80) { ?>
									<p class="content-phrases-box-failure">잔고</p>
									<p class="content-phrases-box-failure">감당 가능해?</p>
								<?php } else if($percent_days >= 80 && $percent_days < 99) { ?>
									<p class="content-phrases-box-failure">너 혹시</p>
									<p class="content-phrases-box-failure">제정신이야?</p>
								<?php } else { ?>
									<p class="content-phrases-box-failure">다음 달도</p>
									<p class="content-phrases-box-failure">텅장이다</p>
								<?php } ?>
							</div>
						</div>
					<?php } ?>

					<div class="content-btn-box">

							<a class="content-btn-before content-btn-box-hover" href="/1st_project/src/php/list.php/?date=<?php echo $date; ?>">이전</a>

							<a class="content-btn-correction content-btn-box-hover" href="/1st_project/src/php/update.php/?id=<?php echo $id; ?>&date=<?php echo $date; ?>">수정</a>

							<a class="content-btn-delete content-btn-box-hover" href="/1st_project/src/php/delete.php/?id=<?php echo $id; ?>&date=<?php echo $date; ?>">삭제</a>

					</div>
					
				</div>
			</div>

			<?php require_once(ROOT."php/side.php") ?>
		</main>
		
	</body>
</html>