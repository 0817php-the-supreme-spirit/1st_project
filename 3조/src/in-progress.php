<?php

define("ROOT", $_SERVER["DOCUMENT_ROOT"]."/project1/src/");
define("FILE_HEADER", ROOT."html/header.html");
define("FILE_STATUS", ROOT."status.php");
define("FILE_CHALLENGE", ROOT."challenge_bar.php");
require_once(ROOT."lib/in_lib.php");
require_once(ROOT."lib/bar_lib.php");

$com = [];
$conn = null;
$flg_tran = false;
$arr_get = [];
$err_msg = [];
$arr_post = [];

try{
	if(!my_db_conn($conn)) {
		// DB Instance 에러
		throw new Exception("DB Error : PDO Instance");
	}
	
	$http_method = $_SERVER["REQUEST_METHOD"];
	if($http_method === "POST") {
		$arr_post["create_id"] = isset($_POST["create_id"]) ? $_POST["create_id"] : "";
		$arr_post["l_id"] = isset($_POST["l_id"]) ? $_POST["l_id"] : "";

		$flg_tran = $conn->beginTransaction();

		$com_check = db_select_complete_check($conn, $arr_post);
		if($com_check === false) {
			throw new Exception("complete_check Error");
		}

		$complete_list = $com_check[0]["l_com_at".$arr_post["l_id"]];

		if($complete_list != NULL) {
			if(db_complete_cancel($conn, $arr_post) === false) {
				throw new Exception("complete_list cancel Error");
			}

			$complete_count = db_complete_count($conn, $arr_post);
			if($complete_count === false) {
				throw new Exception("complete count Error");
			}

			if($complete_count[0]["cnt"] < 4) {
				if(db_complete_at_cancel($conn, $arr_post) === false) {
					throw new Exception("complete_cancel Error");
				}
			}
		} else {
			$com_list = db_complete_list($conn, $arr_post);
			if($com_list === false) {
			throw new Exception("complete_list Error");
			}

			$com_check = db_select_complete_check($conn, $arr_post);
			if($com_check === false) {
				throw new Exception("complete_check Error");
			}
			if($com_check[0]["l_com_at1"] != "" && $com_check[0]["l_com_at2"] != "" && $com_check[0]["l_com_at3"] != "" && $com_check[0]["l_com_at4"] != "") {
				$c_com = ["create_id" => $com_check[0]["create_id"]];
				if(db_complete_at($conn, $c_com) === false) {
					throw new Exception("complete_at Error");
				}
			}
		}
		$conn->commit();
		$arr_get = $arr_post;
	}
	
	$challenge_first = db_challenge_first($conn);
	if($challenge_first === false) {
		// DB Instance 에러
		throw new Exception("challenge_first Error");
	}

	if(isset($_GET["create_id"])) {
		$arr_get["create_id"] = $_GET["create_id"];
	} else if (isset($arr_get["create_id"])) {
		$arr_get["create_id"] = $arr_get["create_id"];
	} else {
		$arr_get["create_id"] = $challenge_first[0]["create_id"];
	}

	$list = db_select_list($conn, $arr_get);
	if($list === false) {
		// DB Instance 에러
		throw new Exception("list Error");
	}
	if(count($list) === 0) {
		$err_msg[] = "error";
	}
	if(count($err_msg) === 1) {
		header("Location: in-progress_error.php");
		exit;
	}

	$list_per = db_complete_num($conn, $arr_get);
	if($list_per === false) {
		throw new Exception("list_name Error");
	}
	
} catch(Exception $e) {
	if(!$flg_tran) {
		$conn->rollBack();
	}
	echo $e->getMessage(); // Exception 메세지 출력
	exit;
} finally {
	db_destroy_conn($conn);
}

$in_progress_c_id = $arr_get["create_id"];
?>

<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="./css/in-progress.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Black+Han+Sans&family=Nanum+Pen+Script&family=Noto+Sans+KR:wght@300;400&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="./css/header.css">
	<link rel="stylesheet" href="./css/status.css">
	<link rel="stylesheet" href="./css/challenge_bar.css">
	<title>in-progress</title>
</head>
<body>
	<?php
    require_once(FILE_HEADER);
	require_once(FILE_STATUS);
    ?>
	<section class="section-in">
		<form class="form-in" action="in-progress.php" method="post">
			<p class="create_at"><?php echo $list[0]["DATE(cr.c_created_at)"]; ?></p>

			<p class="ch-name"><?php echo $list[0]["c_name"]; ?></p>
			<?php
			if($list_per[0]["per"] === 100) { ?> 
				<progress class="progress-com" value="<?php echo $list_per[0]["per"]; ?>" max="100"></progress>
			<?php } else { ?>
				<progress class="progress" value="<?php echo $list_per[0]["per"]; ?>" max="100"></progress>
			<?php } ?>
			<?php
			foreach($list as $item) { ?>
			<input type="hidden" name="create_id" value="<?php echo $item["create_id"] ?>">
			<?php
			if($item["l_id"] == 1 && $item["l_com_at1"] != "") {
			?>	<button class="button-com" name="l_id" value="<?php echo $item["l_id"];?>"><?php
			} else if($item["l_id"] == 2 && $item["l_com_at2"] != "") {
			?>	<button class="button-com" name="l_id" value="<?php echo $item["l_id"];?>"><?php
			} else if($item["l_id"] == 3 && $item["l_com_at3"] != "") {
			?>	<button class="button-com" name="l_id" value="<?php echo $item["l_id"];?>"><?php
			} else if($item["l_id"] == 4 && $item["l_com_at4"] != "") {
			?>	<button class="button-com" name="l_id" value="<?php echo $item["l_id"];?>"><?php
			} else {
			?>	<button class="button-in" name="l_id" value="<?php echo $item["l_id"];?>"><?php
			}
			?>
			<p class="pro-menu" ><?php echo $item["l_name"] ?></p>
			<p class="pro-clear"><?php if($item["l_id"] == 1 && $item["l_com_at1"] != "") {
				echo "1/1";
			} else if($item["l_id"] == 2 && $item["l_com_at2"] != "") {
				echo "1/1";
			} else if($item["l_id"] == 3 && $item["l_com_at3"] != "") {
				echo "1/1";
			} else if($item["l_id"] == 4 && $item["l_com_at4"] != "") {
				echo "1/1";
			} else {
				echo "0/1";
			} ?></p>
		</button>
		<?php } ?>
	</form>
	<form action="delete.php" method="get">
		<input type="hidden" name="page_flg" value="0">
		<button type="submit" name="create_id" value="<?php echo $arr_get["create_id"]; ?>" class="trash"></button>
	</form>
	</section>
	<?php
	require_once(FILE_CHALLENGE);
	?>
</body>
</html>