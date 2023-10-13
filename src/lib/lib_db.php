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
				." 		category_table cate "
				." ON "
				." 		todo.category_id = cate.category_id "
				." WHERE "
				." 		todo.create_date = :date "
				." AND "
				." 		todo.delete_date IS NULL ";
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
	// 기능 	: 1st_project 해당 날짜와 카테고리 게시물 조회
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
			." AND "
			." 		delete_date IS NULL ";
			;

		$arr_ps = [
			":date" => $arr_param["date"]
		];

		// : = 플레이스홀더(실제 값으로 대체될 위치, 해당 sql문이 실행 시에 결정된 값을 넣어줌) :date 배열의 키, $arr_param["date"]는 :date에 대응하는 값
		// 동적 쿼리문, lisg.php에서 카테고리 값이 넘어왔을 경우에  sql문에 해당 AND문을 추가하고 받은 카테고리 값을 arr_ps에 :categpry에 값을 넘김
		//(empty = 비어있을 경우에, 현재는 !empty기 때문에 값이 있을 경우)
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

// ----------------------------
	// 함수명 	: db_select_user_table
	// 기능 	: user_table 유저 일일 급여 조회
	// 파라미터 : PDO 		&$conn
	// 			: Array 	&$arr_param | 쿼리 작성용 배열
	// 리턴 	: Array / false
	// ----------------------------

	function db_select_user_table(&$conn) {
		$sql =
			" SELECT "
			." 		daily_salary "
			." FROM "
			."		user_table "
			." WHERE "
			." 		input_date "
			." BETWEEN "
			." 		date_format(now(), '%Y-%m-01') "
			." AND "
			." 		date_format(now(), '%Y-%m-%d') "
			;
			// 현재 달의 1일과 마지막일사이에 값이 있을 경우의 조건문
		try {
			$stmt = $conn->query($sql);
			$result = $stmt->fetchAll();
			return $result;
		}
		catch(Exception $e) {
			return false;
		} 
	}


	// ----------------------------
	// 함수명 	: db_select_amount_used
	// 기능 	: user_table 유저 일일 급여 조회
	// 파라미터 : PDO 		&$conn
	// 			: Array 	&$arr_param | 쿼리 작성용 배열
	// 리턴 	: Array / false
	// ----------------------------

	function db_select_amount_used(&$conn, &$arr_param) {
		$sql =
			" SELECT "
			." 		sum(amount_used) as amount_used "
			." FROM "
			."		todolist_table "
			." WHERE "
			." 		create_date = :date "
			." AND "
			." 		delete_date IS NULL ";
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
			return false;
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

// ----------------------------
// ******* insert lib. *************
	// 함수명 	: db_insert
	// 기능 	: boards 레코드 작성
	// 파라미터 : PDO 	&$conn
	//			Array	&$arr_param 쿼리 작성용 배열
	// 리턴 	: Boolean
	// ----------------------------

	function db_insert(&$conn, &$arr_param) {
		$sql =
			" INSERT INTO todolist_table ( "
			." title "
			." ,memo "
			." ,amount_used "
			." ,create_date "
			." ,category_id "
			." ) "
			." VALUES ( "
			." :title "
			." ,:memo "
			." ,:amount_used "
			." ,:create_date "
			." ,:category_id "
			." ) "
			;
		$arr_ps = [
			":title" => $arr_param["title"]
			,":memo" => $arr_param["memo"]
			,":amount_used" => $arr_param["amount_used"]
			,":create_date" => $arr_param["create_date"]
			,":category_id" => $arr_param["category_id"]
		];
		try {
			$stmt = $conn->prepare($sql);
			$result = $stmt->execute($arr_ps);
			return $result; //결과 리턴
		}catch(Exception $e) {
			return false;
		}
	}
// ******* insert lib. *************
// ----------------------------


// ----------------------------
// ******* delete lib. *************
//------------------------------------------
// 함수명   : db_delete_boards_id
// 기능     : 특정 id 레코드 삭제처리
// 파라미터 : PDO  &$conn
//            Array &$arr_param          
// 리턴     : boolean
// -----------------------------------------
function db_delete_date_id(&$conn, &$arr_param) {
    $sql =
    " UPDATE "
	." todolist_table "
    ." SET " 
    ." delete_date = now() "
    ." WHERE "
    ." id = :id "
    ;

    $arr_ps = [
        ":id" => $arr_param["id"]
    ];

    try {
        //쿼리 실행
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute($arr_ps);

        return $result;
    } catch(Exception $e) {
        echo $e->getMessage();
        return false;
    }
}
// ******* delete lib. *************
// ----------------------------



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
// ----------------------------
// ******* update lib. *************
	// ----------------------------
	// 함수명 	: db_user_salary_date_compare
	// 기능 	: 유저의 한달 주기 비교
	// 파라미터 : PDO 		&$conn
	// 			: Array 	&$arr_param | 쿼리 작성용 배열
	// 리턴 	: Array / false
	// ----------------------------

	function update_execute( &$conn, &$arr_param ){
		try{ $sql = " UPDATE "
			."			todolist_table "
			."		SET "
			."			title = :title "
			."			,memo = :memo "
			."			,amount_used = :amount_used "
			."			,create_date = :create_date "
			."			,modify_date = NOW() "
			."			,category_id = :category_id "
			."		WHERE "
			."			id = :id "
			;
		
			$arr_ps = [
				":title" => $arr_param["title"]
				,":memo" => $arr_param["memo"]
				,":amount_used" => $arr_param["amount_used"]
				,":create_date" => $arr_param["create_date"]
				,":category_id" => $arr_param["category_id"]
				,":id" => $arr_param["id"]
			];

			$stmt = $conn->prepare($sql);
			$result = $stmt->execute($arr_ps);
			return $result;
	}catch(Exception $e){
		echo $e->getMessage(); // Exception 메세지 출력
		return false;
	}
	
	}
	// ----------------------------
	// 함수명 	: db_user_salary_date_compare
	// 기능 	: 유저의 한달 주기 비교
	// 파라미터 : PDO 		&$conn
	// 			: Array 	&$arr_param | 쿼리 작성용 배열
	// 리턴 	: Array / false
	// ----------------------------

	function select_change_detail( &$conn, &$arr_param_id ){
		try{$sql = " SELECT "
		."			cate.category_name "
		."			,tod.title "
		."			,tod.memo "
		."			,tod.amount_used "
		."			,tod.create_date "
		."		FROM "
		."			todolist_table tod "
		."		JOIN "
		."			category_table cate "
		."		ON "
		."			cate.category_id = tod.category_id "
		."		WHERE "
		."			id = :id ";
	
		$arr_param = [
			":id" => $arr_param_id["id"]
		];
	
		$stmt = $conn->prepare($sql);
		$stmt->execute($arr_param);
		$result = $stmt->fetchAll();
		return $result;
	}catch(Exception $e){
		echo $e->getMessage(); // Exception 메세지 출력
		return false;
	}
	}
// ******* update lib. *************
// ----------------------------


// ----------------------------
// ******* total lib. *************


// ******* total lib. *************
// ----------------------------
?>