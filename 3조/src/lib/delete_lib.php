<?php
// ---------------------------------
// 함수명   : my_db_conn
// 기능     : DB Connect
// 파라미터 : PDO   &$conn
// 리턴     : boolen
// ---------------------------------
function my_db_conn( &$conn ) {
	$db_host	= "192.168.0.142"; // host
	$db_user	= "team3"; // user
	$db_pw		= "team3"; // password
	$db_name	= "todolist"; // DB name
	$db_charset	= "utf8mb4"; // charset
	$db_dns		= "mysql:host=".$db_host.";dbname=".$db_name.";charset=".$db_charset;

	try {
		$db_options	= [
			PDO::ATTR_EMULATE_PREPARES		=> false // DB의 Prepared Statement 기능을 사용하도록 설정
			,PDO::ATTR_ERRMODE				=> PDO::ERRMODE_EXCEPTION // PDO Exception을 Throws하도록 설정
			,PDO::ATTR_DEFAULT_FETCH_MODE	=> PDO::FETCH_ASSOC // 연상배열로 Fetch를 하도록 설정
		];

		// PDO Class로 DB 연동
		$conn = new PDO($db_dns, $db_user, $db_pw, $db_options);
		return true;
	} catch (Exception $e) {
		echo $e->getMessage(); // Exception 메세지 출력
		$conn = null; // DB 파기
		return false;
	}
}

// ---------------------------------
// 함수명   : db_destroy_conn
// 기능     : DB Destroy
// 파라미터 : PDO   &$conn
// 리턴     : 없음
// ---------------------------------
function db_destroy_conn(&$conn) {
	$conn = null;
}

// ---------------------------------
// 함수명   : db_select_boards_id
// 기능     : boards 레코드 작성
// 파라미터 : PDO		&$conn
//			  Array		&$arr_param 쿼리 작성용 배열
// 리턴     : Array / false
// ---------------------------------
function db_select_boards_id(&$conn, &$arr_get) {
	$sql =
	" SELECT "
	." 		ci.c_name, "
	." 		cr.create_id, " 
	." 		cr.c_com_at " 
	." FROM " 
	." 		create_information cr "
	." JOIN "
	." 		chal_info ci "
	." ON "
	." 		cr.c_id = ci.c_id "
	." AND "
	." 		cr.create_id = :create_id " ;

	$arr_ps = [
		// get으로 받아온 create_id를 대입해줌
		":create_id" => $arr_get["create_id"]
	];

	try {
		$stmt = $conn->prepare($sql);
		$stmt->execute($arr_ps);
		$result = $stmt->fetchAll();
		return $result;
	} catch(Exception $e) {
		echo $e->getMessage(); // Exception 메세지 출력
		return false; // 예외발생 : false 리턴
	} 
}

// ---------------------------------
// 함수명   : db_delete_boards_id
// 기능     : 특정 ID의 레코드 삭제처리
// 파라미터 : PDO		&$conn
//			 Array		&$arr_param
// 리턴     : boolean
// ---------------------------------
function db_delete_boards_id(&$conn, &$arr_post) {
	$sql =
	 " UPDATE "
	."		create_information " 
	." SET "
	."		c_deleted_at = NOW() "
	." WHERE "
	."		create_id = :create_id "
	;

	$arr_ps = [
		// POST로 받아온 create_id
		":create_id" => $arr_post["create_id"]
	];

	try {
		$stmt = $conn->prepare($sql);
		$result = $stmt->execute($arr_ps);
		return $result; 
	} catch(Exception $e) {
		echo $e->getMessage(); 
		return false; 
	}
}	

?>