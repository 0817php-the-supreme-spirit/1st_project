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
			
			if(db_user_salary_compare($conn) === 0)
				{
					header("Location: main.php");
				}

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
			}
		}
		else {
			// date값 확인 후 받은 date값이 있으면 해당 값을 넘기고 없을 경우 오늘의 date값을 변수에 넘김
			$date = isset($_POST["date"]) ? trim($_POST["date"]) : date('Y-m-d');

			// 카테고리 부분에서 POST로 값을 전달 시에 값이 있는지 없는지 확인
			// $life = isset($_POST["life"]) ? trim($_POST["life"]) : "";
			// $activity = isset($_POST["activity"]) ? trim($_POST["activity"]) : "";
			// $stupid = isset($_POST["stupid"]) ? trim($_POST["stupid"]) : "";

			$category = isset($_POST["category"]) ? trim($_POST["category"]) : "";
			
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
		<link rel="stylesheet" href="/1st_project/src/css/side/style.css">
		<title>아껴봐요 절약의 숲 리스트 페이지</title>
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
							<!-- date값을 보내주기 위함 보내 주는 값의 키값은 name가 되고 사용자가 지정한 date값은 값이 된다. -->
							<!-- 해당 부분에 hidden이 필요한가? -->
							<label class="date-label">
								<input class="date-box" type="date" id="date" name="date" value="<?php echo $date; ?>">
								<button class="date-btn" type="sibmit"><img src="/1st_project/src/img/date.png" alt=""></button>
							</label>

					<div class="side-left-line-1"></div>

					<a href="/1st_project/src/php/list.php"><div class="side-left-page side-left-on"><p>오늘의 지출</p></div></a>
					<a href="/1st_project/src/php/insert.php/?date=<?php echo $date; ?>"><div class="side-left-page side-left-off"><p>지출 작성부</p></div></a>
					<a href="/1st_project/src/php/total.php/?date=<?php echo $date; ?>"><div class="side-left-page side-left-off"><p>지출 통계서</p></div></a>

					<div class="side-left-line-2"></div>

						<!-- 카테고리 값을 라디오 박스 형태로 지정  -->
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
					<!-- sql 구문에서 받아온 값들이 하나라도 없을 경우 화면에 에러 코드를 출력 -->
					<?php foreach($arr_err_msg as $val) { ?>
						<div class="error-box">
						<p class="err_msg"><?php echo $val; ?></p>
						</div>
					<?php } ?>

					<?php if(!$arr_err_msg) { ?>
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

			<?php require_once(ROOT."php/side.php") ?>
		</main>
		
	</body>
</html>