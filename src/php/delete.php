<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/1st_project/src/"); //웹 서버
define("ERROR_MSG_PARAM", "해당 값을 찾을 수 없습니다.");
require_once(ROOT."lib/lib_db.php");

//db_conn($conn);

$arr_err_msg = [];

try {
    //2. db connect
    //2-1. connection 함수 호출
    $conn=null; // PDO 객체 변수
    if(!db_conn($conn)) {
        throw new Exception("DB Error : PDO Instance");
    }

    // METHOD 획득?
    $http_method = $_SERVER["REQUEST_METHOD"];

    if($http_method === "GET") {
		$date = isset($_GET["date"]) ? trim($_GET["date"]) : date('Y-m-d');
        $id = isset($_GET["id"]) ? $_GET["id"] : "";
        $arr_err_msg = [];
        if($id === "") {
            $arr_err_msg[] = "Parameter Error : ID";
        }
        if(count($arr_err_msg) >= 1) {
            throw new Exception(implode("<br>", $arr_err_msg));
        }

        // 게시글 정보 획득
        $arr_param = [
            "id" => $id
        ];
        $result = db_select_id($conn, $arr_param);
        // 예외처리
        if($result === false) {
            throw new Exception("DB Error : Select id");
        } else if(!(count($result) === 1)) {
            throw new Exception("DB Error : Select id Count");
        }
        $item = $result[0];

		$arr_param = [
			"date" => $date
		];

		$amount_used = db_select_amount_used($conn, $arr_param);
		if($amount_used === false) {
			throw new Exception("DB Error : select_user_table");
		}
		$amount_used = isset($amount_used) ? $amount_used : "지출 없음";
		
		$amount_used = $amount_used[0];

    } else {
        //3-2. post일 경우
        //파라미터 id획득
        $id = isset($_POST["id"]) ? $_POST["id"] : "";
		$date = isset($_POST["date"]) ? trim($_POST["date"]) : date('Y-m-d');
        $arr_err_msg = [];
        if($id === "") {
            $arr_err_msg[] = "Parameter Error : ID";
        }

        if(count($arr_err_msg) >= 1) {
            throw new Exception(implode("<br>", $arr_err_msg));
        }

		$arr_param = [
			"date" => $date
		];

		$amount_used = db_select_amount_used($conn, $arr_param);
		if($amount_used === false) {
			throw new Exception("DB Error : select_user_table");
		}
		$amount_used = isset($amount_used) ? $amount_used : "지출 없음";
		
		$amount_used = $amount_used[0];

        //트랜젝션 시작
        $conn->beginTransaction();

        //게시글 정보 삭제
        $arr_param = [
            "id" => $id
        ];
        $result = db_delete_date_id($conn, $arr_param);

        //예외처리
        if(!$result) {
            throw new Exception("DB Error : Delete_date id");
        }
        $conn->commit();
		header("Location: /1st_project/src/php/list.php");
        exit;
    }
	$user_data = db_select_user_table($conn);
	if($user_data === false) {
		throw new Exception("DB Error : select_user_table");
	}

	$user_days = $user_data[0];

	$user_days_percent = $user_days["daily_salary"];

	$amount_used_percent = $amount_used["amount_used"];

	$percent = ($amount_used_percent / $user_days_percent) * 100;

	$percent = (int)$percent;
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
								<input class="date-box" type="date" id="date" name="date" value="<?php echo $date; ?>">
								<button class="date-btn" type="sibmit"><img src="/1st_project/src/img/date.png" alt=""></button>
							</label>
					</form>

					<div class="side-left-line-1"></div>

					<a href=""><div class="side-left-page side-left-on"><p>오늘의 지출</p></div></a>
					<a href=""><div class="side-left-page side-left-off"><p>지출 작성부</p></div></a>
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
							<h1 class="box2-1"><?php echo $item["category_name"]?></h1> <h1 class="box2-2"><?php echo $item["create_date"]?></h1>
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
							<button type="submit" class="box6-1">삭제</button>
							<a href="/1st_project/src/php/list.php/?id=<?php echo $id; ?>" class="box6-2">취소</a>
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
						<p>사용 금액 : <?php if($amount_used["amount_used"] == 0) { echo 0; } else { echo $amount_used["amount_used"]; }?>원</p>
						<p class="p_gpa">남은 금액 : <?php echo $user_days["daily_salary"]; ?>원</p>
					</div>

				</div>
			</div>
		</main>
		
	</body>
</html>