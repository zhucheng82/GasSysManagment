<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class pay_service
{
    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('User_pay_model');
    }

    /*
     * 创建支付订单，发起支付时创建
     * $funds_id 资金订单主键ID
     * $uid 用户id
     * $price充值（交易）额
     * $type 类型
     * $desc 描述
     * $paytpye 支付方式
     * $orderid 交易订单表id
     * $order_type
     * $ordernum 订单编码（需要可用）
     * $user_type 用户类型（需要可用）
     * $ip 用户ip（需要可用）
     */
    protected function createOrder($funds_id,$price, $desc, $paytpye, $orderid, $order_type, $uid)
    {
        $aFundOrder = $this->ci->User_pay_model->get_by_where(array('order_id' => "'".$orderid."'", 'order_type' => $order_type,'user_id'=>$uid));

        $data = array(
            'funds_id' => $funds_id,
            'amount' => $price,
            'createtime' => time(),
            'updatetime' => time(),
            'pay_type' => $paytpye,
            'status' => 0,
            'title' => $desc,
            'order_type' => $order_type,
            'order_id' => $orderid,
            'user_id' => $uid,
            'order_num' => date('YmdHis',time()).rand(0000,9999)
        );
        //print_r($data);exit();
        $payObj = $this->tryGetPay($paytpye);

        if (!empty($payObj) && !empty($payObj->respond_name)) {
            if($payObj->respond_name == 'WeixinPayApp'){
                $data['return_url'] = BASE_SITE_URL . '/api/respond/jump_' . $payObj->respond_name;
                $data['notice_url'] = BASE_SITE_URL . '/api/respond/notice_' . $payObj->respond_name;
            }else{
                $data['return_url'] = BASE_SITE_URL . '/api/respond/jump_' . $payObj->respond_name;
                $data['notice_url'] = BASE_SITE_URL . '/api/respond/notice_' . $payObj->respond_name;
            }
        } else {
            $data['return_url'] = BASE_SITE_URL . '/api/respond/jump';
            $data['notice_url'] = BASE_SITE_URL . '/api/respond/notice';
        }
        if (!empty($aFundOrder)) {
            $this->ci->User_pay_model->update_by_id($aFundOrder['fund_order_id'],$data);
            return $aFundOrder['fund_order_id']; 
            //return -12;//该订单已经创建过支付订单了
        }
        //$data['return_url'] = 'http://mahjong.qixiangnet.com/api/respond/notice_WeixinPayApp_1';
        //$data['notice_url'] = 'http://mahjong.qixiangnet.com/api/respond/notice_WeixinPayApp_1';
        log_message('debug', 'create pay order:' . json_encode($data));

        return $this->ci->User_pay_model->insert_string($data);
    }

    /*
     * *
     * 自动还款更新订单状态
     */
    public function updateFundOrderStatus($fundOrderId)
    {
        $data = array(
            'status' => 1,
            'updatetime' => time()
        );
        log_message('debug', 'change pay order status:id ' . $fundOrderId . ' status:' . json_encode($data));
        return $this->ci->User_pay_model->update_by_id($fundOrderId, $data);
    }

    /*
     * 修改资金订单状态
     * 支付成功后调用
     *
     * */
    protected function chageFundOrderStatus($fid, $seqno)
    {
        $data = array(
            'status' => 1,
            'netpay_seqno' => $seqno,
            'updatetime' => time(),
        );
        log_message('debug', 'change pay order status:id ' . $fid . ' status:' . json_encode($data));
        return $this->ci->User_pay_model->update_by_id($fid, $data);
    }

    protected function tryGetPayByName($payMethodName)
    {
        $obj = null;
        if ($payMethodName == 'WeixinPayApp' || $payMethodName == 'WeixinPayJs' || $payMethodName == 'AlipayApp'  || $payMethodName == 'YinlianPay') {
            if ($payMethodName == 'YinlianPay') {
                $payMethodNameUrl = 'yinlian/YinlianPay';
                $this->ci->load->library($payMethodNameUrl);
                $obj = new $payMethodName();
                return $obj;
            }
            if ($payMethodName == 'AlipayApp') {
                $payMethodNameUrl = 'alipay/AlipayApp';
                $this->ci->load->library($payMethodNameUrl);
                $obj = new $payMethodName();
                return $obj;
            }

            $this->ci->load->library($payMethodName);
            $obj = new $payMethodName();
        }
        return $obj;
    }

    //返回对象
    protected function tryGetPay($payMethod)
    {
        $obj = null;
        if ($payMethod == 12) {
            $this->ci->load->library('WeixinPayJs');
            $obj = new WeixinPayJs();
        }

        elseif ($payMethod == 11) {
            $this->ci->load->library('WeixinPayApp');
            $obj = new WeixinPayApp();
        }

        elseif ($payMethod == 13) {
            $this->ci->load->library('alipay/AlipayApp');
            $obj = new AlipayApp();
        }
        elseif ($payMethod == 15) {//银联
            
            $this->ci->load->library('yinlian/YinlianPay');
            $obj = new YinlianPay();
        }
        elseif ($payMethod == 16) {//银联代收

            //$this->ci->load->library('unionpay_sdk/acp_service');
            //$obj = new AcpService();
        }
        return $obj;
    }

    // 解析页面跳转返回的函数
    //$param  array
    public function notice($payMethodName, $param){

        log_message('debug', 'pay call back:'.json_encode($param));
        $arrReturn = array('code' => 'Empty', 'errInfo' => '', 'fund_order_id' => 0, 'order_id' => 0);    //,'order_sn'=>''  多订单
        $obj = $this->tryGetPayByName($payMethodName);

        //判断支付方式是否存在
        if (empty($obj) && $payMethodName != 'UnionPayDS') {
            $arrReturn['code'] = C('OrderResultError.Failure');
            $arrReturn['errInfo'] = "notice:接口($payMethodName)不存在";
            //系统日志
            return $arrReturn;
        }
        //判断支付回调解析是否成功
        $payNoticeResult = array();
        if ($obj)
        {
            $payNoticeResult = $obj->parseNotice($param);
        }
        elseif($payMethodName == 'UnionPayDS')
        {
            //$payNoticeResult['fund_order_id'] = $result['out_trade_no'];
            //$payNoticeResult['seq_no'] = $result['transaction_id'];
            //$payNoticeResult['amount'] = $result['total_fee']/100;
            //$payNoticeResult['isSuccess'] = true;
        }

        log_message('debug', $payNoticeResult);
        if (empty($payNoticeResult) || !$payNoticeResult['isSuccess']) {
            $arrReturn['code'] = C('OrderResultError.Failure');
            $arrReturn['errInfo'] = "notice:接口($payMethodName))跳转参数解析失败";
            return $arrReturn;
        }
        if ($payMethodName == C('PayMethod.WeixinPayApp') || $payMethodName == C('PayMethod.WeixinPayJs')) {//如果是微信支付，
            log_message('debug', '@@@--WeiXin Pay,order_id:'.$payNoticeResult['fund_order_id']);
            $payNoticeResult['fund_order_id'] = substr($payNoticeResult['fund_order_id'],0,strlen($payNoticeResult['fund_order_id'])-10);
        }
        else if ($payMethodName == C('PayMethod.YinlianPay')) {//如果是银联支付，
            $fund_order_info = $this->ci->User_pay_model->get_by_where(array('order_num'=>$payNoticeResult['fund_order_num']));
            $payNoticeResult['fund_order_id'] = $fund_order_info['fund_order_id'];
        }
        elseif ($payMethodName == C('PayMethod.UnionPayDS')) {//如果是银联代收支付，
            $fund_order_info = $this->ci->User_pay_model->get_by_where(array('order_num'=>$payNoticeResult['fund_order_num']));
            $payNoticeResult['fund_order_id'] = $fund_order_info['fund_order_id'];
        }elseif($payMethodName == C('PayMethod.AlipayApp')){//如果是支付宝APP支付
            log_message('debug', '@@@--Ali Pay,order_id:'.$payNoticeResult['fund_order_id']);
            $payNoticeResult['fund_order_id'] = substr($payNoticeResult['fund_order_id'],0,strlen($payNoticeResult['fund_order_id'])-10);
        }
        $this->dealNetPayed($payNoticeResult, $arrReturn);
        log_message('debug', $arrReturn['errInfo']);
        return $arrReturn;
    }

    /*
     *
     * 处理订单更改状态
     * */
    protected function dealNetPayed(&$payNoticeResult, &$arrReturn)
    {
        $fund_order_id = $payNoticeResult['fund_order_id'];
        $aFundOrder = $this->ci->User_pay_model->get_by_id($payNoticeResult['fund_order_id']);

        if (empty($aFundOrder)) {

            log_message('debug', '---@@@---order not exist---'.json_encode($payNoticeResult));

            $arrReturn['code'] = C('OrderResultError.Failure');
            $arrReturn['errInfo'] = "订单不存在";
            //系统日志
        //   logger.error(String.format("订单(%d)不存在", $fund_order_id));
            return;
        }
        $arrReturn['fund_order_id'] = $aFundOrder['fund_order_id'];
        $arrReturn['order_id'] = $aFundOrder['order_id'];
        if ($aFundOrder['status'] == 1) {//0：创建，1：成功，2：失败
            $arrReturn['code'] = C('OrderResultError.Success');
            $arrReturn['errInfo'] = '订单已支付';
            return;
        }
        //判断订单金额是否正确
        if ($aFundOrder['amount'] != $payNoticeResult['amount']) {
            $arrReturn['code'] = C('OrderResultError.Failure');
            $arrReturn['errInfo'] = "金额不对";
            //系统日志
        //logger.error(String.format("订单(%d)金额不对,订单金额:%f,传参金额:%f", $fund_order_id, fundOrder.getNetpayAmt(), payNoticeResult.getAmount()));
            return;
        }

        if (!$this->chageFundOrderStatus($payNoticeResult['fund_order_id'], $payNoticeResult['seq_no'])) {
            $arrReturn['code'] = C('OrderResultError.Failure');
            $arrReturn['errInfo'] = "订单:" . $payNoticeResult['fund_order_id'] . " 修改支付订单状态失败";
            log_message('debug', $arrReturn['errInfo']);
        } else {
            $arrReturn['code'] = C('OrderResultError.Success');
            $arrReturn['errInfo'] = '';
            $res = $this->changeTrdOrderStatus($aFundOrder['user_id'], $aFundOrder['order_id'], $aFundOrder['order_type'],$aFundOrder);//订单处理
            if (!$res) {
                $arrReturn['errInfo'] = '修改交易订单状态失败';
            }
            $this->sendMSG($aFundOrder['user_id'], $aFundOrder['order_id'], $aFundOrder['order_type']);//发送消息什么的
        }

    }


    /*
     * 支付结束处理交易订单
     * */
    protected function changeTrdOrderStatus($uid, $order_id, $order_type,$user_pay_info= array())
    {


    }

    /*
     * 发送消息
     * */
    protected function sendMSG($uid, $order_id, $order_type)
    {

    }


    /*
     * 根据订单id和类型找到支付订单返回支付需要的信息
     * */
    public function doNextStep($orderId, $ordertype,$user_id,$show_url='')
    {
        $arrReturn = array('code' => 'Empty', 'errInfo' => '', 'fund_order_id' => 0);
        $aFundOrder = $this->ci->User_pay_model->get_by_where(array('order_id' => "'".$orderId."'", 'order_type' => $ordertype,'user_id'=>$user_id));

        if (empty($aFundOrder)) {
            $arrReturn['code'] = C('OrderResultError.OrderNotExits');
            $arrReturn['errInfo'] = '未找到订单';

        }
        if ($aFundOrder['status'] == 1) {
            $arrReturn['code'] = C('OrderResultError.Failure');
            $arrReturn['errInfo'] = '订单已支付';
            return $arrReturn;
        }

        $arrReturn['fund_order_id'] = $aFundOrder['fund_order_id'];

        $arrReturn['pay_type'] = $aFundOrder['pay_type'];
        $obj = $this->tryGetPay($aFundOrder['pay_type']);

        //微信JSAPI需要openid

        if ($aFundOrder['pay_type'] == C('PayMethodType.WeixinPayJs')) {
            $openid = empty($_POST['openid'])?'':$_POST['openid'];
            if (empty($openid)) {
                if (empty($_POST['code'])) {
                    $url = $obj->createWxUrl($show_url);
                    $arrReturn['url'] = $url;
                    return $arrReturn;
                }else{
                    $openid = $obj->GetOpenidFromMp($_POST['code']);
                }
            }
            $aFundOrder['fund_order_id'] = $aFundOrder['fund_order_id'].time();//加上时间戳，防止商户订单号重复
            if (!empty($openid)) {
                $urlOrPage = $obj->payRequest($aFundOrder, $openid);
                if (empty($urlOrPage))
                    $arrReturn['code'] = C('OrderResultError.Failure');
                else
                    $arrReturn['code'] = C('OrderResultError.NetPaying');

                $arrReturn['payInfo'] = $urlOrPage;
                $arrReturn['openid'] = $openid;
            }else{
                $arrReturn['payInfo'] = '';
                $arrReturn['openid'] = '';
            }
        } else if ($aFundOrder['pay_type'] == C('PayMethodType.AliPayJs')) {
            //支付宝网页支付
            require_once("../application/libraries/alipay_submit.class.php");

            /**************************请求参数**************************/
            //构造要请求的参数数组，无需改动
            $parameter = array(
                "service" => config_item('service'),
                "partner" => config_item('partner'),
                "seller_id" => config_item('seller_id'),
                "payment_type" => config_item('payment_type'),
                "notify_url" => config_item('notify_url'),
                "return_url" => config_item('return_url') ,//. '?paytype=' . $aFundOrder['type_id'],
                "_input_charset" => trim(strtolower(config_item('input_charset'))),
                "out_trade_no" => $aFundOrder['fund_order_id'],//商户订单号，商户网站订单系统中唯一订单号，必填
                "subject" => $aFundOrder['title'], //订单名称，必填
                "total_fee" => $aFundOrder['amount'], //付款金额，必填
                "show_url" => $show_url,//收银台页面上，商品展示的超链接，必填
                "body" => '支付',//商品描述，可空
                //其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.2Z6TSk&treeId=60&articleId=103693&docType=1
                //如"参数名"	=> "参数值"   注：上一个参数末尾需要“,”逗号。
            );
            $alipaySubmit = new AlipaySubmit();
            $arrReturn['code'] = C('OrderResultError.NetPaying');
            $arrReturn['alipay_html'] = $alipaySubmit->buildRequestForm($parameter, "get", "确认");

        } else if ($aFundOrder['pay_type'] == C('PayMethodType.AlipayApp')) {
            $aFundOrder['fund_order_id'] = $aFundOrder['fund_order_id'].time();//加上时间戳，防止商户订单号重复
            $alipaySign = $obj->getAlipay($aFundOrder);
            $arrReturn['code'] = C('OrderResultError.NetPaying');
            $arrReturn['alipay_param'] = $alipaySign;
        } else if ($aFundOrder['pay_type'] == C('PayMethodType.WeixinPayApp')) {
            $aFundOrder['fund_order_id'] = $aFundOrder['fund_order_id'].time();//加上时间戳，防止商户订单号重复
            $urlOrPage = $obj->payRequest($aFundOrder);
            if (empty($urlOrPage))
                $arrReturn['code'] = C('OrderResultError.Failure');
            else
                $arrReturn['code'] = C('OrderResultError.NetPaying');

            $arrReturn['payInfo'] = $urlOrPage;

        }else if ($aFundOrder['pay_type'] == C('PayMethodType.YinlianPay')) { //银联支付
            $urlOrPage = $obj->payRequest($aFundOrder);
            if (empty($urlOrPage)){
                $arrReturn['code'] = C('OrderResultError.Failure');
            }else{
                $arrReturn['code'] = C('OrderResultError.NetPaying');
            }

            $arrReturn['payInfo'] = $urlOrPage;
        }
        else if ($aFundOrder['pay_type'] == C('PayMethodType.UnionPayDS')) { //银联代收支付

        }else {
            $arrReturn['code'] = C('OrderResultError.Failure');
            $arrReturn['errInfo'] = '支付方式不存在';
        }

        log_message('debug', json_encode($arrReturn));

        return $arrReturn;
    }



}