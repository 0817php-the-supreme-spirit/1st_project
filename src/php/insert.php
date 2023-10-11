<?php 
define("ROOT",$_SERVER["DOCUMENT_ROOT"]."/1st_project/src/");
require_once(ROOT."lib/insert_lib_db.php"); // db관련 라이브러리

$conn = null; 
$http_method = $_SERVER["REQUEST_METHOD"];
$arr_err_msg = []; // 에러 메세지 저장
$title = "";
$memo = "";
$amount_used = "";
$create_date = "";
$category_id = "";

// POSt로 request가 왔을 때 처리
// $mttp_method = $_SERVER["REQUEST"];
if($http_method === "POST") {
	try {
		$title = isset($_POST["title"]) ? trim($_POST["title"]) : "";
        $memo =isset($_POST["memo"]) ? trim($_POST["memo"]) : "";
		$amount_used = isset($_POST["amount_used"]) ? trim($_POST["amount_used"]) : "";
		$create_date = isset($_POST["create_date"]) ? trim($_POST["create_date"]) : "";
		$category_id = isset($_POST["category_id"]) ? trim($_POST["category_id"]) : "";
	
		if($title === "") {
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "title");
        }
        if($name_t === "") {
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "memo");
        }
		if($name_t === "") {
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "amount_used");       
		}
		if($name_t === "") {
		$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "create_date");
		}
		if($name_t === "") {
		$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "category_id");
		}

		if(count($arr_err_msg) === 0) {

			$arr_post = $_POST;
			$conn = null;

 		// DB 접속
	    if(!db_conn($conn)) {
            // DB Instance 에러
		    throw new Exception("DB Error : PDO Instance");

	    }
        $conn ->beginTransaction(); //트랜잭션 시작 하는 부분

        //insert
        if(!db_insert($conn, $arr_post)) {
            throw new Exception("DB Error : Insert test");
        }

        $conn->commit(); //모든 처리 완료 시 커밋

        //리스트 페이지로 이동
        header("Location: list.php");
        exit;
      }
	} catch(Exception $e) {
        if($conn !== null) {
        $conn->rollBack();
        }
        // echo $e->getMessage(); //Exception 메세지 출력
        header("Location: error.php/?err_msg={$e->getMessage()}");
        exit;
    } finally {
        db_destroy_conn($conn); // db 파기
    }
}

?>

<!DOCTYPE html>
<html lang="ko">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="/1st_project/src/css/insert/style.css">
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
					<div class="content-date-box">
						<!-- <p>날짜</p> -->
						<input type="date">
					</div>
					<form action="/1st_project/src/insert.php" method="post">
							<table>
								<label for="text-title" class="content-title-box1">제목</label>
								<input type="text" id="text-title" class="content-title-box2" required placeholder="뭘 샀는지 궁금해요!">
								<div class="content-memo-box">
									<label for="text-memo" class="content-memo-box1">메모</label>
									<textarea class="content-memo-box2" id="text-memo" maxlength="50" placeholder="같이 작성하면 좋아요!"></textarea>
								</div>
							</table>
						<div class="content-value-box">
							<div class="content-float1">
									<select name="category1" id="category" class="content-category" required>
										<option value="" selected disabled hidden>선택해주세요</option>
										<option value="life">생활비용</option>
										<option value="activity">활동비용</option>
										<option value="stupid">멍청비용</option>
									</select>
								<p class="content-category-money"><input type="number" name="number" required placeholder="금액을 입력해 주세요"></p>
							</div>
							<div class="content-float2">
								<p>벌써 지출</p>
								<p>할려고요?</p>
							</div>
						</div>
						<div class="content-button">
							<button class="content-button-go" type="submit">작성</button>
						<a href="/1st_project/src/list.php" class="content-button-back">돌아가기</a>
					</form>
					</div>
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