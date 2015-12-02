<?php

    include_once 'secken.class.php';

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
        var_dump($secken_api->getCode(), $secken_api->getMessage());

    } else {
        var_dump($ret);
    }

?>
