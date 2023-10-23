<?php
define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/todolist/src/");
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/todolist/src/css/style.css">
    <title>메인페이지</title>
</head>
<body>
	<a class="main_a" href="/todolist/src/list.php">
		<div class="main_jpg">
			<div class="main_msg">아무 곳이나 클릭해 주세요</div>
			<div class="zzz zzz-zzz">Z</div>
			<div class="zzz zzz-zz">Z</div>
			<div class="zzz zzz-z">Z</div>
			<div class="main_div">꿀잠</div>
			<img class="main_img_moon" src="/todolist/src/img/moon.png">
		</div>
		</a>
    </body>

</html>
<!-- define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/todolist/src/");: PHP 코드로 시작하며, 상수 ROOT를 정의합니다. 이 상수는 현재 서버의 웹 루트 디렉토리 경로를 저장합니다. /todolist/src/는 웹 애플리케이션의 루트 디렉토리에 대한 하위 경로를 나타냅니다.

HTML 문서 시작: <!DOCTYPE html>은 HTML5 문서임을 나타내며, 한국어 언어로 작성되었습니다.

<head> 섹션: 웹 페이지의 헤더 부분으로, 문자 인코딩 및 뷰포트 설정을 정의하고 외부 스타일 시트를 연결합니다. 페이지 제목은 "메인페이지"로 설정됩니다.

<body> 섹션: 웹 페이지의 본문 내용이 시작됩니다.

<a> 요소: 화면의 어느 곳이든 클릭하면 메인 페이지로 이동할 수 있는 하이퍼링크를 생성합니다. 이 링크는 /todolist/src/list.php 페이지로 이동합니다.

.main_jpg: 이 클래스를 가진 div 요소는 메인 페이지의 가운데 정렬된 컨테이너로, 페이지를 클릭하라는 안내 메시지와 "Z"로 된 잠자는 얼굴이 표시됩니다.

.main_msg: 이 클래스를 가진 div 요소는 "아무 곳이나 클릭해 주세요"라는 안내 메시지를 표시합니다.

.zzz: 이 클래스를 가진 세 개의 div 요소는 "Z"로 된 잠자는 얼굴을 나타냅니다. 각 div는 "Z"가 하나씩 나타납니다.

.main_div: 이 클래스를 가진 div 요소에는 "꿀잠"이라는 메시지가 표시됩니다.

.main_img_moon: 이 클래스를 가진 img 요소는 /todolist/src/img/moon.png 이미지를 표시합니다. -->