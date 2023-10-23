<?php

    // ------------------------------------
    // 함수명        : db_select_cnt
    // 기능          : board count 조회
    // 파라미터      : PDO    &$conn
    // 리턴          : INT / false
    // ------------------------------------


    function db_select_cnt(&$conn) {

        $sql = 
        " SELECT ".
        " count(create_id) cnt ".
        " FROM ".
        " create_information ".
        " WHERE ".
        " c_com_at is not null and c_deleted_at IS NULL ";
    
        try {
            $stmt = $conn->query($sql);
            $result = $stmt->fetchAll();
            return (int)$result[0]["cnt"]; //정상 : 쿼리 결과 리턴
        }
        catch(Exception $e) {
            return false; // 예외발생 : false 리턴
    
        }
    }

// 리스트 조회 함수
function db_select_create_information(&$conn, &$arr_param) {
try {
    $sql =
    " SELECT DISTINCT
    ci.create_id
    ,ci.c_id
    ,date(ci.c_created_at) c_created_at
    ,date(ci.c_com_at) c_com_at
    ,ch.c_name
    FROM create_information ci
    JOIN chal_info ch
        ON ci.c_id = ch.c_id
    WHERE ci.c_com_at IS NOT NULL and ci.c_deleted_at IS NULL
    ORDER BY ci.c_created_at DESC
    LIMIT :list_cnt
    OFFSET :offset ";

$arr_ps = [
    ":list_cnt" => $arr_param["list_cnt"]
    ,":offset" => $arr_param["offset"]
];

$stmt = $conn->prepare($sql);
$stmt->execute($arr_ps);
$result = $stmt->fetchAll();
return $result; // 정상 : 쿼리 결과 리턴
} catch(Exception $e) {
return false; // 예외발생 : false 리턴
}
}

// db 완료된 리스트 출력 함수
function db_select_com_list(&$conn, &$arr_param) {
    try {
        $sql =
            " SELECT
            ch.l_name
            FROM chal_info ch
            WHERE ch.c_id = :c_id "
        ;

        $arr_ps = [
            ":c_id" => $arr_param["c_id"]
        ];
        $stmt = $conn->prepare($sql);
        $stmt->execute($arr_ps);
        $result = $stmt->fetchAll();
        return $result; // 정상 : 쿼리 결과 리턴
    } catch(Exception $e) {
        return false; // 예외발생 : false 리턴
    }
}