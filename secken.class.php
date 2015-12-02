<?php
/**
 * Server SDK for PHP
 * SDK Server Api v1.0
 *
 * 洋葱开放API文档
 * https://www.yangcong.com/api
 *
 **/

class secken {

    //应用id
    private $app_id = '';

    //应用Key
    private $app_key = '';

    //api请求地址
    const BASE_URL              = 'https://api.sdk.yangcong.com/';

    //获取通过洋葱进行验证的二维码
    const QRCODE_FOR_AUTH       = 'qrcode_for_auth';

    //根据 event_id 查询详细事件信息
    const EVENT_RESULT          = 'event_result';

    //请求洋葱推送验证
    const REALTIME_AUTH         = 'realtime_authorization';
    
    //复验洋葱验证结果
    const CHECK_AUTHTOKEN       = 'query_auth_token';

    /**
     * 错误码
     * @var array
     */
    private $errorCode = array(
        200 => '请求成功',
        201 => '通知用户，等待用户操作',
        400 => '请求参数格式错误',
        403 => '请求签名错误',
        404 => '请求API不存在',
        406 => '不在应用白名单里',
        407 => '请求超时',
        500 => '系统错误',
        501 => '获取二维码错误',
        601 => '用户拒绝授权',
        602 => '等待用户响应',
        603 => '等待用户响应超时',
        604 => '用户或event_id不存在',
        605 => '用户未开启该验证类型',
        606 => '已使用回调函数获取结果，不支持直接查询',
        607 => '用户不存在',
    );

    /**
     * 初始化
     */
    public function __construct($app_id, $app_key) {
        $this->app_id   = $app_id;
        $this->app_key  = $app_key;
    }

    /**
     * 获取二维码内容和事件标识
     * @param
     * auth_type       Int    验证类型(可选)（1: 点击确认按钮,默认 3: 人脸验证 4: 声音验证）
     * action_type     String 该操作的名称，比如支付(可选)
     * action_details  String 该操作的具体详情，比如什么方面的支付(可选)
     * callback        String 回调地址(可选)
     * @return array
     * status       Int     状态码
     * description  String  状态码对应描述信息
     * qrcode_url   String  二维码地址
     * qrcode_data  String  二维码图片的字符串内容
     * event_id     String  事件ID,可调用event_result API来获取扫描结果,如果设置了callback，则无法获取扫描结果
     * signature    String  签名，可保证数据完整性
     */
    public function getQrCode($auth_type = 1,$action_type = '',$action_details = '',$callback = '') {
        $data   = array();
        $data   = array(
            'app_id'    => $this->app_id
        );

        if( $auth_type ) $data['auth_type'] = intval($auth_type);
        if( $action_type ) $data['action_type'] = urlencode($action_type);
        if( $action_details ) $data['action_details'] = urlencode($action_details);
        if( $callback ) $data['callback'] = urlencode($callback);

        $data['signature'] = $this->getSignature($data);

        $url    = $this->gen_get_url(self::QRCODE_FOR_AUTH, $data);
        $ret    = $this->request($url);

        return $this->prettyRet($ret);
    }

    /**
     * 查询验证事件的结果
     * @param
     * event_id     String   事件id
     * signature    String   签名，用于确保客户端提交数据的完整性
     * @return array
     * status       Int     状态码
     * description  String  状态码对应描述信息
     * event_id     String  事件ID,可调用event_result API来获取扫描结果,如果设置了callback，则无法获取扫描结果
     * uid          String  第三方应用的用户ID，针对单个第三方应用，不可重名。
     * signature    String  签名，可保证数据完整性
     */
    public function getResult($event_id) {
        $data   = array();
        $data   = array(
            'app_id'    => $this->app_id,
            'event_id'  => $event_id
        );

        $data['signature'] = $this->getSignature($data);

        $url    = $this->gen_get_url(self::EVENT_RESULT, $data);
        $ret    = $this->request($url);

        return $this->prettyRet($ret);
    }

    /**
     * 发起推送验证事件
     * @param
     * uid             String  第三方应用的用户ID，针对单个第三方应用，不可重名。
     * auth_type       Int     验证类型（1: 点击确认按钮 3: 人脸验证 4: 声音验证）
     * action_type     String  该操作的名称，比如支付(可选)
     * action_details  String  该操作的具体详情，比如什么方面的支付(可选)
     * callback        String  回调地址，当用户同意或拒绝验证的后续处理（可选）
     * signature       String  签名，用于确保客户端提交数据的完整性
     * @return array
     * status        Int     状态码
     * description   String  状态码对应描述信息
     * event_id      String  事件ID,可调用event_result API来获取扫描结果,如果设置了callback，则无法获取扫描结果
     * signature     String  签名，可保证数据完整性
     */
    public function askPushAuth($uid, $auth_type = 1,$action_type = '',$action_details = '',$callback='') {
        $data   = array();
        $data   = array(
            'app_id'        => $this->app_id,
            'uid'           => $uid,
            'auth_type'     => intval($auth_type)
        );

        if ( $action_type )  $data['action_type']  = $action_type;
        if ( $action_details )  $data['action_details']  = $action_details;
        if ( $callback ) $data['callback'] = urlencode($callback);

        $data['signature'] = $this->getSignature($data);

        $url    = self::BASE_URL . self::REALTIME_AUTH;

        $ret    = $this->request($url, 'POST', $data);

        return $this->prettyRet($ret);
    }

    /**
     * 复验洋葱验证结果
     * @param
     * auth_token   String  验证id
     * signature    String  签名，用于确保客户端提交数据的完整性
     * @return array
     * status       Int     状态码
     * description  String  状态码对应描述信息
     * signature    String  签名，可保证数据完整性
     */
    public function checkAuthToken($auth_token) {
        $data   = array();
        $data   = array(
            'app_id'    => $this->app_id,
            'auth_token'  => $auth_token
        );

        $data['signature'] = $this->getSignature($data);

        $url    = $this->gen_get_url(self::CHECK_AUTHTOKEN, $data);
        $ret    = $this->request($url);

        return $this->prettyRet($ret);
    }
    
    /**
     * 生成签名
     * @param
     * params  Array  要签名的参数
     * @return String 签名的MD5串
     */
    private function getSignature($params) {
        ksort($params);
        $str = '';

        foreach ( $params as $key => $value ) {
            $str .= "$key=$value";
        }

        return sha1($str . $this->app_key);
    }

    /**
     * 返回错误消息
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * 返回错误码
     * @return string
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * 处理返回信息
     * @return Mix
     */
    private function prettyRet($ret) {
        if ( is_string($ret) ) {
            return $ret;
        }

        $this->code = isset($ret['status'])? $ret['status'] : false;

        if(isset($this->errorCode[$this->code])){
            $this->message = $this->errorCode[$this->code];
        }else{
            $this->message = isset($ret['description']) ? $ret['description'] : 'UNKNOW ERROR';
        }

        return $ret;
    }


    /**
     * 生成请求连接，用于发起GET请求
     * @param
     * action_url    String    请求api地址
     * data          Array     请求参数
     * @return String
     **/
    private function gen_get_url($action_url, $data) {
        return self::BASE_URL . $action_url. '?' . http_build_query($data);
    }


    /**
     * 发送HTTP请求到洋葱服务器
     * @param
     * url      String  API 的 URL 地址
     * method   Sting   HTTP方法，POST | GET
     * data     Array   发送的参数，如果 method 为 GET，留空即可
     * @return  Mix
     **/
    private function request($url, $method = 'GET', $data = array()) {
        if ( !function_exists('curl_init') ) {
            die('Need to open the curl extension');
        }

        if ( !$url || !in_array($method, array('GET', 'POST')) ) {
            return false;
        }

        $ci = curl_init();

        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_USERAGENT, 'Server SDK for PHP v1.0 (yangcong.com)');
        curl_setopt($ci, CURLOPT_HEADER, FALSE);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_TIMEOUT, 30);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);

        if ( $method == 'POST' ) {
            curl_setopt($ci, CURLOPT_POST, TRUE);
            curl_setopt($ci, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $response   = curl_exec($ci);

        if ( curl_errno($ci) ) {
            return curl_error($ci);
        }

        $ret    = json_decode($response, true);
        if ( !$ret ) {
            return 'response is error, can not be json decode: ' . $response;
        }

        return $ret;
    }

}
