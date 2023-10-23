<!-- xcopy D:\workspace\project1 C:\Apache24\htdocs\project1 /E /Y -->
<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/project1/src/"); //웹 서버
define("FILE_HEADER", ROOT."html/header.html");
define("FILE_STATUS", ROOT."status.php");
define("FILE_CHALLENGE", ROOT."challenge_bar.php");
require_once(ROOT. "lib/com_lib.php"); // DB 라이브러리
require_once(ROOT. "lib/bar_lib.php"); // DB 라이브러리
// require_once("")
// DB connect
$conn = null; // DB 커넥션 변수

$list_cnt = 9; // 한 페이지 최대 표시 수
$page_num = 1; // 페이지 번호 초기화

$err_msg = [];
try {
       // DB 접속
       if (!my_db_conn($conn)) {
        // DB Instance 에러
        //아규먼트로 에러 메세지를 출력
        throw new Exception("DB Error : PDO instance"); // 강제 예외 발생 : DB Instance
        }

    // ----------------------------
    // 페이징 처리 / 페이지 세팅 하기 
    // ----------------------------
    // 총 게시글 수 검색
    $chal_cnt = db_select_cnt($conn);
    if ($chal_cnt === false) {
        throw new Exception("DB Error : SELECT Count");
    }

    // 최대 페이지 개수 = (올림) ceil(게시글 개수(27) / 페이지 개수(5))
    $max_page_num = ceil($chal_cnt / $list_cnt);

    // GET Method 확인
    if(isset($_GET["page"])) {
        $page_num = $_GET["page"]; // 유저가 보내온 페이지 세팅
    }
     // 오프셋 계산
    $offset = ($page_num - 1) * $list_cnt;

     // 이전 버튼
     $prev_page_num = $page_num - 1;
     if($prev_page_num === 0) {
         $prev_page_num = 1;
     }

     // 다음 버튼
     $next_page_num = $page_num + 1;
     if($next_page_num > $max_page_num) {
         $next_page_num = $max_page_num;
     }

    // DB 조회시 사용할 데이터 배열 생성
    $arr_param = [
    "list_cnt" => $list_cnt
    ,"offset" => $offset
    ];

//---------------------------------------------------------------------------
    // 리스트 조회
    $result = db_select_create_information($conn, $arr_param);
    if(count($result) === 0) {
        $err_msg[] = "error";
    }
    if(count($err_msg) >= 1) {
            header("Location: complete_error.php"); // error 메세지 출력 (error.php)
      } 
        
      $data = [];

    foreach($result as $item) {
          $arr_param = [
               "c_id" => $item["c_id"]
          ];
          // 완료 리스트 출력
          $result1 = db_select_com_list($conn, $arr_param);
             if(!$result1) {
                 // Select 에러
                 throw new Exception("DB Error : SELECT com list"); // 강제 예외 발생 : SELECT board
             }

          // 화면 표시용 데이터 배열에 데이터 삽입
          $arr_item = [
                "create_id" => $item["create_id"]
                ,"c_id" => $item["c_id"]
                ,"c_created_at" => $item["c_created_at"]
                ,"c_com_at" => $item["c_com_at"]
                ,"c_name" => $item["c_name"]
                ,"list" => $result1
          ];

          $data[] = $arr_item;
    }
        //  [
        //     [
        //         "c_id" => 1
        //         ,"c_created_at" => 20231010
        //         ,"list" => [
        //             0 => ["l_name" => "이름1"]
        //             ,1 => ["l_name" => "이름2"]
        //             ,2 => ["l_name" => "이름3"]
        //             ,3 => ["l_name" => "이름4"]
        //         ]
        //     ]
        //     ,
        //     [
        //         "c_id" => 2
        //         ,"list" => [
        //             "이름1"
        //             ,"이름2"
        //             ,"이름3"
        //             ,"이름4"
        //         ]
        //     ]
        // ];
    }
catch(Exception $e) {
        // 예외 발생 메세지 (getMessage 메소드) 출력
        echo $e->getMessage();

        // 처리 종료
        exit;
    }
finally {
        db_destroy_conn($conn); //DB 파기
    }
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete</title>
    <link rel="stylesheet" href="/project1/src/css/complete.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Black+Han+Sans&family=Nanum+Pen+Script&family=Noto+Sans+KR:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/project1/src/css/header.css">
    <link rel="stylesheet" href="/project1/src/css/status.css">
    <link rel="stylesheet" href="/project1/src/css/challenge_bar.css">
    
    
</head>
<body>
    <?php
    require_once(FILE_HEADER);
	require_once(FILE_STATUS);
    ?>
    <section class="section-in">
    <div class="list_section">
        <?php
            foreach($data as $item){
        ?>
                <div class="list_com_bg">
                    <div class="list_com_border">
                        <div class="list_header">
                        <p class="list_com_title"><span class="pink"><?php echo $item["create_id"] ?></span> <?php echo $item["c_name"]; ?></>
                        <p class="list_date"> <?php echo $item["c_created_at"]; ?> ~ <span class="red"><?php echo $item["c_com_at"]; ?> <span> </p>
                        </div>
                        <ul>
                        <?php
                            foreach($item["list"] as $list_name) {
                        ?>
                                    <li><div class="bullet"></div><?php echo $list_name["l_name"]; ?></li>
                                    <div class="border_line"></div>
                        <?php
                            }
                        ?>
                        </ul>
                        <!-- 휴지통 버튼 -->
                        <form action="/project1/src/delete.php" method="GET">
                        <input type="hidden" name="page_flg" value="1">
                        <input type="hidden" name="create_id" value="<?php echo $item["create_id"]; ?>">
                        <button type="submit" class="delete_button"><a class="list_delete" href=""></a><button>
                        </form>
                    </div>
                </div>
        <?php
            }
        ?>
    </div>
    <div class = "page_section">
    <!-- 이전 페이지 버튼 -->
    <a class = "page_prev_button button_shadow" href="/project1/src/complete.php/?page=<?php echo $prev_page_num ?>"><</a>
    <!-- $i=1, 1이 증가하면서 최대 페이지수까지만 반복 -->
    <?php
            for($i = 1; $i <= $max_page_num; $i++) {


            // 현재 페이지에 활성화
            if ((int)$page_num === $i) {
            ?>
             <!-- a : 페이지 표시 버튼 -->
            <a class="act_bbg button_shadow" href="/project1/src/complete.php/?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php
            } else {
            ?>
            <a class="bbg button_shadow" href="/project1/src/complete.php/?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php
            }
            }   
    ?>
    <!-- 다음 페이지 버튼 -->
    <a class = "page_next_button button_shadow" href="/project1/src/complete.php/?page=<?php echo $next_page_num ?>">></a>
    </div>
        </section>
        <?php
        require_once(FILE_CHALLENGE);
        ?>
</body>
</html>