<?php
// define 상수 
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/todolist/src/"); // 웹서버 root 패스 생성
define("IMG", "/todolist/doc/img/");
define("ERROR_MSG_PARAM", "⛔%s을 입력해 주세요."); //파라미터 에러 메세지 // 제목, 내용
define("ERROR_MSG_PARAM2", "⛔%s을 선택해 주세요."); //파라미터 에러 메세지 // 감정
require_once(ROOT."lib/lib_db.php");// DB관련 라이브러리

// update page : 
//              왼) 수정할 이모션 선택 시 값 보내기⭕
//              오) 작성일자(월, 일, 요일 - 수정x)⭕, 제목⭕, 내용⭕

$conn = null; // DB 연결용 변수
$http_method = $_SERVER["REQUEST_METHOD"]; // Method 확인
$arr_err_msg = []; // 에러 메세지 저장용
$title = "";
$content = "";
$em_id = "";
$yoil = array("일요일","월요일","화요일","수요일","목요일","금요일","토요일"); //요일 출력하기 위한 세팅

try { 
    // DB 접속
    if(!db_conn($conn)) {
        // DB Instance 에러
        throw new Exception("DB Error : PDO Instance"); // 강제 예외 발생
    }

    // 사용자가 처리할 수 없는 에러
    if($http_method === "GET") {
        $id = isset($_GET["id"]) ? $_GET["id"] : ""; // id 세팅
        $page = isset($_GET["page"]) ? $_GET["page"] : ""; // page 세팅
        
        if($id === "") {
			$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "id");
		}
		if($page === "") {
			$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "page");
		}
        if(count($arr_err_msg) >= 1) {
			throw new Exception(implode("<br>", $arr_err_msg));
		}
        // 사용자가 처리할 수 있는 에러
	} else {
		// POST Method
		// 게시글 수정을 위해 파라미터 세팅
		$id = isset($_POST["id"]) ? $_POST["id"] : ""; // id 세팅
		$page = isset($_POST["page"]) ? $_POST["page"] : ""; // page 세팅
		$title = trim(isset($_POST["title"]) ? trim($_POST["title"]) : ""); // title 세팅
		$content = trim(isset($_POST["content"]) ? trim($_POST["content"]) : ""); // content 세팅
		$em_id = trim(isset($_POST["em_id"]) ? trim($_POST["em_id"]) : ""); // em_id 세팅

		if($id === "") {
			$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "id");
		}
		if($page === "") {
			$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "page");
		}

		// id, page가 없을 경우(예외처리)
		if(count($arr_err_msg) >= 1) {
			throw new Exception(implode("<br>", $arr_err_msg));
		}


		// title, content, em_id가 없을 경우(처리 속행)
		if($title === "") {
			$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "제목");
		}
		if($content === "") {
			$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "내용");
		}
		if($em_id === "") {
			$arr_err_msg[] = sprintf(ERROR_MSG_PARAM2, "감정");
		}

		// 에러 메세지가 없을 경우에 업데이트 처리
		if(count($arr_err_msg) === 0) {
			// 게시글 수정을 위해 파라미터 세팅
			$arr_param = [
				"id" => $id
				,"title" => $title
				,"content" => $content 
				,"em_id" => $em_id 
				// ,"title" => $_POST["title"]
				// ,"content" => $_POST["content"]
			];

			// 게시글 수정 처리 POST Method 일 경우에만 트랜잭션 시작 
			$conn->beginTransaction(); // 123line catch 업데이트 위치 전으로 트랜잭션 시작

			if(!db_update_boards_id($conn, $arr_param)) {
				// DB  Update_Boards 에러 
				// 에러 발생했을 경우 catch로 throw
				throw new Exception("DB Error : Update_Boards_id");
			}
			$conn->commit(); // 정상 처리가 될 경우 commit

			// 게시글 수정 했을 경우 detail page로 이동
			header("Location: 02_detail.php/?id={$id}&page={$page}");
			exit;
		}

	}
	// 게시글 데이터 조회를 위한 파라미터 세팅 -> 함수명 : db_select_boards_id
	$arr_param = [
		"id" => $id
	];

	// 게시글 데이터 조회
	$result = db_select_boards_id($conn, $arr_param);
	// 게시글 조회 예외처리
	if($result === false) {
		// 게시글 조회 에러
		throw new Exception("DB Error : PDO Select_id");
	} else if(!(count($result) === 1)) {
		// 게시글 조회 count 에러
		throw new Exception("DB Error : PDO Select_id Count, ".count($result));
	}
	$item = $result[0];
	// 0000-00-00 -> 0000년 00월 00일로 출력
	$arr = explode ("-", $item["create_at"]);
	$print_date = $arr[0]."년 ".$arr[1]."월 ".$arr[2]."일";
} catch(Exception $e) {
	if($http_method === "POST") {
		$conn->rollBack(); // rollback
	}
		echo $e->getMessage(); // 예외 발생 메시지 출력
	//         header("Location: 06_error.php/?err_msg={$e->getMessage()}");
	exit; // 처리 종료
		
} finally {
	db_destroy_conn($conn); // DB 파기
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>수정 페이지</title>
	<link rel="stylesheet" href="/todolist/src/css/common.css">	
</head>
<body>

	<div class="top_container">
	</div>
	<form action="/todolist/src/04_update.php" method="post">
		<!-- POST에 id페이지 값을 저장 시켜주는 것 -->
		<input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="hidden" name="page" value="<?php echo $page ?>">
        <div class="main_container">
            <div class="main_container_box">
                <div class="left_box">
                    <div class="box_layout">
                        <div class="post_it">
							<div class="emotion_ma1">
								<div class="emotion_lay1">
									<label class="emotion_each" for="emotion1">
									<input type="radio" name="em_id" id="emotion1" value="1">
									<img class="emo" src="/todolist/doc/img/emotion_1.png">
									<br>
                                    <p class="emotion_lay_p">행복</p>
                                    </label>
									</label>
								</div>
								<div class="emotion_lay1">
									<label class="emotion_each" for="emotion2">
									<input type="radio" name="em_id" id="emotion2" value="2">
									<img class="emo" src="/todolist/doc/img/emotion_2.png">
									<br>
                                    <p class="emotion_lay_p">기쁨</p>
                                    </label>
									</label>
								</div>
								<div class="emotion_lay1">
									<label class="emotion_each" for="emotion3">
									<input type="radio" name="em_id" id="emotion3" value="3">
									<img class="emo" src="/todolist/doc/img/emotion_3.png">
									<br>
                                    <p class="emotion_lay_p">평온</p>
                                    </label>
									</label>
								</div>
							</div>
							<div class="emotion_ma2">
								<div class="emotion_lay2">
									<label class="emotion_each" for="emotion4">
									<input type="radio" name="em_id" id="emotion4" value="4">
									<img class="emo" src="/todolist/doc/img/emotion_4.png">
									<br>
                                    <p class="emotion_lay_p">슬픔</p>
                                    </label>
									</label>
								</div>
								<div class="emotion_lay2">
									<label class="emotion_each" for="emotion5">
									<input type="radio" name="em_id" id="emotion5" value="5">
									<img class="emo" src="/todolist/doc/img/emotion_5.png">
									<br>
                                    <p class="emotion_lay_p">우울</p>
                                    </label>
									</label>
								</div>
								<div class="emotion_lay2">
									<label class="emotion_each" for="emotion6">
									<input type="radio" name="em_id" id="emotion6" value="6">
									<img class="emo" src="/todolist/doc/img/emotion_6.png">
									<br>
                                    <p class="emotion_lay_p">피곤</p>
                                    </label>
									</label>
								</div>
							</div>
							<div class="emotion_ma3">
								<div class="emotion_lay3">
									<label class="emotion_each" for="emotion7">
									<input type="radio" name="em_id" id="emotion7" value="7">
									<img class="emo" src="/todolist/doc/img/emotion_7.png">
									<br>
                                    <p class="emotion_lay_p">화남</p>
                                    </label>
									</label>
								</div>
								<div class="emotion_lay3">
									<label class="emotion_each" for="emotion8">
									<input type="radio" name="em_id" id="emotion8" value="8">
									<img class="emo" src="/todolist/doc/img/emotion_8.png">
									<br>
                                    <p class="emotion_lay_p">불만</p>
                                    </label>
									</label>
								</div>
							</div>
                        </div>
                        <div class="align_center">
							<p class="align_center_txt">감정을 수정해 주세요 !</p>							
							<?php
								foreach ($arr_err_msg as $item_err) {
							?>
								<p class="err_msg letter_spacing line_height"><?php echo $item_err; ?></p>
							<?php
								}
							?>							
                        </div>
                    </div>
                </div>
                <div class="right_box">
                    <div class="box_layout">
                        <div class="align_center date">
                            <img class="flower_y" src="/todolist/doc/img/flower_yellow.png">
							<!-- 년, 월, 일 출력 -->
                            <p class="align_center_date"><?php echo $print_date ?><br> 
							<!-- 요일 출력 -->
							<?php
								$item_yoil=$yoil[date('w', strtotime($item["create_at"]))];
								//  strtotime 문자열(Y-m-d H:i:s)
								// w : 요일 숫자 표현 [ 0(일요일)에서 6(토요일) ]
								// var_dump($item["create_at"]);
								// var_dump(strtotime($item["create_at"]));
								echo $item_yoil;
							?>
                            </p>
                        </div>
                        <br>
                        <!-- <form class="align_center" action="" method="post"> -->
                        <div class="align_center">
							<!-- 상위 php에 넣어서 설정하는게 맞음 -->
							<?php 
							if($http_method === "GET"){ // GET으로 처음 고유의 값 tit랑 con을 받아온다
								$tit_stay= $item["title"];
								$con_stay= $item["content"];
							} else { // 에러가 떴을 때 수정 중인 내용을 POST 메소드에 저장해서 그 값을 val값으로 넣어줘서 변경 중이던 값을 그대로 출력할 수 있게 해준다
								$tit_stay= $_POST["title"];
								$con_stay= $_POST["content"];
							}
							?>
                            <label for="title"></label>
                            <input type="text" class='text_tit' name="title" id="title" value="<?php echo $tit_stay ?>" maxlength="20" spellcheck="false">
                            <br><br>
                            <label for="content"></label>
                            <textarea class='text_con' name="content" id="content" cols="25" rows="10" spellcheck="false"><?php echo $con_stay ?></textarea>
                        </div>	
                    </div>
                </div>
                <div class="side_box">
                    <div class="side_category bgc_cate1">
						<!-- 수정 버튼 클릭 시 수정
                             post > update.php
                            게시글의 id를 이용해서 update -->
                        <button class= "side_text button_text" type="submit">수정</button>
                    </div>
                    <div class="side_category bgc_cate3">
						<!-- 취소 버튼 클릭 시 디테일 페이지로 이동 -->
                        <a class= "side_text" href="/todolist/src/02_detail.php/?id=<?php echo $id; ?>&page=<?php echo $page; ?>">취소</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</body>
</html>