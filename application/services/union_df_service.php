<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once '../application/libraries/unionpay_sdk/acp_service.php';

class union_df_service
{
    public function __construct()
    {
        $this->ci = &get_instance();
    }

    public function dfrequest($order, $accNo, $customerInfo)
    {
        $params = array(

            //以下信息非特殊情况不需要改动
            'version' => '5.0.0',              //版本号
            'encoding' => 'utf-8',              //编码方式
            'signMethod' => '01',              //签名方法
            'txnType' => '12',                  //交易类型
            'txnSubType' => '00',              //交易子类
            'bizType' => '000401',              //业务类型
            'accessType' => '0',              //接入类型
            'channelType' => '08',              //渠道类型
            'currencyCode' => '156',          //交易币种，境内商户勿改
            'backUrl' => BASE_SITE_URL+'/api/respond/unionpaydfNotify', //后台通知地址
            'encryptCertId' => com\unionpay\acp\sdk\AcpService::getEncryptCertId(), //验签证书序列号

            //TODO 以下信息需要填写
            'merId' => $order["merId"],        //商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'orderId' => $order["orderId"],    //商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime' => $order["txnTime"],    //订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
            'txnAmt' => $order["txnAmt"],    //交易金额，单位分，此处默认取demo演示页面传递的参数
// 		'reqReserved' =>'透传信息',        //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据

// 		'accNo' => $accNo,     //卡号，旧规范请按此方式填写
// 		'customerInfo' => com\unionpay\acp\sdk\AcpService::getCustomerInfo($customerInfo), //持卡人身份信息，旧规范请按此方式填写
            'accNo' => com\unionpay\acp\sdk\AcpService::encryptData($accNo),     //卡号，新规范请按此方式填写
            'customerInfo' => com\unionpay\acp\sdk\AcpService::getCustomerInfoWithEncrypt($customerInfo), //持卡人身份信息，新规范请按此方式填写
        );

        com\unionpay\acp\sdk\AcpService::sign($params); // 签名
        $url = com\unionpay\acp\sdk\SDK_BACK_TRANS_URL;
        log_message('debug','111') ;
        $result_arr = com\unionpay\acp\sdk\AcpService::post($params, $url);
        log_message('debug','222'.json_encode($result_arr)) ;
        $arrReturn = array('code' => 0, 'errInfo' => '', 'order_id' => $order["orderId"]);    //,'order_sn'=>''  多订单
        if (count($result_arr) <= 0) { //没收到200应答的情况
            $this->printResult($url, $params, "");
            $arrReturn['code'] = -1;
            $arrReturn['errInfo'] = '验签失败';
            log_message('debug','333') ;
            return $arrReturn;
        }
        log_message('debug',json_encode($arrReturn)) ;

        $this->printResult($url, $params, $result_arr); //页面打印请求应答数据

        if (!com\unionpay\acp\sdk\AcpService::validate($result_arr)) {
            $arrReturn['code'] = -2;
            $arrReturn['errInfo'] = '应答报文验签失败';
            return $arrReturn;
        }

        if ($result_arr["respCode"] == "00") {
            //交易已受理，等待接收后台通知更新订单状态，如果通知长时间未收到也可发起交易状态查询
            //TODO
            $arrReturn['code'] = 1;
            $arrReturn['errInfo'] = '业务受理成功';
            return $arrReturn;
        } else if ($result_arr["respCode"] == "03"
            || $result_arr["respCode"] == "04"
            || $result_arr["respCode"] == "05"
            || $result_arr["respCode"] == "01"
            || $result_arr["respCode"] == "12"
            || $result_arr["respCode"] == "34"
            || $result_arr["respCode"] == "60"
        ) {
            //后续需发起交易状态查询交易确定交易状态
            //TODO
            $arrReturn['code'] = -3;
            $arrReturn['errInfo'] = '处理超时，请稍后查询';
            return $arrReturn;
        } else {
            //其他应答码做以失败处理
            //TODO
            echo "失败：" . $result_arr["respMsg"] . "。<br>\n";
            $arrReturn['code'] = -4;
            $arrReturn['errInfo'] = "失败：" . $result_arr["respMsg"];
            return $arrReturn;
        }
    }

    private function printResult($url, $req, $resp)
    {
        log_message('debug',"=============") ;
        log_message('debug',"address:" . $url) ;
        log_message('debug',"request:" . htmlentities(com\unionpay\acp\sdk\createLinkString($req, false, true))) ;
        log_message('debug',"respond:" . htmlentities(com\unionpay\acp\sdk\createLinkString($resp, false, true))) ;
        log_message('debug',"=============") ;

    }


}