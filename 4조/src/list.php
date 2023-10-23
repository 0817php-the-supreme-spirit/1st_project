<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/todolist/src/");
define("FILE_HEADER", ROOT."header.php");
require_once(ROOT."lib/lib_db.php");

$conn = null; // DB connection 변수
$content = "";
$date="";

$today = date("Y-m-d");
$list_cnt = 4; // 한 페이지 최대 표시 수
$page_num = 1; // 페이지 번호 초기화
try {
    // DB 접속
    if(!my_db_conn($conn)){
        // DB Instance 에러
        throw new Exception("DB Error : PDO instance"); //강제예외발생 : DB Insrance
    }
    $content = isset($_GET["content"]) ? $_GET["content"] : "";
    $date = isset($_GET["date"]) ? $_GET["date"] : "";
    // -------------
    // 페이징 처리
    // -------------
    $boards_cnt = db_select_boards_cnt($conn);
    if($boards_cnt === false) {
        throw new Exception("DB Error : SELECT Count"); // 강제 예외 발생 : DB SELECT Count
    }

     // 삼항연산자로 작성
    $page_num = isset($_GET["page"]) ? $_GET["page"] : 1;
    // 1~3 이외의 값 입력시
    if($page_num < 1 || $page_num > 3){
        throw new Exception("DB Error : Page Num error"); // 강제 예외 발생 : SELECT boards
    }

    $offset = ($page_num - 1)* $list_cnt; //오프셋계산
    
    // DB 조회시 사용할 데이터 배열
    $arr_param = [
        "list_cnt" => $list_cnt
        ,"offset" => $offset
    ];

    // 게시글 리스트 조회 
    $result  = db_select_boards_paging($conn, $arr_param);
    if($result === false){
        throw new Exception("DB Error : SELECT boards"); // 강제 예외 발생 : SELECT boards
    }


} catch(Exception $e) {
    echo $e->getMessage(); //예외발생 메세지 출력  //v002 del
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
    <title>리스트페이지</title>
</head>

<body>
    <input type="checkbox" id="icon">
        <label for="icon">  <!--label은 인라인 스타일-->
            <span></span>
            <span></span>
            <span></span>
        </label>
    <div id="header">
        <ul>
            <li class="side_search_li_msg center">
                <?php 
                    if($content==="none"){
                ?>
                    내용을 입력해 주세요.
                <?php
                    }
                ?>    
            </li>
            <li class="side_search_li">
                <form action="/todolist/src/search_con.php" method="get">      
                    <input type="search" name="content" id="content" required>
                    <button type ="submit"><img src="/todolist/src/img/search_btn.png" alt=""></button>                  
                </form>
            </li>
                <li class="side_search_li_msg center">
            <li class="side_week_li"><a href="/todolist/src/stats.php/?date=week">주간 통계</a></li>
            <br>
            <li class="side_month_li"><a href="/todolist/src/stats.php/?date=month">월간 통계</a></li>
            <br>
            <li class="side_search_li_msg_2 center">
                <?php 
                    if($date==="none"){
                ?>
                    조회기간을 확인해주세요.
                <?php
                    }
                ?>    
            </li>
            <form action="/todolist/src/pickstats.php" method="get">           
                <label class="list_label">
                    <input type="date" name="from_date" required class="list_side_date" max="<?php echo $today ?>">
                    <div class="side_tilde center">~</div>
                    <input type="date" name="to_date" required class="list_side_date" max="<?php echo $today ?>">
                    <button type="submit" class="list_side_date_btn">검색</button>
                </label>
            </form>
        </ul>
    </div>
    <?php
            require_once(FILE_HEADER);
    ?>
    <table class="list_table">    
        <thead> 
            <tr>
                <td class="list_head_td">오늘의 할 일                   
                    <form action="/todolist/src/search.php" method="get">           
                        <label class="list_label">
                            <input type="date" name="date" required class="list_date_search_input" value="<?php echo $today; ?>" max="<?php echo $today; ?>">
                            <button type="submit" class="list_date_search_btn"><img src="/todolist/src/img/lens.png" alt=""></button>
                        </label>
                    </form>
                </td>
            </tr>
        </thead>
            <?php 
                if($boards_cnt === 0){ 
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
                    <?php
                        if($item["chk_flg"] === "0"){
                    ?>  
                        <a href="/todolist/src/chk_flg.php/?id=<?php echo $item["id"]; ?>&chk_flg=<?php echo $item["chk_flg"] ?>&page=<?php echo $page_num ?>">
                            <img src="/todolist/src/img/check_b.svg" alt="">
                        </a>
                        <a class="list_content" href="/todolist/src/detail.php/?id=<?php echo $item["id"]; ?>&page=<?php echo $page_num; ?>">
                            <?php echo $item["content"]; ?>
                        </a>
                    <?php
                        }else {
                    ?>
                        <a href="/todolist/src/chk_flg.php/?id=<?php echo $item["id"]; ?>&chk_flg=<?php echo $item["chk_flg"] ?>&page=<?php echo $page_num ?>">
                            <img src="/todolist/src/img/check_a.svg" alt=""> 
                        </a>
                        <a class="list_content_chk" href="/todolist/src/detail.php/?id=<?php echo $item["id"]; ?>&page=<?php echo $page_num; ?>">
                            <?php echo $item["content"]; ?>
                        </a>
                    <?php
                        }
                    ?>  
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
                    <?php
                        if($item["chk_flg"] === "0"){
                    ?>  
                        <a href="/todolist/src/chk_flg.php/?id=<?php echo $item["id"]; ?>&chk_flg=<?php echo $item["chk_flg"] ?>&page=<?php echo $page_num ?>">
                            <img src="/todolist/src/img/check_b.svg" alt="">
                        </a> 
                        <a class="list_content" href="/todolist/src/detail.php/?id=<?php echo $item["id"]; ?>&page=<?php echo $page_num; ?>">
                            <?php echo $item["content"]; ?>
                        </a>
                    <?php
                        }else {
                    ?>
                        <a href="/todolist/src/chk_flg.php/?id=<?php echo $item["id"]; ?>&chk_flg=<?php echo $item["chk_flg"] ?>&page=<?php echo $page_num ?>">
                            <img src="/todolist/src/img/check_a.svg" alt=""> 
                        </a>
                        <a class="list_content_chk" href="/todolist/src/detail.php/?id=<?php echo $item["id"]; ?>&page=<?php echo $page_num; ?>">
                            <?php echo $item["content"]; ?>
                        </a>
                    <?php
                        }
                    ?>  
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
                    <?php
                        if($item["chk_flg"] === "0"){
                    ?>  
                        <a href="/todolist/src/chk_flg.php/?id=<?php echo $item["id"]; ?>&chk_flg=<?php echo $item["chk_flg"] ?>&page=<?php echo $page_num ?>">
                            <img src="/todolist/src/img/check_b.svg" alt="">
                        </a>
                        <a class="list_content" href="/todolist/src/detail.php/?id=<?php echo $item["id"]; ?>&page=<?php echo $page_num; ?>">
                            <?php echo $item["content"]; ?>
                        </a>
                    <?php
                        }else {
                    ?>
                        <a href="/todolist/src/chk_flg.php/?id=<?php echo $item["id"]; ?>&chk_flg=<?php echo $item["chk_flg"] ?>&page=<?php echo $page_num ?>">
                            <img src="/todolist/src/img/check_a.svg" alt=""> 
                        </a>
                        <a class="list_content_chk" href="/todolist/src/detail.php/?id=<?php echo $item["id"]; ?>&page=<?php echo $page_num; ?>">
                            <?php echo $item["content"]; ?>
                        </a>
                    <?php
                        }
                    ?>  
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
                    <a class="lsit_page <?php echo $class; ?> " href="/todolist/src/list.php/?page=<?php echo $i ?>">●</a>        
                    <?php
                        }
                    ?>
                </td>
            </tr>
        </tfoot>
    </table>  
    <a class="list_insert_btn center" href="/todolist/src/insert.php">등록</a>
</body>
</html>