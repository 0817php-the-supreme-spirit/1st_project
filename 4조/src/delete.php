<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/TODOLIST/src/");
define("FILE_HEADER", ROOT."header.php");
require_once(ROOT."lib/lib_db.php");

$id = ""; // 빈 문자열로 초기화
$conn = null; //null로 초기화
$arr_err_msg= []; //빈 배열로 초기화
$content = "";//빈 문자열로 초기화
$http_method = $_SERVER["REQUEST_METHOD"]; //method확인

try {
    // 2. DB Connect
    // 2-1. connection 함수호출
    if(!my_db_conn($conn)){
        // 2-2 예외처리
        throw new Exception("DB Error : PDO instance");
    }
    if($http_method === "GET"){
     
        $id = isset($_GET["id"]) ? $_GET["id"] : ""; //파라미터 획득
        $page = isset($_GET["page"]) ? $_GET["page"] : "";
        $arr_err_msg= [];
        if($id === ""){
            $arr_err_msg[] = "Parameter Error : ID"; //에러 메세지를 배열에 추가
        }
        if($page === ""){
            $arr_err_msg[] = "Parameter Error : Page";
        }
        if(count($arr_err_msg)>= 1){
            throw new Exception(implode("<br>",$arr_err_msg)); //하나 이상의 에러메세지 처리
        }
        // 3-1-2. 게시글 정보 획득
        $arr_param = [ //연관배열
            "id" => $id //id = 키 $id =값
        ];
        $result = db_select_boards_id($conn, $arr_param);

        // 3-1-3. 예외 처리
        if($result === false){
            throw new Exception("DB Error : Select id");
        } else if(!(count($result) === 1)){
            throw new Exception("DB Error : Select id count");
        }
        $item = $result[0];
    } else {
        // 3-2-1.파라미터에서 id 획득
        $id = isset($_POST["id"]) ? $_POST["id"] : "";
        $arr_err_msg = [];
			//error 메세지
			if($id === ""){
				$arr_err_msg[] = "Parameter Error : ID";
			}
			if(count($arr_err_msg)>= 1){
				throw new Exception(implode("<br>",$arr_err_msg));
			}
        // 3-2-2.Transaction 시작
        $conn->beginTransaction();

        // 3-2-3. 게시글 정보 삭제
        $arr_param = [
            "id" => $id
        ];

        // 3-2-4. 예외 처리
        if(!db_delete_boards_id($conn, $arr_param)){
            throw new Exception("DB Error : Delete Boards id");
        }

        $conn->commit(); // commit
        header("Location: list.php"); // 리스트 페이지로 이동 
        exit;
    }
} catch(Exception $e) {
    if($http_method === "POST"){
        $conn->rollback();
    }
    echo $e->getMessage(); //에러 메세지 출력
    exit; // 처리종료
} finally{
    db_destroy_conn($conn);
}

?>




<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=
    , initial-scale=1.0">
    <link rel="stylesheet" href="/TODOLIST/src/css/style.css">
    <title>삭제페이지</title>
</head>
<body>
    <?php
        require_once(FILE_HEADER);
    ?>  
    <main>
        <table class="delete_table center">
            <tr>
                <td class="delete_p">
                    - Warning -
                </td>
            </tr>
            <tr>
                <td class="delete_msg_1">                 
                    한번 삭제한 리스트는 복구가 불가능 합니다.
                </td>
            </tr>
            <tr>
                <td class="delete_msg_2">                 
                    그래도 하시겠습니까?
                </td>
            </tr>
           
            <tr>
                <td class = "delete_td"><span><?php echo $item["content"] ?></span></td>
            </tr>
         
        </table>
    </main>
    <section class="delete_section">
        <form action="/todolist/src/delete.php" method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <button class="delete_btn" type="submit"> 
                <img src="/TODOLIST/src/img/delete_chk2.png" class="delete_btn_img">
                <span class="delete_span">확인</span>
            </button>

            <a class="delete_a" href="/TODOLIST/src/detail.php/?id=<?php echo $id; ?>&page=<?php echo $page; ?> ">
                <img src="/TODOLIST/src/img/delete_cancel2.png"  class="delete_btn_img2" alt="">
                <span class="delete_span2">취소</span>
            </a>
        </form>
    </section>
</body>
</html>

