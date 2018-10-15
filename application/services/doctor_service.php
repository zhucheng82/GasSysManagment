<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class doctor_service
{
    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('doctor_model');
        $this->ci->load->model('service_model');
        $this->ci->load->model('user_price_lists_model');
    }

    //根据医生ID获取医生基本信息(毛卫华)
    public function getDoctorInfo($doctor_id)
    {
        if (empty($doctor_id)) {
            $returnArr = array('code' => 0, 'msg' => '医生ID不能为空');
        } else {
            $doctorInfo = $this->ci->doctor_model->getDoctorInfo($doctor_id);
            if (empty($doctorInfo)) {
                $returnArr = array('code' => 0, 'msg' => '医生信息不存在');
            } else {
                $returnArr = array('code' => 1, 'msg' => '获取成功', 'data' => $doctorInfo);
            }
        }
        return $returnArr;
    }

    //根据医生ID获取医生开通服务
    public function getDoctorServiceInfo($ServiceId, $doctor_id)
    {
        if (empty($ServiceId) || empty($doctor_id)) {
            $returnArr = array('code' => 0, 'msg' => '医生ID,或服务类型不能为空');
        } else {
            $ServiceInfo = $this->ci->service_model->get_by_where(array('type' => $ServiceId, 'doctor_id' => $doctor_id));
            if (empty($ServiceInfo)) {
                $returnArr = array('code' => 0, 'msg' => '该医生未开通此服务');
            } else {
                $returnArr = array('code' => 1, 'msg' => '获取成功', 'data' => $ServiceInfo);
            }
        }
        return $returnArr;
    }

    /**
     * 医生端支付接口处理流程
     * 1 文章购买支付   2 服务购买支付
     * @param int $type
     * @param array $data
     */
    public function pay($type = 0, $data = array())
    {
        if ($type == 1)
            $this->article_pay($data);
        else if ($type == 2)
            $this->service_pay($data);
        else
            output_error(-1, '支付类型异常');;
    }

    /**
     * 文章都买流程
     * @param array $data
     */
    private function article_pay($data = array())
    {
        $this->ci->load->model("Doctor_buy_model");
        $this->ci->load->model("Doctor_article_model");
        $this->ci->load->model("Userinfo_model");
        if (!isset($data['article_id']) || !isset($data['doctor_id']) || !isset($data['user_id']) || !$data['article_id'] || !$data['doctor_id'] || !$data['user_id'])
            output_error(-1, '参数异常');
        $article_info = $this->ci->Doctor_article_model->fetch_row("id = " . $data['article_id'], "price,doctor_id,status");
        $balance = $this->ci->Userinfo_model->fetch_field("id = " . $data['user_id'], 'balance');
        $buy_flag = $this->ci->Doctor_buy_model->count("doctor_id = " . $data['doctor_id'] . " and article_id = " . $data['article_id']);
        if (!$article_info)
            output_error(-1, '文章信息校验失败');
        if ($article_info['status'] == 0)
            output_error(-1, '该文章未审核');
        if ($article_info['doctor_id'] == $data['doctor_id'])
            output_error(-1, '不可购买自己的文章');
        if ($buy_flag)
            output_error(-1, '你已经购买过了这篇文章');
        if ($balance < $article_info['price'])
            output_error(-1, '余额不足');
        $userId = $this->ci->Doctor_model->fetch_field("id = " . $article_info['doctor_id'], 'user_id');//作者ID
        $user_info = $this->ci->Userinfo_model->fetch_row("id = $userId", 'balance,username');
        $user_balance = $user_info['balance'];
        if (!$userId)
            output_error(-1, '当前购买医生信息校验失败');
        $this->ci->db = _get_db('default');
        /* 开始事务 */
        $this->ci->db->trans_start();
        /* 事务处理流程 */
        $article_buy = $data;
        $article_buy['price'] = $article_info['price'];
        $article_buy['createtime'] = time();
        unset($article_buy['user_id']);
        $insert_id = $this->ci->Doctor_buy_model->insert_ignore($article_buy);
        $this->ci->Doctor_article_model->setField($data['article_id'], 'number', 'number+1');
        $record = array(
            'user_id' => $data['user_id'],
            'user_type' => 2,
            'item_id' => $insert_id,
            'price' => -$article_info['price'],
            'type' => 8,
            'desc' => "文章购买",
            'createtime' => time(),
        );
        $this->ci->user_price_lists_model->insert_ignore($record);

        $this->ci->Userinfo_model->update_by_id($data['user_id'], array('balance' => $balance - $article_info['price']));
        $this->ci->Userinfo_model->update_by_id($userId, array('balance' => $user_balance + $article_info['price']));
        /* 结束事务 */
        $this->ci->db->trans_complete();
        if ($this->ci->db->trans_status() === FALSE) {
            output_error(-1, '购买失败');
        } else {
            /*发送消息给作者医生*/
            $this->ci->load->service('message_service');
            $push_arr = array('{name}' => $this->ci->loginUser['doctor_real_name'], '{num}' => $article_info['price']);
            $data = array('msgtype' => '204', 'itemid' => $data['article_id']);
            $this->ci->message_service->send_sys(21, $userId, 2, $push_arr, $data, $push_arr, $user_info['username']);

            output_data();
        }
    }

    /**
     * 服务购买
     * @param array $data
     */
    private function service_pay($data = array())
    {
        $this->ci->load->model("Service_model");
        $this->ci->load->model("Doctor_model");
        $this->ci->load->model("Userinfo_model");
        if (!isset($data['doctor_id']) || !isset($data['user_id']) || !isset($data['price']) ||
            !$data['doctor_id'] || !$data['user_id'] || !$data['price']
        )
            output_error(-1, '参数异常');
        $service_config = $this->ci->config->item('doctor_service');
        $service_config = $service_config[0];
        $balance = $this->ci->Userinfo_model->fetch_field("id = " . $data['user_id'], 'balance');
        $buy_flag = $this->ci->Service_model->count("doctor_id = " . $data['doctor_id'] . " and type = 1");
        if (!$service_config)
            output_error(-1, '服务配置文件校验失败');
        if ($buy_flag)
            output_error(-1, '你已经购买过该服务');
        if ($balance < $service_config['price'])
            output_error(-1, '余额不足');
        $this->ci->db = _get_db('default');
        /* 开始事务 */
        $this->ci->db->trans_start();
        /* 事务处理流程 */
        $service_buy = $data;
        $service_buy['createtime'] = time();
        $service_buy['type'] = 1;
        unset($service_buy['user_id'], $service_buy['price']);
        $insert_id = $this->ci->Service_model->insert_ignore($service_buy);//famous_price

        $record = array(
            'user_id' => $data['user_id'],
            'user_type' => 2,
            'item_id' => $insert_id,
            'price' => -$service_config['price'],
            'type' => 9,
            'desc' => "名医号脉购买",
            'createtime' => time(),
        );
        $this->ci->user_price_lists_model->insert_ignore($record);
        $this->ci->Doctor_model->update_by_id($data['doctor_id'], array('famous_price' => $data['price'], 'service_flag' => 1));
        $this->ci->Userinfo_model->update_by_id($data['user_id'], array('balance' => $balance - $service_config['price']));
        /* 结束事务 */
        $this->ci->db->trans_complete();
        if ($this->ci->db->trans_status() === FALSE) {
            output_error(-1, '购买失败');
        } else {
            output_data();
        }
    }
}