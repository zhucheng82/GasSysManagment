<?php
class RonghubApi{

    //public $appKey='z3v5yqkbzcnf0';//appKey//开发环境
    public $appKey='vnroth0kvp9qo';//appKey//生成环境
    //public $appSecret='4dR5Mzy7y4DH';//secret//开发环境
    public $appSecret='L36D5cU9Oc';//secret//生成环境

    const   SERVERAPIURL = 'https://api.cn.ronghub.com';    //请求服务地址
    public $format;                //数据格式 json/xml


    /**
     * 参数初始化
     * @param $appKey
     * @param $appSecret
     * @param string $format
     */
    public function __construct($appKey='vnroth0kvp9qo',$appSecret='L36D5cU9Oc',$format = 'json'){

        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->format = $format;
    }

    /**
     * 获取 Token 方法
     * @param $userId   用户 Id，最大长度 32 字节。是用户在 App 中的唯一标识码，必须保证在同一个 App 内不重复，重复的用户 Id 将被当作是同一用户。
     * @param $name     用户名称，最大长度 128 字节。用来在 Push 推送时，或者客户端没有提供用户信息时，显示用户的名称。
     * @param $portraitUri  用户头像 URI，最大长度 1024 字节。
     * @return json|xml
     */
    public function getToken($userId, $name, $portraitUri) {
        try{
            if(empty($userId))
                throw new Exception('用户 Id 不能为空');
            if(empty($name))
                throw new Exception('用户名称 不能为空');
            if(empty($portraitUri))
                throw new Exception('用户头像 URI 不能为空');

            $ret = $this->curl('/user/getToken',array('userId'=>$userId,'name'=>$name,'portraitUri'=>$portraitUri));
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
            print_r($e->getMessage());
        }
    }



    /**
     * 获取 APP 内指定某天某小时内的所有会话消息记录的下载地址
     * @param $date     指定北京时间某天某小时，格式为：2014010101,表示：2014年1月1日凌晨1点。（必传）
     * @return json|xml
     */
    public function messageHistory($date) {
        try{
            if(empty($date))
                throw new Exception('时间不能为空');
            $ret = $this->curl('/message/history', array('date' => $date));
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

    /**
     * 删除 APP 内指定某天某小时内的所有会话消息记录
     * @param $date string 指定北京时间某天某小时，格式为2014010101,表示：2014年1月1日凌晨1点。（必传）
     * @return mixed
     */
    public function messageHistoryDelete($date) {
        try{
            if(empty($date))
                throw new Exception('时间 不能为空');
            $ret = $this->curl('/message/history/delete', array('date' => $date));
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

    /**
     *刷新用户信息 方法  说明：当您的用户昵称和头像变更时，您的 App Server 应该调用此接口刷新在融云侧保存的用户信息，以便融云发送推送消息的时候，能够正确显示用户信息
     * @param $userId   用户 Id，最大长度 32 字节。是用户在 App 中的唯一标识码，必须保证在同一个 App 内不重复，重复的用户 Id 将被当作是同一用户。（必传）
     * @param string $name  用户名称，最大长度 128 字节。用来在 Push 推送时，或者客户端没有提供用户信息时，显示用户的名称。
     * @param string $portraitUri   用户头像 URI，最大长度 1024 字节
     * @return mixed
     */
    public function userRefresh($userId,$name='',$portraitUri='') {
        try{
            if(empty($userId))
                throw new Exception('用户 Id 不能为空');
            if(empty($name))
                throw new Exception('用户名称不能为空');
            if(empty($portraitUri))
                throw new Exception('用户头像 URI 不能为空');
            $ret = $this->curl('/user/refresh',
                array('userId' => $userId, 'name' => $name, 'portraitUri' => $portraitUri));
            if(empty($ret))
                throw new Exception('请求失败');
            return $ret;
        }catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

    /**
     * 创建http header参数
     * @param array $data
     * @return bool
     */
    private function createHttpHeader() {
        $nonce = mt_rand();
        $timeStamp = time();
        $sign = sha1($this->appSecret.$nonce.$timeStamp);
        return array(
            'RC-App-Key:'.$this->appKey,
            'RC-Nonce:'.$nonce,
            'RC-Timestamp:'.$timeStamp,
            'RC-Signature:'.$sign,
        );
    }

    /**
     * 重写实现 http_build_query 提交实现(同名key)key=val1&key=val2
     * @param array $formData 数据数组
     * @param string $numericPrefix 数字索引时附加的Key前缀
     * @param string $argSeparator 参数分隔符(默认为&)
     * @param string $prefixKey Key 数组参数，实现同名方式调用接口
     * @return string
     */
    private function build_query($formData, $numericPrefix = '', $argSeparator = '&', $prefixKey = '') {
        $str = '';
        foreach ($formData as $key => $val) {
            if (!is_array($val)) {
                $str .= $argSeparator;
                if ($prefixKey === '') {
                    if (is_int($key)) {
                        $str .= $numericPrefix;
                    }
                    $str .= urlencode($key) . '=' . urlencode($val);
                } else {
                    $str .= urlencode($prefixKey) . '=' . urlencode($val);
                }
            } else {
                if ($prefixKey == '') {
                    $prefixKey .= $key;
                }
                if (is_array($val[0])) {
                    $arr = array();
                    $arr[$key] = $val[0];
                    $str .= $argSeparator . http_build_query($arr);
                } else {
                    $str .= $argSeparator . $this->build_query($val, $numericPrefix, $argSeparator, $prefixKey);
                }
                $prefixKey = '';
            }
        }
        return substr($str, strlen($argSeparator));
    }

    /**
     * 发起 server 请求
     * @param $action
     * @param $params
     * @param $httpHeader
     * @return mixed
     */
    public function curl($action, $params,$contentType='urlencoded') {
        $action = self::SERVERAPIURL.$action.'.'.$this->format;
        $httpHeader = $this->createHttpHeader();
        //echo "<pre>";print_r($httpHeader);exit();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $action);
        curl_setopt($ch, CURLOPT_POST, 1);
        if ($contentType=='urlencoded') {
            $httpHeader[] = 'Content-Type:application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->build_query($params));
        }
        if ($contentType=='json') {
            $httpHeader[] = 'Content-Type:Application/json';
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params) );
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false); //处理http证书问题
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $ret = curl_exec($ch);
        if (false === $ret) {
            $ret =  curl_errno($ch);
        }
        curl_close($ch);
        return $ret;
    }
}
