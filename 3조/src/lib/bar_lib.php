<?php

function my_db_conn( &$conn ) {
    $db_host    = "192.168.0.142"; // host
    $db_user    = "team3"; // user
    $db_pw      = "team3"; //password
    $db_name    = "todolist"; // DB name
    $db_charset = "utf8mb4"; //charset
    $db_dns     = "mysql:host=".$db_host.";dbname=".$db_name.";charset=".$db_charset;

    try {
        $db_options = [
            // DB의 Prepared Statement 기능을 사용하도록 설정
            PDO::ATTR_EMULATE_PREPARES      => false
            // PDO Exception을 Throws 하도록 설정
            ,PDO::ATTR_ERRMODE              => PDO::ERRMODE_EXCEPTION
            // 연상배열로 Fetch를 하도록 설정
            ,PDO::ATTR_DEFAULT_FETCH_MODE   => PDO::FETCH_ASSOC
        ];

        // PDO Class로 DB 연동
        $conn = new PDO($db_dns, $db_user, $db_pw, $db_options);
        return true;
    } catch (Exception $e){
        $conn = null;
        return false;
    }
}

function db_destroy_conn(&$conn) {
    $conn = null;
}

function db_select_challenge_bar(&$conn) {
	try {
		$sql = 
        " SELECT "
        ." cr.create_id, ch.c_name, cr.c_com_at "
        ." FROM "
        ." create_information cr "
        ." JOIN "
        ." chal_info ch "
        ." ON "
        ." cr.c_id = ch.c_id "
        ." AND "
        ." cr.c_deleted_at IS NULL "
        ." GROUP BY cr.create_id "
        ." ORDER BY cr.c_created_at DESC";


		$stmt = $conn->query($sql);
        $result = $stmt->fetchAll();
        return $result; // 정상 : 쿼리 결과 리턴
    } catch(Exception $e) {
        return false; // 예외 발생 : false 리턴
    }
}