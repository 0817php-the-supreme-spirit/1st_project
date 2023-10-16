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
				throw new Exception("DB Error : PDO Instance");
			}

			if($http_method === "GET") {

				if(db_user_salary_compare($conn) === 1)
				{
					header("Location: list.php");
				}
			
			}

			else {
				$arr_post = $_POST;
				
				// 파라미터 획득
				$monthly_salary = isset($_POST["monthly_salary"]) ? trim($_POST["monthly_salary"]) : ""; // title 셋팅

				if($monthly_salary === "") {
					$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "한달 급여를 입력하지 않으셨습니다.");
				}

				$days = date('t')-date('d');

				$daily_salary = $monthly_salary / $days;

				$daily_salary = (int)$daily_salary;
				
				if(count($arr_err_msg) === 0) {

					if(!db_conn($conn))
					{
						throw new Exception("DB Error : PDO Instance");
					}
					$conn->beginTransaction();

					// 게시글 작성을 위헤 파라미터 셋팅
					$arr_param = [
						"monthly_salary" => $_POST["monthly_salary"]
						,"daily_salary" => $daily_salary
					];

					// insert
					if(!db_user_salary_insert($conn, $arr_param)) {
						// DB Insert 에러
						throw new Exception("DB Error : Insert Boards");
					}

					$conn->commit();

					// 리스트 페이지로 이동
					header("Location: list.php"); //Location을 콜론(:) 이후의 주소로 이동하라는 헤더 메시지이다
					exit;
				}	
			}
		}
		catch(Exception $e) {
			if($conn !== null) {
				$conn->rollBack();
			}
			// echo $e->getMessage(); // 예외발생 메세지 출력
			header("Location: /mini_board/src/error.php/?err_msg={$e->getMessage()}"); // 에러 메세지 error.php로 이동
			exit;
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
		<link rel="stylesheet" href="/1st_project/src/css/main/style.css">
		<title>아껴봐요 절약의 숲 메인 페이지</title>
	</head>

	<body>
		<div class="input-box">
			<div class="input-box-int">
				<form action="/1st_project/src/php/main.php" method="post">
					<input type="number" name="monthly_salary" id="monthly_salary" placeholder="한달 급여를 입력해주세요">

					<div class="start-btn">
						<button type="sibmit">START</button>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>