<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/1st_project/src/");
require_once(ROOT."lib/lib_db.php");
define("ERROR_MSG_PARAM", "해당 값을 찾을 수 없습니다.");

$conn = null;
db_conn($conn);
$http_method = $_SERVER["REQUEST_METHOD"];
$arr_err_msg = [];


try{

	if ($http_method === "GET") {
		$id = isset($_GET["id"]) ? trim($_GET["id"]) : $_POST["id"]; //get일 경우 아이디 값 세팅
		$date = isset($_GET["date"]) ? trim($_GET["date"]) : date('Y-m-d'); //기본 날짜 세팅
			
			if(!db_conn($conn)) {
				// DB Instance 에러
				throw new Exception("DB Error : PDO Instance"); //db가 연결되지 않을 경우 에러 출력
			}
			
			// if($id === "" ) {
			// 	$arr_err_msg[] = sprint(ERROR_MSG_PARAM, "제목");
			// }
			// if($date === "") {
			// 	$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "date");
			// }

			// if(count($arr_err_msg) >= 1){
			// 	throw new Exception(implode("<br>", $arr_err_msg));
			// }

			// if($arr_err_msg === 0 ){

			//일일 사용금액 계산을 위한 조건(날짜) 세팅
			$arr_param = [
				"date" => $date
			];
			//일일 사용금액 계산
			$amount_used = db_select_amount_used($conn, $arr_param);
			if($amount_used === false) {
				throw new Exception("DB Error : select_user_table");
			}
			$amount_used = $amount_used[0];
		// };

	}
	else {
	$id = isset($_POST["id"]) ? $_POST["id"] : ""; //post일 경우 id값 세팅
	$date = isset($_POST["create_date"]) ? trim($_POST["create_date"]) : date('Y-m-d'); //수정할 때 날짜 세팅. 유저가 보내지 않을 경우 오늘 날짜

		// if($id === "") {
		// 	$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "id");
		// }
		// if($page === "") {
		// 	$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "create_date");
		// }

		// if(count($arr_err_msg) === 0) {

			//POST 값 변수지정
			$title = $_POST["title"] ? $_POST["title"] : "";
			$memo = $_POST["memo"] ? $_POST["memo"] : null; //memo 값 없을 시 null 세팅
			$amount_used = $_POST["amount_used"] ? $_POST["amount_used"] : "";
			$create_date = $_POST["create_date"] ? $_POST["create_date"] : "";
			$category_id = $_POST["category_id"] ? $_POST["category_id"] : "";
			

			//POST 값 받아오기
			$arr_param = [
				"title" => $title
				,"memo" => $memo
				,"amount_used" => $amount_used
				,"create_date" => $create_date
				,"category_id" => $category_id
				,"id" => $id
			];
			//트랜잭션 시작
			$conn->beginTransaction();

			//POST값 입력
				if(!update_execute($conn, $arr_param)){
					throw new Exception("DB Error : Update_boards_id");
				}
			
			//커밋
			$conn->commit();

			//업데이트 완료 후 디테일 페이지로 이동
			header("Location: /1st_project/src/php/datail.php/?id={$id}&date={$date}");
			exit;
		}

			//이번달 유저 일일 급여 조회
			$user_data = db_select_user_table($conn);

				//실패시 false
				if($user_data === false) {
					throw new Exception("DB Error : select_user_table");
				}

			//사용자가 입력한 일일 사용금액 불러오기
			$user_days = $user_data[0];
			$user_days_percent = $user_days["daily_salary"];
			//일일 총 사용금액 변수에 담기
			$amount_used_percent = $amount_used["amount_used"];
			//퍼센트 계산 (1일 총 사용금액 / 일일 목표 금액)
			$percent = ($amount_used_percent / $user_days_percent) * 100;
			//퍼센트 int값으로 변환
			$percent = (int)$percent;

			//업데이트 완료한거 불러오기
			$arr_param_id = [
				"id" => $id
			];

			// 게시글 데이터 조회
			$result = select_change_detail( $conn, $arr_param_id );

				//게시글 조회 실패시 에러메세지 출력
				if($result === false){
					throw new Exception("DB Error : PDO Select_id");
				}
			//결과값을 $item변수에 담음
			$item = $result[0];

} catch(Exception $e) {

	if($http_method === "POST") {
	$conn->rollBack();
	}
	echo $e->getmessage(); // Exception 메세지 출력
	exit;
}finally{
	db_destroy_conn($conn);
}

?>

<!DOCTYPE html>
<html lang="ko">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="/1st_project/src/css/update/style.css">
		<title>Update</title>
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
					</form>

					<div class="side-left-line-1"></div>

					<a href="/1st_project/src/php/list.php"><div class="side-left-page side-left-on"><p>오늘의 지출</p></div></a>
					<a href="/1st_project/src/php/insert.php"><div class="side-left-page side-left-off"><p>지출 작성부</p></div></a>
					<a href=""><div class="side-left-page side-left-off"><p>지출 통계서</p></div></a>

					<div class="side-left-line-2"></div>

					<div class="update-icon"></div><!-- 좌측 사이드바 아이콘 -->
<!-- 
					<form action="" method="post">
						<input type="radio" name="category" id="category1">
						<label for="category1" class="category-box">전체 비용</label>
				
						<input type="radio" name="category" id="category2">
						<label for="category2" class="category-box">생활 비용</label>
				
						<input type="radio" name="category" id="category3">
						<label for="category3" class="category-box">활동 비용</label>
				
						<input type="radio" name="category" id="category4">
						<label for="category4" class="category-box">멍청 비용</label>
					</form> -->

				</div>
			</div>

			<div class="content">
				<div class="content-box">
					<form action="/1st_project/src/php/update.php" method="POST">
						<input type="hidden" name="id" value="<?php echo $id; ?>">
						<input type="date" name="create_date" class="update-date" value="<?php echo $item["create_date"]; ?>">
						<div class="update-category">
							<select name="category_id" class="update-category">
								<option value="0">생활 비용</option>
								<option value="1">활동 비용</option>
								<option value="2">멍청 비용</option>
							</select>
						</div>
						<div class="update-title-memo">
							<div class="update-title">
								<label for="update-title" id ="title1">제목</label>
								<input type="text" name="title" id="update-title" placeholder="뭘 샀는지 궁금해요!" required value="<?php echo $item["title"]; ?>">
							</div>
							<div class="update-memo">
								<label for="update-memo">메모</label>
								<textarea name="memo" id="update-memo" cols="50" rows="1" maxlength="49"><?php echo $item["memo"]; ?></textarea>
							</div>
						</div>
						<div class="update-spent">
							<label for="update-spent"></label>
							<input type="number" name="amount_used" id="update-spent" placeholder="금액을 입력해주세요." required value="<?php echo $item["amount_used"]; ?>">
						</div>
						<div class="update-button">
							<button type="submit">수정확인</button>
							<a href="/1st_project/src/php/datail.php/?id=<?php echo $id; ?>&date=<?php echo $date; ?>">수정취소</a>
						</div>
					</form>
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
							<p class="small">사용 금액 : <?php if($amount_used["amount_used"] == 0) { echo 0; } else { echo $amount_used["amount_used"]; }?>원</p>
							<p class="small p_gpa">남은 금액 : <?php echo $user_days["daily_salary"] - $amount_used["amount_used"]; ?>원</p>
							<div class="bar"></div>
							<p class="small p_gpa all">전체 금액 : <?php echo $user_days["daily_salary"]; ?>원</p>
						</div>
					</div>

				</div>
			</div>
		</main>
		
	</body>
</html>