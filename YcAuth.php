<?php

include_once 'secken.class.php';

$app_id     = 'IXgdZ1A7CFUej2ytUbVjFJKS5ICiorw4';
$app_key    = 'ELD0DNzMYep7m6Uo1v3v';

$secken_api = new secken($app_id, $app_key);

if ($_GET["Action"] == "GetYcAuthQrCode") {
	$ret  = $secken_api->getQrCode(3);
	if ( $secken_api->getCode() != 200 ){
		$arr = array(
			'status'=> $secken_api->getCode(),
			'description' => $secken_api->getMessage(),
			);
		$json = json_encode($arr);
		echo $json;
	} else {
		$json = json_encode($ret);
		echo $json;
	}
	return;
}


if ($_GET["Action"] == "CheckYcAuthResult") {
	$event_id = $_GET["eid"];

	$ret  = $secken_api->getResult($event_id);

	if ( $secken_api->getCode() != 200 ){
		//var_dump($secken_api->getCode(), $secken_api->getMessage());
		$arr = array(
			'status'=> $secken_api->getCode(),
			'description' => $secken_api->getMessage(),
			);
		$json = json_encode($arr);
		echo $json;
	} else {
		$json = json_encode($ret);
		echo $json;
	}

	return;
}

if ($_GET["Action"] == "AskYangAuthPush") {
	$uid = $_GET["uid"];

	$ret = $secken_api->askPushAuth($uid, 3);

	if ( $secken_api->getCode() != 200 ){
		$arr = array(
			'status'=> $secken_api->getCode(),
			'description' => $secken_api->getMessage(),
			);
		$json = json_encode($arr);
		echo $json;
	} else {
		$json = json_encode($ret);
		echo $json;
	}

	return;
}

if ($_GET["Action"] == "YangAuthTokenLogin") {
	$auth_token = $_GET["authtoken"];

    $ret = $secken_api->checkAuthToken($auth_token);

	if ( $secken_api->getCode() != 200 ){
		$arr = array(
			'status'=> $secken_api->getCode(),
			'description' => $secken_api->getMessage(),
			);
		$json = json_encode($arr);
		echo $json;
	} else {
		$json = json_encode($ret);
		echo $json;
	}

}
