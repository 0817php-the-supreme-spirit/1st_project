<?php
		define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/todolist/src/");
		define("IMGPATH", "/todolist/doc/img/");
		require_once(ROOT."lib/lib_db.php");
		
		try {
			// DB Connect
			// connection 함수 호출
			$conn = null;
			if(!db_conn($conn)) {
				// 예외 처리
				throw new Exception("DB Error : PDO Instance");
			}
	
			// Method 가 get post인지 구분
			$http_method = $_SERVER["REQUEST_METHOD"];
	
			if($http_method === "GET") {
				//  GET 일 경우
				//  파라미터에서 id 획득
				$id = isset($_GET["id"]) ? $_GET["id"] : "";
				$page = isset($_GET["page"]) ? $_GET["page"] : "";
				$arr_err_msg = [];
				if($id === "") {
				 $arr_err_msg[] = "Parameter Error : id";
				}
				if($page === "") {
					$arr_err_msg[] =  "Parameter Error : page";
				}
				if(count($arr_err_msg) >= 1) {
					throw new Exception(implode("<br>", $arr_err_msg));
				}
			
	
				// 게시글 정보 획득
				$arr_param = [
					"id" => $id
				];


				$result = db_select_boards_id($conn, $arr_param);
	
				// 예외 처리
				if($result === false) {
					throw new Exception("DB Error : Select id");
				} else if(!(count($result) === 1)) {
					throw new  Exception("DB Error : Select id count");
				}
				// 날짜 출력
				$item = $result[0];
				// explode 문자열을 분할하여 배열로 저장하는 함수
				$arr = explode('-', $item['create_at']);
				$print_date = $arr[0]."년 ".$arr[1]."월 ".$arr[2]."일";
	
			} else {
				// POST일 경우
				$id = isset($_POST["id"]) ? $_POST["id"] : "";
				$arr_err_msg = [];
				if($id === "") {
				 $arr_err_msg[] = "Parameter Error : id";
				}
			// Transaction 시작
			$conn->beginTransaction();
			
			// 게시글 정보 삭제
			$arr_param = [
				"id" => $id
			];
			
			// 예외 처리
			if(!db_delete_boards_id($conn, $arr_param)) {
				throw new Exception("DB Error : Delete Boards id");
			}
			$conn->commit(); // commit
			// 삭제후 리스트 페이지로 이동
			header("Location: 01_list.php");
			exit;
		
		 }
		} catch(Exception $e) {
			// POST일 경우에만 rollback
			if($http_method === "POST") {
				$conn->rollBack();
			}
			echo $e->getMessage(); // 에러 메세지 출력
			exit; // 처리종료
		} finally {
				db_destroy_conn($conn);
		}
		// W(요일을 숫자 형태로 변환) strtotime 사람이 인식하는날짜를 컴퓨터가 인식하는 날짜로 바꿔주는 함수.
		$yoil = array("일요일","월요일","화요일","수요일","목요일","금요일","토요일");
		$item_yoil=$yoil[date('w', strtotime($item['create_at']))];
?>

<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>삭제 페이지</title>
	<link rel="stylesheet" href="/todolist/src/css/common.css">
</head>
<body>
	<div class="top_container">
	</div>
	<form action="/todolist/src/05_delete.php" method="post">	
		<div class="main_container">
			<div class="main_container_box">
				<div class="left_box">
					<div class="box_layout">
						<div class="layout_delete_top">
							<img src="/todolist/doc/img/heart.png">
						</div>
						<div class="align_center layout_delete_middle">
							<p class="align_center_txt_del">삭제하면 영구적으로 복구 할 수 없어요.</p>
							<br>
							<p class="align_center_txt_red">정말로 삭제하시나요?</p>
							<br>
							<p class="align_center_txt_del">삭제할 내용을 한 번 더 확인해 주세요!</p>
						</div>
						<div class="layout_delete_bottom">
							<img src="/todolist/doc/img/smile.png">
						</div>
					</div>
				</div>					
				<div class="right_box">
					<div class="box_layout">
					<div class="align_center date">
							<img class="detail_emo" src="<?php echo IMGPATH.$item['em_path']; ?>">
							<p class="align_center_date"><?php echo $print_date; ?>
							<p class="align_center_date"><?php echo $item_yoil ?></p>
							<br>
							</p>
						</div>
						<br>					
						<table class ="detail_delete_table">
							<thead>
								<tr>
									<td class ="delete_textarea_1">
										<?php echo $item["title"]; ?>
									</td>
								</tr>
							</thead>
							<tr>
								<td class ="delete_textarea_3">
								</td>
							</tr>
							<tr>
								<td class ="delete_textarea_2">
								<?php echo str_replace("\r\n", "<br>", $item["content"]); ?>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="side_box">
					<div class="side_category bgc_cate1">
						<input type="hidden" name="id" value="<?php echo $id; ?>">
						<button class= "side_text button_text" type="submit">삭제</button>
					</div>
					<div class="side_category bgc_cate3">
						<a class= "side_text" href="/todolist/src/02_detail.php/?id=<?php echo $id ?>&page=<?php echo $page ?>">취소</a>
					</div>
				</div>
			</div>
		</div>
	</form>
</body>
</html>