<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/1st_project/src/"); //상수 설정, 웹서버 root패스 생성
define("ERROR_MSG_PARAM", "해당 값을 찾을 수 없습니다."); //상수 설정, 파라미터 에러 메세지 불러오기 
require_once(ROOT."lib/lib_db.php"); //db파일 불러오기

//db_conn($conn);

$arr_err_msg = [];//에러메세지 저장용

// TRY문 시작
try {
    //2. db connect
    //2-1. connection 함수 호출
    $conn=null; // PDO 객체 변수
    if(!db_conn($conn)) {
		//예외 처리 (PDO 제대로 연결안되면? 에러메세지 출력?)
        throw new Exception("DB Error : PDO Instance");
    }

    // METHOD 획득 >> 안넣으면 어떻게되지? 서버의 값을 아예 못받아오나?
    $http_method = $_SERVER["REQUEST_METHOD"];

	// detail page에서 get으로 출력될 때 삭제 버튼 클릭 시
    if($http_method === "GET") {
		//파라미터에서 받아올 date, id의 값
		$date = isset($_GET["date"]) ? trim($_GET["date"]) : date('Y-m-d');
		//삼항연산자 사용, date값이 참이면 trim date를 반환, 거짓이면 현재 date를 반환
		//date는 빈값이 될수가 없으니까?되면안되니까?
        $id = isset($_GET["id"]) ? $_GET["id"] : "";
        $arr_err_msg = [];

        if($id === "") {
            $arr_err_msg[] = "Parameter Error : ID";
        }
		//여기서 나 date의 에러메세지는 없앴는데 이래도괜찮은건가?
		//지금은 괜찮으나 현업에선안댐..
        if(count($arr_err_msg) >= 1) {
		//에러메세지 수를 카운트하고 1보다 이상이 되면 밑에 실행
            throw new Exception(implode("<br>", $arr_err_msg));
			//에러메세지 출력할 때 한 배열에 출력하기 위해 (implode(): 배열에 속한 문자열을 한 문자열로 만드는 함수) 사용 
        }

        // 게시글 정보 획득
        $arr_param = [
            "id" => $id
        ];
		// 파라미터에 받아올 id값?
        $result = db_select_id($conn, $arr_param);
		// list페이지의 정보를 result에 담아주겠다

        // 예외처리 >> db_select_id함수를 못불러왔을때?
        if($result === false) {
            throw new Exception("DB Error : Select id");
			// 에러메세지로 얘를 보내주겠다
        } else if(!(count($result) === 1)) {
			//또는 함수의 결과 값이 1개가 아닐때(디테일페이지 표시는 1항목만 가능하니까)
            throw new Exception("DB Error : Select id Count");
			//에러메세지
        }
        $item = $result[0];
		// result[0]방에 담아준것을 item변수에 담겠다

		//date값 획득?
		$arr_param = [
			"date" => $date
		];
		//파라미터에 date값을 받아올거임

		$amount_used = db_select_amount_used($conn, $arr_param);
		// 유저 일일급여 조회 함수 변수에 담아주기
		if($amount_used === false) {
			throw new Exception("DB Error : select_user_table");
			//함수 가져오는데 실패했을때 이렇게 에러메세지 띄우겟따
		}
		$amount_used = isset($amount_used) ? $amount_used : "지출 없음";
		//(isset=it is set~~)유저 일일급여 함수가 셋팅되어있는지 확인하면서 참이 되면 함수를 그대로 갖고오고 거짓이 되면 지출없음
		$amount_used = $amount_used[0];
		//배열에 담아줌

    } else {
        //3-2. post일 경우 (삭제버튼 클릭시)
        //파라미터 id, date획득
        $id = isset($_POST["id"]) ? $_POST["id"] : "";
		$date = isset($_POST["date"]) ? trim($_POST["date"]) : date('Y-m-d');
        $arr_err_msg = [];
		//에러메세지 받아오기
        if($id === "") {
			//id값이 빈값이면
            $arr_err_msg[] = "Parameter Error : ID";
			//이렇게 에러메세지 띄우게따
        }

        if(count($arr_err_msg) >= 1) {
			//에러메세지가 하나 이상이 되면
            throw new Exception(implode("<br>", $arr_err_msg));
			//여러 메세지를 출력하기 위해서 정리해주기! implode함수를 사용하여.
        }

	
        //트랜젝션 시작
        $conn->beginTransaction();
		//트랜젝션: 연산작업의 단위 , 데이터베이스 변동?실행시 필요함. 그래서 insert,delete,update에서 사용하는것.
		//얘로 안묶어주면 데이터가 이상해짐.

        //게시글 정보 삭제
        $arr_param = [
            "id" => $id
        ];
		//삭제할 id값 받아오기
        $result = db_delete_date_id($conn, $arr_param);
		//delete함수 변수에 넣어주기

        //예외처리
        if(!$result) {
            throw new Exception("DB Error : Delete_date id");
			//함수 제대로 못받아오면 에러
        }
        $conn->commit();
		header("Location: /1st_project/src/php/list.php/?date={$date}");
        exit;
    }

	// 우측 사이드바 소비한 벨의 값 출력을 위해
	$user_data = db_select_user_table($conn);
	//user_table의 유저 일일급여 조회 함수 변수에 넣어놓기

	//예외처리
	if($user_data === false) {
		throw new Exception("DB Error : select_user_table");
	}

	$user_days = $user_data[0];
	//유저 일일급여의 0번방에 있는 값을 변수에 담기

	$user_days_percent = $user_days["daily_salary"];
	//daily_selary에 있는 값을 다른 변수에 넘겨줌

	$amount_used_percent = $amount_used["amount_used"];
	//유저 일일 사용금액을 변수에 넣어줌

	$percent = ($amount_used_percent / $user_days_percent) * 100;
	//

	$percent = (int)$percent;
	//실수로 나올테니까 정수로 바꿔줄 수 있는 int사용

} catch(Exception $e) {
    if($http_method === "POST") {
        $conn->rollBack();
    }
    //echo $e->getMessage(); // 에러메세지 출력
    //header("Location: error.php/?err_msg={$e->getMessage()}");
    exit; // 처리종료
} finally {
    db_destroy_conn($conn);
}


?>


<!DOCTYPE html>
<html lang="ko">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="/1st_project/src/css/delete/style.css">
		<title>삭제 페이지</title>
	</head>

	<body>

		<main>
			<div class="header">
				<a href="/1st_project/src/php/list.php"><h1>: 아껴봐요 절약의 숲</h1></a>
			</div>

			<div class="side-left">
				<div class="side-left-box">
					<form action="/1st_project/src/php/list.php" method="post">
							<!-- <input class="date-box" type="date" required value={props.date} onChange={props.changeHandler}> -->
							<label class="date-label">
								<input type="hidden" name="date" value="<?php echo $date; ?>">
								<input class="date-box" type="date" id="date" name="date" value="<?php echo $date;  ?>">
								<button class="date-btn" type="sibmit"><img src="/1st_project/src/img/date.png" alt=""></button>
							</label>
					</form>

					<div class="side-left-line-1"></div>

					<a href="/1st_project/src/php/list.php"><div class="side-left-page side-left-off"><p>오늘의 지출</p></div></a>
					<a href="/1st_project/src/php/insert.php"><div class="side-left-page side-left-off"><p>지출 작성부</p></div></a>
					<a href=""><div class="side-left-page side-left-off"><p>지출 통계서</p></div></a>

					<div class="side-left-line-2"></div>

					<!-- <form action="" method="post">
						<input type="radio" name="category" id="category1">
						<label for="category1" class="category-box">전체 비용</label>
				
						<input type="radio" name="category" id="category2">
						<label for="category2" class="category-box">생활 비용</label>
				
						<input type="radio" name="category" id="category3">
						<label for="category3" class="category-box">활동 비용</label>
				
						<input type="radio" name="category" id="category4">
						<label for="category4" class="category-box">멍청 비용</label>
					</form> -->

					<div class="img-left-box">
						<p class="font-left-box">삭제중..</p>
					</div>

				</div>
			</div>

			<div class="content">
				<div class="content-box">
					
						<div class="box1">
							<h1>리스트를 <span>삭제</span>하시겠습니까?</h1>
							<p>삭제 후 복구할 수 없습니다.</p>
						</div>
					<br>	
						<div class="box2">
							<h1 class="box2-1"><span><?php if($item["category_name"] == 'life') { ?>
									<p>생활 비용</p>
								<?php } else if($item["category_name"] == 'activity') { ?>
									<p>활동 비용</p>
								<?php }  else { ?>
									<p>멍청 비용</p>
								<?php } ?></span>
							</h1> <h1 class="box2-2"><?php echo $item["create_date"]?></h1>
						</div>
					<br>	
						<div class="box3">
							<p class="box3-1">제목</p>
							<p class="box3-2"><?php echo $item["title"]?></p>
						</div>
					<br>
						<div class="box4">
							<p class="box4-1">메모</p>
							<p class="box4-2"><?php echo $item["memo"]?></p>
						</div>
					<br>
					<br>
						<div class="box5">
							<span class="box5-1">일일 사용 금액 : <?php echo $item["amount_used"]?></span> <span class="box5-2">일일 잔여 금액 : </span>
						</div>
					
				<br>
				<br>
					<div class="box6">
						<form action="/1st_project/src/php/delete.php" method="post">
							<input type="hidden" name="id" value="<?php echo $id; ?>">
							<input type="hidden" name="date" value="<?php echo $date; ?>">
							<button type="submit" class="box6-1">삭제</button>
							<a href="/1st_project/src/php/list.php/?id=<?php echo $id; ?>&date=<?php echo $date; ?>" class="box6-2">취소</a>
						</form>
					</div>
				</div>
			</div>

			<div class="side-right">
				<div class="side-right-box">
					
					<div class="side-right-top">
						<?php if($percent >= 0 && $percent < 80) { ?>
							<p class="success">성 공!</p>
						<?php } else if($percent >= 80 && $percent < 99) { ?>
							<p class="danger">위 험!</p>
						<?php } else { ?>
							<p class="failure">실 패!</p>
						<?php } ?>
					</div>
					<div class="side-right-character">
						<?php if($percent >= 0 && $percent < 20) { ?>
							<div class="side-right-character-1"></div>
						<?php } else if($percent >= 20 && $percent < 40) { ?>
							<div class="side-right-character-2"></div>
						<?php } else if($percent >= 40 && $percent < 60) { ?>
							<div class="side-right-character-3"></div>
						<?php } else if($percent >= 60 && $percent < 80) { ?>
							<div class="side-right-character-4"></div>
						<?php } else if($percent >= 80 && $percent < 100) { ?>
							<div class="side-right-character-5"></div>
						<?php } else if($percent > 100) { ?>
							<div class="side-right-character-6"></div>
						<?php } ?>
					</div>
					<div class="side-right-bottom">
						<p>소비한 벨</p>
						<progress id="progress" value="<?php echo $amount_used["amount_used"]; ?>" min="0" max="<?php echo $user_days["daily_salary"]; ?>"></progress>
						<div class="side-right-user">
							<p class="small">사용 벨 : <?php if($amount_used["amount_used"] == 0) { echo 0; } else { echo $amount_used["amount_used"]; }?>원</p>
							<p class="small p_gpa">남은 벨 : <?php echo $user_days["daily_salary"] - $amount_used["amount_used"]; ?>원</p>
							<div class="bar"></div>
							<p class="small p_gpa all">전체 벨 : <?php echo $user_days["daily_salary"]; ?>원</p>
						</div>
					</div>

				</div>
			</div>
		</main>
		
	</body>
</html>