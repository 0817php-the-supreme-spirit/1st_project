<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/todolist/src/"); // 웹서버root
define("ERROR_MSG_PARAM", "⛔%s을 입력해 주세요."); // 파라미터 에러 메세지 // 제목, 내용
define("ERROR_MSG_PARAM2", "⛔%s을 선택해 주세요."); // 파라미터 에러 메세지 // 감정
require_once(ROOT."lib/lib_db.php");// DB관련 라이브러리

// post로 request가 있을 때 처리
$conn = null; // DB Connection 변수
$http_method = $_SERVER["REQUEST_METHOD"]; // Method 확인
$arr_err_msg = []; // 에러 메세지 저장용
$title = "";
$content = "";
$em_id = "";
$yoil = array("일요일","월요일","화요일","수요일","목요일","금요일","토요일"); //요일 출력하기 위한 세팅

try {
    if(!db_conn($conn)) { // DB 연결
        throw new Exception("DB Error : PDO Instance");	// DB Instance 에러	
    }
    $conn->beginTransaction(); // 트랜잭션 시작
    $result=db_select_boards_now($conn); // 현재 날짜 호출을 위해 함수 db_select_boards_now($conn) 호출 후 변수($result)로 저장
    if($result === FALSE) {        
        throw new Exception("DB Error : Select Boards Now");	// DB Instance 에러	
    }
    if($http_method === "POST") { // method가 post인 경우
        
		$title = isset($_POST["title"]) ? trim($_POST["title"]) : ""; //title 셋팅
		$content = isset($_POST["content"]) ? trim($_POST["content"]) : ""; //content 셋팅
		$em_id = isset($_POST["em_id"]) ? trim($_POST["em_id"]) : ""; //em_id 셋팅
		
		if($title === "") {
			$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "제목"); // title 없을 경우 $arr_err_msg[]에 오류 메세지 저장
		}
		if($content === "") {
			$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "내용"); // content 없을 경우 $arr_err_msg[]에 오류 메세지 저장
		}
		if($em_id === "") {
			$arr_err_msg[] = sprintf(ERROR_MSG_PARAM2, "감정"); // em_id 없을 경우 $arr_err_msg[]에 오류 메세지 저장
		}

		if(count($arr_err_msg) === 0) { // $arr_err_msg[]가 0일 경우(=title, content, em_id 유효하게 입력 시) 
            $arr_param = $_POST; // 게시글 작성을 위해 파라미터 셋팅

            // insert
            if(!db_insert_boards($conn, $arr_param)) { // 게시글 삽입을 위해 함수 db_insert_boards($conn, $arr_param) 호출     
                throw new Exception("DB Error : Insert Boards"); // DB Instance 에러	
            }
            $conn->commit(); // 모든 처리 완료 시 커밋		
            
            header("Location: 01_list.php"); // List 페이지로 이동
            exit;
		}      
    }

} catch(Exception $e) { // try문에서 예외 발생 시 catch문 실행 > 예외 변수($e)로 저장
    if($http_method === "POST"){
        $conn->rollBack(); // DB 연결 존재(=null 아닌 경우)시 rollback
    }
    echo $e->getMessage(); // 예외발생 메세지 출력
    exit;
} finally { // 예외 발생 여부 상관 없이 실행하여 DB 파기
    db_destroy_conn($conn); // DB파기 위해 함수 db_destroy_conn($conn) 호출
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>삽입 페이지</title>
	<link rel="stylesheet" href="/todolist/src/css/common.css">	
</head>
<body>
	<div class="top_container">

	</div>	
	<form action="/todolist/src/03_insert.php" method="post">
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
                                </div>
                                <div class="emotion_lay1">
                                    <label class="emotion_each" for="emotion2">
                                    <input type="radio" name="em_id" id="emotion2" value="2">
                                    <img class="emo" src="/todolist/doc/img/emotion_2.png">
                                    <br>
                                    <p class="emotion_lay_p">기쁨</p>
                                    </label>
                                </div>
                                <div class="emotion_lay1">
                                    <label class="emotion_each" for="emotion3">
                                    <input type="radio" name="em_id" id="emotion3" value="3">
                                    <img class="emo" src="/todolist/doc/img/emotion_3.png">
                                    <br>
                                    <p class="emotion_lay_p">평온</p>
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
                                </div>
                                <div class="emotion_lay2">
                                    <label class="emotion_each" for="emotion5">
                                    <input type="radio" name="em_id" id="emotion5" value="5">
                                    <img class="emo" src="/todolist/doc/img/emotion_5.png">
                                    <br>
                                    <p class="emotion_lay_p">우울</p>
                                    </label>
                                </div>
                                <div class="emotion_lay2">
                                    <label class="emotion_each" for="emotion6">
                                    <input type="radio" name="em_id" id="emotion6" value="6">
                                    <img class="emo" src="/todolist/doc/img/emotion_6.png">
                                    <br>
                                    <p class="emotion_lay_p">피곤</p>
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
                                </div>
                                <div class="emotion_lay3">
                                    <label class="emotion_each" for="emotion8">
                                    <input type="radio" name="em_id" id="emotion8" value="8">
                                    <img class="emo" src="/todolist/doc/img/emotion_8.png">
                                    <br>
                                    <p class="emotion_lay_p">불만</p>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="align_center">
                            <p class="align_center_txt">감정을 선택해 주세요 !</p>                            
                            <?php
                                foreach ($arr_err_msg as $item) {
                            ?>
                                <p class="err_msg letter_spacing line_height"><?php echo $item; ?></p>
                            <?php
                                }
                            ?>                            
                        </div>
                    </div>
                </div>
                <div class="right_box">
                    <div class="box_layout">
                        <div class="align_center date">
                            <img class="flower_y" src="/todolist/doc/img/flower_red.png">
                            <?php
                                $item_yoil=$yoil[date('w', strtotime($result))];
                                // 함수 date : 날짜나 시간을 지정된 형식으로 변환
                                // w : 함수 date에서 사용할 수 있는 요일을 나타내는 포맷 문자 > 0(일요일)~6(토요일) 
                                // 함수 strtotime : 날짜나 시간 타임스탬프로 변환 
                                // $result=db_select_boards_now($conn); 현재 날짜 호출을 위해 함수 db_insert_boards_now($conn) 호출 후 변수($result)로 저장해둠
                                // 변수($result)에 저장해 둔 현재 날짜의 요일을 숫자(0~6)로 반환 
                                // $yoil : 변수 $result에 저장해둔 요일 숫자을 숫자(0~6) 배열형태로 저장
                                // $yoil = array("일요일","월요일","화요일","수요일","목요일","금요일","토요일")로 변수 선언해두었으므로, 해당 숫자의 값 호출 가능
                                // 변수 $yoil에서 가져온 요일 > 변수 $item_yoil 저장

                                $string = preg_replace('/-/','년 ',$result,1);
                                // 함수 preg_replace : 문자열을 변환하고 결과를 변수($string) 저장
                                // // : 정규 표현식 패턴 > /-/ : 검색할 문자열은 -
                                // 1 : 대체 1회 수행 > 첫번째 '-'
                                // 함수 strtotime 으로 변수($result)에 저장해 둔 20xx-yy-zz의 첫번째 '-'을 '년 '으로 변경
                                // 변수($string)에는 "20xx년 yy-zz" 저장해둠

                                $string = preg_replace('/-/','월 ',$string,1)."일 ";
                                // 함수 preg_replace : 문자열을 변환하고 결과를 변수($string) 저장
                                // // : 정규 표현식 패턴 > /-/ : 검색할 문자열은 -
                                // 1 : 대체 1회 수행 > 첫번째 '-'
                                // 변수($string)에 저장해 둔 20xx년 yy-zz의 첫번째 '-'을 '월 '로 변경
                                // "20xx년 yy월 zz" 로 변경
                                // ."일 " : 문자열을 추가하기 위해 . 사용하여 연결
                                // 변수($string)에는 "20xx년 yy월 zz일 " 저장해둠
                            ?>
                            <p class="align_center_date"><?php echo $string; ?><br><?php echo $item_yoil; ?></p>
                            <!-- $string : 20xx년 yy월 zz일(현재 날짜) / $itme_yoil : a요일(현재 날짜의 요일)  -->
                        </div>
                        <br>
						<div class="align_center">
							<label for="title"></label>
							<input type="text" class = "textarea_1" name="title" id="title" value="<?php echo $title; ?>"
							maxlength="20" placeholder="제목을 작성해주세요." spellcheck="false">
                            <!-- $title = ""; 로 선언해두었고, $title = ""; 출력하여 입력 기본 값으로 설정 -->
                            <!-- value 설정해주면 post 파라미터에 저장됨 -->
							<br><br>
							<label for="content"></label>
							<textarea class = "textarea_2" name="content" id="content" cols="25" rows="10"
							placeholder="내용을 작성해주세요." spellcheck="false"><?php echo $content; ?></textarea>
                            <!-- $content = ""; 로 선언해두었고, $content = ""; 출력하여 입력 기본 값으로 설정 -->
						</div>
                    </div>
                </div>
                <div class="side_box">
                    <div class="side_category bgc_cate1">
                        <button class= "side_text button_text" type="submit">작성</button>
                    </div>
                    <div class="side_category bgc_cate3">
                        <a class= "side_text" href="/todolist/src/01_list.php/?page=1">취소</a>
                        <!-- 취소버튼 클릭 시 List 페이지 이동 -->
                    </div>
                </div>
            </div>
        </div>
    </form>
</body>
</html>