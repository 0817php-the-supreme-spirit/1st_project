<!DOCTYPE html>
<html lang="ko">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="./css/style.css">
		<title>Document</title>
	</head>

	<body>

		<main>
			<div class="header">
				<a href=""><h1>: 아껴봐요 절약의 숲</h1></a>
			</div>

			<div class="side-left">
				<div class="side-left-box">
					<form action="list.html/?date=" method="post">
						<table>
							<!-- <input class="date-box" type="date" required value={props.date} onChange={props.changeHandler}> -->
							<input class="date-box" type="date">
						</table>
					</form>

					<div class="side-left-line-1"></div>

					<a href=""><div class="side-left-page side-left-on"><p>오늘의 지출</p></div></a>
					<a href=""><div class="side-left-page side-left-off"><p>지출 작성부</p></div></a>
					<a href=""><div class="side-left-page side-left-off"><p>지출 통계서</p></div></a>

					<div class="side-left-line-2"></div>

					<form action="" method="post">
						<input type="radio" name="category" id="category1">
						<label for="category1" class="category-box">전체 비용</label>
				
						<input type="radio" name="category" id="category2">
						<label for="category2" class="category-box">생활 비용</label>
				
						<input type="radio" name="category" id="category3">
						<label for="category3" class="category-box">활동 비용</label>
				
						<input type="radio" name="category" id="category4">
						<label for="category4" class="category-box">멍청 비용</label>
					</form>

				</div>
			</div>

			<div class="content">
				<div class="content-box">
					<div class="content-date-box">
						<p>날짜</p>
					</div>

					<div class="content-title-box">
						<div class="content-title-box-1">
							<p>제목</p>
						</div>

						<div class="content-title-box-2">
							<p>제목 값</p>
						</div>
					</div>

					<div class="content-memo-box">
						<div class="content-memo-box-1">
							<p>메모</p>
						</div>

						<div class="content-memo-box-2">
							<p>메모 값</p>
						</div>
					</div>

					<div class="content-value-box">
						<div class="content-user-box">
							<div class="content-user-amount">
								<p>일일 사용 금액 : 20000</p>
							</div>
	
							<div class="content-user-remaining">
								<p>일일 남은 금액 : 20000</p>
							</div>
						</div>

						<div class="content-phrases-box">
							<p>파이어족이</p>
							<p>될꺼야?</p>
						</div>
					</div>

					<div class="content-btn-box">
						<div class="content-btn-before">
							<a href="">이전</a>
						</div>
						<div class="content-btn-correction">
							<a href="">수정</a>
						</div>
						<div class="content-btn-delete">
							<a href="">삭제</a>
						</div>

					</div>
					
				</div>
			</div>

			<div class="side-right">
				<div class="side-right-box">
					
					<div class="side-right-top"><p>성 공!</p></div>
					<div class="side-right-character"></div>
					<div class="side-right-bottom">
						<p>소비한 벨</p>
						<meter value="15" min="0" max="100" optimum="15" id="meter"></meter>
						<p>사용 금액 / 전체 금액</p>
					</div>

				</div>
			</div>
		</main>
		
	</body>
</html>