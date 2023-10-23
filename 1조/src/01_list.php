<?php
	define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/todolist/src/"); //ROOT 경로 세팅
	define("IMGPATH", "/todolist/doc/img/"); //IMG 파일 경로 세팅
	require_once(ROOT."lib/lib_db.php"); // 함수파일 로드

	$conn = null; // DB connection 변수
	$list_cnt = 5; //한 페이지 당 게시글 수
	$page_num = 1; // 페이지 번호 초기화
	$yoil = array("일요일","월요일","화요일","수요일","목요일","금요일","토요일"); //요일 출력하기 위한 세팅

	try{
		// ------------------------------------------------------------------------
		// DB 접속
		// ------------------------------------------------------------------------
		if (!db_conn($conn)) {
			throw new Exception("DB Error : PDO instance");
		}

		// ------------------------------------------------------------------------
		// 페이징 처리
		// ------------------------------------------------------------------------

		// * 총 게시글 수 검색 *
		//		$boards_cnt는 return이 int
		$boards_cnt = db_select_boards_cnt($conn);
		if ($boards_cnt === false){
			throw new Exception("DB Error : select COUNT ERROR");
		}
		
		// * 유저가 보내온 페이지 세팅 *
		if (isset($_GET["page"])) {
			$page_num = (int)$_GET["page"];
		};
		// * 최대 페이지 수 세팅 *
		//		$max_page_num = 올림함수(총 게시글 수 / 한페이지당 게시글 수)
		$max_page_num = ceil($boards_cnt / $list_cnt);

		// * 오프셋 세팅 *
		//		db_select_boards_paging 함수에서 게시글의 반복을 방지
		//		limit 5 offset 0, 5, 10, 15 이런식으로 적용
		//		첫번째 페이지의 경우	 $offset =(1 - 1) * 5  = 0    	=> 1 ~ 5번 게시글 출력
		//		두번째 페이지의 경우 	 $offset =(2 - 1) * 5  = 5		=> 6 ~ 10번 게시글 출력
		$offset = ($page_num - 1) * $list_cnt;

		// * 이전 버튼 변수 세팅 *
		//		첫번째 페이지에서 이전 버튼을 눌렀을 경우
		//		$prev_page_num = 1 - 1 = 0 이기 때문에 if문 추가
		$prev_page_num = $page_num - 1;
		if ($prev_page_num === 0) {
			$prev_page_num = 1;
		}

		// * 다음 버튼 변수 세팅 *
		//		마지막 페이지(3이라고 가정)에서 다음 버튼을 눌렀을 경우
		//		$next_page_num = 3 + 1 = 4 이기 때문에 없는 페이지가 설정되어 if문 추가
		$next_page_num = $page_num + 1;
		if ($next_page_num > $max_page_num) {
			$next_page_num = $max_page_num;
		}

		// ------------------------------------------------------------------------
		// 게시글 조회
		// ------------------------------------------------------------------------
		
		// * 게시글 조회를 위한 파라미터 변수 세팅 *
		$arr_param = [
			"list_cnt" => $list_cnt
			,"offset" => $offset
		];
		
		// * 게시글 조회 *
		$result = db_select_boards_paging($conn, $arr_param);
		if($result === False) {
			throw new Exception("DB Error : SELECT boards paging ERROR");
		}
		
		// ------------------------------------------------------------------------
		// 감정 통계 조회
		// ------------------------------------------------------------------------
		$rank_array = db_select_boards_emo_rank($conn);
		if($rank_array === False) {
			throw new Exception("DB Error : SELECT boards emo rank ERROR");
		}

	} catch (Exception $e) {
		echo $e->getMessage();
		exit;
	} finally {
		db_destroy_conn($conn);
	}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>메인 페이지</title>
	<link rel="stylesheet" href="/todolist/src/css/common.css">
</head>
<body>
	<div class="top_container">

	</div>
	<div class="main_container">
		<div class="main_container_box">
			<div class="left_box">
				<div class="box_layout">
					<div class="left_top">
						<img src="/todolist/doc/img/list_spring.png">
					</div>
					<div class="left_top2">
						<p class="left_top2_text1">나의 감정은 어때 ?</p>
						<p class="left_top2_text2">- 자주 기록했던 감정을 확인할 수 있어요</p>
					</div>
					<div class="left_middle">
						<?php
						$i=0;
						// * 감정 통계 조회 부분 *
						foreach ($rank_array as $item) {
							$i++;
							if($i == 4) {
								break;
							} // 1, 2, 3번까지 출력
						?>
							<div class="left_bottom_layout1">
								<!-- 1 : 아이콘, 2 : 랭크, 3 : 결과 값 -->
								<img class="left_emotion_size1" src='<?php echo IMGPATH.$item['em_path']; ?>'> 
								<img class="left_star_size1" src="/todolist/doc/img/star_<?php echo $i; ?>.png">
								<p><?php echo $item['em_name']." : ".$item['cnt_em_id']; ?></p>
							</div>
						<?php
						}
						?>
					</div>
					<div class="left_bottom">
						<?php
						$y=0;
						// * 감정 통계 조회 부분 2 *
						foreach ($rank_array as $item) {
							$y++;
							if($y <=3) {
								continue;
							} // contiune를 통해 3번 넘어가고 4, 5번 출력 | db에 limit 5를 걸어놨기 때문에 5개만 
						?>
							<div class="left_bottom_layout2">
								<img class="left_emotion_size2" src='<?php echo IMGPATH.$item['em_path']; ?>'>
								<img class="left_star_size2" src="/todolist/doc/img/star_<?php echo $y; ?>.png">
								<p><?php echo $item['em_name']." : ".$item['cnt_em_id']; ?></p>
							</div>
						<?php
						}
						?>
					</div>
				</div>
			</div>
			
			<div class="right_box">
				<div class="box_layout list_position_rela">
					<div class="right_top">
						<p>Diary</p>
					</div>
					<table class="right_table">
						<colgroup>
							<col width= "12%">
							<col width= "88%">
						</colgroup>
						<?php
							// * 게시글 조회 부분 *
							$replace_before=['/-/','/-/'];
							$replace_after=['년 ','월 '];
							foreach ($result as $item) {
								$string = preg_replace($replace_before, $replace_after, $item['create_at'], 1)."일";
								$item_yoil=$yoil[date('w', strtotime($item['create_at']))]; //요일 출력하기 위한 세팅
						?>
							<tr class="table_tr1 table_cursor" onclick="location.href='/todolist/src/02_detail.php/?id=<?php echo $item['id']; ?>&page=<?php echo $page_num;?>'">
								<td class="table_emotion"><img src='<?php echo IMGPATH.$item['em_path']; ?>'></td>
								<td class="table_date"><?php echo $string; ?><br><?php echo $item_yoil; ?></td>
							</tr>
							<tr class="table_tr2 table_cursor" onclick="location.href='/todolist/src/02_detail.php/?id=<?php echo $item['id']; ?>&page=<?php echo $page_num;?>'">
								<td class="table_title"colspan="2"><?php echo $item['title']; ?></td>
							</tr>
						<?php
							}
						?>
					</table>
					<br><br>
					<div class="right_page list_position_paging">
						<a class="right_page_num hovor_bgc" href="/todolist/src/01_list.php/?page=<?php echo $prev_page_num; ?>"><<</a>
						<?php
							// * 페이징 부분 *
							$block_num=(int)ceil($page_num/5); // 블럭 페이지
							$block_first_num=(5*$block_num)-4; // 블럭 페이지 당 첫번째 페이지 값
							$present_num=$block_first_num-1;
							for($i = $block_first_num; $i <= $block_num*5; $i++) {
								$present_num+=1;
								
								if ($i > $max_page_num) {
									break;
								} //max page num 까지만 출력하는 if문

								$str = $page_num === $present_num ? "bgc_black" : "hovor_bgc";
								// 현재 페이지에 hovor 주기위해서 다른 클래스 적용
						?>	
							<a class="right_page_num <?php echo $str; ?>" href="/todolist/src/01_list.php/?page=<?php echo $i; ?>"><?php echo $i; ?></a>
						<?php
							}
						?>
						<a class="right_page_num hovor_bgc" href="/todolist/src/01_list.php/?page=<?php echo $next_page_num; ?>">>></a>
					</div>
				</div>
			</div>

			<div class="side_box">
				<div class="side_category bgc_cate1">
					<a class= "side_text" href="/todolist/src/03_insert.php">작성</a>
				</div>
			</div>
		</div>
	</div>
</body>
</html>