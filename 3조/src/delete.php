<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/project1/src/"); // 웹서버 root 패스 생성
require_once(ROOT."lib/delete_lib.php"); // DB관련 라이브러리

// 삭제할 대상 = 챌린지id? 게시글id? 게시글id로 구별해야됨. 챌린지id는 중복이 있기때문
// 삭제 취소하면 돌아갈 페이지
// $create_id = $_GET["create_id"]; // create_id : 게시글 구별하는 id
// $page_flg = $_GET["page_flg"];	// 페이지 구별하는 플래그. "0":in_pro(진행중), "1":com(완료). 
// 삭제 취소하거나 삭제하면 원래 있던 페이지로 돌아감

// $create_id = [
// 	"create_id" => $create_id
// ];
// $page_flg = [
// 	"page_flg" => $page_flg
// ];


$arr_get = [];
$arr_post = [];
// ****** arr_get,arr_post로 나누지말고 $arr_request로 한번에 받아서 사용하기.
// $_GET, $_POST로 받아 온 값(배열)을 변수에 담아 놓고 사용할것임.
// 빈 배열로 선언해주고 시작.

$conn = null;
try {
	// DB 연동 함수
	if(!my_db_conn($conn)) {
		throw new Exception("DB Error : PDO Instance");
	}
	// 요청받은 Method가 무엇인지
	$http_method = $_SERVER["REQUEST_METHOD"];

	// Method가 GET일 경우
	if($http_method === "GET") {
		// 모든 파라미터에는 trim을 넣어서 앞뒤의 공백을 없애줘야함. (중간의 공백은 안 없어짐)
		$arr_get["create_id"] = isset($_GET["create_id"]) ? $_GET["create_id"] : "";
		$arr_get["page_flg"] = isset($_GET["page_flg"]) ? $_GET["page_flg"] : "";
	

		// print_r($arr_get);
		$result = db_select_boards_id($conn, $arr_get);
	
		// 예외 처리
		if($result === false) {
			throw new Exception("DB Error : Select id");
		}
	} else {
		// Method가 POST일 경우
		// 삭제페이지에서 받아온 POST값 변수에 담아줌
		$arr_post["create_id"] = isset($_POST["create_id"]) ? $_POST["create_id"] : "";
		$arr_post["page_flg"] = isset($_POST["page_flg"]) ? $_POST["page_flg"] : "";

		$conn->beginTransaction();

		if(!db_delete_boards_id($conn, $arr_post)) {
			throw new Exception("delete Error");
		}
		
		$conn->commit();

		// 삭제 후 돌아갈 페이지 구분
		if($arr_post["page_flg"] === "0") {
			header("Location: in-progress.php");
		} else if($arr_post["page_flg"] === "1") {
			header("Location: complete.php");
		}
		exit;
	}
} catch(Exception $e) {
	$conn->rollBack();
	echo $e->getMessage();
	exit;
}finally {
	db_destroy_conn($conn);
}
	

?>

<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="./css/delete.css">
	<title>Delete</title>
</head>

<body class="delete_body">
	<div class="abc">
		<table class="cancel_box" >
				<tr>
					<td>
						<?php echo $result[0]["c_name"]; ?>
					</td>
				</tr>
				<tr>
					<td>
						<p><?php if($result[0]["c_com_at"] === null) {
							echo "진행중인";
						 } else if($result[0]["c_com_at"] != null) {
							echo "완료된"; } ?> 페이지를 삭제하시겠습니까?</p>
					</td>
				</tr>
				<tr>
					<td>
					<!-- form태그 : 웹 페이지에서 사용자 입력을 수집하고 서버로 데이터를 제출하는데 사용됨 -->
					<form action="delete.php" method="post">
						<!-- GET으로 받아온 page_flg 값을 hidden으로 숨겨놓고 -->
						<input type="hidden" name="page_flg" value="<?php echo $arr_get["page_flg"] ?>">

						<!-- value 속성에 지정된 값이 POST로 제출됨 = GET으로 가져온 create_id값 -->
						<button class="btn2" type='submit' name="create_id" value="<?php echo $result[0]["create_id"] ?>">ㅇㅇ..</button>

						<!-- 취소 누르면 GET으로 받아온 page_flg로 페이지 구별해서 해당 페이지로 이동함. -->
						<a class="btn1" href="<?php if($arr_get["page_flg"] === "0") {
							echo "in-progress.php";
						} else if($arr_get["page_flg"] === "1") {
							echo "complete.php";
						} 
						?>">ㄴㄴ..</a>
					</form>
					</td>
				</tr>
		</table>
	</div>
</body>

</html>
