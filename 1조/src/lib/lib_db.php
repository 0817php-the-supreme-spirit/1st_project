<?php

    // ******************************
    // 파일명 : lib_db.php
    // 기능 : DB 관련 함수
    // 버전 : v001 new Oh.sc 231012
    // ******************************

    // ------------------------
    // 함수명 : db_conn
    // 기능 : DB connect
    // 파라미터 : PDO  &$conn
    // 리턴 : boolean
    // ------------------------
	function db_conn( &$conn ) { //reference 파라미터 = (&) 변수 주소값을 넘겨 받는다.
		$db_host = "192.168.0.77"; // host
		$db_user = "team1"; //user
		$db_pw = "team1"; // password
		$db_name = "diarydb"; // DB name
		$db_charset = "utf8mb4"; // charset
		$db_dns = "mysql:host=".$db_host.";dbname=".$db_name.";charset=".$db_charset;
		
		try {
			$db_options = [
				// DB의 Prepared Statement 기능을 사용하도록 설정
				PDO::ATTR_EMULATE_PREPARES => false
				// PDO Exception을 Throws하도록 설정
				,PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
				// 연상배열로 Fetch를 하도록 설정
				,PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
			];
			//PDO Class로 DB연동
			$conn = new PDO($db_dns, $db_user, $db_pw, $db_options);
			return true;
			
		} catch (exception $e) {
			$conn = null;
			return false;
		}
	}

	// ------------------------
    // 함수명 : db_destroy_conn
    // 기능 : DB Destroy
    // 파라미터 : PDO  &$conn
    // 리턴 : 없음
    // ------------------------
    function db_destroy_conn( &$conn ) {
        $conn = null;
    }

	// ------------------------
    // 함수명 : db_select_boards_emo_rank
    // 기능 : 감정별 통계 순위 조회
    // 파라미터 : PDO 	 &$conn
    // 리턴 : Array / false
    // ------------------------
	function db_select_boards_emo_rank(&$conn) {
		try {
			$sql = 
				" SELECT "
				."		emo.em_name "
				."		,emo.em_path "
				."		,COUNT(dia.em_id) AS cnt_em_id "
				." FROM "
				." 		diary AS dia "
				."		JOIN emotion AS emo "
				."			ON dia.em_id = emo.em_id "
				." WHERE "
				."		dia.delete_flag = '0' "
				." GROUP BY "
				."		dia.em_id "
				." ORDER BY "
				."		cnt_em_id DESC "
				." LIMIT 5 "
			;
			
			$stmt = $conn->query($sql);
			$result = $stmt->fetchAll();

			return $result;
		} catch (Exception $e) {
			echo $e->getMessage();
			return false;
		}
	}

	// ------------------------
    // 함수명 : db_select_boards_paging
    // 기능 : list paging 조회
    // 파라미터 : PDO 	 &$conn
	//			 Array	&$arr_param 쿼리 작성용 배열
    // 리턴 : Array / false
    // ------------------------
	function db_select_boards_paging(&$conn, &$arr_param) {
		try {
			$sql = 
				" SELECT "
				."		dia.id "
				."		,dia.title "
				."		,cast(dia.create_at as date) as create_at "
				."		,emo.em_path "
				." FROM "
				." 		diary AS dia "
				."		JOIN emotion AS emo "
				."			ON dia.em_id = emo.em_id "
				." WHERE "
				."		dia.delete_flag = '0' "
				." ORDER BY "
				."		dia.id DESC "
				." LIMIT :list_cnt OFFSET :offset "
			;

			$arr_ps = [
				":list_cnt" => $arr_param["list_cnt"]
				,":offset" => $arr_param["offset"]
			];

			$stmt = $conn->prepare($sql);
			$stmt->execute($arr_ps);
			$result = $stmt->fetchAll();
			return $result;
		} catch (exception $e) {
			echo $e->getMessage();
			return false;
		}
	}

	// ------------------------
    // 함수명 : db_select_boards_cnt
    // 기능 : boards count 조회
    // 파라미터 : PDO  &$conn
    // 리턴 : int / false
    // ------------------------
	function db_select_boards_cnt(&$conn) {
		try {
			$sql = 
				" SELECT "
				."		COUNT(id) as cnt "
				." FROM "
				." 		diary "
				." WHERE "
				." 		delete_flag = '0' "
			;

			$stmt = $conn->query($sql);
			$result = $stmt->fetchAll();
			return (int)$result[0]["cnt"];
		} catch (exception $e) {
			echo $e->getMessage();
			return false;
		}
	}

	// ------------------------
    // 함수명 : db_select_boards_id
    // 기능 : id를 통한 제목, 내용 출력
    // 파라미터 : PDO 	 &$conn
	//			Array	&$arr_param
    // 리턴 : Array / False
    // ------------------------
	function db_select_boards_id(&$conn, &$arr_param) {
		try {
			$sql = 
				" SELECT "
				."		dia.id "
				."		,dia.title "
				."		,dia.content "
				."		,cast(dia.create_at as date) as create_at "
				."		,emo.em_path "
				."		,emo.em_comment "
				." FROM "
				." 		diary AS dia "
				."		JOIN emotion AS emo "
				."			ON dia.em_id = emo.em_id "
				." WHERE "
				."		dia.id = :id "
				." 		AND "
				." 		dia.delete_flag = '0' "
			;

			$arr_ps = [
				":id" => $arr_param["id"]
			];

			$stmt = $conn->prepare($sql);
			$stmt->execute($arr_ps);
			$result = $stmt->fetchAll();
			return $result;
		} catch (Exception $e) {
			echo $e->getMessage();
			return false;
		}
	}

	// ------------------------
    // 함수명 : db_select_boards_now
    // 기능 : 현재 날짜 출력
    // 파라미터 : PDO 	 &$conn
    // 리턴 : String / False
    // ------------------------

	function db_select_boards_now(&$conn) {
		try {
		$sql =
			" SELECT "
			."		cast(NOW() as date) as now_a"
		;

		$stmt = $conn->query($sql);
		$result = $stmt->fetchAll();
		return $result[0]["now_a"];
		} catch (exception $e) {
			echo $e->getMessage();
			return false;
		}
	}


	// ------------------------
    // 함수명 : db_insert_boards
    // 기능 : boards 데이터 추가
    // 파라미터 : PDO    &$conn
    //           Array  &$arr_param 쿼리 작성용 배열
    // 리턴 : Boolean
    // ------------------------
    function db_insert_boards(&$conn, &$arr_param) {
		$sql =
			" INSERT INTO diary( "
			."      title "
			."      ,content "
			."      ,em_id "
			."      ) "
			." VALUES( "
			."      :title "
			."      ,:content "
			."      ,:em_id "
			." ) "
		;
		$arr_ps = [
			":title" => $arr_param["title"]
			,":content" => $arr_param["content"]
			,":em_id" => $arr_param["em_id"]
		];
		try {
			$stmt = $conn->prepare($sql);
			$result = $stmt->execute($arr_ps);
			return $result;
		} catch (exception $e) {
			echo $e->getMessage();
			return false;
		}
	}

	// ------------------------
    // 함수명 : db_update_boards_id
    // 기능 : boards 레코드 수정
    // 파라미터 : PDO 	 &$conn
	//			Array	&$arr_param
    // 리턴 : Boolean
    // ------------------------

	function db_update_boards_id(&$conn, &$arr_param) {
		try {
			$sql = 
			" UPDATE " 
			."		diary "
			." SET " 
			." 		title = :title "
			.", 	content = :content "
			.", 	em_id = :em_id "
			." WHERE "
			." 		id = :id "
			;
		// prepare 설정 준비
			$arr_ps = [
				":id" => $arr_param["id"]
				,":title" => $arr_param["title"]
				,":content" => $arr_param["content"]
				,":em_id" => $arr_param["em_id"]
			];
			
			$stmt = $conn->prepare($sql);
			$result = $stmt->execute($arr_ps);
			return $result;
		} catch (Exception $e) {
			echo $e->getMessage(); // Exception 메세지 출력
			return false; // 예외발생 : false 리턴
		}
	}

	// ------------------------
    // 함수명 : db_delete_boards_id
    // 기능 : boards 레코드 수정
    // 파라미터 : PDO 	 &$conn
	//			Array	&$arr_param
    // 리턴 : Boolean
    // ------------------------
	function db_delete_boards_id(&$conn, &$arr_param) {
		try {
			$sql = 
			" UPDATE diary "
			." SET "
			."		delete_at = now() "
			."		,delete_flag = '1' "
			." WHERE "
			."		id = :id "
			;

			$arr_par = [
				":id" => $arr_param["id"]
			];
			
			// 2. Query 실행
			$stmt = $conn->prepare($sql);
			$result = $stmt->execute($arr_par);
			return $result; //정상 종료: true 리턴
		} catch (Exception $e) {
			echo $e->getMessage();
			return false; // 예외발생 false리턴
		}
	}
?>