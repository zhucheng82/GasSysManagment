<?php
class PayConfig
{
	//=======【基本信息设置】=====================================
	//
	/**
	 * TODO: 修改这里配置为您自己申请的商户信息
	 * 微信公众号信息配置
	 *
	 * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
	 *
	 * MCHID：商户号（必须配置，开户邮件中可查看）
	 *
	 * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
	 * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
	 *
	 * APPSECRET：公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
	 * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
	 * @var string
	 */
	const WxAPPID = 'wx4d8d716d6a610ca2';
	const WxMCHID = '1303493701';
	const WxKEY = '181d0ecef6e3d3a6cb8c1008ddfa9055';
	const WxAPPSECRET = 'e3d24fda456340f32f5ae5211a49bb36';

	// const WapWxAPPID = 'wxf1d3385c898dca36';
	// const WapWxMCHID = '1271547101';
	// const WapWxKEY = '137d30ecef6e3d3a6cb8c1008ddfa90d';
	// const WapWxAPPSECRET = '425f17a1bc7f2e2be2deeeb9a85ad212';
	// const WapWxTOKEN = 'EVeOhxeU7PAMffjxl6AIniNq2N';

	const WapWxAPPID = 'wxd5ce9dcd4330c550';
	const WapWxMCHID = '1350048901';
	const WapWxKEY = '183d1ecef6e3d3a6cb9c1108ddfa9075';
	const WapWxAPPSECRET = '198c479dd9105898217ffc3df01484fd';
	//const WapWxTOKEN = 'kuFeOhxeU7PdXsfjxl6AIniNq2N';


	const PatientWxAPPID = 'wx9b893f8e82ce23e0';
	const PatientWxMCHID = '1376665702';
	const PatientWxKEY = '190d1ecef6d4d3a6cb9c1128ddfa9058';
	const PatientWxAPPSECRET = 'ef778d60268495c37f13efd1033c3a32';


	const DoctorWxAPPID = 'wx9b893f8e82ce23e0';
	const DoctorWxMCHID = '1376665702';
	const DoctorWxKEY = '190d1ecef6d4d3a6cb9c1128ddfa9058';
	const DoctorWxAPPSECRET = 'ef778d60268495c37f13efd1033c3a32';

	const HospitalWxAPPID = 'wx9b893f8e82ce23e0';
	const HospitalWxMCHID = '1376665702';
	const HospitalWxKEY = '190d1ecef6d4d3a6cb9c1128ddfa9058';
	const HospitalWxAPPSECRET = 'ef778d60268495c37f13efd1033c3a32';
	//=======【证书路径设置】=====================================
	/**
	 * TODO：设置商户证书路径
	 * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
	 * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
	 * @var path
	 */
	const WxSSLCERT_PATH = '../cert/apiclient_cert.pem';
	const WxSSLKEY_PATH = '../cert/apiclient_key.pem';

	//=======【curl代理设置】===================================
	/**
	 * TODO：这里设置代理机器，只有需要代理的时候才设置，不需要代理，请设置为0.0.0.0和0
	 * 本例程通过curl使用HTTP POST方法，此处可修改代理服务器，
	 * 默认CURL_PROXY_HOST=0.0.0.0和CURL_PROXY_PORT=0，此时不开启代理（如有需要才设置）
	 * @var unknown_type
	 */
	const WxCURL_PROXY_HOST = "0.0.0.0";//"10.152.18.220";
	const WxCURL_PROXY_PORT = 0;//8080;

	//=======【上报信息配置】===================================
	/**
	 * TODO：接口调用上报等级，默认紧错误上报（注意：上报超时间为【1s】，上报无论成败【永不抛出异常】，
	 * 不会影响接口调用流程），开启上报之后，方便微信监控请求调用的质量，建议至少
	 * 开启错误上报。
	 * 上报等级，0.关闭上报; 1.仅错误出错上报; 2.全量上报
	 * @var int
	 */
	const WxREPORT_LEVENL = 0;

	public static $USER_TYPE = 0;


}
/*
 * 患者端支付信息
 * */
$config['WxPayConfigType_1'] = array(
	'WxAPPID' => 'wx9b893f8e82ce23e0',
	'WxMCHID' => '1376665702',
	'WxKEY' => '190d1ecef6d4d3a6cb9c1128ddfa9058',
	'WxAPPSECRET' => 'ef778d60268495c37f13efd1033c3a32',

);

$config['WxPayConfigType_2'] = array(
	'WxAPPID' => 'wx9b893f8e82ce23e0',
	'WxMCHID' => '1376665702',
	'WxKEY' => '190d1ecef6d4d3a6cb9c1128ddfa9058',
	'WxAPPSECRET' => 'ef778d60268495c37f13efd1033c3a32',

);

$config['WxPayConfigType_3'] = array(
	'WxAPPID' => 'wx9b893f8e82ce23e0',
	'WxMCHID' => '1376665702',
	'WxKEY' => '190d1ecef6d4d3a6cb9c1128ddfa9058',
	'WxAPPSECRET' => 'ef778d60268495c37f13efd1033c3a32',

);
function getWxappIDByType(){
	$obj = config_item('WxPayConfigType_'.PayConfig::$USER_TYPE);
	if($obj){
		return $obj['WxAPPID'];
	}else{
		return PayConfig::WxAPPID;
	}
}

function getWxMCHIDByType(){
	$obj = config_item('WxPayConfigType_'.PayConfig::$USER_TYPE);
	if($obj){
		return $obj['WxMCHID'];
	}else{
		return PayConfig::WxMCHID;
	}
}

function getWxKEYByType(){
	$obj = config_item('WxPayConfigType_'.PayConfig::$USER_TYPE);
	if($obj){
		return $obj['WxKEY'];
	}else{
		return PayConfig::WxKEY;
	}
}

function getWxAPPSECRETByType(){
	$obj = config_item('WxPayConfigType_'.PayConfig::$USER_TYPE);
	if($obj){
		return $obj['WxKEY'];
	}else{
		return PayConfig::WxAPPSECRET;
	}
}
?>