<?php 

	if(!db_conn($conn)) {
		//예외 처리 (PDO 제대로 연결안되면? 에러메세지 출력?)
		throw new Exception("DB Error : PDO Instance");
	}

	// $date = isset($_POST["date"]) ? trim($_POST["date"]) : date('Y-m-d');

	//일일 사용금액 계산을 위한 조건(날짜) 세팅
	$arr_param = [
		"date" => $date
	];

	$amount_used = db_select_amount_used($conn, $arr_param);
	if($amount_used === false) {
		throw new Exception("DB Error : select_user_table");
	}
	$amount_used = isset($amount_used) ? $amount_used : "지출 없음";
	
	$amount_used = $amount_used[0];
	
	// $user_data = db_select_user_table($conn);
	// if($user_data === false) {
	// 	throw new Exception("DB Error : select_user_table");
	// }

	$arr_err_msg = [];

	$arr_param = [
		"date1" => $date
		,"date2" => $date
	];

	// 데이터 베이스에서 유저의 사용 금액을 조회하는 함수
	$user_data = db_select_user_table_all($conn, $arr_param);

	if($user_data === false) {
		$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "data");
	}	

	
	if(count($user_data) == 0) {
		$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "data");
	}	

	if(count($arr_err_msg) === 0) {
		// 유저의 일일 급여의 0번 방에 있는 값을 넘겨줌
		$user_days = $user_data[0];

		//daily_selary에 있는 값을 다른 변수에 넘겨줌, 위와 아래는 통합 가능, 코드 리뷰를 위해 풀어서 정리 
		$user_days_percent = $user_days["daily_salary"];

		//유저의 사용 금액을 총합을 해당 변수로 넘김
		$amount_used_percent = $amount_used["amount_used"];

		// 사용 금액의 퍼센트를 구하는 계산식
		$percent = ($amount_used_percent / $user_days_percent) * 100;

		// 실수가 아닌 정수로 값을 보기 위해 데이터타입 변환
		$percent = (int)$percent;
	}
	else {

		// $user_data = db_select_user_table($conn);
		// if($user_data === false) {
		// 	throw new Exception("DB Error : select_user_table");
		// }

		// $user_days = $user_data[0];

		//daily_selary에 있는 값을 다른 변수에 넘겨줌, 위와 아래는 통합 가능, 코드 리뷰를 위해 풀어서 정리 
		$user_days_percent = $user_days["daily_salary"] = 0;

		//유저의 사용 금액을 총합을 해당 변수로 넘김
		$amount_used_percent = $amount_used["amount_used"] = 0;

		// 사용 금액의 퍼센트를 구하는 계산식
		// $percent = ($amount_used_percent / $user_days_percent) * 100;

		// 실수가 아닌 정수로 값을 보기 위해 데이터타입 변환
		$percent = 0;

	}



		// //유저의 사용 금액을 id값에 대응하는 금액으로 넣어줌
		// $amunt_used_days_percent = $item["amount_used"];

		// // 유저의 id값에 대응하는 사용 금액과 유저의 일일 사용 가능 금액을 퍼센트로 변환
		// $percent_days = ($amunt_used_days_percent / $user_days_percent) * 100;
		
		// // 실수가 아닌 정수로 값을 보기 위해 데이터타입 변환
		// $percent_days = (int)$percent_days;




		// // 유저의 일일 급여의 0번 방에 있는 값을 넘겨줌
		// $user_days = $user_data[0];

		// //daily_selary에 있는 값을 다른 변수에 넘겨줌, 위와 아래는 통합 가능, 코드 리뷰를 위해 풀어서 정리 
		// $user_days_percent = $user_days["daily_salary"];

		// //유저의 사용 금액을 총합을 해당 변수로 넘김
		// $amount_used_percent = $amount_used["amount_used"];

		// // 사용 금액의 퍼센트를 구하는 계산식
		// $percent = ($amount_used_percent / $user_days_percent) * 100;

		// // 실수가 아닌 정수로 값을 보기 위해 데이터타입 변환
		// $percent = (int)$percent;

?>