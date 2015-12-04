# Secken Private Cloud Server SDK For PHP

A php library for using the Secken API.

## Prerequisites

 * PHP 5.3 or above
 * Secken Account and An application

 Download [here](https://www.yangcong.com/download) secken client, create account, and log in secken Dashboard.
 A new sdk application can be created in secken Dashboard, you can get appid、 appkey

## Overview

Secken provides a simple and secure authentication service. Secken APIs can be integrated by any application to enforce the security of user accounts.

The PHP library is an easy-to-use tool, which allows the developers to access Secken more effectively.

For more detailed information, you can find [here](https://www.yangcong.com/api/).

## How To Use
### Initialize

	include_once 'secken.class.php';

    $app_id     = 'app_id';
    $app_key    = 'app_key';


### Creating a instance

	$secken_api = new secken($app_id,$app_key);

### Request a QRCode for Auth Or Binding

If the request is successful, will return the qrcode url,
and a single event_id correspond to the qrcode,the event_id will use in the getResult interface.

    $ret  = $secken_api->getQrCode();

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

### Request a user Push Authentication

When calling this method, the server will push a verifying request to client’s mobile device, the client can select allowing or refusing this operation.

    $ret  = $secken_api->askPushAuth($uid);

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


### Get event results

Once the methods like getQrCode() and askPushAuth() are called successfully, it triggers a special event, which is identified by a unique event_id.

    $ret  = $secken_api->getResult($event_id);

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

Regarding the event_id, this method returns a status code and informs those methods which value should be returned. A list of status code is described below:

* 200 ok
getQrCode() or askPushAuth() return a value called uid. which represents the uid has been verified.

* 602 re-inquiry
The event is still in period of validity. This method requests event_id repeatedly.

* 603 invalid
The event is out of date. This method cancels requesting event_id.

### Check authtoken results

Once the sdk client methods like authFace() are called successfully, it triggers a special token, which is identified by a unique authtoken.

    $ret  = $secken_api->checkAuthToken($auth_token);

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

##Status Code

#####Success

* 200 - ok
* 201 - wait for user authentication

#####Client Error

* 400 - requested parameter format not satisfiable
* 403 - requested signature error
* 404 - requested API not exist
* 406 - not in application whitelists
* 407 - Too many requests in 30s

#####Server Error

* 500 - service unavailable
* 501 - failed generating QR code
* 601 - refuse authorization
* 602 - wait for user's response
* 603 - response timeout
* 604 - user or eventid not exist
* 605 - user is not support the authentication type
* 606 - used callback mode
* 607 - user is not exist

## Contact

web：[www.yangcong.com](https://www.yangcong.com)

Email: [support@secken.com](mailto:support@secken.com)
