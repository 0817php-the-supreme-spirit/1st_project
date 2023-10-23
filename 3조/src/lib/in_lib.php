<?php

function db_challenge_first(&$conn) {
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
        ." AND "
        ." cr.c_com_at IS NULL "
        ." GROUP BY cr.create_id "
        ." ORDER BY cr.c_created_at DESC"
        ." LIMIT 1 ";


		$stmt = $conn->query($sql);
        $result = $stmt->fetchAll();
        return $result; // 정상 : 쿼리 결과 리턴
    } catch(Exception $e) {
        return false; // 예외 발생 : false 리턴
    }
}

function db_select_list(&$conn, &$arr_get) {
	try {
		$sql =
        " SELECT "
        ." cr.create_id, cr.c_id, ch.l_id, ch.l_name, ch.c_name, DATE(cr.c_created_at)
        , cr.l_com_at1, cr.l_com_at2, cr.l_com_at3, cr.l_com_at4, cr.c_com_at "
        ." FROM create_information cr "
        ." JOIN "
        ." chal_info ch "
        ." ON "
        ." cr.c_id = ch.c_id "
        ." AND "
        ." cr.create_id = :create_id "
        ." AND "
        ." cr.c_deleted_at IS NULL ";

        $arr_ps = [
            ":create_id" => $arr_get["create_id"]
        ];

        $stmt = $conn->prepare($sql);
        $stmt->execute($arr_ps);
        $result = $stmt->fetchAll();
        return $result; // 정상 : 쿼리 결과 리턴
    } catch(Exception $e) {
        return false; // 예외 발생 : false 리턴
    }
}

function db_complete_list(&$conn, &$arr_post) {
    $sql = " UPDATE "
    ." create_information cr "
    ." JOIN "
    ." chal_info ch "
    ." ON cr.c_id = ch.c_id "
    ." SET cr.l_com_at".$arr_post["l_id"]." = NOW() "
    ." WHERE "
    ." cr.create_id = :create_id "
    ." AND "
    ." ch.l_id = :l_id ";
    
    $arr_ps = [
        ":create_id" => $arr_post["create_id"]
        ,":l_id" => $arr_post["l_id"]
    ];
    
    try {
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute($arr_ps);
        return $result; // 정상 : 쿼리 결과 리턴
    } catch(Exception $e) {
        return false; // 예외 발생 : false 리턴
    }
}

function db_select_complete_check(&$conn, &$arr_post) {
	try {
		$sql =
        " SELECT "
        ." cr.create_id, cr.c_id, ch.l_id, ch.l_name
        , cr.l_com_at1, cr.l_com_at2, cr.l_com_at3, cr.l_com_at4, cr.c_com_at "
        ." FROM create_information cr "
        ." JOIN "
        ." chal_info ch "
        ." ON "
        ." cr.c_id = ch.c_id "
        ." AND "
        ." cr.create_id = :create_id ";

        $arr_ps = [
            ":create_id" => $arr_post["create_id"]
        ];

        $stmt = $conn->prepare($sql);
        $stmt->execute($arr_ps);
        $result = $stmt->fetchAll();
        return $result; // 정상 : 쿼리 결과 리턴
    } catch(Exception $e) {
        return false; // 예외 발생 : false 리턴
    }
}

function db_complete_at(&$conn, &$c_com) {
    try{
        $sql = 
        " UPDATE "
        ." create_information "
        ." SET "
        ." c_com_at = NOW() "
        ." WHERE "
        ." create_id = :create_id ";

        $arr_ps = [
            ":create_id" => $c_com["create_id"]
        ];

        $stmt = $conn->prepare($sql);
        $result = $stmt->execute($arr_ps);
        return $result; // 정상 : 쿼리 결과 리턴
    } catch(Exception $e) {
        return false; // 예외 발생 : false 리턴
    }
}

function db_complete_num(&$conn, &$arr_get) {
    try{
        $sql = 
        " SELECT "
        ." ( (case "
        ."        when ci.l_com_at1 IS NOT NULL then 25 "
        ."        ELSE 0 "
        ."    END) "
        ."    + "
        ."    (case "
        ."        when ci.l_com_at2 IS NOT NULL then 25 "
        ."        ELSE 0 "
        ."    END) "
        ."    + "
        ."    (case "
        ."        when ci.l_com_at3 IS NOT NULL then 25 "
        ."        ELSE 0 "
        ."    END) "
        ."    + "
        ."    (case "
        ."        when ci.l_com_at4 IS NOT NULL then 25 "
        ."        ELSE 0 "
        ."    END)) AS per "
        ." FROM create_information ci "
        ." WHERE "
        ." create_id = :create_id ";

        $arr_ps = [
            ":create_id" => $arr_get["create_id"]
        ];

        $stmt = $conn->prepare($sql);
        $stmt->execute($arr_ps);
        $result = $stmt->fetchAll();
        return $result; // 정상 : 쿼리 결과 리턴
    } catch(Exception $e) {
        return false; // 예외 발생 : false 리턴
    }
}

function db_complete_cancel(&$conn, &$arr_post) {
    $sql = " UPDATE "
    ." create_information cr "
    ." JOIN "
    ." chal_info ch "
    ." ON cr.c_id = ch.c_id "
    ." SET cr.l_com_at".$arr_post["l_id"]." = NULL "
    ." WHERE "
    ." cr.create_id = :create_id "
    ." AND "
    ." ch.l_id = :l_id ";
    
    $arr_ps = [
        ":create_id" => $arr_post["create_id"]
        ,":l_id" => $arr_post["l_id"]
    ];
    
    try {
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute($arr_ps);
        return $result; // 정상 : 쿼리 결과 리턴
    } catch(Exception $e) {
        return false; // 예외 발생 : false 리턴
    }
}

function db_complete_count(&$conn, $arr_post) {
    $sql = " SELECT "
    . "( (case "
    ."         when ci.l_com_at1 IS NOT NULL then 1 "
    ."        ELSE 0 "
    ."    END) "
    ."    + "
    ."    (case "
    ."        when ci.l_com_at2 IS NOT NULL then 1 "
    ."        ELSE 0 "
    ."    END) "
    ."    + "
    ."    (case "
    ."        when ci.l_com_at3 IS NOT NULL then 1 "
    ."        ELSE 0 "
    ."    END) "
    ."    + "
    ."    (case "
    ."        when ci.l_com_at4 IS NOT NULL then 1 "
    ."        ELSE 0 "
    ."    END)) AS cnt "
    ." FROM create_information ci "
    ." WHERE create_id = :create_id ";
    
    $arr_ps = [
        ":create_id" => $arr_post["create_id"]
    ];
    
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($arr_ps);
        $result = $stmt->fetchAll();
        return $result; // 정상 : 쿼리 결과 리턴
    } catch(Exception $e) {
        return false; // 예외 발생 : false 리턴
    }
}

function db_complete_at_cancel(&$conn, &$c_com) {
    try{
        $sql = 
        " UPDATE "
        ." create_information "
        ." SET "
        ." c_com_at = NULL "
        ." WHERE "
        ." create_id = :create_id ";

        $arr_ps = [
            ":create_id" => $c_com["create_id"]
        ];

        $stmt = $conn->prepare($sql);
        $result = $stmt->execute($arr_ps);
        return $result; // 정상 : 쿼리 결과 리턴
    } catch(Exception $e) {
        return false; // 예외 발생 : false 리턴
    }
}

