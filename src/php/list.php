<?php
	define("ROOT",$_SERVER["DOCUMENT_ROOT"]."/1st_project/src/");
	require_once(ROOT."lib/lib_db.php");
	define("ERROR_MSG_PARAM", "값을 찾을 수 없습니다.");

	$conn = null;
	$http_method = $_SERVER["REQUEST_METHOD"];
	$arr_err_msg = []; // 에러 메세지 저장용

	try {
		if(!db_conn($conn))
		{
			//강제 예외 발생 : DB Instance
			throw new Exception("DB Error : PDO Instance");
		}

		// 메소드 확인 해당 페이지는 기본 데이터를 출력하기 위한 GET와 날짜와 카테고리 값을 받는 POST가 존재
		if($http_method === "GET") {

			// date값 확인 후 받은 date값이 있으면 해당 값을 넘기고 없을 경우 오늘의 date값을 변수에 넘김
			$date = isset($_GET["date"]) ? trim($_GET["date"]) : date('Y-m-d');
			// id값 확인 후 받은 id값을 변수로 넘김
			// $id = isset($_GET["id"]) ? $_GET["id"] : "";

			// date값이 비어있을 경우 arr_err_msg배열에 상수 ERROP_MSG_PAEAM값과 ""값을 이어서 넣어줌
			if($date === "") {
                $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "date1");
            }

			// id값이 비어있을 경우 arr_err_msg배열에 상수 ERROP_MSG_PAEAM값과 ""값을 이어서 넣어줌
			// if($id === "") {
            //     $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "id");
            // }
			

			//arr_err_msg의 카운트 값이 0일 경우에만 실행
			if(count($arr_err_msg) === 0) {
				// arr_param에 "date"라는 키에 $date 값을 할당
				$arr_param = [
					"date" => $date
				];

				// 데이터 베이스에 있는 값을 불러오기 위한 함수
				$result = db_select($conn, $arr_param);

				if(!$result) {
					$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "data");
				}

				$arr_param = [
					"date" => $date
				];
		
				// 데이터 베이스에서 유저의 사용 금액을 조회하는 함수
				$amount_used = db_select_amount_used($conn, $arr_param);
				if($amount_used === false) {
					throw new Exception("DB Error : select_user_table");
				}
				// $amount_used의 0번방에 있는 값들을 넣어주는 구문
				$amount_used = $amount_used[0];

			}
		}
		else {
			// date값 확인 후 받은 date값이 있으면 해당 값을 넘기고 없을 경우 오늘의 date값을 변수에 넘김
			$date = isset($_POST["date"]) ? trim($_POST["date"]) : date('Y-m-d');

			// 카테고리 부분에서 POST로 값을 전달 시에 값이 있는지 없는지 확인
			$life = isset($_POST["life"]) ? trim($_POST["life"]) : "";
			$activity = isset($_POST["activity"]) ? trim($_POST["activity"]) : "";
			$stupid = isset($_POST["stupid"]) ? trim($_POST["stupid"]) : "";

			// 동적 쿼리를 위해 카테고리를 받을 빈 배열 생성
			$category = [];

			if($date === "") {
                $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "date3");
            }

			// POST에 카테고리 값이 있을 경우에 $category에 넘김
			if(isset($_POST["category"])) {
				$category = $_POST["category"];
			}

			if(count($arr_err_msg) === 0) {

				$arr_param = [
					"date" => $date
					,"category" => $category
				];

				// 유저가 보낸 날짜값과 카테고리값이 맞는 게시물 조회를 위한 함수
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
		
				// 데이터 베이스에서 유저의 사용 금액을 조회하는 함수
				$amount_used = db_select_amount_used($conn, $arr_param);
				if($amount_used === false) {
					throw new Exception("DB Error : select_user_table");
				}
				$amount_used = isset($amount_used) ? $amount_used : "지출 없음";
				
				$amount_used = $amount_used[0];
				
			}
		}

		// 유저의 일일 급여를 조회하느 함수
		$user_data = db_select_user_table($conn);
		if($user_data === false) {
			throw new Exception("DB Error : select_user_table");
		}

		// 유저의 일일 급여의 0번 방에 있는 값을 넘겨줌
		$user_days = $user_data[0];

		//daily_selary에 있는 값을 다른 변수에 넘겨줌, 위와 아래는 통합 가능, 코드 리뷰를 위해 풀어서 정리 
		$user_days_percent = $user_days["daily_salary"];

		//유저의 사용 금액을 해당 변수로 넘김
		$amount_used_percent = $amount_used["amount_used"];

		// 사용 금액의 퍼센트를 구하는 계산식
		$percent = ($amount_used_percent / $user_days_percent) * 100;

		// 실수가 아닌 정수로 값을 보기 위해 데이터타입 변환
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

					<a href="/1st_project/src/php/list.php"><div class="side-left-page side-left-on"><p>오늘의 지출</p></div></a>
					<a href="/1st_project/src/php/insert.php/?date=<?php echo $date; ?>"><div class="side-left-page side-left-off"><p>지출 작성부</p></div></a>
					<a href="/1st_project/src/php/total.php/?date=<?php echo $date; ?>"><div class="side-left-page side-left-off"><p>지출 통계서</p></div></a>

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
							<!-- if문을 통해 카테고리 값에 따라 이미지 변경 -->
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
							<td class="content-amount-box"><?php echo number_format($item["amount_used"]), "원"; ?></td>
						</tr>
						<?php 
							}
						?>
					</table>
				</div>
			</div>

			<div class="side-right">
				<div class="side-right-box">
					<!-- if문을 통해 우측 사이드바 글귀와 이미지 변경 -->
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
							<p class="small">사용 벨 : <?php if($amount_used["amount_used"] == 0) { echo 0; } else { echo number_format($amount_used["amount_used"]); }?>원</p>
							<p class="small p_gpa">남은 벨 : <?php echo number_format($user_days["daily_salary"] - $amount_used["amount_used"]); ?>원</p>
							<div class="bar"></div>
							<p class="small p_gpa all">전체 벨 : <?php echo number_format($user_days["daily_salary"]); ?>원</p>
						</div>
					</div>

				</div>
			</div>
		</main>
		
	</body>
</html>