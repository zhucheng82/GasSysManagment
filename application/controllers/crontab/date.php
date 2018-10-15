<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Date extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        $this->load->service('cron_service');
        $this->load->model('Repayment_schedule');
        $this->load->service('message_service');
        $this->load->model('user_model');
        $this->load->model('Borrowing_model');

        $this->load->service('pay_service');
        $this->load->service('trd_service');
        $this->load->service('repayment_service');

    }

    /**
     * 每天定时处理任务(自动评价)
     */
    public function index(){
        //echo $this->cron_service->commOrder();
        //echo $this->cron_service->CommentByregister();
    }

    //每天上午9点跑一遍
    public function checkRepayment()
    {
        $time = time();//当前时间
        $afterTime = $time + 86400*2;//3天后时间（提前三天通知）

        log_message('debug','$time:'.$time.'$afterTime'.$afterTime);
        //即将到期记录(并且未通知)
        $where = array('repayment_status'=>0,'repayment_deadline<'=>$afterTime,'repayment_deadline>'=>$time,'repayment_notify_flag'=>0);
        $willExpireRepayment = $this->Repayment_schedule->get_list($where);
        if (!empty($willExpireRepayment))
        {
            log_message('debug','$willExpireRepayment'.json_encode($willExpireRepayment));
            foreach($willExpireRepayment as $repaymentSchedule)
            {
                //
                $repaymentSchedule['repayment_notify_flag'] = 1;

                /*发送消息给用户*/

                $userId = $repaymentSchedule['user_id'];
                $deadline = date('Y-m-d',$repaymentSchedule['repayment_deadline']);
                $amount = $repaymentSchedule['repayment_amount'] + $repaymentSchedule['repayment_interest'];
                $push_arr = array('{amount}' => $amount, '{deadline}' => $deadline);
                $data = array('msgtype' => '1', 'itemid' => $repaymentSchedule['borrowing_id']);
                $user_info = $this->user_model->fetch_row("user_id = $userId", 'user_name');
                $msgTempId = 1;//还款通知 //消息模板id 1还款提醒 2逾期提醒 3审批贷款通知 4提现成功 5保证金返还客户 6
                $res = $this->message_service->send_sys($msgTempId, $repaymentSchedule['user_id'], 1, $push_arr, $data, $push_arr, $user_info['user_name']);

                if ($res)
                {
                    $this->Repayment_schedule->update_by_id($repaymentSchedule['id'],array('repayment_notify_flag'=>1));
                }

            }
        }

        //逾期记录
        $where = array('repayment_status'=>0,'repayment_deadline<'=>$time,'overdue_notify_flag'=>0);
        $overdueRepayment = $this->Repayment_schedule->get_list($where);
        if (!empty($overdueRepayment))
        {
            log_message('debug','$overdueRepayment@@@@@-----'.json_encode($overdueRepayment));
            foreach($overdueRepayment as $repaymentSchedule)
            {
                //
                log_message('debug','$overdueRepayment loop###########');
                $repaymentSchedule['overdue_notify_flag'] = 1;

                //标记有逾期，借贷介绍后，不会把扣留金返还给用户
                $res = $this->Borrowing_model->update_by_id($repaymentSchedule['borrowing_id'],array('has_delay'=>1));

                log_message('debug','$overdueRepayment process1');

                /*发送消息给用户*/

                $userId = $repaymentSchedule['user_id'];
                $deadline = date('Y-m-d',$repaymentSchedule['repayment_deadline']);
                $amount = $repaymentSchedule['repayment_amount'] + $repaymentSchedule['repayment_interest'];
                $push_arr = array('{amount}' => $amount, '{deadline}' => $deadline);
                $data = array('msgtype' => '2', 'itemid' => $repaymentSchedule['borrowing_id']);//自定义消息内容
                $user_info = $this->user_model->fetch_row("user_id = $userId", 'user_name');
                $msgTempId = 2;//逾期通知 //消息模板id 1还款提醒 2逾期提醒 3审批贷款通知 4提现成功 5保证金返还客户
                $res = $this->message_service->send_sys($msgTempId, $repaymentSchedule['user_id'], 1, $push_arr, $data, $push_arr, $user_info['user_name']);

                log_message('debug','send_sys#####'.json_encode($res));

                if ($res)
                {
                    $res = $this->Repayment_schedule->update_by_id($repaymentSchedule['id'],array('overdue_notify_flag'=>1));

                    log_message('debug','update_by_id@@@@@'.json_encode($res));
                }


            }

            log_message('debug','$overdueRepayment@@@@@-----end');
        }

        //已经结束的借贷,没有延期，没有归还扣留金的，要返还扣留金
        $where = array('status'=>3,'has_delay'=>0,'is_detain_amount_reback'=>0);
        $finishedBorrowing = $this->Borrowing_model->get_list($where);
        if (!empty($finishedBorrowing))
        {
            foreach($finishedBorrowing as $borrowingItem)
            {
                //
                $amount = $borrowingItem['detain_amount'];

                /*发送消息给用户*/

                $userId = $borrowingItem['user_id'];
                $push_arr = array('{product}' => $borrowingItem['product_name'],'{amount}' => $amount);
                $data = array('msgtype' => '5', 'itemid' => $borrowingItem['id']);
                $user_info = $this->user_model->fetch_row("user_id = $userId", 'user_name');//手机号
                $msgTempId = 5;//消息模板id 1还款提醒 2逾期提醒 3审批贷款通知 4提现成功 5保证金返还客户
                $res = $this->message_service->send_sys($msgTempId, $borrowingItem['user_id'], 1, $push_arr, $data, $push_arr, $user_info['user_name']);

                //更新数据
                $this->Borrowing_model->db->trans_strict(FALSE);
                $this->Borrowing_model->db->trans_begin();//启动事务

                $this->Borrowing_model->update_by_id($borrowingItem['id'],array('is_detain_amount_reback'=>1));
                $this->user_model->setInc($userId, 'balance', (float)$amount);

                if ($this->Borrowing_model->db->trans_status() === FALSE)
                {
                    log_message('debug', '++++++ 更新归还扣留金状态和用户余额失败');
                    $this->Borrowing_model->db->trans_rollback();
                }
                else
                {
                    $this->Borrowing_model->db->trans_commit();
                }

            }
        }
    }

    //每晚11点跑一遍
    public function autoRepayment()
    {
        log_message('debug', 'performance autoRepayment');
        //自动还款（当账户余额足够还款）
        $time = time();//当前时间
        $afterOneDayTime = $time + 86400;//1天后时间
        //即将到期记录
        $where = array('repayment_status'=>0,'repayment_deadline<'=>$afterOneDayTime,'repayment_deadline>'=>$time);
        $willExpireOnTodayRepayment = $this->Repayment_schedule->get_list($where);
        if (!empty($willExpireOnTodayRepayment))
        {
            log_message('debug','$willExpireOnTodayRepayment'.json_encode($willExpireOnTodayRepayment));
            foreach($willExpireOnTodayRepayment as $repaymentSchedule)
            {
                $userId = $repaymentSchedule['user_id'];
                $borrowingId = $repaymentSchedule['borrowing_id'];

                $user_info = $this->user_model->fetch_row("user_id = $userId", 'user_name, balance');

                if(!empty($user_info))
                {
                    $repaymentScheduleId = $repaymentSchedule['id'];
                    //$amount = $repaymentSchedule['repayment_amount'] + $repaymentSchedule['repayment_interest'];
                    $amount = $this->repayment_service->calculateRepaymentAmount($borrowingId, $repaymentScheduleId);

                    $balance = 0.00;
                    if (isset($user_info['balance']))
                    {
                        $balance = $user_info['balance'];
                    }
                    if ($balance >= $amount)//余额足够还款
                    {

                        //更新数据
                        //$this->Repayment_schedule->db->trans_strict(FALSE);
                        $this->Repayment_schedule->db->trans_begin();//启动事务

                        //订单创建
                        $order_type = 1;//1还款 2充值
                        $pay_type = 17;//余额
                        $fund_id = 0;
                        $trdInfo = $this->trd_service->createTrd($fund_id, $amount, $userId, $pay_type, $repaymentScheduleId, $order_type);

                        if ($trdInfo['code'] < 0) {
                            log_message('debug', '############create auto repayment pay order failed:' + $trdInfo['code']);
                            $this->Repayment_schedule->db->trans_rollback();
                            continue;//交易订单创建失败
                        }

                        //实际还款金额
                        $data = array('real_repayment_amount'=> $amount);
                        $res = $this->Repayment_schedule->update_by_id($repaymentScheduleId, $data);
                        if($res == 0)//更新失败，有可能是值未变
                        {
                            $where = array('id'=>$repaymentScheduleId);
                            $realRepaymentAmountInfo = $this->Repayment_schedule->get_by_where($where,'real_repayment_amount');
                            if(empty($realRepaymentAmountInfo) || number_format($amount, 2, '.', '') != number_format($realRepaymentAmountInfo['real_repayment_amount'], 2, '.', ''))
                            {


                                log_message('debug', '############update repayment_schedule info failed，$amount:'.$amount.'$realRepaymentAmountInfo[real_repayment_amount]'.$realRepaymentAmountInfo['real_repayment_amount']);
                                $this->Repayment_schedule->db->trans_rollback();
                                continue;
                            }
                        }
                        //账号金额
                        $res = $this->user_model->setDec($userId, 'balance', (float)$amount);//余额扣除
                        if(!$res)
                        {
                            log_message('debug', '############update user balance info failed');
                            $this->Repayment_schedule->db->trans_rollback();
                            continue;
                        }
                        log_message('debug', '############setDec sql:'.$this->user_model->db->last_query());

                        //还款完成的处理工作。
                        $resDealRepayment = $this->afterRepaymentFinish($userId, $trdInfo['order_id']);
                        if (!$resDealRepayment)
                        {
                            log_message('debug', '############afterRepaymentFinish failed');
                            $this->Repayment_schedule->db->trans_rollback();
                            continue;//交易订单创建失败
                        }

                        $res = $this->trd_service->updateFundOrderStatus($trdInfo['fund_order_id']);
                        if(!$res)
                        {
                            log_message('debug', '############updateFundOrderStatus failed,fund_order_id'.$trdInfo['fund_order_id']);
                            $this->Repayment_schedule->db->trans_rollback();
                            continue;
                        }
                        else
                        {
                            log_message('debug', '############updateFundOrderStatus succeed,fund_order_id'.$trdInfo['fund_order_id']);
                        }

                        if ($this->Repayment_schedule->db->trans_status() === FALSE)
                        {
                            log_message('debug', '++++++ 自动还款失败');
                            $this->Repayment_schedule->db->trans_rollback();
                        }
                        else
                        {
                            $this->Repayment_schedule->db->trans_commit();

                            log_message('debug', '@@@@++++##### autorepayment borrowing succeed!!!');

                            //成功后消息通知
                            $deadline = date('Y-m-d',$repaymentSchedule['repayment_deadline']);
                            $push_arr = array('{amount}' => $amount, '{deadline}' => $deadline);
                            $data = array('msgtype' => '6', 'itemid' => $repaymentSchedule['borrowing_id']);
                            $msgTempId = 6;//还款通知 //消息模板id 1还款提醒 2逾期提醒 3审批贷款通知 4提现成功 5保证金返还客户 6自动还款通知
                            $res = $this->message_service->send_sys($msgTempId, $repaymentSchedule['user_id'], 1, $push_arr, $data, $push_arr, $user_info['user_name']);
                        }

                    }

                }

            }
        }

        //逾期还款自动还款
        $where = array('repayment_status'=>0,'repayment_deadline<'=>$time);
        $hasBeenExpiredRepayment = $this->Repayment_schedule->get_list($where);
        if (!empty($hasBeenExpiredRepayment))
        {
            log_message('debug','There has expired repayment.');
            //log_message('debug','$hasBeenExpiredRepayment'.json_encode($hasBeenExpiredRepayment));
            foreach($hasBeenExpiredRepayment as $repaymentSchedule)
            {
                log_message('debug','$repaymentSchedule'.json_encode($repaymentSchedule));
                $userId = $repaymentSchedule['user_id'];
                $borrowingId = $repaymentSchedule['borrowing_id'];

                $user_info = $this->user_model->fetch_row("user_id = $userId", 'user_name, balance');

                log_message('debug','$user_info'.json_encode($user_info));

                if(!empty($user_info))
                {
                    $repaymentScheduleId = $repaymentSchedule['id'];
                    $amount = $this->repayment_service->calculateRepaymentAmount($borrowingId, $repaymentScheduleId);
                    $balance = 0.00;
                    if (isset($user_info['balance']))
                    {
                        $balance = $user_info['balance'];
                    }

                    log_message('debug','need repayment amount:'."$amount"."###balance:"."$balance");

                    if ($balance >= $amount)//余额足够还款
                    {

                        //更新数据
                        //$this->Repayment_schedule->db->trans_strict(FALSE);
                        $this->Repayment_schedule->db->trans_begin();//启动事务

                        //订单创建
                        $order_type = 1;//1还款 2充值
                        $pay_type = 17;//余额
                        $fund_id = 0;
                        $trdInfo = $this->trd_service->createTrd($fund_id, $amount, $userId, $pay_type, $repaymentScheduleId, $order_type);

                        if ($trdInfo['code'] < 0) {
                            log_message('debug', 'create auto repayment pay order failed:' + $trdInfo['code']);
                            $this->Repayment_schedule->db->trans_rollback();
                            continue;//交易订单创建失败
                        }

                        //实际还款金额,还款状态,还款时间更新
                        $data = array('real_repayment_amount'=> $amount);
                        $res = $this->Repayment_schedule->update_by_id($repaymentScheduleId, $data);
                        if($res == 0)//更新失败，有可能是值未变
                        {
                            $where = array('id'=>$repaymentScheduleId);
                            $realRepaymentAmountInfo = $this->Repayment_schedule->get_by_where($where,'real_repayment_amount');
                            if(empty($realRepaymentAmountInfo) || number_format($amount, 2, '.', '') != floatval($realRepaymentAmountInfo['real_repayment_amount']))
                            {

                                log_message('debug', '@@@@@@$amount:'.$amount.'$realRepaymentAmountInfo[real_repayment_amount]'.$realRepaymentAmountInfo['real_repayment_amount']);

                                $lastQuery = $this->Repayment_schedule->db->last_query();
                                log_message('debug', '############(expired)update repayment_schedule info failed,sql:'.$lastQuery);
                                $this->Repayment_schedule->db->trans_rollback();
                                continue;
                            }

                        }
                        //账号金额
                        $res = $this->user_model->setDec($userId, 'balance', (float)$amount);//余额扣除
                        if(!$res)
                        {
                            log_message('debug', '############(expired)update user balance info failed');
                            $this->Repayment_schedule->db->trans_rollback();
                            continue;
                        }

                        //还款完成的处理工作。

                        $resDealRepayment = $this->afterRepaymentFinish($userId, $trdInfo['order_id']);
                        if (!$resDealRepayment)
                        {
                            log_message('debug', '############(expired)afterRepaymentFinish failed');
                            $this->Repayment_schedule->db->trans_rollback();
                            continue;//交易订单创建失败
                        }

                        if ($this->Repayment_schedule->db->trans_status() === FALSE)
                        {
                            log_message('debug', '++++++ 自动还款失败');
                            $this->Repayment_schedule->db->trans_rollback();
                        }
                        else
                        {
                            $this->Repayment_schedule->db->trans_commit();

                            log_message('debug', '@@@@++++##### autorepayment expired borrowing succeed!!!');

                            //成功后消息通知
                            $deadline = date('Y-m-d',$repaymentSchedule['repayment_deadline']);
                            $push_arr = array('{amount}' => $amount, '{deadline}' => $deadline);
                            $data = array('msgtype' => '6', 'itemid' => $repaymentSchedule['borrowing_id']);
                            $msgTempId = 6;//还款通知 //消息模板id 1还款提醒 2逾期提醒 3审批贷款通知 4提现成功 5保证金返还客户 6自动还款通知
                            $res = $this->message_service->send_sys($msgTempId, $repaymentSchedule['user_id'], 1, $push_arr, $data, $push_arr, $user_info['user_name']);
                        }

                    }

                }
                else
                {
                    log_message('debug','get user info failed');
                }

            }
        }
        else
        {
            log_message('debug','$hasBeenExpiredRepayment is empty');
        }
    }

    //还款支付成功后处理
    private function afterRepaymentFinish($uid, $order_id)
    {
        log_message('debug', '@@@@@@afterRepaymentFinish-'.'uid:'.$uid.'    order_id:'.$order_id);

        $borrowingId = '';

        $repaymentId = $order_id;

        $data = array(
            'repayment_time' => time(),
            'repayment_status' => 1,
        );
        $res = $this->Repayment_schedule->update_by_id($repaymentId, $data);

        if(empty($borrowingId))
        {
            $where = array('id'=>$repaymentId ,'user_id'=>$uid);
            $schedule = $this->Repayment_schedule->get_by_where($where);
            if (empty($schedule)) {
                $lastQuery = $this->Repayment_schedule->db->last_query();
                log_message('debug', 'query borrowging info failed++++'." last query:".$lastQuery);
                return false;
            }
            $borrowingId = $schedule['borrowing_id'];
        }

        /*
         * 判读是否全部还清，全部还清，需要把borrowing表标识
         */
        //还款状态 0:未还款 1：已还款
        $repayment_status = 0;
        $where = array('user_id'=>$uid, 'borrowing_id'=>$borrowingId, 'repayment_status'=>$repayment_status);
        $repaymentScheduleList = $this->Repayment_schedule->get_list($where);

        if (empty($repaymentScheduleList)) {//无未还款清单,表明已经还清
            $data = array(
                'status' => 3,//-1—审批被拒绝；0—申请中；1—通过申请；2-还款中 3-还款结束 4-因故被终止
            );
            $res = $this->Borrowing_model->update_by_id($borrowingId, $data);

        }
        else {
            $data = array(
                'status' => 2,//-1—审批被拒绝；0—申请中；1—通过申请；2-还款中 3-还款结束 4-因故被终止
            );
            $res = $this->Borrowing_model->update_by_id($borrowingId, $data);

        }

        log_message('debug', '@@@@@@@----- afterRepaymentFinish succeed!!! -----@@@@@@@');

        return true;
    }

}