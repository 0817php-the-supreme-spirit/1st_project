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
			$date = isset($_GET["date"]) ? trim($_GET["date"]) : "";
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
				if($result === false)
				{
					throw new Exception("DB Error : SELECT boards");
				}
				//에러 없으면 id값 받아오고(여기도 id값 받아오는 이유는 함수에서 받아오니까!!)
				$arr_param = [
					"id" => $id
				];
		
				//함수 실행함
				$result = db_select_id($conn, $arr_param);
				
				if($result === false) { //또 실패하면 이 에러를 발생하게따!!
					throw new Exception("DB Error : Select id");
				}
				else if(!(count($result) === 1)) { //id값은 여러개 중복될 수 없으니까! 하나만 와야함.
					throw new Exception("DB Error : Select id Count");//이 함수는 리스트페이지의 게시물 id값
				}
				//함수의 첫번째 방에 있는 값을 변수에 담아준다
				$item = $result[0]; //이렇게 안하면 $result[0]["title"]이렇게 일일히 지정해 주어야하기 때문에 편의성을 위해 [0]번방을 $item에 담아준것.

			}
		}
		//post로 받을 경우
		else {
			$date = isset($_POST["date"]) ? trim($_POST["date"]) : date('Y-m-d');
			//date값이 없을 경우 에러메세지 내겠다
			if($date === "") {
                $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "date");
            }

			//카테고리를 포스트로 받아오면(지금페이지에선 카테고리가 필요없긴하나 동적쿼리사용 때문에 남겨둠)
			if(isset($_POST["category"])) {
				$category = $_POST["category"];
			}

			//에러메세지 카운트해서 0이면
			if(count($arr_err_msg) === 0) {
				
				//데이터 받아오고
				$arr_param = [
					"date" => $date
					,"category" => $category
				];

				$result = db_select_search($conn, $arr_param);
				//이함수에서 데이트와 카테고리값을 받아옴.동적 쿼리를 이용해서 카테고리값을 찾는  where문이 구동하지 않기 때문에 넣어줌
				
				// 왜 여기는 Exception을 사용한것인가!!>> 이부분은 개발자가 정해야함. 
				//사용자가 잘못해서 생기는 에러가 아닌 서버에서 지정된 값에 대한 오류는 보이지 않게 처리하기 위해
				if($result === false) {
					throw new Exception("DB Error : select_search");
				}
				else if(!count($result) === 1) {
					$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "date");
					// throw new Exception("DB Error : select_date");
				}
			}
		}
		require_once(ROOT."php/amount.php");//공통페이지 불러옴 >> 오른쪽 사이드바
	} catch(Exception $e) {
		echo $e->getMessage(); // 예외발생 메세지 출력
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
					<div class="content-date-box">
						<span><?php echo $item["create_date"]; ?></span>
					</div>
						<!-- 카테고리는 변수에 활동/생활/멍청 들어온 이름에 따라 출력됨 -->
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
							<!-- 변수에 담긴 제목 출력 -->
						</div>
					</div>
					<div class="content-memo-box">
						<div class="content-memo-box-1">
							<p>메모</p>
						</div>
						<div class="content-memo-box-2">
							<!-- 메모의 경우 빈값으로 남겨두었을때 문구출력과 입력값이 있을 때 출력값을 위해 if문 사용 -->
							<p><?php if($item["memo"] === "") {
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
								<!-- number_format함수가 1000단위로 점찍어줌.그래서 사용함 -->
								<p>일일 사용 금액 : <?php echo number_format($item["amount_used"]); ?>원</p>
							</div>
							<div class="content-user-remaining">
								<p>일일 남은 금액 : <?php echo number_format($user_days["daily_salary"] - $amount_used["amount_used"]); ?>원</p>
							</div>
						</div>
							<div class="content-phrases-box">
								<!-- if문사용하여 백분율에 따라 문구 출력해줌.ex:가위바위보게임,if문예제인 순위출력 -->
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