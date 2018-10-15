<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/15
 * Time: 10:48
 */
class doctor_task_service
{
    public $ci;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model("Order_model");
        $this->ci->load->model("User_inquiry_model");
        $this->ci->load->model("User_register_model");
        $this->ci->load->model("Userinfo_model");
        $this->ci->load->model("Doctor_model");
    }

    public function out_time_set($type = 0)
    {
        if (!$type || !in_array($type,array(1,2))) {
            output_error(-1, '类型异常');
        }
        $this->ci->db = _get_db('default');
        /* 开始事务 */
        $this->ci->db->trans_start();
        if($type == 1){
            $this->inquiry();
        }else{
            $this->register();
        }
        /* 事务完成 */
        $this->ci->db->trans_complete();
        if ($this->ci->db->trans_status() === FALSE) {
            output_error(-1, '更新失败');
        } else {
            output_data();
        }
    }

    private function inquiry()
    {
        $search = "paid = 1 and status = 0 and (type = 1 or type = 2) and createtime < " . strtotime(date('Y-m-d', time()));
        $limit = array();
        $orders = $this->ci->Order_model->fetch_rows($search, "id,item_id", "id asc", $limit);
        //无操作数据
        if (!$orders) {
            output_error(-1, '无操作数据');
        }
        foreach ($orders as $value) {
            $this->ci->Order_model->update_by_id($value['id'], array('status' => -3));
            $this->ci->User_inquiry_model->update_by_id($value['item_id'], array('status' => 4));
            $info = $this->ci->User_inquiry_model->fetch_row('id = ' . $value['item_id'], "user_id,price");
            if ($info) {
                $this->ci->Userinfo_model->setField($info['user_id'], 'balance', 'balance+' . $info['price']);
            }
        }
    }

    private function register()
    {
        $search = "paid = 1 and (status = 1 or status = 0) and createtime < " . strtotime(date('Y-m-d', time()));
        $limit = array();
        $orders = $this->ci->User_register_model->fetch_rows($search, '*', 'id asc', $limit);
        if (!$orders) {
            output_error(-1, '无操作数据');
        }
        foreach ($orders as $value) {
            $this->ci->User_register_model->update_by_id($value['id'], array('status' => 5));
            $search_str = "(type = 3 or type = 5) and status = 0 and item_id = " . $value['id'];
            $this->ci->Order_model->update_by_where($search_str, array('status' => -3));

            $info = $this->ci->Doctor_model->fetch_row('id = ' . $value['doctor_id'], "user_id");
            if ($info) {
                $this->ci->Userinfo_model->setField($info['user_id'], 'balance', 'balance+' . $value['price']);
            }
        }
    }
}