<?php 
define("ROOT",$_SERVER["DOCUMENT_ROOT"]."/1st_project/src/");
require_once(ROOT."lib/lib_db.php"); // db관련 라이브러리
define("ERROR_MSG_PARAM", "해당 값을 찾을 수 없습니다.");

$conn = null; 
$http_method = $_SERVER["REQUEST_METHOD"]; //REQUEST == 요청 METHOD == 방식
$arr_err_msg = []; // 에러 메세지 저장
// =--------------------------------------------------------------------------------
// $title = "";
// $memo = "";
// $amount_used = "";
// $create_date = "";
// $category_id = "";

// POSt로 request가 왔을 때 처리한다
// $mttp_method = $_SERVER["REQUEST"];
// if($http_method === "POST") {
// 	try {
		

// 		$arr_post = $_POST;
// 		//iset안에 함수 확인 트루일경우 삼항연산자 true 문 싫행 아닐경우 false 실행
// 		$date = isset($_POST["create_date"]) ? trim($_POST["create_date"]) : date('Y-m-d');
// 		$title = isset($_POST["title"]) ? trim($_POST["title"]) : "";
//         $memo =isset($_POST["memo"]) ? trim($_POST["memo"]) : null; 
// 		$amount_used = isset($_POST["amount_used"]) ? trim($_POST["amount_used"]) : "";
// 		$create_date = isset($_POST["create_date"]) ? trim($_POST["create_date"]) : "";
// 		$category_id = isset($_POST["category_id"]) ? trim($_POST["category_id"]) : "";
		
// 		//넘어와서 if 조건문이 빈 값일때 오류 실행 하는 if문
// 		if($title === "") {
//             $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "title");
//         }
// 		if($amount_used === "") {
//             $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "amount_used");
// 		}
// 		if($create_date === "") {
// 			$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "create_date");
// 		}
// 		// var_dump($create_date);
// 		if($category_id === "") {
// 			$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "category_id");
// 		}
// 		//넘어와서 (오류값이 안뜰때) 값이 0일때 if문 실행
// 		if(count($arr_err_msg) === 0) {

// 			// DB 접속
// 			if(!db_conn($conn)) {
// 				// DB Instance 에러
// 				throw new Exception("DB Error : PDO Instance");
// 			}

// 			$conn ->beginTransaction(); //트랜잭션 시작 하는 부분

			

// 			// 게시글 작성을 위해 파라미터 셋팅
// 			$arr_post = [
// 				"title" => $_POST["title"]
// 				,"memo" => $_POST["memo"]
// 				,"amount_used" => $_POST["amount_used"]
// 				,"create_date" => $_POST["create_date"]
// 				,"category_id" => $_POST["category_id"]
// 			];

// 			//insert
// 			if(!db_insert($conn, $arr_post)) {
// 				throw new Exception("DB Error : Insert page");
// 			}
// 			$conn->commit(); //모든 처리 완료 시 커밋

// 			//리스트 페이지로 이동
// 			header("Location: list.php/?date={$date}");
// 			exit;
//     	}
// 	} catch(Exception $e) {
//         if($conn !== null) {
//         $conn->rollBack();
//         }
//         echo $e->getMessage(); //Exception 메세지 출력
//         // header("Location: error.php/?err_msg={$e->getMessage()}");
//         exit;
//     } finally {
//         db_destroy_conn($conn); // db 파기
//     }
// }
// else {
// 	$date = isset($_GET["date"]) ? trim($_GET["date"]) : date('Y-m-d');

// 	if(!db_conn($conn)) {
// 		// DB Instance 에러
// 		throw new Exception("DB Error : PDO Instance");
// 	}

// }
// =--------------------------------------------------------------------------------
	try {

	if(!db_conn($conn)) {
		// DB Instance 에러
		throw new Exception("DB Error : PDO Instance");
	}
	
	if($http_method === "GET") {
	$date = isset($_GET["date"]) ? trim($_GET["date"]) : "";

	if(db_user_salary_compare($conn) === 0)
	{
		header("Location: main.php");
	}
	
	} else {
		// $arr_post = $_POST;
		//iset안에 함수 확인 트루일경우 삼항연산자 true 문 싫행 아닐경우 false 실행
		$date = isset($_POST["create_date"]) ? trim($_POST["create_date"]) : "";
		$title = isset($_POST["title"]) ? trim($_POST["title"]) : "";
        $memo =isset($_POST["memo"]) ? trim($_POST["memo"]) : ""; 
		$amount_used = isset($_POST["amount_used"]) ? trim($_POST["amount_used"]) : "";
		$create_date = isset($_POST["create_date"]) ? trim($_POST["create_date"]) : "";
		$category_id = isset($_POST["category_id"]) ? trim($_POST["category_id"]) : "";
		
		//넘어와서 if 조건문이 빈 값일때 오류 실행 하는 if문
		if($title === "") {
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "title");
        }
		if($amount_used === "") {
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "amount_used");
		}
		if($create_date === "") {
			$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "create_date");
		}
		// var_dump($create_date);
		if($category_id === "") {
			$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "category_id");
		}

		//넘어와서 (오류값이 안뜰때) 값이 0일때 if문 실행
		if(count($arr_err_msg) === 0) {
			// DB 접속
			$conn ->beginTransaction(); //트랜잭션 시작 하는 부분
			// 게시글 작성을 위해 파라미터 셋팅
			$arr_post = [
				"title" => $_POST["title"]
				,"memo" => $_POST["memo"]
				,"amount_used" => $_POST["amount_used"]
				,"create_date" => $_POST["create_date"]
				,"category_id" => $_POST["category_id"]
			];

			//insert
			if(!db_insert($conn, $arr_post)) {
				throw new Exception("DB Error : Insert page");
			}
			$conn->commit(); //모든 처리 완료 시 커밋

			//리스트 페이지로 이동
			header("Location: list.php/?date={$date}");
			exit;
		}
	}
	
	require_once(ROOT."php/amount.php");
	
	} catch(Exception $e) {
		if($conn !== null) { //null값과 타입이 다르거나 값이 다르면 롤백
			$conn->rollBack();
		}
		echo $e->getMessage(); //Exception 메세지 출력
		// header("Location: error.php/?err_msg={$e->getMessage()}");
		exit;
	} finally {
		db_destroy_conn($conn); // db 파기
	}


?>

<!DOCTYPE html>
<html lang="ko">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="/1st_project/src/css/insert/style.css">
		<link rel="stylesheet" href="/1st_project/src/css/side/style.css">
		<title>아껴봐요 절약의 숲 작성 페이지</title>
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

						<a href="/1st_project/src/php/list.php"><div class="side-left-page side-left-off"><p>오늘의 지출</p></div></a>
						<a href="/1st_project/src/php/insert.php"><div class="side-left-page side-left-on"><p>지출 작성부</p></div></a>
						<a href="/1st_project/src/php/total.php/?date=<?php echo $date; ?>"><div class="side-left-page side-left-off"><p>지출 통계서</p></div></a>

						<div class="side-left-line-2"></div>
						
						<div class="category-box">
							<!-- <p>작성중..</p> -->
							<div class="category-box2"></div>
						</div>
					</form> 

				</div>
			</div>

			<div class="content">
				<div class="content-box">
					<form action="/1st_project/src/php/insert.php" method="post">
					<div class="content-date-box">
						<!-- <p>날짜</p> -->
						<input type="date" name="create_date" value="<?php echo $date; ?>">
					</div>
							<div class="content-title-box">
								<label for="text-title" class="content-title-box1">제목</label>
								<input type="text" name="title" id="text-title" class="content-title-box2" maxlength="25" required placeholder="뭘 샀는지 궁금해요!">
							</div>
							<div class="content-memo-box">
								<label for="text-memo" class="content-memo-box1">메모</label>
								<textarea class="content-memo-box2" name="memo" id="text-memo" maxlength="25" placeholder="메모도 중요해요!"></textarea>
							</div>
							
						<div class="content-value-box">
							<div class="content-float1">
									<select name="category_id" id="category" class="content-category" required>
										<option value="" selected disabled hidden>선택해주세요</option>
										<option value="0">생활비용</option>
										<option value="1">활동비용</option>
										<option value="2">멍청비용</option>
									</select>
								<div class="content-category-money">
									<input type="number" name="amount_used" id="amount_used" min="1" maxlength="6" required placeholder="금액을 입력해 주세요">
								</div>
							</div>
							<div class="content-float2">
							<p>
							<?php 
							$randment = array("힘든 생활..", "우리 돈 없엉", "돈좀 쓰네?", "절약하자!!", "그만써~~!");
							$selected = array_rand($randment);
							echo $randment[$selected];
							?>
							</p>
							</div>
						</div>
						<div class="content-button">
							<button class="content-button-go" type="submit">작성</button>
						<a href="/1st_project/src/php/list.php/?date=<?php echo $date; ?>" class="content-button-back">돌아가기</a>
					</form>
					</div>
				</div>
			</div>
			<?php require_once(ROOT."php/side.php") ?>
		</main>
	</body>
</html>