<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//余额支付
class balance_service
{   
    public function __construct(){  
		$this->ci = & get_instance();
        $this->ci->load->model('inquiry_model');
        $this->ci->load->model('user_register_model');
        $this->ci->load->model('userinfo_model');
        $this->ci->load->model('doctor_worktime_model');
        $this->ci->load->model('doctor_worktime_detail_model');
        $this->ci->load->model('user_prescrition_model');
        $this->ci->load->service('user_service');
	}
	
    //根据订单ID获取订单信息
	public function getOrderInfo($infoArr){
        if (empty($infoArr) || !is_array($infoArr)) {
            $returnArr = array('code'=>0,'msg'=>'必要参数为空');
        }else{
            $user_id    = $infoArr['user_id'];
            $order_id   = $infoArr['order_id'];
            $from_type  = $infoArr['from_type'];
            if (empty($user_id) || empty($order_id)|| empty($from_type)) {
               $returnArr = array('code'=>0,'msg'=>'必要参数为空');
            }else{
                if ($from_type==1 || $from_type==2  || $from_type==5) {//重金求医、专家门诊、普通门诊
                    $result = $this->ci->user_register_model->get_by_where(array('id'=>$order_id,'paid'=>0,'user_id'=>$user_id),'id,type,user_id,receiver_id,register_num,price,paid,status,doctor_id,worktime_id,worktime_detail_id,hospital_id');
                    if (!empty($result)) {
                        $returnArr = array('code'=>1,'info'=>$result);
                    }else{
                        $returnArr = array('code'=>0,'msg'=>'未找到该订单');
                    }
                }elseif ($from_type==3 || $from_type==4) {//疑难杂症、//名医号脉
                    $result = $this->ci->inquiry_model->get_by_where(array('id'=>$order_id,'paid'=>0,'user_id'=>$user_id),'id,type,user_id,receiver_id,price,paid,status,doctor_id');
                    if (!empty($result)) {
                        $returnArr = array('code'=>1,'info'=>$result);
                    }else{
                        $returnArr = array('code'=>0,'msg'=>'未找到该订单');
                    }
                }elseif ($from_type==6) {//下医单支付
                    $result = $this->ci->user_prescrition_model->get_by_where(array('id'=>$order_id,'user_id'=>$user_id,'status'=>0),'id,user_id,total_price as price,deliver_price,register_id');
                    //print_r($result);exit();
                    if (!empty($result)) {
                        $returnArr = array('code'=>1,'info'=>$result);
                    }else{
                        $returnArr = array('code'=>0,'msg'=>'未找到该订单');
                    }
                }
            }    
        }
        return $returnArr;
    }

    //检测支付信息
    public function checkpayInfo($orderInfo,$fromType,$payPsw){
        //print_r($orderInfo);exit();
        if (!empty($orderInfo) && !empty($payPsw) && is_array($orderInfo)) {
            $userPriceInfo = $this->ci->user_service->getUserBalance($orderInfo['user_id']);
            if ($userPriceInfo['code']===1) {
                if (empty($orderInfo['price'])) {
                    $returnArr = array('code'=>0,'msg'=>'订单金额有误');
                }elseif (empty($userPriceInfo['data']['balance']) || $orderInfo['price']>$userPriceInfo['data']['balance']) {
                    $returnArr = array('code'=>'-4000','msg'=>'余额不足');
                }else{//验证支付密码
                    $pswInfo = $this->ci->userinfo_model->get_by_id($orderInfo['user_id'],'id,paypsw');
                    if (!empty($pswInfo) && $pswInfo['paypsw']==$payPsw) {
                        //减用户余额
                        $minus_res = $this->minusUserbalance($orderInfo,$fromType);
                        if ($minus_res['code']!=1) {
                            $returnArr = array('code'=>$minus_res['code'],'msg'=>$minus_res['msg']); 
                        }else{
                            //改变订单状态
                            $modify_order_res = $this->modifyOrderStatus($orderInfo,$fromType);
                            
                            $returnArr = array('code'=>$modify_order_res['code'],'msg'=>$modify_order_res['msg']); 
                            
                        }
                    }else{
                        $returnArr = array('code'=>-1,'msg'=>'支付密码错误'); 
                    }
                }
            }else{
                $returnArr = array('code'=>0,'msg'=>'未找到用户余额信息');
            }
        }else{
            $returnArr = array('code'=>0,'msg'=>'支付信息错误');
        }
        return $returnArr;
    }

    //改变订单状态
    private function modifyOrderStatus($orderInfo,$fromType){
        $user_id    = $orderInfo['user_id'];
        $order_id   = $orderInfo['id'];
        $this->ci->db = _get_db('default');
        $this->ci->db->trans_strict(FALSE);
        $this->ci->db->trans_begin();//启动事务
        if ($fromType==1 || $fromType==2 || $fromType==5) {//重金求医、专家门诊、普通门诊
            $result = $this->ci->user_register_model->update_by_where(array('id'=>$order_id,'paid'=>0,'user_id'=>$user_id),array('paid'=>1));
            if ($fromType==2 || $fromType==5) {//专家门诊、普通门诊
                $mod_work_det = $this->ci->doctor_worktime_detail_model->update_by_where(array('id'=>$orderInfo['worktime_detail_id'],'user_id'=>$user_id,'status'=>0),array('status'=>1));
                if ($mod_work_det) {
                    //修改已挂号人数
                    $this->ci->load->model('doctor_worktime_model');
                    $modi_orktime = $this->ci->doctor_worktime_model->setInc($orderInfo['worktime_id'],'registered_num',1);
                    if (!empty($modi_orktime)) {
                        //$this->db->trans_commit();
                        $result = 1;
                    }else{
                        $this->db->trans_rollback();
                        $result = 0;
                    }
                }else{
                    $this->db->trans_rollback();
                    $result = 0;
                }
            }
        }elseif ($fromType==3 || $fromType==4) {//疑难杂症、//名医号脉
            $result = $this->ci->inquiry_model->update_by_where(array('id'=>$order_id,'paid'=>0,'user_id'=>$user_id),array('paid'=>1));
        }elseif ($fromType==6) {//处方单下单
            $result = $this->ci->user_prescrition_model->update_by_where(array('id'=>$order_id,'status'=>0,'user_id'=>$user_id),array('status'=>1));
            if ($result) {
                $result = $this->ci->user_register_model->update_by_where(array('id'=>$orderInfo['register_id'],'user_id'=>$user_id),array('status'=>6));
            }
        }
        if (empty($result)) {
            $this->ci->db->trans_rollback();
            $returnArr = array('code'=>0,'msg'=>'修改订单状态失败');
            return $returnArr;
        }else{
            if (in_array($fromType, array('1','3','4'))) {//如果为1重金求医、3疑难杂症、4名医号脉 则添加order表数据
                $this->ci->load->service('order_service');
                switch ($fromType) {
                    case '1':
                        $this->ci->load->model('hospital_model');
                        $hospitalInfo = $this->ci->hospital_model->get_by_id($orderInfo['hospital_id'],'longitude,latitude');
                        $order_dat = array('type'=>3,'doctor_id'=>'0','item_id'=>$order_id,'longitude'=>$hospitalInfo['longitude'],'latitude'=>$hospitalInfo['latitude']);//重金求医
                        break;
                    case '3':
                        $order_dat = array('type'=>1,'doctor_id'=>'0','item_id'=>$order_id);//疑难杂症
                        break;
                    case '4':
                        $order_dat = array('type'=>2,'doctor_id'=>$orderInfo['doctor_id'],'item_id'=>$order_id);//名医号脉
                        break;
                }
                $res_order = $this->ci->order_service->addOrder($order_dat);
                if ($res_order) {
                    $this->ci->db->trans_commit();
                    $returnArr = array('code'=>1,'msg'=>'支付成功');
                    return $returnArr;
                }else{
                    $this->ci->db->trans_rollback();
                    $returnArr = array('code'=>0,'msg'=>'支付失败');
                    return $returnArr;
                }
            }
            $this->ci->db->trans_commit();
            $returnArr = array('code'=>1,'msg'=>'支付成功');
            return $returnArr;
        }
    }

    //减用户余额
    private function minusUserbalance($orderInfo,$fromType){
        $userPriceInfo = $this->ci->user_service->getUserBalance($orderInfo['user_id']);//再次检测用户余额，避免出错
        if ($userPriceInfo['code']===1 && $userPriceInfo['data']['balance']>=$orderInfo['price']) {
            $this->ci->db = _get_db('default');
            $this->ci->db->trans_strict(FALSE);
            $this->ci->db->trans_begin();//启动事务
            $minus_res = $this->ci->userinfo_model->setDec($orderInfo['user_id'],'balance',$orderInfo['price']);
            if ($minus_res) {
                switch (intval($fromType)) {
                    case '1'://重金求医
                        $priceType = 1;
                        $desc = '重金求医';
                        break;
                    case '2'://专家门诊
                        $priceType = 2;
                        $desc = '专家门诊';
                        break;
                    case '3'://疑难杂症
                        $priceType = 4;
                        $desc = '疑难杂症';
                        break;
                    case '4'://名医号脉
                        $priceType = 5;
                        $desc = '名医号脉';
                        break;
                    case '5'://普通门诊
                        $priceType = 3;
                        $desc = '普通门诊';
                        break;
                    case '6'://下医单
                        $priceType = 6;
                        $desc = '处方单支付';
                        break;         
                }
                $add_pricelist_res = $this->ci->user_service->fundrecord_add($orderInfo['user_id'],$orderInfo['user_type'],-$orderInfo['price'],$priceType,$desc,$orderInfo['id']);//添加消费记录
                if ($add_pricelist_res) {
                    $this->ci->db->trans_commit();
                    $returnArr = array('code'=>1,'msg'=>'扣款成功');
                }else{
                    $this->ci->db->trans_rollback();
                    $returnArr = array('code'=>0,'msg'=>'扣款失败');
                }
            }else{
                $this->ci->db->trans_rollback();
                $returnArr = array('code'=>0,'msg'=>'扣款失败');
            }
        }else{
            $this->ci->db->trans_rollback();
            $returnArr = array('code'=>-4000,'msg'=>'余额不足');
        }
        return $returnArr;
    }    

    
}