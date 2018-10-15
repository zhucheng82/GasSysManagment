<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class trd_service extends pay_service
{
    public function __construct()
    {
        parent::__construct();
    }

    /*
     *创建交易业务在这里实现
     * */
    public function createTrd($funds_id, $amount, $uid, $pay_type, $order_id, $order_type, $ip = '')
    {
        $res = array(
            'code' => 1,
            'order_id' => $order_id,
            'order_type' => $order_type,
            'fund_order_id' => 0
        );
        if ($order_id) {
            switch ($order_type) {
                case '1':
                    $title = '还款';
                    break;
                case '2':
                    $title = '充值';
                    break;
                case '3':
                    $title = '提现';
                    break;
                case '4':
                    $title = '贷款到账';
                    break;
            }
            $ss = $this->createOrder($funds_id, $amount, $title, $pay_type, $order_id, $order_type, $uid);

            if ($ss < 0) {
                $res['code'] = $ss;
            } else {
                $res['fund_order_id'] = $ss;
            }
        } else {
            log_message('debug', '创建交易业务失败！');
            $res['code'] = -10;
        }

        return $res;
    }

    /*
     * 支付结束处理交易订单 覆盖父类
     * */
    protected function changeTrdOrderStatus($uid, $order_id, $order_type, $user_pay_info= array())
    {
        if ($order_type == 1) {//还款
            //处理订单状态
            return $this->dealRepayment($uid, $order_id);
        }
        if ($order_type == 2) {//充值
            return $this->dealRecharge($uid, $order_id);
        }

    }

    //还款支付成功后处理
    private function dealRepayment($uid, $order_id)
    {
        log_message('debug', '@@@@@@@ dealRepayment-'.'uid:'.$uid.'    order_id:'.$order_id);
        $repaymentScheduleIds = $order_id;//存放的是还款清单id，格式如：123;124;125;126
        $arrToRepaymentScheduleId = explode(',',$repaymentScheduleIds);

        $borrowingId = '';
        $this->ci->load->model('Repayment_schedule');
        $this->ci->load->model('Borrowing_model');

        $this->ci->db = _get_db('default');
        $this->ci->db->trans_strict(FALSE);
        $this->ci->db->trans_begin();//启动事务

        foreach ($arrToRepaymentScheduleId as $repaymentId){

            $data = array(
                'repayment_time' => time(),
                'repayment_status' => 1,
            );
            $res = $this->ci->Repayment_schedule->update_by_id($repaymentId, $data);
            if (!$res) {
                $this->ci->db->trans_rollback();
                log_message('debug', '更新还款状态状态失败！');
                return false;
            }

            if(empty($borrowingId))
            {
                $where = array('id'=>$repaymentId ,'user_id'=>$uid);
                $schedule = $this->ci->Repayment_schedule->get_by_where($where);
                if (empty($schedule)) {
                    $this->ci->db->trans_rollback();
                    log_message('debug', '获取借款id失败！');
                    return false;
                }
                $borrowingId = $schedule['borrowing_id'];
            }
        }

        /*
         * 判读是否全部还清，全部还清，需要把borrowing表标识
         */
        //还款状态 0:未还款 1：已还款
        $repayment_status = 0;
        $where = array('user_id'=>$uid, 'borrowing_id'=>$borrowingId, 'repayment_status'=>$repayment_status);
        $repaymentScheduleList = $this->ci->Repayment_schedule->get_list($where);

        if (empty($repaymentScheduleList)) {//无未还款清单,表明已经还清
            $data = array(
                'status' => 3,//-1—审批被拒绝；0—申请中；1—通过申请；2-还款中 3-还款结束 4-因故被终止
            );
            $res = $this->ci->Borrowing_model->update_by_id($borrowingId, $data);
            if (!$res) {
                $this->ci->db->trans_rollback();
                log_message('debug', '更新borrow表还款状态状态失败！');
                return false;
            }
        }
        else {
            $data = array(
                'status' => 2,//-1—审批被拒绝；0—申请中；1—通过申请；2-还款中 3-还款结束 4-因故被终止
            );
            $res = $this->ci->Borrowing_model->update_by_id($borrowingId, $data);
            if (!$res) {
                $this->ci->db->trans_rollback();
                log_message('debug', '更新borrow表还款状态状态失败！');
                return false;
            }
        }

        /*--------------------暂时注释------------------
        // 添加记录
        $this->ci->load->service('funds_service');
        //添加日志
        $res =  $this->ci->funds_service->account_edit($uid, $order_id, $order['amount_price'], 4);

        if ($res < 0) {
            $this->ci->db->trans_rollback();
            log_message('debug', '添加充值记录失败！');
            return $res;
        }
        *--------------------暂时注释------------------*/

        $this->ci->db->trans_commit();

        log_message('debug', '@@@@@@@----- dealRepayment succeed!!! -----@@@@@@@');

        return true;
    }

    //消费者充值
    protected function dealRecharge($uid, $order_id)
    {
        $this->ci->load->model('User_recharge_model');
        $order = $this->ci->User_recharge_model->get_by_id($order_id);
        log_message('debug',json_encode($order));
        $data = array(
            'update_time' => time(),
            'pay_status' => 1,
        );
        $this->ci->db = _get_db('default');
        $this->ci->db->trans_strict(FALSE);
        $this->ci->db->trans_begin();//启动事务
        // 获得余额
        $this->ci->load->model('user_model');
        $rechargeAmount = (float)$order['amount_price'] + (float)$order['reward'];
        $minus_res = $this->ci->user_model->setInc($uid, 'balance', $rechargeAmount);
        //$minus_res = $this->ci->user_model->setInc($uid, 'acct_recharge', (float)$order['amount_price']);
        if (!$minus_res) {
            $this->ci->db->trans_rollback();
            log_message('debug', '更新余额失败！！');
            return false;
        }
        /*--------------------暂时注释------------------
        // 添加记录
        $this->ci->load->service('funds_service');
        //添加日志
        $res =  $this->ci->funds_service->account_edit($uid, $order_id, $order['amount_price'], 4);

        if ($res < 0) {
            $this->ci->db->trans_rollback();
            log_message('debug', '添加充值记录失败！');
            return $res;
        }
        *--------------------暂时注释------------------*/
        $res = $this->ci->User_recharge_model->update_by_id($order_id, $data);
        if (!$res) {
            $this->ci->db->trans_rollback();
            log_message('debug', '更新充值订单状态失败！');
        } else {
            $this->ci->db->trans_commit();

            //执行提现申请成功后，给放款方发送短信通知提醒
            $push_arr = array('{amount}' => $rechargeAmount);
            $msgTempId = 8;//还款通知 //消息模板id 1还款提醒 2逾期提醒 3审批贷款通知 4提现成功 5保证金返还客户 6自动还款通知 7提现申请通知客户 8用户充值通知

            $this->ci->load->service('message_service');
            //$this->ci->load->model('Cache_model');
            $this->ci->load->model('Notify_mobile_list_model');

            /*
            $notifyMobileList = C('withdraw_notify_list');
            log_message('debug', '@@@@@@recharge $notifyMobileList:'.$notifyMobileList);
            if (isset($notifyMobileList) && !empty($notifyMobileList))
            {
                $arrMobile = explode(',',$notifyMobileList);
                $arrMobile = array_filter($arrMobile);
                $arrMobile = array_unique($arrMobile);//去掉重复id

                foreach ($arrMobile as $mobile)
                {
                    $this->ci->message_service->send_sms($msgTempId, $push_arr, $mobile);
                }
            }
            */

            $arrMobile = $this->ci->Notify_mobile_list_model->get_list(array(), 'mobile');
            log_message('debug', '(dealRecharge)$arrMobile:'.json_encode($arrMobile));
            foreach ($arrMobile as $mobile)
            {
                log_message('debug', '$mobile:'.json_encode($mobile).' num:'.$mobile['mobile']);
                $this->ci->message_service->send_sms($msgTempId, $push_arr, $mobile['mobile']);
            }

        }
        return $res;
    }

    /*
     * 发送消息 覆盖父类
     * */
    protected function sendMSG($uid, $order_id, $order_type)
    {

    }

    public function test()
    {
        $test = array(
            'fund_order_id' => '10',
            'seq_no' => '12312312312312',
        );
        $arrReturn = array('code' => 'Empty', 'errInfo' => '', 'fund_order_id' => 0, 'order_id' => 0);
        $this->dealNetPayed($test, $arrReturn);
        return $arrReturn;
    }

/*
 * 代付回调处理
 * */
    public function dfrespond($orderid,$rescode){
        log_message('debug','7777777777777777777'.json_encode($orderid));
        $id = str_replace('jiedaibaob','',$orderid);
        $order = $res = $this->Withdraw_model->get_by_id($id);
        $this->load->model('Withdraw_model');
//        $this->db = _get_db('default');
//        $this->db->trans_begin();//启动事务
        if($rescode=='00' || $rescode =='A6'){
            //todo 提现成功;;发送短信,
            $this->ci->load->model('user_model');
            $this->ci->load->service('message_service');
            $data = array('status'=>2);
            $push_arr = array('{amount}' => $order['amount']);
            $msgdata = array('msgtype' => '4', 'itemid' => $order['id']);
            $user_info = $this->user_model->fetch_row('user_id ='.$order['user_id'], 'user_name');
            $msgTempId = 4;//还款通知 //消息模板id 1还款提醒 2逾期提醒 3审批贷款通知 4提现成功 5保证金返还客户
            $res = $this->message_service->send_sys($msgTempId, $order['user_id'], 1, $push_arr, $msgdata, $push_arr, $user_info['user_name']);
        }else{
            //todo 提现失败;;发送短信,
            $data = array('status'=>4);

        }

        $res = $this->Withdraw_model->update_by_id($id,$data);
        if(!$res){
//            $this->db->trans_rollback();
            if($data['status'] ==4){
                log_message('error','代付失败回调成功,修改提现订单状态失败,id:'+$orderid);
            }else{
                log_message('error','代付成功回调成功,修改提现订单状态失败,id:'+$orderid);
            }
            return;
//                output_error(-4,'更新失败');
        }
//        $this->db->trans_commit();
        if($data['status'] ==4){
            $this->load->model('User_model');
            $res2 = $this->User_model->setInc($order['user_id'],'balance',$order['amount']);
            if(!$res2){
                log_message('error','代付失败回调成功,返回用户账户金额失败,id:'+$orderid);
                return;

            }
        }
        log_message('debug','提现成功,id:'+$orderid);
    }


}