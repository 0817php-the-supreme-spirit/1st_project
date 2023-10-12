<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/1st_project/src/");
require_once(ROOT."lib/update_lib_db.php");

$conn = null;
db_conn($conn);
$http_method = $_SERVER["REQUEST_METHOD"];

$date = date('Y-m-d');

try{

	if ($http_method === "GET") {
		$id = isset($_GET["id"]) ? trim($_GET["id"]) : $_POST["id"];

	}
	else {
	$id = isset($_POST["id"]) ? $_POST["id"] : "";
	
	$title = $_POST["title"];
	$memo = $_POST["memo"];
	$amount_used = $_POST["amount_used"];
	$create_date = $_POST["create_date"];
	$category_id = $_POST["category_id"];

	$arr_param = [
		"title" => $title
		,"memo" => $memo
		,"amount_used" => $amount_used
		,"create_date" => $create_date
		,"category_id" => $category_id
		,"id" => $id
	];

	$conn->beginTransaction();

	if(!update_execute($conn, $arr_param)){
		throw new Exception("DB Error : Update_boards_id");
	}
	$conn->commit();

	header("Location: /1st_project/src/php/datail.php/?id={$id}"); //업데이트 완료 후 디테일 페이지로 이동
	exit;
	}
	//업데이트 완료한거 불러오기

	$arr_param_id = [
		"id" => $id
	];

	$result = select_change_detail( $conn, $arr_param_id );

		//게시글 조회 예외처리
		if($result === false){
			throw new Exception("DB Error : PDO Select_id");
		}
		
	$item = $result[0];

} catch(Exception $e) {
	if($http_method === "POST") {
	$conn->rollBack();
	}
	echo $e->getmessage();
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
					<form action="list.html/?date=" method="POST">
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
								<label for="update-title">제목</label>
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
							<a href="/1st_project/src/php/datail.php/?id=<?php echo $id; ?>">수정취소</a>
						</div>
					</form>
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