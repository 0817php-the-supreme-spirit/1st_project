<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/todolist/src/");//루트 설정
define("FILE_HEADER", ROOT."header.php");//헤더 루트설정
require_once(ROOT."lib/lib_db.php");//lib_db파일연결

// 변수 초기값 설정
$conn = null;
$date = "";
$cnt = "";
$arr_param = [];
$clear_cnt = "";

try {
    // DB 접속
    if(!my_db_conn($conn)){
        // DB Instance 에러
        throw new Exception("DB Error : PDO instance"); //강제예외발생 : DB Insrance
    }
   	// id확인
	if(isset($_GET["date"])){
		$date=$_GET["date"]; //id 셋팅
	} else {
		throw new Exception("Parameter ERROR : NO date"); //강제 예외 발생
	}
     // DB 조회시 사용할 데이터 배열
    $arr_param = [
        "date" => $date
    ];

    // 게시글 리스트 % 조회
    $result  = db_select_boards_stats($conn, $arr_param);
    if($result === false){
        throw new Exception("DB Error : SELECT boards %"); // 강제 예외 발생 : SELECT boards %
    }
    // 조회된 총 리스트 cnt 조회
    $cnt = db_select_search_boards_stats_cnt($conn, $arr_param);
    if($cnt === false){
        throw new Exception("DB Error : SELECT boards cnt"); // 강제 예외 발생 : SELECT boards cnt
    }
    // 조회된 총 리스트 중 수행한 리스트 cnt 조회
    $clear_cnt  = db_select_search_boards_clear_stats_cnt($conn, $arr_param);
    if($clear_cnt === false){
        throw new Exception("DB Error : SELECT complete boards cnt"); // 강제 예외 발생 : SELECT complete boards cnt
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
    <title>주/월간 통계페이지</title>
</head>
<body>
    <table class="stats_table">
        <?php
                require_once(FILE_HEADER);
        ?>
        <thead>
            <tr>
                <th>
                    <?php
                        if($date === "week"){
                    ?>
                    <a href="/todolist/src/stats.php/?date=month">월간통계</a>
                    <?php
                        } else if($date === "month"){
                    ?>
                    <a href="/todolist/src/stats.php/?date=week">주간통계</a>
                    <?php
                        }
                    ?>
                </th>
                <th>        
                    <?php
                        if($date === "week"){
                    ?>
                        <span>주간 달성률</span>
                    <?php
                        } else if($date === "month"){
                    ?>
                        <span>월간 달성률</span>
                    <?php
                        }
                    ?>
                </th>
                <th>
                    <a href="/todolist/src/list.php">메인으로</a>
                </th>
            </tr>
        </thead>
            <tr class="center">
                <td>
                    <?php echo $result; ?>%
                </td>
            </tr>
            <tr class="center">
                <td>    
                    <progress value="<?php echo $result; ?>" max="100" class="
                                                                            <?php
                                                                                if($result === 100){
                                                                            ?> 
                                                                                stats_progress_1
                                                                            <?php        
                                                                                }
                                                                            ?>
                                                                            <?php
                                                                                if($result < 100 && $result >= 80){
                                                                            ?> 
                                                                                stats_progress_2
                                                                            <?php        
                                                                                }
                                                                            ?>
                                                                            <?php
                                                                                if($result < 80 && $result >= 60){
                                                                            ?> 
                                                                                stats_progress_3
                                                                            <?php        
                                                                                }
                                                                            ?>
                                                                            <?php
                                                                                if($result < 60 && $result >= 40){
                                                                            ?> 
                                                                                stats_progress_4
                                                                            <?php        
                                                                                }
                                                                            ?>
                                                                            <?php
                                                                                if($result < 40 && $result >= 20){
                                                                            ?> 
                                                                                stats_progress_5
                                                                            <?php        
                                                                                }
                                                                            ?>
                                                                            <?php
                                                                                if($result < 20 && $result >= 0){
                                                                            ?> 
                                                                                stats_progress_6
                                                                            <?php        
                                                                                }
                                                                            ?>
                                                                            ">
                </progress>
            </td>
            </tr>
            <tr class="center">
                <td>
                    총 <?php echo $cnt ?>개 중 <?php echo $clear_cnt; ?>개 성공!
                </td>
            </tr>
            <tr>
                <td class="center">
                    <?php
                        if($result === 100){
                    ?>
                        <span>
                            !!!!!꿀잠성공!!!!!
                        </span>
                    <?php        
                        } else if($result < 100 && $result >= 80){
                    ?>
                        <span>
                            다 왔어요! 조금만 더!
                        </span>
                    <?php        
                        } else if($result < 80 && $result >= 60){
                    ?>
                        <span>
                            오...힘을내요..!
                        </span>
                    <?php        
                        } else if($result < 60 && $result >= 40){
                    ?>
                        <span>
                            흠....네...뭐..
                        </span>
                    <?php        
                        } else if($result < 40 && $result >= 20){
                    ?>
                        <span>
                            고작...? 노력하세요!
                        </span>
                    <?php        
                        } else if($result < 20 && $result >= 0){
                    ?>
                        <span>
                            아니....? 잠을 포기하셨나요?
                        </span>
                    <?php        
                        } else {
                    ?>
                        <span>
                            Error : 잘못된 % 가 설정되었습니다.
                        </span>
                    <?php
                        }
                    ?>
                </td>
            </tr>	
    </table>
</body>
</html>