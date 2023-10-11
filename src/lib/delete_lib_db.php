<?php
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



//--------------------------------------------------------
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
            ." todo.id = :id "
            ;

    $arr_ps = [
        ":id" => $arr_param["id"]
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



?>