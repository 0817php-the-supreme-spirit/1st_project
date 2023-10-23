<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/todolist/src/");
define("FILE_HEADER", ROOT."header.php");
define("ERROR_MSG_PARAM", "%s을 입력해주세요.");// 파라미터 에러메세지
require_once(ROOT."lib/lib_db.php");

$conn = null; //기본값셋팅
$arr_err_msg= []; //에러 메세지 저장용
$content = "";//콘텐츠 기본값셋팅
$http_method = $_SERVER["REQUEST_METHOD"];

if($http_method === "POST") {
    try {
        if(!my_db_conn($conn)) {
            throw new Exception ("DB Error : PDO instance");
        }
        $content = isset($_POST["content"]) ? trim($_POST["content"]) : ""; //content 셋팅
        // 만약 빈공간으로 보내져왔을시 오류메세지 출력
        
        // 오늘의 할일을 작성해서 보이는 건데 공백으로 작성된 것 자체가 비정상적인 것이고
        // 만약 작성됐다면 유저입장에서 글 자체가 클릭이 안될 뿐더러 수정, 삭제 또한 불가능해서
        // 공백으로 보내면 안된다.
        if($content === ""){
            $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "내용");
        }
        if(count($arr_err_msg) === 0){
            
            $arr_param = [
                "content" => $content
            ];

            $conn->beginTransaction();

            if(!db_insert_boards($conn, $arr_param)) {
                throw new Exception("DB Error : Insert Boards");
            }
            $conn->commit();

            header("Location: list.php");
            exit;
        }
    } catch (Exception $e) {
        if($http_method === "POST") {
            $conn->rollBack(); // rollback // 1017 코드리뷰 후 수정
        }
        echo $e->getMessage();
        exit;
    } finally {
        db_destroy_conn($conn);
    }
}



?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <link rel="stylesheet" href="/todolist/src/css/style.css">
    <title>작성페이지</title>
</head>
<body>
    <?php
        require_once(FILE_HEADER);
    ?>
    <?php
        foreach($arr_err_msg as $val){
    ?> 
        <p class="insert_error_p"><?php echo $val ?></p>
    <?php        
        }
    ?>
    <form action="/todolist/src/insert.php" method="post" class="insert_form">
            <input class="insert_input" type="text" name="content" placeholder="          내용을 입력해 주세요(최대30글자)" maxlength='30' required>
            <button class="insert_butt" type="submit">
                <img class="insert_pen" src="/todolist/src/img/흐리멍텅한연필.png" alt="연필">
            </button>
            <a class="insert_a"href="/todolist/src/list.php">
                <img src="/todolist/src/img/delete_cancel2.png" alt="연필">
            </a>
            <img class="insert_sheep" src="/todolist/src/img/sheeps.svg" alt="sheeps">
            <img class="insert_ufo" src="/todolist/src/img/ufo.png" alt="ufo">
        <div class="insert_div">
            <div class="face cat_front"><img class="insert_cat2" src="/todolist/src/img/cat_sleep.png" alt="눈감은고양이"></div>
            <div class="face cat_back"><img class="insert_cat1" src="/todolist/src/img/cat_coffee.png" alt="눈뜬고양이"></div>
        </div>
    </form>
</body>
</html>