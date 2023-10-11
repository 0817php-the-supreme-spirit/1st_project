<?php

//------------------------------------------
// 함수명   : db_delete_boards_id
// 기능     : 특정 id 레코드 삭제처리
// 파라미터 : PDO  &$conn
//            Array &$arr_param          
// 리턴     : boolean
// -----------------------------------------
function db_delete_boards_id(&$conn, &$arr_param) {
    $sql =
    " UPDATE "
    ." boards "
    ." SET "
    ." delete_at = now() "
    ." ,delete_flg = '1' "
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


?>