<?php
define( "ROOT", $_SERVER["DOCUMENT_ROOT"]."/project1");
define( "FILE_HEADER", ROOT ."/header.php" );
define( "FILE_FOOTER", ROOT ."/footer.php" );
// define( "ERROR_MSG_PARAM", "오류입니다~" );
require_once( ROOT ."/lib_db.php" );


// var_dump($_FILES);
$target_dir = "img/"; // 아무것도 안적으면 htdocs에 바로 저장됨
$uploadOk = 1;
$http_method = $_SERVER["REQUEST_METHOD"];
if($http_method === "POST") {
		
		$conn = null; // DB Connection 변수

		//파라미터 획득
		$item_name = $_POST["item_name"];
		//$item_name = isset($_POST["item_name"]) ? trim($_POST["item_name"]) : ""; // item_name 셋팅
		$tag_id = isset($_POST["tag_id"]) ? trim($_POST["tag_id"]) : ""; // tag_id 셋팅
		$d_day = isset($_POST["d_day"]) ? trim($_POST["d_day"]) : ""; // finished_at 셋팅
		$img = isset($_FILES["img"]) ? $_FILES["img"]["name"] : ""; // img 셋팅
		// var_dump($img);
		$memo = isset($_POST["memo"]) ? trim($_POST["memo"]) : ""; // img 셋팅
		$amount = isset($_POST["amount"]) ? trim($_POST["amount"]) : ""; // img 셋팅
		$target_file = $target_dir.basename($_FILES["img"]["name"]); 
		$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

		// var_dump($item_name);
			PDO_set($conn);
			$conn->beginTransaction(); // 트랜잭션 시작

			// 글 작성을 위해 파라미터 셋팅
			$arr_param = [
				"item_name" => $item_name
				,"memo" => $memo
				,"tag_id" => $tag_id
				,"d_day" => $d_day
				,"img" => $img
				,"amount" => $amount
			];
// var_dump($arr_param); exit;

			// 이미지용 코드
			// $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			// print_r($check);
			// echo "<br>↑check<br><br>";
			// if($check !== false) {
			// echo "Type : " . $check["mime"] . "."."<br><br>";
			// $uploadOk = 1;
			// } else {
			// echo "Type : Not image"."<br><br>";
			// $uploadOk = 0;
			// }
			
			

			// if (file_exists($target_file)) {
			// 	echo "-중복"."<br><br>";
			// 	$uploadOk = 0;
			//   }
				// var_dump($imageFileType);
			  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
				&& $imageFileType != "gif" && $imageFileType != "") {
				$uploadOk = 0;
				} else {
					if($imageFileType != ""){
						move_uploaded_file($_FILES["img"]["tmp_name"], $target_file);
					} else {
						echo "업로드 안함";
					}

					if(!db_insert_boards($conn, $arr_param)){
						echo "헉 오류";
						// exit;
					}
					$conn->commit();
					header("Location: list.php");
					exit;
				}
		}

?>

<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="/project1/project_890.css">
	<title>작성 페이지</title>
</head>
<body>
	<?php
		require_once(FILE_HEADER);
	?>
	<main class="insert_main" id="container">
		<form action="/project1/insert.php" method="post" enctype="multipart/form-data">
			<div class="insert_ma">
					<div class="insert_div">
					<div class="insert_tit">제품명</div>
					<input class="insert_in2" type="text" name="item_name" maxlength="50" required>
					<div class="insert_te"></div>
				</div>
				<textarea class="insert_me" name="memo" id="memo" placeholder="메모"></textarea>
				<div class="insert_div">
					<div class="insert_tit">수량</div>
					<input class="insert_in2" type="number" name="amount" min="0" max="50" required>
					<div class="insert_te"></div>
				</div>
				<div class="insert_div">
					<div class="insert_tit">태그</div>
					<select class="insert_in2" name="tag_id" id="tag">
						<option value="0" selected>미선택</option>
						<option value="1">패션/뷰티</option>
						<option value="2">식품/생활</option>
						<option value="3">가구/홈/데코</option>
						<option value="4">가전/디지털</option>
						<option value="5">반려동물/취미</option>
					</select>
					<div class="insert_te"></div>
				</div>
				<div class="insert_div">
					<div class="insert_tit">기한</div>
					<input class="insert_in2" type="date" name="d_day" required>
					<div class="insert_te"></div>
				</div>
				<div class="insert_div">
					<label for="file">
						<div class="insert_tit">파일첨부</div>
					</label>
					<div class="insert_te"><input class="input_f" type="file" name="img" id="img"></div>
				</div>
				<?php
				if($uploadOk == 0){
					?>
					<p class="insert_uploadOk">이미지파일형식이 아닙니다</p>
				<?php
				}
				?>
			</div>
			<section class="insert_set">
				<button class="insert_se" id="insert_se" type="submit">작성</button>
				<a class="insert_se" href="/project1/list.php">취소</a>
			</section>
		</form>
	</main>
	<?php require_once(FILE_FOOTER); ?>
</body>
</html>