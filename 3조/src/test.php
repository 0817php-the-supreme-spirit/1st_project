<?php

print_r($_POST);

?>


<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
	<form action="test.php" method="post">
		<button type="submit" value="test" name="a">a</button>
		<button name="a">b</button>
	</form>
</body>
</html>