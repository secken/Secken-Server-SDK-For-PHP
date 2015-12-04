<?php

    include_once 'secken.class.php';

    // login YangCong Developer Center, A new sdk application can be created in secken Dashboard, you can get appid、 appkey
    $app_id     = 'app_id';
    $app_key    = 'app_key';

    // Create an API object using your credentials
    $secken_api = new secken($app_id,$app_key);

    # Step 1 - Get an qrcode for binding
    $ret  = $secken_api->getQrCode(1,"test","action to pay", "https://callback.com/path");

    //$ret = $secken_api->askPushAuth('zhangsan', 1, "test","action to delete", 'https://callback.com/path');

    //$ret = $secken_api->checkAuthToken('asdfh34dgvfhy6gfg45vcv');

    # Step 2 - Check the returned result
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

?>
