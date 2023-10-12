<?php
	// ----------------------------
	// 함수명 	: db_conn
	// 기능 	: DB Connecy
	// 파라미터 : PDO &$conn
	// 리턴 	: 없음
	// ----------------------------

	function db_conn( &$conn )
	{
		$db_host 	= "192.168.0.85"; //host | 127.0.0.1 = localhost 
		$db_user 	= "team2"; // user
		$db_pw 		= "team2"; // password
		$db_name 	= "1st_project"; // DB name
		$db_charset = "utf8mb4"; // charset
		$db_dns		= "mysql:host=".$db_host.";dbname=".$db_name.";charset=".$db_charset;
	
		try
		{
			$db_options = [
			PDO::ATTR_EMULATE_PREPARES		=> false
			,PDO::ATTR_ERRMODE 				=> PDO::ERRMODE_EXCEPTION
			,PDO::ATTR_DEFAULT_FETCH_MODE 	=> PDO::FETCH_ASSOC
			];
		
			$conn = new PDO($db_dns, $db_user, $db_pw, $db_options);
			return true;
		}
		catch (Exception $e)
		{
			$conn = null;
			return false;
		}
	}

	// ----------------------------
	// 함수명 	: db_destroy_conn
	// 기능 	: DB Destoroy
	// 파라미터 : PDO &$conn
	// 리턴 	: 없음
	// ----------------------------

	function db_destroy_conn(&$conn)
	{
		$conn = null;
	}


// -------------------------------------------------------------
// list.php

	// ----------------------------
	// 함수명 	: db_select
	// 기능 	: 1st_project 게시물 조회
	// 파라미터 : PDO 		&$conn
	// 			: Array 	&$arr_param | 쿼리 작성용 배열
	// 리턴 	: Array / false
	// ----------------------------

	function db_select(&$conn, &$arr_param) {
			$sql = 
				" SELECT "
				." 		todo.id "
				."		,cate.category_name "
				."		,todo.title "
				."		,todo.amount_used "
				." FROM "
				."		todolist_table todo "
				." JOIN "
				." category_table cate "
				." ON "
				." todo.category_id = cate.category_id "
				." WHERE "
				." todo.create_date = :date "
				;

		$arr_ps = [
			":date" => $arr_param["date"]
		];
		
		try {
			$stmt = $conn->prepare($sql);
			$stmt->execute($arr_ps);
			$result = $stmt->fetchAll();
			return $result; // 결과 리턴
		}
		catch(Exception $e) {
			return false; // 예외 발생 : flase 리턴
		}
	}

// 	// ----------------------------
// 	// 함수명 	: db_select_date
// 	// 기능 	: 1st_project 해당 날짜 게시물 조회
// 	// 파라미터 : PDO 		&$conn
// 	// 			: Array 	&$arr_param | 쿼리 작성용 배열
// 	// 리턴 	: Array / false
// 	// ----------------------------

// 	function db_select_date(&$conn, &$arr_param) {
// 			$sql = 
// 				" SELECT "
// 				." 		todo.id "
// 				."		,cate.category_name "
// 				."		,todo.title "
// 				."		,todo.amount_used "
// 				." FROM "
// 				."		todolist_table todo "
// 				." JOIN "
// 				." category_table cate "
// 				." ON "
// 				." todo.category_id = cate.category_id "
// 				." WHERE "
// 				." todo.create_date = :date "
// 				;

// 			$arr_ps = [
// 				":date" => $arr_param["date"]
// 			];
// 		try {
// 			$stmt = $conn->prepare($sql);
// 			$stmt->execute($arr_ps);
// 			$result = $stmt->fetchAll();
// 			return $result; // 결과 리턴
// 		}
// 		catch(Exception $e) {
// 			return false; // 예외 발생 : flase 리턴
// 		}
// 	}
	
// 	// ----------------------------
// 	// 함수명 	: db_select_category
// 	// 기능 	: 1st_project 해당 카테고리 게시물 조회
// 	// 파라미터 : PDO 		&$conn
// 	// 			: Array 	&$arr_param | 쿼리 작성용 배열
// 	// 리턴 	: Array / false
// 	// ----------------------------

// 	function db_select_category(&$conn, &$arr_param) {
// 		$sql = 
// 			" SELECT "
// 			." 		todo.id "
// 			."		,cate.category_name "
// 			."		,todo.title "
// 			."		,todo.amount_used "
// 			." FROM "
// 			."		todolist_table todo "
// 			." JOIN "
// 			." category_table cate "
// 			." ON "
// 			." todo.category_id = cate.category_id "
// 			." WHERE "
// 			." cate.category_name = :category "
// 			;

// 		$arr_ps = [
// 			":category" => $arr_param["category"]
// 		];
// 	try {
// 		$stmt = $conn->prepare($sql);
// 		$stmt->execute($arr_ps);
// 		$result = $stmt->fetchAll();
// 		return $result; // 결과 리턴
// 	}
// 	catch(Exception $e) {
// 		return false; // 예외 발생 : flase 리턴
// 	}
// }

	// ----------------------------
	// 함수명 	: db_select_search
	// 기능 	: 1st_project 해당 날짜 게시물 조회
	// 파라미터 : PDO 		&$conn
	// 			: Array 	&$arr_param | 쿼리 작성용 배열
	// 리턴 	: Array / false
	// ----------------------------

	function db_select_search(&$conn, &$arr_param) {
		$sql = 
			" SELECT "
			." 		todo.id "
			."		,cate.category_name "
			."		,todo.title "
			."		,todo.amount_used "
			." FROM "
			."		todolist_table todo "
			." JOIN "
			." 		category_table cate "
			." ON "
			." 		todo.category_id = cate.category_id "
			." WHERE "
			." 		todo.create_date = :date "
			;

		$arr_ps = [
			":date" => $arr_param["date"]
		];

		if (!empty($arr_param["category"])) {
			$sql .= " AND cate.category_name = :category ";
			$arr_ps[":category"] = $arr_param["category"];
		}
		
	try {
		$stmt = $conn->prepare($sql);
		$stmt->execute($arr_ps);
		$result = $stmt->fetchAll();
		return $result; // 결과 리턴
	}
	catch(Exception $e) {
		echo $e->getMessage(); // 예외발생 메세지 출력
		return false; // 예외 발생 : flase 리턴
	}
}

// -------------------------------------------------------------


// -------------------------------------------------------------
// datal.php

	// ----------------------------
	// 함수명 	: db_select_id
	// 기능 	: list.php의 게시물 id값
	// 파라미터 : PDO 		&$conn
	// 			: Array 	&$arr_param | 쿼리 작성용 배열
	// 리턴 	: Array / false
	// ----------------------------
	
	function db_select_id(&$conn, &$arr_param) {
		$sql = 
			" SELECT "
			." 		todo.id "
			."		,cate.category_name "
			."		,todo.title "
			." 		,todo.memo "
			."		,todo.amount_used "
			." 		,todo.create_date "
			." FROM "
			."		todolist_table todo "
			." JOIN "
			." 		category_table cate "
			." ON "
			." 		todo.category_id = cate.category_id "
			." WHERE "
			." 		todo.id = :id "
			;

			$arr_ps = [
				":id" => $arr_param["id"]
			];
			
		try {
			$stmt = $conn->prepare($sql);
			$stmt->execute($arr_ps);
			$result = $stmt->fetchAll();
			return $result;
		}
		catch(Exception $e) {
			return false; // 예외 발생 : flase 리턴
		} 
	}

// -------------------------------------------------------------


// -------------------------------------------------------------
// main.php

	// ----------------------------
	// 함수명 	: db_user_salary_insert
	// 기능 	: 유저의 한달 급여 입력
	// 파라미터 : PDO 		&$conn
	// 			: Array 	&$arr_param | 쿼리 작성용 배열
	// 리턴 	: Array / false
	// ----------------------------

	function db_user_salary_insert(&$conn, &$arr_param) {
		$sql = 
			" INSERT INTO user_table ( "
			."		monthly_salary "
			."		,daily_salary "
			." ) "
			." VALUES ( "
			."		:monthly_salary "
			." 		,:daily_salary "
			." ) "
			;

		$arr_ps = [
			":monthly_salary" => $arr_param["monthly_salary"]
			,":daily_salary" => $arr_param["daily_salary"]
		];

		try {
			$stmt = $conn->prepare($sql);
			$result = $stmt->execute($arr_ps);
			return $result; // 결과 리턴
		}
		catch(Exception $e) {
			return false; // 예외 발생 : flase 리턴
		}
	}

	// ----------------------------
	// 함수명 	: db_user_salary_compare
	// 기능 	: 유저의 한달 주기 비교
	// 파라미터 : PDO 		&$conn
	// 			: Array 	&$arr_param | 쿼리 작성용 배열
	// 리턴 	: Array / false
	// ----------------------------

	function db_user_salary_date_compare(&$conn, &$arr_param) {
		$sql = 
			" SELECT "
			." 		input_date "
			." FROM "
			."		user_table "
			." WHERE "
			." 		input_date "
			." BETWEEN "
			." date_format(now(), '%Y-%m-01') "
			." AND "
			." date_format(now(), '%Y-%m-%d') "
			;

		try {
			$stmt = $conn->prepare($sql);
			$stmt->execute($arr_ps);
			$result = $stmt->fetchAll();
			return count($result);
		}
		catch(Exception $e) {
			return false; // 예외 발생 : flase 리턴
		} 
	}


	// ----------------------------
	// 함수명 	: db_user_salary_date_compare
	// 기능 	: 유저의 한달 주기 비교
	// 파라미터 : PDO 		&$conn
	// 			: Array 	&$arr_param | 쿼리 작성용 배열
	// 리턴 	: Array / false
	// ----------------------------

	function db_user_salary_compare(&$conn) {
		$sql = 
			" SELECT "
			." 		input_date "
			." FROM "
			."		user_table "
			." WHERE "
			." 		input_date "
			." BETWEEN "
			." date_format(now(), '%Y-%m-01') "
			." AND "
			." date_format(now(), '%Y-%m-%d') "
			;
			
		try {
			$stmt = $conn->query($sql);
			$result = $stmt->fetchAll();
			return count($result);
		}
		catch(Exception $e) {
			return false;
		} 
	}

// -------------------------------------------------------------
?>