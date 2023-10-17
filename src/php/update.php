<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/1st_project/src/");
define("ERROR_MSG_PARAM", "%s은 필수 입력값입니다."); //파라미터 에러 메세지
require_once(ROOT."lib/lib_db.php");

$true_conn = false;
$conn = null;
$http_method = $_SERVER["REQUEST_METHOD"];
$arr_err_msg = [];
	try{
		//db 접속
		if(!db_conn($conn)) {
			// DB Instance 에러
			throw new Exception("DB Error : PDO Instance"); //db가 연결되지 않을 경우 에러 출력
		}
		if ($http_method === "GET") {
			$id = isset($_GET["id"]) ? trim($_GET["id"]) : ""; //get일 경우 아이디 값 세팅
			$date = isset($_GET["date"]) ? trim($_GET["date"]) : ""; //기본 날짜 세팅
				
			if($id === "" ) {
				$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "id");
			}
			if($date === "") {
				$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "date");
			}

			if(count($arr_err_msg) >= 1) {
                throw new Exception(implode("<br>", $arr_err_msg));
            }

		}
		else {
			$id = isset($_POST["id"]) ? $_POST["id"] : ""; //post일 경우 id값 세팅
			$date = isset($_POST["create_date"]) ? trim($_POST["create_date"]) : ""; //수정할 때 날짜 세팅. 유저가 보내지 않을 경우 오늘 날짜
			$title = isset($_POST["title"]) ? trim($_POST["title"]) : ""; //title 세팅
			$memo = isset($_POST["memo"]) ? trim($_POST["memo"]) : ""; //memo 값 없을 시 null 세팅
			$amount_used = isset($_POST["amount_used"]) ? trim($_POST["amount_used"]) : ""; //사용한 금액 세팅
			$create_date = isset($_POST["create_date"]) ? trim($_POST["create_date"]) : ""; //날짜 세팅
			$category_id = isset($_POST["category_id"]) ? trim($_POST["category_id"]) : ""; //카테고리 id 세팅


			if($id === "") {
				$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "id");
			}
			if($date === "") {
				$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "date");
			}
			
			if($title === "") {
				$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "title");
			}
			if($amount_used === "") {
				$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "amount_used");
			}
			if($create_date === "") {
				$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "create_date");
			}
			

			if(count($arr_err_msg) === 0) {
				//트랜잭션 시작
				$true_conn = $conn->beginTransaction();

				//카테고리 값 불러오기
				if( $category_id === ""){
					$arr_ps_id = [
						"id" => $id
					];
					
					$category = category_id( $conn, $arr_ps_id );

					$category = $category[0];
					$category_id = (int)$category;
				}

				//POST 값 받아오기
				$arr_param = [
					"title" => $title
					,"memo" => $memo
					,"amount_used" => $amount_used
					,"create_date" => $create_date
					,"category_id" => $category_id
					,"id" => $id
				];

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
		}
		//기존값 불러오기
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

		require_once(ROOT."php/amount.php");
		
	} catch(Exception $e) {
		if($http_method === "POST") {
			echo $e->getMessage();
			if($conn_true === true)
			{
				$conn->rollBack();
			}
		}
		// header("Location: /1st_project/src/php/update.php/?id={$id}&date={$date}->getMessage()}");
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
		<link rel="stylesheet" href="/1st_project/src/css/side/style.css">
		<title>아껴봐요 절약의 숲 수정 페이지</title>
	</head>
	<body>
		<main>

			<div class="header">
				<a href="/1st_project/src/php/main.php"><h1>: 아껴봐요 절약의 숲</h1> </a>
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
					<a href="/1st_project/src/php/total.php/?date=<?php echo $date; ?>"><div class="side-left-page side-left-off"><p>지출 통계서</p></div></a>

					<div class="side-left-line-2"></div>

					<div class="update-icon"></div><!-- 좌측 사이드바 아이콘 -->

				</div>
			</div>

			<div class="content">
				<div class="content-box">
					<form action="/1st_project/src/php/update.php" method="POST">
						<input type="hidden" name="id" value="<?php echo $id; ?>">
						<input type="date" name="create_date" class="update-date" value="<?php echo $item["create_date"]; ?>">
						<div class="update-category">
							<select name="category_id" class="update-category">
								<?php if($item["category_name"] == "life") { ?>
									<option value="0" selected>생활 비용</option>
									<option value="1">활동 비용</option>
									<option value="2">멍청 비용</option>
								<?php } else if($item["category_name"] == "activity") { ?>
									<option value="0">생활 비용</option>
									<option value="1" selected>활동 비용</option>
									<option value="2">멍청 비용</option>
								<?php }  else if($item["category_name"] === "stupid") { ?>
									<option value="0">생활 비용</option>
									<option value="1">활동 비용</option>
									<option value="2" selected>멍청 비용</option>
								<?php } ?>
							</select>
						</div>
						<div class="update-title-memo">
							<div class="update-title">
								<label for="update-title" id ="title1">제목</label>
								<input type="text" name="title" id="update-title" placeholder="뭘 샀는지 궁금해요!" value="<?php echo $item["title"]; ?>">
							</div>
							<?php
								foreach($arr_err_msg as $val){
							?>
									<p class="update-error"><?php echo $val; ?></p></br>
							<?php		
								}
							?>
							<div class="update-memo">
								<label for="update-memo">메모</label>
								<textarea name="memo" id="update-memo" cols="50" rows="1" maxlength="49" placeholder="메모는 선택 사항입니다."><?php echo $item["memo"]; ?></textarea>
							</div>
						</div>
						<div class="update-spent">
							<label for="update-spent"></label>
							<input type="number" name="amount_used" id="update-spent" placeholder="금액을 입력해주세요." value="<?php echo $item["amount_used"]; ?>">
						</div>
						<div class="update-button">
							<button type="submit">수정확인</button>
							<a href="/1st_project/src/php/datail.php/?id=<?php echo $id; ?>&date=<?php echo $date; ?>">수정취소</a>
						</div>
					</form>
				</div>
			</div>
			<?php require_once(ROOT."php/side.php") ?>
		</main>
	</body>
</html>