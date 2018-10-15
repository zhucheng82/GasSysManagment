<?php

class WeixinPayApp{
	public $respond_name = 'WeixinPayApp';

	public function __construct()
	{
		require_once "WxPayApi.php";
	}

	/**
	 * 
	 * 网页授权接口微信服务器返回的数据，返回样例如下
	 * {
	 *  "access_token":"ACCESS_TOKEN",
	 *  "expires_in":7200, 
	 *  "refresh_token":"REFRESH_TOKEN",
	 *  "openid":"OPENID",
	 *  "scope":"SCOPE",
	 *  "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
	 * }
	 * 其中access_token可用于获取共享收货地址
	 * openid是微信支付jsapi支付接口必须的参数
	 * @var array
	 */
	public $data = null;

    public function payRequest($arrFundOrder) {
        //①、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody($arrFundOrder['title']);
        $input->SetAttach($arrFundOrder['title']);
        $input->SetOut_trade_no($arrFundOrder['fund_order_id']);
        $input->SetTotal_fee($arrFundOrder['amount']*100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 3600));
        $input->SetNotify_url($arrFundOrder['notice_url']);
        $input->SetTrade_type("APP");

        $result = WxPayApi::unifiedOrder($input);
        if($result['return_code']=='SUCCESS' && $result['result_code']=='SUCCESS')
        {
            $appParameters = $this->GetAppParameters($result);
            $appParameters['return_code'] = $result['return_code'];
            $appParameters['result_code'] = $result['result_code'];
            return $appParameters;
        }else{
            return $result;
        }

        // if($result['return_code']=='SUCCESS'){
        // 	$result['randKey'] = md5(time().mt_rand(0,1000));
        // 	$result['WxKEY'] = _aes_encode(PayConfig::WxKEY, $result['randKey']);

        // }
        // return $result;
    }

    //返回PayNoticeResult数组
    // $arrParam['data']: xml字符串
    //$arrPayNoticeResult('fund_order_id','seq_no', 'amount','isSuccess')
    public function parseNotice($xml){
    	$arrPayNoticeResult = array();
    	try {
			$result = WxPayResults::Init($xml);
		} catch (WxPayException $e){
			$arrPayNoticeResult['isSuccess'] = false;
			$arrPayNoticeResult['errInfo'] = $e->errorMessage();
			return $arrPayNoticeResult;
		}
		
		if($result['return_code'] == "SUCCESS")
		{
			$arrPayNoticeResult['fund_order_id'] = $result['out_trade_no'];
			$arrPayNoticeResult['seq_no'] = $result['transaction_id'];
			$arrPayNoticeResult['amount'] = $result['total_fee']/100;
			$arrPayNoticeResult['isSuccess'] = true;
		}
		else
		{
			$arrPayNoticeResult['fund_order_id'] = $result['out_trade_no'];
			$arrPayNoticeResult['isSuccess'] = false;
		}
	
		return $arrPayNoticeResult;
    }

    public function parseJump($arrParam){
    	return null;
    }

//    public function checkOrder($arrFundOrder){
//
//    }

    /**
     *
     * 获取app支付的参数
     * @param array $UnifiedOrderResult 统一支付接口返回的数据
     * @throws WxPayException
     *
     * @return json数据，可直接填入js函数作为参数
     */
    public function GetAppParameters($UnifiedOrderResult)
    {
        if(!array_key_exists("appid", $UnifiedOrderResult)
            || !array_key_exists("prepay_id", $UnifiedOrderResult)
            || $UnifiedOrderResult['prepay_id'] == "")
        {
            throw new WxPayException("参数错误");
        }
        $app_api = new WxPayAppPayObj();
        $app_api->SetAppid($UnifiedOrderResult["appid"]);
        $app_api->SetPartnerid($UnifiedOrderResult["mch_id"]);
        $app_api->SetPrepayid($UnifiedOrderResult["prepay_id"]);
        $app_api->SetPackage("Sign=WXPay");
        $app_api->SetNoncestr(WxPayApi::getNonceStr());
        $timeStamp = time();
        $app_api->SetTimestamp("$timeStamp");
        $app_api->SetSign($app_api->MakeSign());
        $parameters = $app_api->GetValues();
        return $parameters;
    }
}