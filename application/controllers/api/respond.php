<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Respond extends CI_Controller {

	public function __construct()
    {
      	parent::__construct();
      	$this->load->service('pay_service');
      	$this->load->service('trd_service');
      	$this->load->service('union_df_service');

    }

    public function unionpayNotify()
    {
        log_message('debug','@@@@@@@@+++++++ unionpayNotify +++++++++@@@@@@@');
        log_message('debug',json_encode($_POST));

        if (isset ( $_POST ['signature'] )) {
            echo com\unionpay\acp\sdk\AcpService::validate($_POST) ? '验签成功' : '验签失败';
            $orderId = $_POST ['orderId']; //其他字段也可用类似方式获取
            $respCode = $_POST ['respCode']; //判断respCode=00或A6即可认为交易成功

            $payNoticeResult = array();
            if ($respCode == '00' || $respCode == 'A6') {
                $payNoticeResult['isSuccess'] = true;

                $payNoticeResult['fund_order_id'] = $orderId;
                //$payNoticeResult['seq_no'] = $result['transaction_id'];
                //$payNoticeResult['amount'] = $result['total_fee']/100;
            }
        }
    }


	public function jump_WeixinPayJs(){

	}

	public function jump_AlipayJs()
	{
		require_once("../application/libraries/alipay_notify.class.php");

		//计算得出通知验证结果
		$alipayNotify = new AlipayNotify();


		$verify_result = true;
		if ($verify_result) {//验证成功
			if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
				// 支付成功！
				redirect(WAP_SITE_URL . '/page/pay-success.html?success=1&paytype=' . $_GET['paytype']);
			} else {
				//支付失败！
				redirect(WAP_SITE_URL . '/page/pay-success.html?success=2&paytype=' . $_GET['paytype']);
			}
		} else {
			//支付失败！
			redirect(WAP_SITE_URL . '/page/pay-success.html?success=2&paytype=' . $_GET['paytype']);
		}
//		$data = $_POST;
//		$this->notice('AliPayJs',$data);
	}


	public function notice_WeixinPayJs()
	{
		$xml = file_get_contents("php://input");
		file_put_contents('wx_js.log', date('Y-m-d H:i:s') . '=>: ' . $xml . "\r\n\r\n", FILE_APPEND);
		$result = $this->notice('WeixinPayJs', $xml);

		echo $result;
	}

    //微信app支付回调
	public function notice_WeixinPayApp()
	{
        log_message('debug', '@@@--notice_WeixinPayApp');
        $this->weixinApp(0);
	}

	private function weixinApp($utype=0){
		if(!empty($utype)){
			PayConfig::$USER_TYPE = $utype;
		}
		log_message('debug','user_type:'.$utype);

        log_message('debug', '@@@--weixinApp');

		$xml = file_get_contents("php://input");
		file_put_contents('wx.log', date('Y-m-d H:i:s') . '=>: ' . $xml . "\r\n\r\n", FILE_APPEND);
		$result = $this->notice('WeixinPayApp', $xml);

		echo $result;
	}

	/**
	 * 支付宝通知地址
	 */
	public function notice_AlipayJs()
	{
		$this->notice_AlipayApp();
	}

	public function notice_AlipayApp()
	{
		$data = $_POST;
        log_message('debug', 'notice_AlipayApp'.json_encode($data));
		$result = $this->notice('AlipayApp', $data);
		file_put_contents('alipay.log', date('Y-m-d H:i:s') . '=>: ' . file_get_contents("php://input") . "\r\n\r\n", FILE_APPEND);
		echo strtolower($result);
	}


	private function notice($payMethodName, $data)
	{
		$arrReturn = $this->trd_service->notice($payMethodName, $data);

		if ($payMethodName == C('PayMethod.WeixinPayApp') || $payMethodName == C('PayMethod.WeixinPayJs'))
			$czResult = "<xml><return_code><![CDATA[" . strtoupper($arrReturn['code']) . "]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>";
		else
			$czResult = $arrReturn['code'];

		log_message('debug','notice'.json_encode($arrReturn));
		return $czResult;
	}

	//代付回调地址
	public function unionpaydfNotify(){
		log_message('debug','666666666666'.json_encode($_POST));
		if (isset ( $_POST ['signature'] )) {

			if(com\unionpay\acp\sdk\AcpService::validate ( $_POST ) ){
				$this->trd_service->dfrespond($_POST ['orderId'],$_POST ['respCode']);
			}else{

			}
//			$orderId = $_POST ['orderId']; //
//			$this->union_df_service->
//			$respCode = $_POST ['respCode']; //判断respCode=00或A6即可认为交易成功

		} else {

		}
	}

}