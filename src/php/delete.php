<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/1st_project/src/"); //상수 설정, 웹서버 root패스 생성
define("ERROR_MSG_PARAM", "해당 값을 찾을 수 없습니다."); //상수 설정, 파라미터 에러 메세지 불러오기 
require_once(ROOT."lib/lib_db.php"); //db파일 불러오기

//db_conn($conn);

$arr_err_msg = [];//추후 에러메세지를 받기위한 빈배열 세팅

// TRY문 시작
//기본적으로 예외처리 if-else문 만으로 가능함. 
//하지만 예외발생시 처리방법에 일관성(이말은, 에러메세지출력하는건가?)이 있으면 간결한 코드 작성가능.
try {
    //2. db connect
    //2-1. connection 함수 호출
    $conn=null; // PDO 객체 변수
    if(!db_conn($conn)) {
		//예외 처리 (PDO 제대로 연결안되면? 에러메세지 출력할거 던짐)
        throw new Exception("DB Error : PDO Instance");
    }

    // METHOD 획득 >> 슈퍼글로벌안의 변수는 건드리면 안되기 때문에 변수에 담아준다.(혹시 모를 경우)
    $http_method = $_SERVER["REQUEST_METHOD"];//REQUEST_METHOD == 요청방식 == 서버에게 네트워크로 어떤 명령을 보낼 때의 방식..?

	// detail page에서 get으로 출력될 때 삭제 버튼 클릭 시
    if($http_method === "GET") {
		//파라미터에서 받아올 date, id의 값
		$date = isset($_GET["date"]) ? trim($_GET["date"]) : "";
		//삼항연산자 사용, date값이 참이면 trim date를 반환, 거짓이면 현재 date를 반환
		//trim은 공백을 없애주고 값만 가지고 오게한다.
		//date는 빈값이 되면 안됨.
        $id = isset($_GET["id"]) ? $_GET["id"] : "";

        if($id === "") {
            $arr_err_msg[] = "Parameter Error : ID";
        }

		if($date === "") {
            $arr_err_msg[] = "Parameter Error : DATE";
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
		//여기서 가져오는것은 카테고리,날짜,제목,메모
		// list페이지의 정보를 result에 담아주겠다

        // 예외처리 >> db_select_id함수를 못불러왔을때?
        if($result === false) {
            throw new Exception("DB Error : Select id");
			// 에러메세지로 얘를 보내주겠다
        } else if(!(count($result) === 1)) {
			//또는 함수의 결과 값이 1개가 아닐때(디테일페이지 표시는 1항목만 가능하니까 조건걸어줌)
            throw new Exception("DB Error : Select id Count");
			//에러메세지
        }
        $item = $result[0];
		// result[0]방에 담아준것을 item변수에 담겠다

    } else {
        //3-2. post일 경우 (삭제버튼 클릭시)
        //파라미터 id, date획득
        $id = isset($_POST["id"]) ? $_POST["id"] : "";
		$date = isset($_POST["date"]) ? trim($_POST["date"]) : "";
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
			//implode:배열에 속한 문자열을 하나의 문자열로 만드는 함수
        }

	
        //트랜젝션 시작
        $conn->beginTransaction();
		//트랜젝션: 연산작업의 단위 , 데이터베이스 변동후 실행시 필요함. 그래서 insert,delete,update에서 사용하는것.
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
	require_once(ROOT."php/amount.php");

} catch(Exception $e) {
    if($http_method === "POST") {
        $conn->rollBack();
    }
    //echo $e->getMessage(); // 에러메세지 출력
	//에러페이지 없어서 안씀
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
		<link rel="stylesheet" href="/1st_project/src/css/side/style.css">
		<title>아껴봐요 절약의 숲 삭제 페이지</title>
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
								<input class="date-box" type="date" id="date" name="date" value="<?php echo $date;  ?>">
								<button class="date-btn" type="sibmit"><img src="/1st_project/src/img/date.png" alt=""></button>
							</label>
					</form>

					<div class="side-left-line-1"></div>

					<a href="/1st_project/src/php/list.php"><div class="side-left-page side-left-off"><p>오늘의 지출</p></div></a>
					<a href="/1st_project/src/php/insert.php"><div class="side-left-page side-left-off"><p>지출 작성부</p></div></a>
					<a href="/1st_project/src/php/total.php/?date=<?php echo $date; ?>"><div class="side-left-page side-left-off"><p>지출 통계서</p></div></a>

					<div class="side-left-line-2"></div>

					<div class="img-left-box">
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
							<span class="box5-1">일일 사용 금액 : <?php echo number_format($item["amount_used"])?></span> 
							<span class="box5-2">일일 잔여 금액 : <?php echo number_format($user_days["daily_salary"] - $item["amount_used"])?></span>
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
			<?php require_once(ROOT."php/side.php") ?>
		</main>
	</body>
</html>