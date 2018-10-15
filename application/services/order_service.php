<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_service
{   

    public function __construct()
	{  
		$this->ci = & get_instance();
		$this->ci->load->model('Userinfo_model');

		$this->ci->load->library('encryption');
	}
	
    /*
    *根据order 表去关联对应的问诊表或者挂号表数据
    */
	public function get_order_info($order_list)
    {
        if (!empty($order_list)) {
            foreach ($order_list as $key => &$value) {
                if ($value['type'] == 1||$value['type'] == 2){
                    $this->ci->load->model('User_inquiry_model');
                    $order_info = $this->ci->User_inquiry_model->get_by_id($value['item_id'],'receiver_id,receiver_info,desc');
                } elseif($value['type'] == 3||$value['type'] == 4) {
                    $this->ci->load->model('User_register_model');
                    $order_info = $this->ci->User_register_model->get_by_id($value['item_id'],'receiver_id,receiver_info,desc');
                }
                $order_info['receiver_info'] = empty($order_info['receiver_info'])?'':unserialize($order_info['receiver_info']);
                $value['order_info'] = $order_info;
            }
            return $order_list;
        }else {
            return array();
        }
    }


    //根据用户ID获取用户对应的余额(maoweihua)
    /*public function getUserBalance($user_id){
        if (empty($user_id)) {
            $returnArr = array('code' =>0 ,'msg'=>'用户ID不能为空');
        }else{
            $userInfo = $this->ci->userinfo_model->get_by_where(array('id'=>$user_id,'status'=>1),'balance');
            if (empty($userInfo)) {
               $returnArr = array('code' =>0 ,'msg'=>'用户信息不存在');
            }else{
                $returnArr = array('code' =>1 ,'msg'=>'获取成功','data'=>$userInfo);
            }
        }
        return $returnArr;
    }

    //根据用户ID获取就诊人信息
    public function get_user_receiver_info($user_id){
        if (empty($user_id)) {
            $returnArr = array('code' =>0 ,'msg'=>'用户ID不能为空');
        }else{
            $this->ci->load->model('user_receiver_model');
            $receiverInfo = $this->ci->user_receiver_model->get_by_where(array('status'=>1,'is_default'=>1,'user_id'=>$user_id));
            if (empty($receiverInfo)) {
               $returnArr = array('code' =>0 ,'msg'=>'用户就诊人信息不存在');
            }else{
                $returnArr = array('code' =>1 ,'msg'=>'获取成功','data'=>$receiverInfo);
            }
        }
        return $returnArr;
    }*/

    //生成order表数据(maoweihua)
    public function addOrder($data){
        if (empty($data)) {
            return false;
        }else{
            $this->ci->load->model('order_model');
            $dat['type']        = $data['type'];
            $dat['doctor_id']   = $data['doctor_id'];
            $dat['item_id']     = $data['item_id'];
            $dat['status']      = 0;
            if (!empty($data['latitude']) && !empty($data['longitude'])) {
                $dat['latitude']    = $data['latitude'];
                $dat['longitude']   = $data['longitude'];
            }
            $dat['createtime']  = time();
            if ($this->ci->order_model->insert_string($dat)) {
               return true; 
            }else{
               return false;  
            }
        }
    }

}