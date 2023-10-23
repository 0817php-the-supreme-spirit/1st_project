<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"] . "/todolist/src/");
define("FILE_HEADER", ROOT . "header.php");
define("ERROR_MSG_PARAM", "%s을 입력해주세요.");// 파라미터 에러메세지
require_once(ROOT . "lib/lib_db.php");

$conn = null;
$arr_err_msg= []; //에러 메세지 저장용
$content="";
$page_num = 0; // 페이지 번호 초기화

$list_cnt = 4; // 한 페이지 최대 표시 수

try {
    // DB 접속
    if (!my_db_conn($conn)) {
        // DB Instance 에러
        throw new Exception("DB Error : PDO instance"); // 강제 예외 발생 : DB Insrance
    }
    // content 확인
    $content = trim($_GET["content"]); //content 셋팅
    
    if($content === ""){
        $arr_err_msg[] = sprintf(ERROR_MSG_PARAM, "내용");
    }
    if(count($arr_err_msg) >= 1){
        header("Location: list.php/?content=none"); // 리스트 페이지로 이동 
    } else if(count($arr_err_msg) === 0){
        $arr_param = [
            "content" => '%' . $content . '%'
        ];
        // -------------
        // 페이징 처리
        // -------------
        $boards_cnt = db_search_content_boards_cnt($conn, $arr_param); // 수정: 검색어 파라미터 추가
        if ($boards_cnt === false) {
            throw new Exception("DB Error : SELECT Count"); // 강제 예외 발생 : DB SELECT Count
        }
        
        $max_page_num = ceil($boards_cnt / $list_cnt); // 최대페이지 수

        // GET Method 확인
        $page_num = isset($_GET["page"]) ? $_GET["page"] : 1;
        
        $offset = ($page_num - 1) * $list_cnt; // 오프셋 계산
        
        // 이전버튼
        $prev_page_num = $page_num - 1;
        if ($prev_page_num === 0) {
            $prev_page_num = 1;
        }
        
        // 다음버튼
        $next_page_num = $page_num + 1;
        if ($next_page_num > $max_page_num) {
            $next_page_num = $max_page_num;
        }
        
        // 게시글 데이터 조회
        $arr_param = [
            "content" => "%". $content ."%"
            ,"list_cnt" => $list_cnt
            ,"offset" => $offset
        ];

        // 게시글 리스트 조회
        $result = db_search_boards_content($conn, $arr_param);
        if ($result===false) {
            throw new Exception("DB Error : can't search content"); // 강제 예외 발생 : SELECT boards
        }
    }
} catch (Exception $e) {
    echo $e->getMessage(); //예외발생 메세지 출력  //
    exit; //처리종료
} finally {
    db_destroy_conn($conn); // DB 파기
}


?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/todolist/src/css/style.css">
    <title>검색</title>
</head>
<body>
    <!-- 제작 : 민주,다윗 -->
    <?php
        require_once(FILE_HEADER);
    ?>
    <table class="search_table">
        <thead>       
            <tr>
                <td class="search_head_td">
                    검색내용 : <?php echo $content ?>
                    <a class="search_out_btn" href="/todolist/src/list.php/?page=1">메인으로</a>
                </td>
            </tr>
        </thead>
            <?php if($boards_cnt === 0){ 
            ?>   
                <tr>
                    <td class="search_con_msg center">
                        조회된 게시물이 없습니다.
                    </td>
                </tr>
                <tr>
                    <td>
                        <img src="/todolist/src/img/list_paper.svg" alt="" class="list_img_paper">
                    </td>
                </tr>
            <?php 
                } else {
            ?>
            <?php
            // 리스트를 생성
                    foreach ($result as $item) {
            ?>
                <tr>
                    <td colspan="2">
                    <?php if($item["chk_flg"] === "0"){ ?>
                        <span>
                            <img src="/todolist/src/img/check_b.svg" alt="">
                            <span class="search_con_content">
                                <?php echo $item["content"]; ?>
                            </span>
                            <br>
                            <div class="search_con_span"><?php echo $item["write_date"] ?></div>
                        </span>
                    <?php } ?>
                    <?php if($item["chk_flg"] === "1"){ ?>
                        <span>
                            <img src="/todolist/src/img/check_a.svg" alt=""> 
                            <span class="search_con_content_chk">
                                <?php echo $item["content"]; ?>
                            </span>
                            <br>
                            <div class="search_con_span"><?php echo $item["write_date"] ?></div>
                        </span>
                    <?php } ?>
                    </td>
                </tr>
            <?php
                    }
            ?>
            <tr>
                <td>
                    <img src="/todolist/src/img/list_paper.svg" alt="" class="list_img_paper">
                </td>
            </tr>
            
        <tfoot>
            <tr class="center">
                <td>
                    <section><!-- 제작 : 정훈 -->
                        <?php
                            if($boards_cnt > 17){
                        ?> 
                        <a href="/todolist/src/search_con.php/?page=1&content=<?php echo $content ?>"><<</a>
                        <?php
                            }
                        ?>      
                        <?php
                            if($boards_cnt > 4){
                        ?>                  
                        <a href="/todolist/src/search_con.php/?page=<?php echo $prev_page_num ?>&content=<?php echo $content ?>"><</a>
                        <?php
                            }
                        ?>
                        <?php
                            if($boards_cnt < 17){
                                for ($i = 1; $i <= $max_page_num; $i++) {
                                    // 삼항연산자 : 조건 ? 참일때처리 : 거짓일때처리
                                    $class = ($i == $page_num) ? "search_con_page_now_page" : "";
                        ?>
                        <?php
                            if($boards_cnt > 4){
                        ?>
                                    <a class="search_con_page_btn <?php echo $class; ?> " href="/todolist/src/search_Con.php/?page=<?php echo $i ?>&content=<?php echo $content ?>"><?php echo $i ?></a>        
                        <?php
                                }}
                            }
                            else if($boards_cnt >= 17){
                            $min_page = max($page_num - 2, 1); 
                            $max_page = min($page_num + 2, $max_page_num);
                                if($min_page === 1){
                                    $min_page = 1;
                                    $max_page = 5;
                                } 
                                else if($max_page === $max_page_num){
                                    $min_page = $max_page_num-4;
                                    $max_page = $max_page_num;
                                } 
                                for ($i = $min_page; $i <= $max_page; $i++) {
                                    // 삼항연산자 : 조건 ? 참일때처리 : 거짓일때처리
                                    $class = ($i == $page_num) ? "search_con_page_now_page" : "";
                                    // 현재 페이지와 $i를 비교하여 현재 페이지에 해당하는 버튼에 강조 스타일을 적용
                        ?>
                        
                        <a class="page_btn <?php echo $class; ?> " href="/todolist/src/search_con.php/?page=<?php echo $i ?>&content=<?php echo $content ?>"><?php echo $i ?></a>
                        <?php
                                }}             
                        ?>
                        <?php
                            if($boards_cnt > 4){
                        ?>     
                        <a class="page_btn" href="/todolist/src/search_con.php/?page=<?php echo $next_page_num ?>&content=<?php echo $content ?>">></a>
                        <?php
                            }
                        ?>
                        <?php
                            if($boards_cnt > 17){
                        ?> 
                        <a class="page_btn" id="minmax_btn" href="/todolist/src/search_con.php/?page=<?php echo $max_page_num ?>&content=<?php echo $content ?>">>></a>                    
                        <?php
                            }
                        ?>
                        </section>
                </td>
            </tr>
        </tfoot>
        <?php
            }
        ?>
    </table>
</body>
</html>