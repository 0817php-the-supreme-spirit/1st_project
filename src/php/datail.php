<?php
	define("ROOT",$_SERVER["DOCUMENT_ROOT"]."/1st_project/src/");
	require_once(ROOT."lib/lib_db.php");
	define("ERROR_MSG_PARAM", "해당 값을 찾을 수 없습니다.");

	$conn = null;
	$http_method = $_SERVER["REQUEST_METHOD"];
	$arr_err_msg = []; // 에러 메세지 저장용

	try {
		if(!db_conn($conn))
		{
			//강제 예외 발생 : DB Instance
			throw new Exception("DB Error : PDO Instance");
		}
		if($http_method === "GET") {
			$date = isset($_GET["date"]) ? trim($_GET["date"]) : date('Y-m-d');
			$id = isset($_GET["id"]) ? $_GET["id"] : "";

			if($id === "" ) {
				$arr_err_msg[] = "Parameter Error : id";
			}

			if($date === "") {
                $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "date");
            }

			if(count($arr_err_msg) === 0) {
				$arr_param = [
					"date" => $date
				];

				$result = db_select($conn, $arr_param);

				if(!$result)
				{
					throw new Exception("DB Error : SELECT boards");
				}
				$arr_param = [
					"id" => $id
				];
		
				$result = db_select_id($conn, $arr_param);
		
				if($result === false) {
					throw new Exception("DB Error : Select id");
				}
		
				else if(!(count($result) === 1)) {
					throw new Exception("DB Error : Select id Count");
				}
				$item = $result[0];
			}
		}
		else {
			$date = isset($_POST["date"]) ? trim($_POST["date"]) : date('Y-m-d');

			$life = isset($_POST["life"]) ? trim($_POST["life"]) : "";
			$activity = isset($_POST["activity"]) ? trim($_POST["activity"]) : "";
			$stupid = isset($_POST["stupid"]) ? trim($_POST["stupid"]) : "";

			$category = [];

			if($date === "") {
                $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "date");
            }

			if(isset($_POST["category"])) {
				$category = $_POST["category"];
			}

			if(count($arr_err_msg) === 0) {
				$arr_param = [
					"date" => $date
					,"category" => $category
				];

				$result = db_select_search($conn, $arr_param);
				
				if($result === false) {
					throw new Exception("DB Error : select_search");
				}
				else if(count($result) === 0) {
					$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "date");
					// throw new Exception("DB Error : select_date");
				}
			}
		}

	}
	catch(Exception $e) {
		echo $e->getMessage(); // 예외발생 메세지 출력
		exit; // 처리 종료
	}
	finally {
		db_destroy_conn($conn); // DB 파기
	}

?>

<!DOCTYPE html>
<html lang="ko">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="/1st_project/src/css/detail/style.css">
		<title>Document</title>
	</head>

	<body>

		<main>
			<div class="header">
				<a href=""><h1>: 아껴봐요 절약의 숲</h1></a>
			</div>

			<div class="side-left">
				<div class="side-left-box">
					<form action="/1st_project/src/php/list.php/?date=<?php echo $date; ?>" method="post">
							<!-- <input class="date-box" type="date" required value={props.date} onChange={props.changeHandler}> -->
							<label class="date-label">
								<input class="date-box" type="date" id="date" name="date" value="<?php echo $date; ?>">
								<button class="date-btn" type="sibmit"><img src="/1st_project/src/img/date.png" alt=""></button>
							</label>

					<div class="side-left-line-1"></div>

					<a href=""><div class="side-left-page side-left-on"><p>오늘의 지출</p></div></a>
					<a href=""><div class="side-left-page side-left-off"><p>지출 작성부</p></div></a>
					<a href=""><div class="side-left-page side-left-off"><p>지출 통계서</p></div></a>

					<div class="side-left-line-2"></div>

						<div class="category-all-box">
							<input type="radio" name="category" id="category1" value="">
							<!-- <label for="category1" class="category-box">전체 비용</label> -->
							<button class="category-box category-box-select" id="category" name="category">카테고리 선택</button>
						</div>
				
						<div class="category-all-box">
							<input type="radio" name="category" id="category2" value='life'>
							<!-- <label for="category2" class="category-box">생활 비용</label> -->
							<button class="category-box" id="category" name="category" value="life">생활 비용</button>
						</div>
				
						<div class="category-all-box">
							<input type="radio" name="category" id="category3" value='activity'>
							<!-- <label for="category3" class="category-box">활동 비용</label> -->
							<button class="category-box" id="category" name="category" value="activity">활동 비용</button>
						</div>
				
						<div class="category-all-box">
							<input type="radio" name="category" id="category4" value='stupid'>
							<!-- <label for="category4" class="category-box" >멍청 비용</label> -->
							<button class="category-box" id="category" name="category" value="stupid">멍청 비용</button>
						</div>
					</form>

				</div>
			</div>

			<div class="content">
				<?php 
					foreach($result as $item) {
				?>
					<div class="content-box">
						<div class="content-date-box">
							<span><?php echo $item["create_date"]; ?></span>
						</div>
						<div class="content-category-box">
							<span>카테고리</span>
						</div>

						<div class="content-title-box">
							<div class="content-title-box-1">
								<p>제목</p>
							</div>

							<div class="content-title-box-2">
								<p><?php echo $item["title"]; ?></p>
							</div>
						</div>

						<div class="content-memo-box">
							<div class="content-memo-box-1">
								<p>메모</p>
							</div>

							<div class="content-memo-box-2">
								<p><?php if($item["memo"] == 0) {
									echo "메모를 하지 않았어요";
									}
									else {
									echo $item["memo"]; 
									} ?></p>
							</div>
						</div>

						<div class="content-value-box">
							<div class="content-user-box">
								<div class="content-user-amount">
									<p>일일 남은 금액 : <?php echo $item["amount_used"]; ?></p>
								</div>
		
								<div class="content-user-remaining">
									<p>일일 남은 금액 : 20000</p>
								</div>
							</div>

							<div class="content-phrases-box">
								<p>파이어족이</p>
								<p>될꺼야?</p>
							</div>
						</div>
				<?php } ?>

					<div class="content-btn-box">
						<div class="content-btn-before">
							<a href="">이전</a>
						</div>
						<div class="content-btn-correction">
							<a href="/1st_project/src/php/update.php/?id=<?php echo $id; ?>">수정</a>
						</div>
						<div class="content-btn-delete">
							<a href="/1st_project/src/php/delete.php/?id=<?php echo $id; ?>">삭제</a>
						</div>

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