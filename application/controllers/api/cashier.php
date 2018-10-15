<?php defined('BASEPATH') OR exit('No direct script access allowed');
//收银台
class Cashier extends TokenApiController
{   
    public $user_id;
    public function __construct(){
        parent::__construct();
        $this->load->model('inquiry_model');
        $this->load->service('balance_service');
        $this->load->service('user_service');
        $this->load->model('user_model');
        
        $this->user_id = $this->loginUser['user_id'];
        //print_r($this->user_id);exit();
    }

    //收银台
    public function index(){
        $order_id = (int)$this->input->post('order_id');
        $from_type = (int)$this->input->post('from_type');//1重金求医，2专家门诊，3疑难杂症，4名医号脉，5下药单支付
        if (empty($order_id) || empty($from_type)) {
            output_error(-1, '参数不能为空');
        }else{
            $orderInfo_res = $this->balance_service->getOrderInfo(array('user_id'=>$this->user_id,'order_id'=>$order_id,'from_type'=>$from_type));
            if ($orderInfo_res['code']==1) {
                $orderInfo = $orderInfo_res['info'];
                $userPriceInfo = $this->user_service->getUserBalance($this->user_id);
                $pswInfo = $this->user_model->get_by_id($this->user_id,'id,paypsw');
                if (!empty($pswInfo['paypsw'])) {
                    $isPaypsw = 1;
                }else{
                    $isPaypsw = 0;
                }
                if ($userPriceInfo['code']===1) {
                    $data = array('balance'=>$userPriceInfo['data']['balance'],'pay_amount'=>number_format($orderInfo['price'],2,'.',''),'order_id'=>$order_id,'from_type'=>$from_type,'isPaypsw'=>$isPaypsw);
                    output_data($data);
                }else{
                    output_error(-1, $userPriceInfo['msg']);
                }
            }else{
                output_error(-1, $orderInfo_res['msg']);
            }
        }
    }

    //提交支付
    public function submitPayDo(){
        $order_id = (int)$this->input->post('order_id');
        $from_type = (int)$this->input->post('from_type');
        $pay_psw = $this->input->post('pay_psw');
        if (empty($order_id) || empty($from_type) || empty($pay_psw)) {
            output_error(-1, '参数不能为空');
        }else{
            $orderInfo_res = $this->balance_service->getOrderInfo(array('user_id'=>$this->user_id,'order_id'=>$order_id,'from_type'=>$from_type));
            //print_r($orderInfo_res);exit();
            if ($orderInfo_res['code']===1) {
                if ($from_type==1) {
                    if (empty($orderInfo_res['info']['hospital_id'])) {//重金求医的时候，判断是否有医院ID
                        output_error(-1, '订单数据错误');
                    }
                }
                $orderInfo = $orderInfo_res['info'];
                $orderInfo['user_type'] = $this->loginUser['user_type'];
                $check_pay_res = $this->balance_service->checkpayInfo($orderInfo,$from_type,$pay_psw);
                if ($check_pay_res['code']==1) {
                    output_data($check_pay_res);
                }else{
                    output_error($check_pay_res['code'], $check_pay_res['msg']);
                }
            }else{
                output_error(-1, $orderInfo_res['msg']);
            }
        }
    }

    public function pay(){
        $uid = $this->user_id;
        $utype = $this->loginUser['user_type'];
        $amount = (float)$this->input->post('price');
        $pay_type = (int)$this->input->post('pay_type');
        if($amount<=0){
            output_error(-1,'金额不能小于0！');
        }
        if($amount>100000){
            output_error(-2,'金额不能大于100000！');
        }
        $PayMethodName = config_item('PayMethodName');
        if (empty($PayMethodName["$pay_type"])) {
            output_error(-3,'支付方式不存在！');//交易订单创建失败
        }
        PayConfig::$USER_TYPE = $utype;
        $amount_price = $amount;
        $this->load->service('pay_service');
        $this->load->service('trd_service');
        $res = $this->trd_service->createTrd($amount,$amount_price,0,$uid,$utype,$pay_type);

        if($res['code'] < 0){
            output_error($res['code'],'交易订单创建失败！');//交易订单创建失败
            log_message('debug','create pay order failed:'+$res['code']);
        }else{
            $res = $this->trd_service->doNextStep($res['order_id'],$res['order_type'],'http://www.baidu.com');
            output_data($res);//交易订单创建成功
        }

    }

    public function paymethod(){
//        $agent_type = $this->input->post('agent_type');
//        if($agent_type!= 1 && $agent_type!=2){
//            output_error(-1,'');
//        }
//        $data = array(array('title' => '微信','code' => 11),array('title' => '支付宝','code' => 13));
        $data = array(array('title' => '支付宝','code' => 13));
        output_data(array('paymethod'=>$data));
    }

    
}
