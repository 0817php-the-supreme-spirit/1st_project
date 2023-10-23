<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/todolist/src/");
// ROOT는 상수의 이름이고$_SERVER["DOCUMENT_ROOT"]는 슈퍼 글로벌 변수 중 하나인
// _SERVER에서 DOCUMENT_ROOT 값을 가져온것임 결과적으로는 ROOT 상수는 웹 서버의 루트 디렉토리에
//"/todolist/src/"를 추가한 값을 가지게 되는거셈
define("FILE_HEADER", ROOT."header.php");
//"FILE_HEADER" 상수는 "ROOT" 상수의 값인 웹 서버 디렉토리에 "header.php"를 추가한 경로임
//이러한 상수를 사용하면 스크립트 내에서 일관된 경로 또는 파일 이름을 참조할 때 편리하게 사용 가능함
//예를 들어, "FILE_HEADER" 상수를 사용하면 "header.php"파일을 다른 스크립트에서 사용할 때 항상 동일한 경로로 참조가능
require_once(ROOT."lib/lib_db.php");
//파일을 포함하거나 불러올때 사용되며,PHP스크립트에서 다른 PHP파일을 포함하거나 불러오는데 사용됨 그래서 lib_db파일을 불러온거임




try{
    // 초기값설정
	$id = "";
	$conn = null;
    $page = "";
	$ox ="";
	//변수를 초기화 함, 이유는 정의되지 않은 값으로부터 보호 하기 위해서
	//메모리에 내용이 이미 입력되어있으면 원치않는 동작을 유발 할 수 있음
	//쉽게 생각해서 컴퓨터 프로그램에서 데이터를 저장하는 데 사용되는 메모리를 할당할려고 값 설정함
	





// id확인
	if(isset($_GET["id"])){
		$id=$_GET["id"]; //id 셋팅
	} else {
		throw new Exception("Parameter ERROR : NO id"); //강제 예외 발생
	}
	//"id"라는 이름의 URL 매개변수(파라미터)가 현재 요청에서 존재하는지 확인하는거임
	//$_GET 은 PHP의 전역 배열로 현재 URL에서 GET METHOD를 통해 전달된 매개변수를 저장함
	//isset은 매개변수가 존재하는지 확인하며 "id"매개변수가 존재하면 아래 코드를 실행
	//"id" 매개변수(파라미터)가 존재하지 않는경우 강제 예외가 발생하고 오류 메세지 출력, 혹은 예외처리 코드로 이동함
	//$id = $_GET["id"]; "id"매개변수 값을 $id라는 변수에 할당한다 (true) 하면 해당 URL에 매개변수 값을 저장함
	//id는 지금 페이지의 게시글 순번을 나타냄





	if(isset($_GET["page"])){
		$page=$_GET["page"]; //id 셋팅
	} else {
		throw new Exception("Parameter ERROR : NO page"); //강제 예외 발생
	}
	//"page"라는 이름의 URL 매개변수가 존재하는지 확인함
	//$_GET 은 GET METHOD를 통해 전달된 매개변수(파라미터)를 저장함
	//isset으로 매개변수 존재 여부를 검사하고
	//매개변수가 존재한다 하면 $page라는 변수에 할당함 그러면 위와 같이 URL 매개변수 값 저장함
	//없다면 예외 발생하고 코드의 실행을 중지하고 오류 메세지나 예외 처리 코드로 이동함
	//page는 현재 사용자가 보고 있는 페이지의 번호를 나타냄 page=1이렇게







	// DB 연결
	if(!my_db_conn($conn)) {
		throw new Exception("DB Error : PDO Instance");
	}
	//if 조건이 참인지 아닌지 괄호 안의 조건을 평가함
	//!my_db_conn($conn)이게 조건이고 함수가 'false'를 반환할때 참임
	//왜냐하면 ! 기호는 부정을 의미해서 'false'일때 조건이 참이 됨
	//중괄호 내부의 코드 블록은 조건이 참일 때 실행됨
	//따라서 이 코드는 연결 설정이 실패한 경우만 예외를 발생시키는 역할을 함
	//throw new Exception("DB Error : PDO Instance");는
	//데이터베이스 연결이 실패한 경우 예외를 발생 시킴 오류가 나타나면
	//예외 메세지로"DB Error : PDO Instance" 를 설정 시킨거임
	//my_db_conn($conn)은 my_db_conn($conn)
	// 함수가 실패했을 때 (즉, 데이터베이스 연결 설정에 문제가 있는 경우),
	// 조건을 참(true)으로 만듬
	// 이것은 데이터베이스 연결 설정에 문제가 있을 때,
	// 코드 블록 내에서 예외를 발생시키기 위한 조건으로 사용됨








	// 게시글 데이터 조회
	$arr_param = [
		"id" => $id
	];
	$result=db_select_boards_id($conn, $arr_param);
	//데이터 베이스 쿼리를 실행할 때 사용되는 $arr_parm을 사용해서
	//특정 게시판 게시물을 조회하기 위한 조건문 적는데 사용됨
	//$arr_param = ["id" => $id];으로 배열 내부에 "id"키를 사용해서
	//$id 변수의 값을 값으로 설정함
	//그래서 $arr_param 배열은 ["id" => $id] 와 같이 설정됨
	//$result 변수는 게시물 조회 결과를 저장하고 반환해줌
	//db_select_boards_id 함수의 실행 결과는 $result 변수에 할당됨
	//$conn 은 데이터 베이스 연결을 나타내는 PDO객체임
	//$arr_param 은 게시물 조회 조건을 설정하는데 사용되는 배열임
	//여기서 id키를 통해 이전에 설정한 $id변수의 값을 전달함
	//id키를 사용해서 어떤 게시물을 조회할지 지정함
	//id키는 이전에 설정한 $id 변수의 값을 갖고있음
	//db_select_board_id험수는 데이터 베이스 쿼리를 실행해서 ID를 가진
	//게시물을 조회함, db어쩌구 함수는 $conn을 통해 데이터 베이스에 연결해줌
	//$arr_parm배열을 사용해서 쿼리의 조건을 설정해서 $result변수에 저장됨
	//특정 게시물을 식별하고 해당 게시물 정보를 가져오기위해 쓴 코드임





	

	// 게시글 조회 예외 처리
	if(!$result) {
		throw new Exception("DB Error : PDO Select_id");
	} else if(!(count($result) === 1)){
		throw new Exception("DB Error : PDO Select_id count," .count($result));
	} 
//if문은 만약에 데이터 $result 변수를 검사해서 데이터 베이스 조회 결과가
//false일 경우(데이터베이스 조회 실패시)예외를 출력시킴
//에러 메세지는"DB Error : PDO Select_id"로 설정됨
//데이터 베이스 오류가 생겼을 때 오류를 처리하기 위한 코드임
//else if는 조건문의 후속 조건을 나타내는거임 이전 조건이 거짓인경우
//다음 조건을 검사함 
//$result 배열의 길이를 검사하는거임
//count($result)는 결과 배열의 요소 수를 반환해줌
//즉 결과 배열의 길이가 1인지 확인하는거임 원하는 결과가 정확히 하나인지 확인함
//1이 아닌 경우 혹은 아무 결과가 없는 경우 예외를 발생 시킴
//예외 메시지에는"DB Error : PDO Select_id count,"와 $result 배열의 길이가 포함됨
//이것은 결과가 예상대로 아니면 오류 처리를 위한 코드임



$item=$result[0];
	if($item["chk_flg"] === "0"){
		$ox = "X";
	}
	else if($item["chk_flg"] === "1"){
		$ox = "O";
	}
} catch (Exception $e) {
	echo $e->getMessage(); //예외 메세지 출력
	exit; //처리종료
} finally {
	db_destroy_conn($conn);
}
	// 게시글 조회 count 에러


//$result 배열에서 첫번째 요소를 가져와서 $item 변수에 할당함
//이건 데이터 베이스 조회 결과에서 첫번째 게시물을 나타냄
//if($item["chk_flg"] === "0"){ ... }은 
//$item배열에서 chk_flg	키의 값을 검사함
//밑에서 수행여부 O X 를 위해 적음
//체크플래그가 0이면 O X 중 X를 $ox문자열에 할당하고
//체크플래그가 1이면 O X 중 O를 $ox문자열에 할당함
//catch (Exception $e)은 예외 처리를 위한 블록임
//예외가 발생했을 때 실행돼서 $e는 예외 객체를 나타냄
//예외가 발생하면 해당 $e에 예외 정보가 저장됨
//echo $e->getMessage();는 예외 객체 메세지를 출력 에러 메세지임
//exit;는 스크립트 실행 종료함, 즉 예외가 발생하면 스크립트 실행이 중지되기위해 적음
?>







<!DOCTYPE html>
<!-- 문서 정의함 html 로 할거라는 뜻 -->
<html lang="ko">
	<!-- 언어는 한국어로 설정 -->
<head>
	<!-- 페이지 정보랑 스타일 정의하는 섹션임 -->
    <meta charset="UTF-8">
	<!-- 웹 페이지 문자 인코딩을 UTF-8로 설정함 한국어랑 다른 언어때매 함 -->
    <meta name="viewport" content="width=, initial-scale=1.0">
	<!-- 뷰포트 설정 정의함 웹 페이지가 올바르게 화면에 맞게 표시되기 위해서 적음 -->
    <link rel="stylesheet" href="/todolist/src/css/style.css">
	<!-- 외부 스타일 시트를 연결하는 경로가 적힌 코드 -->
    <title>상세페이지</title>
	<!-- 웹 페이지 제목인 detail페이지니까 상세 페이지라 정의함 브라우저 탭에 표시됨 -->
</head>

<body>
    <?php
		require_once(FILE_HEADER);
//PHP 스크립트에서 다른 파일을 포함 또는 요청하는 코드임
//require_once(FILE_HEADER) 변수에 저장된 파일을 한번만 포함하도록 요청하는 명령임
//require_once 함수는 파일을 포함하고, 이미 포함되었거나 중복으로 포함하는걸 방지해줌
//달이랑 꿀잠 공통 헤더 파일을 포함해야해서 이 코드를 사용함 (여러번 페이지나 스크립트에 중복해서 적지않기위해)
	?>  
	
    <div class="detail-container">
		<!-- 상세 페이지 컨테이너 정의하는 div임 상세 페이지 콘텐츠 감싸기위해 적음 -->
        <table class="detail_table center">     
	<!--테이블 요소를 정의함 detail_table이랑 center클래스 갖고있음 상세 페이지 레이아웃이며 클래스를 써서 스타일 적용함 -->
            <tr>
				<!-- tr은 테이블 행을 나타냄 각 행의 콘텐츠를 나누기 위해서 적음 -->
                <td colspan="3" class="detail_head center">
                    <span>수행여부: <?php echo $ox; ?></span>
                </td>
				<!-- PHP코드를 사용해서 변수 $ox값을 출력함 수행여부 : O,X -->
            </tr>
            <tr>
                <td colspan="3" class="detail_content_1">
						<?php echo $item["content"]; ?>
						<!-- HTML 테이블 데이터 셀을 나타냄 게시물 값 출력을 위해서 적음 -->
						<!-- colspan으로 셀을 세개의 열을 병합해서 사용함-->
					</td>
				</tr>
				<tr>
					<td colspan="3" class="detail_content_2">
						<?php echo $item["write_date"]; ?>
						<!-- tr과 td블록은 게시물 작성 날짜를 표시해줌 -->
                </td>
            </tr>
			<tr>
				<td>
					<a href="/todolist/src/update.php/?id=<?php echo $id; ?>&page=<?php echo $page ?>" class="detail_btn">수정</a>
				</td>
				<!-- 업데이트(수정페이지)로 가는 버튼 -->
				<td>
				<a href="/todolist/src/delete.php/?id=<?php echo $id; ?>&page=<?php echo $page ?>" class="detail_btn">삭제</a>
				</td>
				<!-- 딜리트(삭제페이지)로 가는 버튼 -->
				<td>
				<a href="/todolist/src/list.php/?page=<?php echo $page ?>" class="detail_btn">나가기</a>
				</td>
				<!-- 나가기(리스트페이지)로 가는 버튼 -->
			</tr>
			<tr>
				<td>
					<img src="/todolist/src/img/labtop.png" alt="" class="detail_img labtop">
					<!-- 노트북 이미지 넣기 -->
					<img src="/todolist/src/img/c_star.png" alt="" class="detail_img c_star">
					<!-- 별의 커비 이미지 넣기 -->
					<img src="/todolist/src/img/cloude.png" alt="" class="detail_img cloude">
					<!-- 구름 이미지 넣기 -->
				</td>
			</tr>
        </table>

	</div>
	<div class="stars"></div>
	<!-- 배경에 반짝이는 별 넣기 -->
</body>
</html>
