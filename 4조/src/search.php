<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/todolist/src/");
define("FILE_HEADER", ROOT."header.php");
require_once(ROOT."lib/lib_db.php");

$conn = null; // DB connection 변수 초기화
$date = ""; // 변수초기화 
$today="";

$today = date("Y-m-d");//현재날짜
$date = isset($_GET["date"])? $_GET["date"]:date("Y-m-d");//$date 값 설정
$list_cnt = 4; // 한 페이지 최대 표시 수
$page_num = 1; // 페이지 번호 초기화
try {
    // DB 접속
    if(!my_db_conn($conn)){
        // DB Instance 에러
        throw new Exception("DB Error : PDO instance"); //강제예외발생 : DB Insrance
    }
    // -------------
    // 페이징 처리
    // -------------
    // DB 조회값 설정
    $arr_param=[
        "write_date" => $date
    ];
    // 게시글수 조회
    $boards_cnt = db_search_boards_cnt($conn, $arr_param);
    if($boards_cnt === false) {
        throw new Exception("DB Error : SELECT Count"); // 강제 예외 발생 : DB SELECT Count
    }
    // 만약 오늘로 검색할시
    if($date >= $today){
        header("Location: /todolist/src/list.php/?page=1"); // 리스트 페이지로 이동
    }

    // 페이지 기본값 설정
    $page_num = isset($_GET["page"]) ? $_GET["page"] : 1;
    
     //오프셋계산
    $offset = ($page_num - 1)* $list_cnt;
    
    // DB 조회시 사용할 데이터 배열
    $arr_param = [
        "list_cnt" => $list_cnt
        ,"offset" => $offset
        ,"write_date" => $date
    ];

    // 게시글 리스트 조회 
    $result  = db_search_boards($conn, $arr_param);
    if($result === false){
        throw new Exception("DB Error : SELECT boards"); // 강제 예외 발생 : SELECT boards
    }
} catch(Exception $e) {
    echo $e->getMessage(); //예외발생 메세지 출력
    exit; //처리종료
} finally {
    db_destroy_conn($conn); //DB파기
}




?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/todolist/src/css/style.css">
    <title>검색페이지</title>
</head>

<body>
    <?php
        require_once(FILE_HEADER);
    ?>
    <table class="search_table">
        <thead>       
            <tr>
                <td class="search_head_td">
                    <?php echo $date ?>
                    <a class="search_out_btn" href="/todolist/src/list.php/?page=1">메인으로</a>
                </td>
            </tr>
        </thead>
        <?php 
            if($boards_cnt === 0){ 
        ?>   
            <tr>
                <td class="pickstats_msg center">
                    조회된 게시물이 없습니다.
                </td>
            </tr>
        <?php 
            } else if($boards_cnt <= 4){
                if( $page_num >= 2){ 
        ?>  
            <tr>
                <td class="search_con_msg center">
                    등록된 게시물이 없습니다.
                </td>
            </tr>
            <tr>
                <td>
                    <img src="/todolist/src/img/list_paper.svg" alt="" class="list_img_paper">
                </td>
            </tr>
            <?php 
                } else {
                    foreach ($result as $item) {
            ?>
            <tr>
                <td>
                    <?php if($item["chk_flg"] === "0"){ ?>
                        <span class="search_content">
                            <img src="/todolist/src/img/check_b.svg" alt="">
                            <?php echo $item["content"]; ?>
                        </span>
                    <?php } ?>
                    <?php if($item["chk_flg"] === "1"){ ?>
                        <span class="search_content_chk" href="/todolist/src/detail.php/?id=<?php echo $item["id"]; ?>&page=<?php echo $page_num; ?>">
                            <img src="/todolist/src/img/check_a.svg" alt=""> 
                            <?php echo $item["content"]; ?>
                        </span>
                    <?php } ?>
                </td>
            </tr>
                <?php
                        }
                    }
                } else if($boards_cnt <= 8 ){
                    if( $page_num >= 3 ){ 
                ?>  
            <tr>
                <td class="search_con_msg center">
                    등록된 게시물이 없습니다.
                </td>
            </tr>
            <tr>
                <td>
                    <img src="/todolist/src/img/list_paper.svg" alt="" class="list_img_paper">
                </td>
            </tr>
                <?php 
                    } else {
                    // 리스트를 생성
                        foreach ($result as $item) {
                    ?>
            <tr>
                <td>
                        <?php if($item["chk_flg"] === "0"){ ?>
                            <span class="search_content">
                                <img src="/todolist/src/img/check_b.svg" alt="">
                                <?php echo $item["content"]; ?>
                            </span>
                        <?php } ?>
                        <?php if($item["chk_flg"] === "1"){ ?>
                            <span class="search_content_chk" href="/todolist/src/detail.php/?id=<?php echo $item["id"]; ?>&page=<?php echo $page_num; ?>">
                                <img src="/todolist/src/img/check_a.svg" alt=""> 
                                <?php echo $item["content"]; ?>
                            </span>
                        <?php } ?>
                </td>
            </tr>
            <?php
                        }
                    }   
                } else {
            ?>
                <?php
                // 리스트를 생성
                    foreach ($result as $item) {
                ?>
            <tr>
                <td>
                    <?php if($item["chk_flg"] === "0"){ ?>
                        <span class="search_content">
                            <img src="/todolist/src/img/check_b.svg" alt="">
                            <?php echo $item["content"]; ?>
                        </span>
                    <?php } ?>
                    <?php if($item["chk_flg"] === "1"){ ?>
                        <span class="search_content_chk" href="/todolist/src/detail.php/?id=<?php echo $item["id"]; ?>&page=<?php echo $page_num; ?>">
                            <img src="/todolist/src/img/check_a.svg" alt=""> 
                            <?php echo $item["content"]; ?>
                        </span>
                    <?php } ?>
                </td>
            </tr>
            <?php
                    }
                }
            ?>
            <tr>
                <td>
                    <img src="/todolist/src/img/list_paper.svg" alt="" class="list_img_paper">
                </td>
            </tr>
        <tfoot>
            <tr>
                <td class="center">
                    <?php                  
                        for ($i = 1; $i <= 3; $i++) {
                                $class = ($i == $page_num) ? "list_now_page" : "";
                    ?>
                    <a class="lsit_page <?php echo $class; ?> " href="/todolist/src/search.php/?page=<?php echo $i ?>&date=<?php echo $date ?>">●</a>        
                    <?php
                        }
                    ?>
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>