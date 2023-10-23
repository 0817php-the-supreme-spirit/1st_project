<?php
function my_db_conn( &$conn ) {
    $db_host    = "192.168.0.142"; // host
    $db_user    = "team3"; // user
    $db_pw      = "team3"; //password
    $db_name    = "todolist"; // DB name
    $db_charset = "utf8mb4"; //charset
    $db_dns     = "mysql:host=".$db_host.";dbname=".$db_name.";charset=".$db_charset;

    try {
        $db_options = [
            // DB의 Prepared Statement 기능을 사용하도록 설정
            PDO::ATTR_EMULATE_PREPARES      => false
            // PDO Exception을 Throws 하도록 설정
            ,PDO::ATTR_ERRMODE              => PDO::ERRMODE_EXCEPTION
            // 연상배열로 Fetch를 하도록 설정
            ,PDO::ATTR_DEFAULT_FETCH_MODE   => PDO::FETCH_ASSOC
        ];

        // PDO Class로 DB 연동
        $conn = new PDO($db_dns, $db_user, $db_pw, $db_options);
        return true;
    } catch (Exception $e){
        $conn = null;
        return false;
    }
}

function db_destroy_conn(&$conn) {
    $conn = null;
}



function db_select_chal_conn(&$conn) {
try{
$sql = 
	" SELECT DISTINCT "
	." c_name "
	." ,l_name " 
	." ,c_id "
	." FROM "
	." chal_info "
	;

// $arr_ps = [
// 	":c_id" => $arr_param["c_id"]
// ];
$stmt= $conn->prepare($sql);
$stmt->execute();
$result=$stmt->fetchAll();

return $result;
}
catch(Exception $e){
	echo $e->getMessage();
	return false;
}
}
function db_insert_create_at(&$conn, &$arr_post) {
	$sql = 
	" INSERT INTO create_information ( "
	." c_id "
	." ) "
	." VALUES ( "
	." :c_id "
	." ) "
	;
		$arr_ps =[
		":c_id" => $arr_post["chk"]
		];
try{
		$stmt=$conn->prepare($sql);
		$result=$stmt->execute($arr_ps);
		return $result; //결과 리턴
	} catch (Exception $e){
	return false; //예외발생 : false 리턴
}
}


?>
