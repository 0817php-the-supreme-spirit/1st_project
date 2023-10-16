<?php
	require_once(ROOT."lib/lib_db.php");

	$conn = null;
	$http_method = $_SERVER["REQUEST_METHOD"];
	$arr_err_msg = []; // 에러 메세지 저장용

	try {
		if(!db_conn($conn))
		{
			//강제 예외 발생 : DB Instance
			throw new Exception("DB Error : PDO Instance");
		}

		// 데이터 베이스에서 유저의 사용 금액을 조회하는 함수
		$amount_used = db_select_amount_used($conn, $arr_param);
		if($amount_used === false) {
			throw new Exception("DB Error : select_user_table");
		}
		// $amount_used의 0번방에 있는 값들을 넣어주는 구문
		$amount_used = $amount_used[0];

		// 유저의 일일 급여를 조회하느 함수
		$user_data = db_select_user_table($conn);
		if($user_data === false) {
			throw new Exception("DB Error : select_user_table");
		}

		// 유저의 일일 급여의 0번 방에 있는 값을 넘겨줌
		$user_days = $user_data[0];

		//daily_selary에 있는 값을 다른 변수에 넘겨줌, 위와 아래는 통합 가능, 코드 리뷰를 위해 풀어서 정리 
		$user_days_percent = $user_days["daily_salary"];

		//유저의 사용 금액을 총합을 해당 변수로 넘김
		$amount_used_percent = $amount_used["amount_used"];

		// 사용 금액의 퍼센트를 구하는 계산식
		$percent = ($amount_used_percent / $user_days_percent) * 100;

		// 실수가 아닌 정수로 값을 보기 위해 데이터타입 변환
		$percent = (int)$percent;
		

	}
	catch(Exception $e) {
		echo $e->getMessage(); // 예외발생 메세지 출력
		exit; // 처리 종료
	}
	finally {
		
	}

?>



	<div class="side-right">
		<div class="side-right-box">
			<!-- if문을 통해 우측 사이드바 글귀와 이미지 변경 -->
			<div class="side-right-top">
				<?php if($percent >= 0 && $percent < 80) { ?>
					<p class="success">성 공!</p>
				<?php } else if($percent >= 80 && $percent < 99) { ?>
					<p class="danger">위 험!</p>
				<?php } else { ?>
					<p class="failure">실 패!</p>
				<?php } ?>
			</div>
			<div class="side-right-character">
				<?php if($percent >= 0 && $percent < 20) { ?>
					<div class="side-right-character-1"></div>
				<?php } else if($percent >= 20 && $percent < 40) { ?>
					<div class="side-right-character-2"></div>
				<?php } else if($percent >= 40 && $percent < 60) { ?>
					<div class="side-right-character-3"></div>
				<?php } else if($percent >= 60 && $percent < 80) { ?>
					<div class="side-right-character-4"></div>
				<?php } else if($percent >= 80 && $percent < 100) { ?>
					<div class="side-right-character-5"></div>
				<?php } else if($percent > 100) { ?>
					<div class="side-right-character-6"></div>
				<?php } ?>
			</div>
			<div class="side-right-bottom">
				<p>소비한 벨</p>
				<!-- 유저의 일일 총 사용금액과 하루 급여를 비교하여 게이지바와 값으로 출력 -->
				<progress id="progress" value="<?php echo $amount_used["amount_used"]; ?>" min="0" max="<?php echo $user_days["daily_salary"]; ?>"></progress>
				<div class="side-right-user">
					<p class="small">사용 벨 : <?php if($amount_used["amount_used"] == 0) { echo 0; } else { echo number_format($amount_used["amount_used"]); }?>원</p>
					<p class="small p_gpa">남은 벨 : <?php echo number_format($user_days["daily_salary"] - $amount_used["amount_used"]); ?>원</p>
					<div class="bar"></div>
					<p class="small p_gpa all">전체 벨 : <?php echo number_format($user_days["daily_salary"]); ?>원</p>
				</div>
			</div>

		</div>
	</div>
