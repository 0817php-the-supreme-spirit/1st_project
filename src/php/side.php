
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
					<p class="small p_gpa all">전체 벨 :  <?php echo number_format($user_days["daily_salary"]);?>원</p>
				</div>
			</div>

		</div>
	</div>
