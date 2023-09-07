<?php
$print = false;
/*if($print){
	error_log("Entered block.php", 0);
}*/
if(isset($_POST) && is_array($_POST) && !empty($_POST)){
	$user = "writer-public";
	$password = PUBLIC_WRITER_PASSWORD;
}else{
	$user = 'reader-public';
	$password = PUBLIC_READER_PASSWORD;
}
$db = \JulianSeymour\PHPWebApplicationFramework\security\condemn\CondemnedIpAddress::getDatabaseNameStatic();
$table = \JulianSeymour\PHPWebApplicationFramework\security\condemn\CondemnedIpAddress::getTableNameStatic();
$mysqli = new mysqli("localhost", $user, $password, $db);
$drop = function()use($mysqli, $print){
	$mysqli->close();
	unset($mysqli);
	unset($print);
	foreach($GLOBALS as $key => $v){
		unset($GLOBALS[$key]);
	}
	unset($GLOBALS);
	$mem1 = memory_get_usage();
	error_log("Using {$mem1} memory before sleep");
	unset($mem1);
	sleep(60 * 60 * 24 * 365);
	header("HTTP/1.0 404 Not Found");
	exit();
};
if($mysqli->connect_errno){
	error_log("Failed to connect to MySQL: ({$mysqli->connect_errno}) {$mysqli->connect_error}", 0);
	$drop();
}elseif(!$mysqli->ping()){
	error_log("mysqli failed ping test", 0);
	$drop();
}
if($st = $mysqli->prepare("select ipAddress from `{$db}`.`{$table}` where ipAddress=?")){
	/*if($print){
		error_log("Successfully prepared statement", 0);
	}*/
}else{
	error_log("Failed to prepare statement", 0);
	$drop();
}
$bound = $st->bind_param('s', $_SERVER['REMOTE_ADDR']);
if(!$bound){
	error_log("Failed to bind parameters", 0);
	$drop();
}elseif(!$st->execute()){
	error_log("Failed to execute statement", 0);
	$drop();
}/*elseif($print){
	error_log("Successfully bound parameters and executed statement", 0);
}*/
if($result = $st->get_result()){
	if($result->num_rows > 0){
		error_log("IP address {$_SERVER['REMOTE_ADDR']} is banned. Attempting to access \"{$_SERVER['REQUEST_URI']}\"", 0);
		//$drop();
	}elseif($print){
		error_log("IP address {$_SERVER['REMOTE_ADDR']} is not banned", 0);
	}
}else{
	error_log("Failed to get result", 0);
	$drop();
}