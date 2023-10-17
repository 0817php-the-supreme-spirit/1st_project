<?php
	define("ROOT",$_SERVER["DOCUMENT_ROOT"]."/1st_project/src/");
	require_once(ROOT."lib/lib_db.php");
	define("ERROR_MSG_PARAM", "해당 값을 찾을 수 없습니다.");

	$conn = null;
	$http_method = $_SERVER["REQUEST_METHOD"];
	$arr_err_msg = []; // 에러 메세지 저장용

	//try/catch/finally문 프로그램이 실행되는 도중 발생하는 예외를 처리하기 위해 사용.
	try {
		if(!db_conn($conn))//db연결확인
		{
			//강제 예외 발생 : DB Instance
			//연결안되면 에러코드를 던져준다(throw). new(클래스불러오기),Exception()은 클래스임.
			throw new Exception("DB Error : PDO Instance");
		}
		//list페이지의 정보를 가지고 옴.
		if($http_method === "GET") {
			//확인해야할 값
			$date = isset($_GET["date"]) ? trim($_GET["date"]) : date('Y-m-d');
			$id = isset($_GET["id"]) ? $_GET["id"] : "";

			//값을 못 받아올 경우 에러메세지 발생
			if($id === "" ) {
				$arr_err_msg[] = "Parameter Error : id";
			}
			if($date === "") {
                $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "date");//sprintf:출력하는 함수같은건데 에러메세지 상수 설정해놈.
            }

			//에러메세지가 0이면 list페이지 갖고오기 실행!	
			if(count($arr_err_msg) === 0) {
				//에러메세지가 0개일 때 데이트 값을 받아오고 실행하자(date값을 받아오는 이유는 함수에서 그렇게 받아왔으니까!!)
				$arr_param = [
					"date" => $date
				];

				$result = db_select($conn, $arr_param);//게시물 조회 함수. 이 함수를 실행해야 list페이지의 정보를 받아 올 수 있음 

				//값을 못받아올 경우 에러처리! 함수도 확인해야함.
				if(!$result)
				{
					throw new Exception("DB Error : SELECT boards");
				}
				//에러 없으면 id값 받아오고(여기도 id값 받아오는 이유는 함수에서 받아오니까!!)
				$arr_param = [
					"id" => $id
				];
		
				//함수 실행함
				$result = db_select_id($conn, $arr_param);

				if(!(count($result) === 1)) { //id값은 여러개 중복될 수 없으니까! 하나만 와야함.
					throw new Exception("DB Error : Select id Count");//이 함수는 리스트페이지의 게시물 id값
				}
		
				else if($result === false) { //또 실패하면 이 에러를 발생하게따!!
					throw new Exception("DB Error : Select id");
				}

				//함수의 첫번째 방에 있는 값을 변수에 담아준다
				$item = $result[0]; //이렇게 안하면 $result[0]["title"]이렇게 일일히 지정해 주어야하기 때문에 편의성을 위해 [0]번방을 $item에 담아준것.

			}
		}
		//post로 받을 경우
		else {
			$date = isset($_POST["date"]) ? trim($_POST["date"]) : date('Y-m-d');

			//해당 구문은 값확인용으로 만들었고 사용하지 않아 주석처리함. 마지막 작업시 주석처리 내용 삭제예정
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

		require_once(ROOT."php/amount.php");

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
		<link rel="stylesheet" href="/1st_project/src/css/side/style.css">
		<title>아껴봐요 절약의 숲 상세 페이지</title>
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

					<a href=""><div class="side-left-page side-left-on"><p>오늘의 지출</p></div></a>
					<a href="/1st_project/src/php/insert.php"><div class="side-left-page side-left-off"><p>지출 작성부</p></div></a>
					<a href="/1st_project/src/php/total.php/?date=<?php echo $date; ?>"><div class="side-left-page side-left-off"><p>지출 통계서</p></div></a>

					<div class="side-left-line-2"></div>

					<div class="category-box"></div>
					</form>

				</div>
			</div>

			<div class="content">
				<div class="content-box">
					<?php 
						foreach($result as $item) {
					?>
						<div class="content-date-box">
							<span><?php echo $item["create_date"]; ?></span>
						</div>
						<div class="content-category-box">
							<span><?php if($item["category_name"] == 'life') { ?>
									<p>생활 비용</p>
								<?php } else if($item["category_name"] == 'activity') { ?>
									<p>활동 비용</p>
								<?php }  else { ?>
									<p>멍청 비용</p>
								<?php } ?></span>
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
									<p>일일 사용 금액 : <?php echo number_format($item["amount_used"]); ?>원</p>
								</div>
		
								<div class="content-user-remaining">
									<p>일일 남은 금액 : <?php echo number_format($user_days["daily_salary"] - $amount_used["amount_used"]); ?>원</p>
								</div>
							</div>

							<div class="content-phrases-box">
								<?php if($percent >= 0 && $percent< 20) {?>
									<p class="content-phrases-box-color">잘하고</p>
									<p class="content-phrases-box-color">있어</p>
								<?php } else if($percent >= 20 && $percent < 40) { ?>
									<p class="content-phrases-box-color">아직은</p>
									<p class="content-phrases-box-color">괜찮아</p>
								<?php } else if($percent >= 40 && $percent < 60) { ?>
									<p class="content-phrases-box-color">소비 액수가</p>
									<p class="content-phrases-box-color">좀 큰대?</p>
								<?php } else if($percent >= 60 && $percent < 80) { ?>
									<p class="content-phrases-box-failure">잔고</p>
									<p class="content-phrases-box-failure">감당 가능해?</p>
								<?php } else if($percent >= 80 && $percent < 99) { ?>
									<p class="content-phrases-box-failure">너 혹시</p>
									<p class="content-phrases-box-failure">제정신이야?</p>
								<?php } else { ?>
									<p class="content-phrases-box-failure">다음 달도</p>
									<p class="content-phrases-box-failure">텅장이다</p>
								<?php } ?>
							</div>
						</div>
					<?php } ?>

					<div class="content-btn-box">

							<a class="content-btn-before content-btn-box-hover" href="/1st_project/src/php/list.php/?date=<?php echo $date; ?>">이전</a>

							<a class="content-btn-correction content-btn-box-hover" href="/1st_project/src/php/update.php/?id=<?php echo $id; ?>&date=<?php echo $date; ?>">수정</a>

							<a class="content-btn-delete content-btn-box-hover" href="/1st_project/src/php/delete.php/?id=<?php echo $id; ?>&date=<?php echo $date; ?>">삭제</a>

					</div>
					
				</div>
			</div>

			<?php require_once(ROOT."php/side.php") ?>
		</main>
		
	</body>
</html>