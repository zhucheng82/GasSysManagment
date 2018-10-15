<?php

/* *
 * 支付宝接口函数库
 */

class AlipayApp
{

    public $respond_name = 'AlipayApp';

    public function __construct()
    {
        $this->private_key_path = '../application/libraries/alipay/rsa_private_key.pem';//商户私钥文件路径
        $this->public_key_path = '../application/libraries/alipay/rsa_public_key.pem';////商户公钥文件路径（检验才用到,其他不用）
        $this->ali_public_key_path = '../application/libraries/alipay/alipay_public_key.pem';////商户公钥文件路径（检验才用到,其他不用）

    }

    /**
     * RSA签名
     * @param $data 待签名数据
     * @param $private_key_path 商户私钥文件路径
     * return 签名结果
     */
    public function rsaSign($data, $private_key_path)
    {
        $priKey = file_get_contents($private_key_path);
        $res = openssl_get_privatekey($priKey);
        openssl_sign($data, $sign, $res);
        openssl_free_key($res);
        //base64编码
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * RSA验签
     * @param $data 待签名数据
     * @param $ali_public_key_path 支付宝的公钥文件路径
     * @param $sign 要校对的的签名结果
     * return 验证结果
     */
    public function _rsaVerify($data, $sign)
    {
        $pubKey = file_get_contents($this->public_key_path);
        $res = openssl_get_publickey($pubKey);
        $dsign = base64_decode($sign);
        $result = openssl_verify($data, $dsign, $res);
        openssl_free_key($res);
        log_message('debug','RSA result:'.$result);
        return (bool)$result;
    }

    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    function argSort($para)
    {
        ksort($para);
        reset($para);
        return $para;
    }


    /**
     * RSA签名
     * @param $sign 签名结果
     * @param
     * return 验证结果
     */
    public function getAlipay($data)
    {
        $ali_Arr = array(
            'partner' => config_item('partner'),
            'seller_id' => config_item('seller_id'),
            'out_trade_no' => $data['fund_order_id'],//订单号
            'subject' => $data['title'],
            'body' => $data['title'],
            'total_fee' => $data['amount'],//总金额
            'notify_url' => $data['notice_url'],//回调地址urlencode
            'service' => 'mobile.securitypay.pay',//请勿改动
            'payment_type' => 1,//支付类型
            '_input_charset' => 'utf-8',//请勿改动
            'it_b_pay' => '30m',//请勿改动
        );

        $ali_Arr = $this->argSort($ali_Arr);

        $str = '';
        foreach ($ali_Arr as $key => $value) {
            if ($str == '') {
                $str = $key . '=' . '"' . $value . '"';
            } else {
                $str = $str . '&' . $key . '=' . '"' . $value . '"';
            }
        }

        $sign = urlencode($this->rsaSign($str, $this->private_key_path));//签名结果
        if (!empty($sign)) {
            $alipayStr = $str . '&sign=' . '"' . $sign . '"' . '&sign_type=' . '"RSA"';//传给支付宝接口的数据
        }
        return $alipayStr;
    }

    /**
     * RSA验签
     * @param $data 待签名数据
     * @param $ali_public_key_path 支付宝的公钥文件路径
     * @param $sign 要校对的的签名结果
     * return 验证结果
     */
    public function rsaVerify($str)
    {
        return $this->_rsaVerify($str, $this->rsaSign($str, $this->private_key_path));
    }


    public function parseNotice($result)
    {
        //验证签名+是否支付宝发来的通知
        if (!$this->check_sign($result) || !$this->check_alipay_notice($result['notify_id'])) {
            $arrPayNoticeResult['fund_order_id'] = $result['out_trade_no'];
            $arrPayNoticeResult['isSuccess'] = false;
        }else{
            if ($result['trade_status'] == "TRADE_SUCCESS") {
                $arrPayNoticeResult['fund_order_id'] = $result['out_trade_no'];
                $arrPayNoticeResult['seq_no'] = $result['trade_no'];
                $arrPayNoticeResult['amount'] = $result['total_fee'];
                $arrPayNoticeResult['isSuccess'] = true;
            } else {
                $arrPayNoticeResult['fund_order_id'] = $result['out_trade_no'];
                $arrPayNoticeResult['isSuccess'] = false;
            }
        }
        return $arrPayNoticeResult;
    }

    /*
     * 校验支付宝回调通知签名
     */
    public function check_sign(array $param)
    {
        $_sign = urldecode($param['sign']);
        unset($param['sign']);
        unset($param['sign_type']);
        $ali_Arr = $this->argSort($param);

        $str = '';
        foreach ($ali_Arr as $key => $value) {
            if (!in_array($key,array('sign', 'sign_type'))) {
                if ($str == '') {
                    $str = $key . '=' . '"' . urldecode($value) . '"';
                } else {
                    $str = $str . '&' . $key . '=' . '"' . urldecode($value) . '"';
                }
            }
        }

        return $this->_rsaVerify($str, $this->rsaSign($str, $this->private_key_path));

    }

    /*
     * 校验是否支付宝发来的通知
     */
    public function check_alipay_notice($notify_id)
    {
        $url = "https://mapi.alipay.com/gateway.do?service=notify_verify&partner=".config_item('partner').
            "&notify_id=".$notify_id;
        $result= file_get_contents($url);
        log_message('debug','check_alipay_notice:'.$result);
        return $result;
    }


}        