<?php
/**
 * Created by PhpStorm.
 * User: zhucheng
 * Date: 16/9/23
 * Time: 下午2:10
 */

header ( 'Content-type:text/html;charset=utf-8' );
//include_once $_SERVER ['DOCUMENT_ROOT'] . '/upacp_demo_dk/sdk/acp_service.php';

include_once '../application/libraries/unionpay_sdk/acp_service.php';

//class Unionpay extends TokenApiController
class Unionpay extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        /*
        $this->load->service('sms_service');
        $this->load->service('user_service');
        $this->load->model('User_pwd_model');
        $this->load->model('User_model');
        $this->load->model('User_token_model');
        */
        $this->load->model('User_model');
        $this->load->model('student_model');
        $this->load->model('employee_model');
        $this->load->model('additional_user_info_model');

        //$this->user_id = $this->loginUser['user_id'];
    }

    //还款
    public function repayment()
    {
        //$merId => '777290058138272',//$_POST["merId"],		//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
        //'txnTime' => $_POST["txnTime"],	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数

        log_message('debug','@@@@@@@@+++++++ unionpay repayment +++++++++@@@@@@@');

        $orderId = $this->input->post('orderId');	//商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
        $txnAmt = $this->input->post('amount');	//交易金额，单位分，此处默认取demo演示页面传递的参数
        $mobile = $this->input->post('mobile');
        $name = $this->input->post('name');
        $IDCard = $this->input->post('IDCard');

        /**
         * 重要：联调测试时请仔细阅读注释！
         *
         * 产品：代收产品<br>
         * 交易：代收：后台资金类交易，有同步应答和后台通知应答<br>
         * 日期： 2015-09<br>
         * 版本： 1.0.0
         * 版权： 中国银联<br>
         * 说明：以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己需要，按照技术文档编写。该代码仅供参考，不提供编码性能规范性等方面的保障<br>
         * 该接口参考文档位置：open.unionpay.com帮助中心 下载  产品接口规范  《代收产品接口规范》<br>
         *《平台接入接口规范-第5部分-附录》（内包含应答码接口规范）<br>
         * 测试过程中的如果遇到疑问或问题您可以：1）优先在open平台中查找答案：
         * 							        调试过程中的问题或其他问题请在 https://open.unionpay.com/ajweb/help/faq/list 帮助中心 FAQ 搜索解决方案
         *                             测试过程中产生的6位应答码问题疑问请在https://open.unionpay.com/ajweb/help/respCode/respCodeList 输入应答码搜索解决方案
         *                         2） 咨询在线人工支持： open.unionpay.com注册一个用户并登陆在右上角点击“在线客服”，咨询人工QQ测试支持。
         * 确定交易成功机制：商户必须开发后台通知接口和交易状态查询接口（Form09_6_5_Query）确定交易是否成功，建议发起查询交易的机制：可查询N次（不超过6次），每次时间间隔2N秒发起,即间隔1，2，4，8，16，32S查询（查询到03，04，05继续查询，否则终止查询）
         */

        //TODO 填寫卡信息
        //  交易卡要素说明：
        //
        //  银联后台会做控制，如使用真实商户号，要素为根据申请表配置，请参考自己的申请表上送。
        //
        //  测试商户号777290058110097的交易卡要素控制配置：
        //
        //  借记卡必送：
        //  （实名认证交易-后台）卡号，姓名，证件类型，证件号码，手机号
        //  （实名认证交易-前台）卡号，姓名，证件类型，证件号码
        //  （代收）卡号，姓名，证件类型，证件号码，手机号
        //
        //  贷记卡必送：（使用绑定标识码的代收）
        //  （实名认证交易-后台）卡号，有效期，cvn2,姓名，证件类型，证件号码，手机号，绑定标识码
        //  （实名认证交易-前台）卡号，证件类型，证件号码，绑定标识码
        //  （代收）绑定标识码

        $accNo = '6216261000000000018';

        $customerInfo = array(
            'phoneNo' => '13552535506', //手机号
            'certifTp' => '01', //证件类型，01-身份证
            'certifId' => '341126197709218366', //证件号，15位身份证不校验尾号，18位会校验尾号，请务必在前端写好校验代码
            'customerNm' => '全渠道', //姓名
//  		'cvn2' => '248',　//cvn2
//  		'expired' => '1912',　//有效期，YYMM格式，持卡人卡面印的是MMYY的，请注意代码设置倒一下
        );

        $accNo = '6221558812340000';
        $customerInfo = array(
            'phoneNo' => '13552535506', //手机号
            'certifTp' => '01', //证件类型，01-身份证
            'certifId' => '341126197709218366', //证件号，15位身份证不校验尾号，18位会校验尾号，请务必在前端写好校验代码
            'customerNm' => '互联网', //姓名
//  		'cvn2' => '248',　//cvn2
//  		'expired' => '1912',　//有效期，YYMM格式，持卡人卡面印的是MMYY的，请注意代码设置倒一下
        );

        $merId = '777290058138272';

        $params = array(

            //以下信息非特殊情况不需要改动
            'version' => '5.0.0',		      //版本号
            'encoding' => 'utf-8',		      //编码方式
            'signMethod' => '01',		      //签名方法
            'txnType' => '11',		          //交易类型
            'txnSubType' => '00',		      //交易子类
            'bizType' => '000501',		      //业务类型
            'accessType' => '0',		      //接入类型
            'channelType' => '07',		      //渠道类型
            'currencyCode' => '156',          //交易币种，境内商户勿改
            'backUrl' => com\unionpay\acp\sdk\SDK_BACK_NOTIFY_URL, //后台通知地址
            'encryptCertId' => com\unionpay\acp\sdk\AcpService::getEncryptCertId(), //验签证书序列号

            //TODO 以下信息需要填写
            'merId' => $merId,//$_POST["merId"],		//商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'orderId' => $orderId,	//商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime' => date('YmdHis',time()),//'20160924105000',	//订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
            'txnAmt' => $txnAmt,	//交易金额，单位分，此处默认取demo演示页面传递的参数
// 		'reqReserved' =>'透传信息',        //请求方保留域，透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据

// 		'accNo' => $accNo,     //卡号，旧规范请按此方式填写
//  	'customerInfo' => com\unionpay\acp\sdk\AcpService::getCustomerInfo($customerInfo), //持卡人身份信息，旧规范请按此方式填写
            'accNo' => com\unionpay\acp\sdk\AcpService::encryptData($accNo),     //卡号，新规范请按此方式填写
            'customerInfo' => com\unionpay\acp\sdk\AcpService::getCustomerInfoWithEncrypt($customerInfo), //持卡人身份信息，新规范请按此方式填写
        );

        log_message('debug','@@@@@@@@+++++++ unionpay sign +++++++++@@@@@@@');
        com\unionpay\acp\sdk\AcpService::sign ( $params ); // 签名
        $url = com\unionpay\acp\sdk\SDK_BACK_TRANS_URL;

        log_message('debug','@@@@@@@@+++++++ unionpay post'.'$params:'.json_encode($params).'$url:'. $url.'+++++++++@@@@@@@');
        $result_arr = com\unionpay\acp\sdk\AcpService::post ( $params, $url );

        log_message('debug','@@@@@@@@+++++++ unionpay posted~~~~ +++++++++@@@@@@@');

        if(count($result_arr)<=0) { //没收到200应答的情况
            $this->printResult ( $url, $params, "" );
            //return;
            log_message('debug','@@@@@@@@+++++++ unionpay post failed+++++++++@@@@@@@');

            output_error(-1, '没收到200应答的情况');exit;
        }

        //printResult ($url, $params, $result_arr ); //页面打印请求应答数据

        log_message('debug','@@@@@@@@+++++++ unionpay validate +++++++++@@@@@@@');

        if (!com\unionpay\acp\sdk\AcpService::validate ($result_arr) ){
            //echo "应答报文验签失败<br>\n";
            //return;

            output_error(-1, '应答报文验签失败');exit;
        }

        //echo "应答报文验签成功<br>\n";
        if ($result_arr["respCode"] == "00"){
            //交易已受理，等待接收后台通知更新订单状态，如果通知长时间未收到也可发起交易状态查询
            //TODO
            //echo "受理成功。<br>\n";

            log_message('debug','@@@@@@@@+++++++ unionpay succeed +++++++++@@@@@@@');

            output_data();exit;

        } else if ($result_arr["respCode"] == "03"
            || $result_arr["respCode"] == "04"
            || $result_arr["respCode"] == "05" ){
            //后续需发起交易状态查询交易确定交易状态
            //TODO
            //echo "处理超时，请稍后查询。<br>\n";

            log_message('debug','@@@@@@@@+++++++ unionpay timed out +++++++++@@@@@@@');

            output_data();exit;
        } else {
            //其他应答码做以失败处理
            //TODO
            //echo "失败：" . $result_arr["respMsg"] . "。<br>\n";

            log_message('debug','@@@@@@@@+++++++ unionpay failed +++++++++@@@@@@@');
            output_error(-1, "失败：" . $result_arr["respMsg"]);exit;
        }
    }


    /**
    * 打印请求应答
    *
     * @param
     *        	$url
     * @param
     *        	$req
     * @param
     *        	$resp
     */
    public function printResult($url, $req, $resp) {
        echo "=============<br>\n";
        echo "地址：" . $url . "<br>\n";
        echo "请求：" . str_replace ( "\n", "\n<br>", htmlentities ( com\unionpay\acp\sdk\createLinkString ( $req, false, true ) ) ) . "<br>\n";
        echo "应答：" . str_replace ( "\n", "\n<br>", htmlentities ( com\unionpay\acp\sdk\createLinkString ( $resp , false, true )) ) . "<br>\n";
        echo "=============<br>\n";
    }
}