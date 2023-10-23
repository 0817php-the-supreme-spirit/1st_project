<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/todolist/src/"); // httpd.conf파일에 설정된 웹서버의 루트
define("FILE_HEADER", ROOT."header.php");
define("ERROR_MSG_PARAM", "%s을 입력해주세요.");// 파라미터 에러메세지
require_once(ROOT."lib/lib_db.php");

$id = ""; // 변수에 값을 담기 위해 공백으로 설정
$page = "";
$conn = null; // conn 초기화 // DB 연결용 변수
$http_method = $_SERVER["REQUEST_METHOD"]; // Method 확인
$arr_err_msg= []; //에러메세지용 변수를 배열로 넣어줌

try {
    // DB 연결
        if(!my_db_conn($conn)) {
        // DB Instance 에러
            throw new Exception("DB Error : PDO Instance");
        }    

    // GET Method의 경우
    if($http_method === "GET") {
        
        // 파라미터 획득
        $id = isset($_GET["id"]) ? trim($_GET["id"]) :""; // id 셋팅
        $page = isset($_GET["page"]) ? trim($_GET["page"]) : ""; // page 셋팅
        
        if($id === ""){
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "id");
        }       
        if($page === ""){
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "page");
        }
        if(count($arr_err_msg) >= 1){
            throw new Exception(implode("<br>",$arr_err_msg));
        }

        
    } else {
         // POST Method의 경우
        // 게시글 수정을 위해 파라미터 셋팅
        // content 수정칸에 스페이스바 공백을 넣으면 에러 뜨게하고 막아둔 것은 
        // 이대로 입력 돼 버리면 유저가 눌러서 수정을 할수도 삭제도 할 수 없고 
        // 오늘의 할일을 적는 것이기 때문에 빈 값으로 수정되면 안된다.
         
        $id = isset($_POST["id"]) ? trim($_POST["id"]) :""; // id 셋팅
        $page = isset($_POST["page"]) ? trim($_POST["page"]) : ""; // page 셋팅
        $content = isset($_POST["content"]) ? trim($_POST["content"]) : ""; //content셋팅
        if($id === ""){
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "id");
        }       
        if($page === ""){
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "page");
        }
        if(count($arr_err_msg) >= 1){
            throw new Exception(implode("<br>",$arr_err_msg));
        }
        if($content === ""){
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "내용");
        }
        if(count($arr_err_msg) === 0){
            $arr_param = [
                "id" => $id
                ,"content" => $content
            ];
            // 게시글 수정 처리
            $conn->beginTransaction(); // 트랜잭션 시작

            if(!db_update_boards_id($conn, $arr_param)) { 
                throw new Exception("DB Error : Update_boards_id");
            }
            $conn->commit(); // commit
            header("Location: detail.php/?id={$id}&page={$page}"); // detail 페이지로 이동
            exit;
        }
    }

      // 게시글 데이터 조회를 위한 파라미터 셋팅
      $arr_param = [
        "id" => $id
    ];

    // 게시글 데이터 조회
    $result = db_select_boards_id($conn, $arr_param);

    // 게시글 조회 예외처리
    if($result === false) {
        // 게시글 조회 에러
        throw new Exception("DB Error : PDO Select_id");
    } else if(count($result) !== 1) {
        // 게시글 조회 count 에러
        throw new Exception("DB ERROR : PDO Select_id Count,".count($result));
    }
    $item = $result[0];
  

} catch(Exception $e) {
    if($http_method === "POST") {
        $conn->rollBack(); // rollback
    }
    echo $e->getMessage(); // 예외 메세지 출력
	exit; // 처리종료

} finally {
    db_destroy_conn($conn);
}


?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <link rel="stylesheet" href="/todolist/src/css/style.css">
    <title>수정페이지</title>
</head>
<body>
    <?php
        require_once(FILE_HEADER);
    ?>
    <form class="update_form" action="/todolist/src/update.php" method="post">
        <input type="hidden" name="id" value="<?php echo $id ?>">
        <input type="hidden" name="page" value="<?php echo $page ?>"> <!-- 유저가 입력할 필요x / 몰라도 되는 정보지만, 폼 전송과 같이 전송해줘야 하는 정보  -->
        <table class="update_table">
            <tr>
                <td class="update_table_error center">
                    <?php
                        foreach($arr_err_msg as $val){
                    ?> 
                        <?php echo $val ?>
                    <?php        
                        }
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <textarea class="update_text" name="content" id="content" cols="30" rows="2" maxlength="30" required><?php echo $item["content"] ?></textarea>
                </td>
            </tr>            
        </table>
        <img class="update_cat2" src="/todolist/src/img/updatecat2.png" alt="">
        <img class="update_cat" src="/todolist/src/img/updatecat.png" alt="">
        <div class="update_div">
            <button class="update_a" type="submit">완료</button>
            <a class="update_a "href="/todolist/src/detail.php/?id=<?php echo $id; ?>&page=<?php echo $page; ?>">취소</a>
        </div>
        
    </form>
</body>
</html>