<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cron_service
{
    public function __construct()
    {
        $this->ci = &get_instance();

        //$this->ci->load->service('message_service');
    }

    //推送
    public function push_message()
    {
        $arrReturn = array();
        $this->ci->load->model('Message_model');
        $this->ci->load->model('Message_receiver_model');
        //$this->ci->load->model('User_token_model');
        $this->ci->load->library('PushApi');
        $this->ci->load->service('sms_service');

        $objPush = new PushApi();

        $db = $this->ci->Message_receiver_model->db;
        //未登录用户，不推送

        //全员推,未登录也推
        /*
        $aMessageAllList = $this->ci->Message_model->get_list(array('receiver' => 'all', 'is_push' => 1), 'id,parent_id,tpl_id,title,content,receiver,type,type_id');
        //print_r($aMessageAllList);exit();
        if (!empty($aMessageAllList)) {
            foreach ($aMessageAllList as $key => $aMessage) {
                $title = APPNAME;

                $contentArr = json_encode(array('msgtype' => $aMessage['type'], 'content' => $aMessage['content']));
                $res = $objPush->push('all', $aMessage['content'], '1', $contentArr, 600, $title, $aMessage['type']);
                if (!empty($res)) {
                    $res_arr = json_decode($res, true);
                    if (isset($res_arr['error'])) {                   //如果返回了error则证明失败
                        echo $res_arr['error']['message'];          //错误信息
                        echo $res_arr['error']['code'];             //错误码
                        $result = array('status' => 0, 'msg' => '操作错误');
                        $this->ci->Message_receiver_model->update_by_where(array('message_id' => $aMessage['id']), array('push_status' => 3));
                    } else {
                        $result = array('status' => 1, 'msg' => '推送成功');

                        $this->ci->Message_receiver_model->update_by_where(array('message_id' => $aMessage['id']), array('push_status' => 2, 'push_time' => time()));
                    }
                    $this->ci->Message_model->update_by_id($aMessage['id'], array('is_push' => 3));
                } else {
                    $result = array('status' => 0, 'msg' => '接口调用失败或无响应');
                }
            }
        }
        */

        //批量推--用户登录才推
        $aReceiverList = $db->select('GROUP_CONCAT(DISTINCT md5(receiver_id)) as receiver,message_id')->from('inter_message_receiver')
            ->join('user_token', 'user_token.user_id=inter_message_receiver.receiver_id')
            ->where('push_status', 1)->where('is_del', 1)->where('status', 1)
            ->group_by('message_id')->get()->result_array(); //distinct
        if (!empty($aReceiverList)) {
            foreach ($aReceiverList as $k => $a) {
                $aMessage = $this->ci->Message_model->get_by_id($a['message_id'], 'id,parent_id,tpl_id,title,content,receiver,type,type_id,msginfo,sms_parameter');

                $title = APPNAME;
                //$a['receiver']='all';
                $aReceiverTmp = explode(',', $a['receiver']);
                $aJPush = array('alias' => $aReceiverTmp);
                //$contentArr = json_encode(array('msgtype' => $aMessage['type'], 'item_id' => $aMessage['msginfo']));
                //$res = $objPush->push($aJPush, $aMessage['content'], $aMessage['type'], $contentArr, 86400, $title);
                $res = $objPush->push($aJPush, $aMessage['content'], $aMessage['type'], $aMessage['msginfo'], 86400, $title);

                if (!empty($res)) {
                    $res_arr = json_decode($res, true);
                    if (isset($res_arr['error'])) {                   //如果返回了error则证明失败
                        echo $res_arr['error']['message'];          //错误信息
                        echo $res_arr['error']['code'];             //错误码
                        $result = array('status' => 0, 'msg' => '操作错误');
                        $this->ci->Message_receiver_model->update_by_where(array('message_id' => $a['message_id']), array('push_status' => 3));
                    } else {
                        $result = array('status' => 1, 'msg' => '推送成功');

                        $this->ci->Message_receiver_model->update_by_where(array('message_id' => $a['message_id']), array('push_status' => 2, 'push_time' => time()));
                    }
                } else {
                    $result = array('status' => 0, 'msg' => '接口调用失败或无响应');
                }

                $arrReturn[] = $result;

                /*
                //发送信息
                $smsType = '';
                switch($aMessage['type'])
                {
                    case 1:
                    {
                        $smsType = 'RepaymentRemind';
                    }
                        break;
                    case 2:
                    {
                        $smsType = 'DelayRemind';
                    }
                        break;
                    case 3:
                    {
                        $smsType = 'BorrowingApproved';
                    }
                        break;
                    case 4:
                    {
                        $smsType = 'Withdraw';
                    }
                        break;
                    case 5:
                    {
                        $smsType = 'RetentionMoneyReturn';
                    }
                        break;
                }
                $arrSms = array();
                $smsArrParam = json_decode($aMessage['sms_parameter']);
                if(!empty($smsArrParam)){
                    foreach ($smsArrParam as $key => $value) {
                        array_push($arrSms, $value);
                    }
                }


                log_message('debug','send sms start:'.$smsType.' arrSms:'.json_encode($arrSms));

                $this->ci->sms_service->sendSms($smsType,json_encode($arrSms), '13456957779');

                */
            }
        }
        print_r($arrReturn);
    }
}