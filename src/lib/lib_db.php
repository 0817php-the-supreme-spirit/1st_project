<?php
	// ----------------------------
	// 함수명 	: my_db_conn
	// 기능 	: DB Connecy
	// 파라미터 : PDO &$conn
	// 리턴 	: 없음
	// ----------------------------

	function db_conn( &$conn )
	{
		$db_host 	= "localhost"; //host | 127.0.0.1 = localhost 
		$db_user 	= "team2"; // user
		$db_pw 		= "team2"; // password
		$db_name 	= "1st_project"; // DB name
		$db_charset = "utf8mb4"; // charset
		$db_dns		= "mysql:host=".$db_host.";dbname=".$db_name.";charset=".$db_charset;
	
		try
		{
			$db_options = [
			PDO::ATTR_EMULATE_PREPARES		=> false
			,PDO::ATTR_ERRMODE 				=> PDO::ERRMODE_EXCEPTION
			,PDO::ATTR_DEFAULT_FETCH_MODE 	=> PDO::FETCH_ASSOC
			];
		
			$conn = new PDO($db_dns, $db_user, $db_pw, $db_options);
			return true;
		}
		catch (Exception $e)
		{
			$conn = null;
			return false;
		}
	}





?>