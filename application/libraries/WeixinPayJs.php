<?php

class WeixinPayJs{

	public $respond_name = 'WeixinPayJs';

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

    public function payRequest($arrFundOrder, $openId=null) {
    	//获取用户openid
    	if(empty($openId)){
    		$getOpenIdUrl = $this->GetOpenid($arrFundOrder['fund_order_id']);
    		return $getOpenIdUrl;
    	}
    	else{

	    	//统一下单
	    	$input = new WxPayUnifiedOrder();
	    	$input->SetBody($arrFundOrder['title']);
			$input->SetAttach($arrFundOrder['title']);
			$input->SetOut_trade_no($arrFundOrder['fund_order_id']);
			$input->SetTotal_fee($arrFundOrder['amount']*100);
			$input->SetTime_start(date("YmdHis"));
			$input->SetTime_expire(date("YmdHis", time() + 3600));
			$input->SetNotify_url($arrFundOrder['notice_url']);
			$input->SetTrade_type("JSAPI");
			$input->SetOpenid($openId);

			$result = WxPayApi::unifiedOrder($input);
			if($result['return_code']=='SUCCESS' && $result['result_code']=='SUCCESS')
			{
				$jsApiParameters = $this->GetJsApiParameters($result);
				return $jsApiParameters;
			}else{
				return $result;
			}
		}
   
    }

    /**
     * 企业付款
     */
    public function enterprisePay($arrFundOrder, $openId=null){
        //获取用户openid
        if(empty($openId)){
            $getOpenIdUrl = $this->GetOpenid($arrFundOrder['fund_order_id']);
            return $getOpenIdUrl;
        }
        else{
            //企业付款
            $input = new WxPayEnterprise();
            $input->SetDesc($arrFundOrder['title']);
            $input->SetCheckname('NO_CHECK');
            $input->SetPartner_trade_no($arrFundOrder['fund_order_id']);
            //$input->Set_amount(100);
            $input->Set_amount($arrFundOrder['amount']*100);
            $input->SetOpenid($openId);

            $result = WxPayApi::enterprisePay($input);
            return $result;
        }
    }



    //返回PayNoticeResult数组
    // $arrParam['data']: xml字符串
    //$arrPayNoticeResult('fund_order_id','seq_no', 'amount','isSuccess')
    public function parseNotice($xml){
    	$arrPayNoticeResult = array();
    	try {
			$result = WxPayResults::Init($xml);
		} catch (WxPayException $e){
			return $e->errorMessage();
		}

		if($result['result_code'] == "SUCCESS" && $result['return_code'] == "SUCCESS")
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

    public function parseJump(){
    	return null;
    }

//    public function checkOrder($arrFundOrder){
//
//    }

    /**
	 * 
	 * 通过跳转获取用户的openid，跳转流程如下：
	 * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
	 * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
	 * 
	 * @return 用户的openid
	 */
	public function GetOpenid($fund_order_id=0)
	{
		//通过code获得openid
		if (!isset($_GET['code'])){
			//触发微信返回code码
			$redirectUrl =  urlencode(BASE_SITE_URL.'/api/jump/getWxJSAPI?payMethod=12&fundId='.$fund_order_id);
			$url = $this->__CreateOauthUrlForCode($redirectUrl);
//echo $url;die;
			// $rrr = $this->testfff($url);
			// print_r($rrr);die;
			//Header("Location: $url");exit();
			return $url;
		} else {
			//获取code码，以获取openid
		    $code = $_GET['code'];
			$openid = $this->getOpenidFromMp($code);
			return $openid;
		}
	}


	/**
	 * 
	 * 获取jsapi支付的参数
	 * @param array $UnifiedOrderResult 统一支付接口返回的数据
	 * @throws WxPayException
	 * 
	 * @return json数据，可直接填入js函数作为参数
	 */
	public function GetJsApiParameters($UnifiedOrderResult)
	{
		if(!array_key_exists("appid", $UnifiedOrderResult)
		|| !array_key_exists("prepay_id", $UnifiedOrderResult)
		|| $UnifiedOrderResult['prepay_id'] == "")
		{
			throw new WxPayException("参数错误");
		}
		$jsapi = new WxPayJsApiPayObj();
		$jsapi->SetAppid($UnifiedOrderResult["appid"]);
		$timeStamp = time();
		$jsapi->SetTimeStamp("$timeStamp");
		$jsapi->SetNonceStr(WxPayApi::getNonceStr());
		$jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
		$jsapi->SetSignType("MD5");
		$jsapi->SetPaySign($jsapi->MakeSign());
		$parameters = json_encode($jsapi->GetValues());
		return $parameters;
	}
	
	/**
	 * 
	 * 通过code从工作平台获取openid机器access_token
	 * @param string $code 微信跳转回来带上的code
	 * 
	 * @return openid
	 */
	public function GetOpenidFromMp($code)
	{
		$url = $this->__CreateOauthUrlForOpenid($code);
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, 500);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		if(PayConfig::WxCURL_PROXY_HOST != "0.0.0.0"
			&& PayConfig::WxCURL_PROXY_PORT != 0){
			curl_setopt($ch,CURLOPT_PROXY, PayConfig::WxCURL_PROXY_HOST);
			curl_setopt($ch,CURLOPT_PROXYPORT, PayConfig::WxCURL_PROXY_PORT);
		}
		//运行curl，结果以jason形式返回
		$res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		$data = json_decode($res,true);
		$this->data = $data;
		$openid = $data['openid'];
		return $openid;
	}

	public function testfff($url){
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, 500);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		if(PayConfig::WxCURL_PROXY_HOST != "0.0.0.0"
			&& PayConfig::WxCURL_PROXY_PORT != 0){
			curl_setopt($ch,CURLOPT_PROXY, PayConfig::WxCURL_PROXY_HOST);
			curl_setopt($ch,CURLOPT_PROXYPORT, PayConfig::WxCURL_PROXY_PORT);
		}
		//运行curl，结果以jason形式返回
		$res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		return $res;

	}
	
	/**
	 * 
	 * 拼接签名字符串
	 * @param array $urlObj
	 * 
	 * @return 返回已经拼接好的字符串
	 */
	private function ToUrlParams($urlObj)
	{
		$buff = "";
		foreach ($urlObj as $k => $v)
		{
			if($k != "sign"){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
	
	/**
	 * 
	 * 获取地址js参数
	 * 
	 * @return 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用

	public function GetEditAddressParameters()
	{	
		$getData = $this->data;
		$data = array();
		$data["appid"] = PayConfig::WxAPPID;
		$data["url"] = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$time = time();
		$data["timestamp"] = "$time";
		$data["noncestr"] = "1234568";
		$data["accesstoken"] = $getData["access_token"];
		ksort($data);
		$params = $this->ToUrlParams($data);
		$addrSign = sha1($params);
		
		$afterData = array(
			"addrSign" => $addrSign,
			"signType" => "sha1",
			"scope" => "jsapi_address",
			"appId" => PayConfig::WxAPPID,
			"timeStamp" => $data["timestamp"],
			"nonceStr" => $data["noncestr"]
		);
		$parameters = json_encode($afterData);
		return $parameters;
	}
	 */
	/**
	 * 
	 * 构造获取code的url连接
	 * @param string $redirectUrl 微信服务器回跳的url，需要url编码
	 * 
	 * @return 返回构造好的url
	 */
	private function __CreateOauthUrlForCode($redirectUrl)
	{
		$urlObj["appid"] = PayConfig::WapWxAPPID;
		$urlObj["redirect_uri"] = "$redirectUrl";
		$urlObj["response_type"] = "code";
		$urlObj["scope"] = "snsapi_base";
		$urlObj["state"] = $this->respond_name."#wechat_redirect";
		$bizString = $this->ToUrlParams($urlObj);
		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
	}

	public function createWxUrl($redirectUrl){
		return $this->__CreateOauthUrlForCode($redirectUrl);
	}
	
	/**
	 * 
	 * 构造获取open和access_toke的url地址
	 * @param string $code，微信跳转带回的code
	 * 
	 * @return 请求的url
	 */
	private function __CreateOauthUrlForOpenid($code)
	{
		$urlObj["appid"] = PayConfig::WapWxAPPID;
		$urlObj["secret"] = PayConfig::WapWxAPPSECRET;
		$urlObj["code"] = $code;
		$urlObj["grant_type"] = "authorization_code";
		$bizString = $this->ToUrlParams($urlObj);
		return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
	}
}