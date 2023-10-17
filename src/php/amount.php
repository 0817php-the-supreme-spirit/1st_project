<?php 

$user_days_percent = $user_days["daily_salary"] = 0;
$amount_used_percent = $amount_used["amount_used"] = 0;
$percent = 0;
$arr_err_msg = [];

	if(!db_conn($conn)) {
		//예외 처리 (PDO 제대로 연결안되면? 에러메세지 출력?)
		throw new Exception("DB Error : PDO Instance");
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

	$arr_param = [
		"date1" => $date
		,"date2" => $date
	];

	// 데이터 베이스에서 유저의 사용 금액을 조회하는 함수
	$user_data = db_select_user_table_all($conn, $arr_param);

	if($user_data === false) {
		throw new Exception("DB Error : db_select_user_table_all");
	}	

	
	if(count($user_data) == 0) {
		$arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "급여");
	}	

	if(count($arr_err_msg) === 0) {
		// 유저의 일일 급여의 0번 방에 있는 값을 넘겨줌
		$user_days = $user_data[0];

		//daily_selary에 있는 값을 다른 변수에 넘겨줌, 위와 아래는 통합 가능, 코드 리뷰를 위해 풀어서 정리 
		$user_days_percent = $user_days["daily_salary"];

		//유저의 사용 금액을 총합을 해당 변수로 넘김
		$amount_used_percent = $amount_used["amount_used"];

		// 사용 금액의 퍼센트를 구하는 계산식
		// $percent = ($amount_used_percent / $user_days_percent) * 100;

		// 실수가 아닌 정수로 값을 보기 위해 데이터타입 변환
		$percent = (int)$percent;
		
	}
	else {
		$user_days_percent = $user_days["daily_salary"] = 0;
		$amount_used_percent = $amount_used["amount_used"] = 0;
		$percent = 0;
	}

?>