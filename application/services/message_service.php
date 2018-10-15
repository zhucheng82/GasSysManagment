<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message_service
{
    public function __construct()
	{
		$this->ci = & get_instance();
		$this->ci->load->model('Message_def_model');
		$this->ci->load->model('Message_model');
        $this->ci->load->model('Message_receiver_model');
        //$this->ci->load->model('User_pwd_model');
        $this->ci->load->service('usernum_service');
        $this->ci->load->service('sms_service');
	}

    /**
     * 站内信($tpl_id,$sender_id,$receiver,$receiver_type,$type_id,$arrParam)
     * sender_id 短消息发送人
     * receiver 接收者
     * receiver_type 接收人类型  1所有用户和店铺 2所有用户 3所有和店铺 4批量用户 5批量店铺 6单个普通用户
     * title  短消息标题
     * message 短消息内容
     * type_id 消息类型 1:通知消息 2:交易消息 3:物流消息
     * action_title 跳转按钮名
     * web_url 跳转网址
     * app_url app跳转
     */
    public function send($sender_id,$receiver,$receiver_type,$title,$message,$type_id,$action_title,$web_url,$app_url){
        $arrReturn = array('code'=>'ENPIY','message'=>'');
        if(empty($receiver))
        {
            $arrReturn['code'] = 'FAIL';
            $arrReturn['message'] = '接受者为空';
            return $arrReturn;
        }

        $data = array(
            'sender_id'=>$sender_id, //短消息发送人
            'parent_id'=>0,  //回复短消息id
            'tpl_id'=>0, //模板_id
            'receiver'=>$receiver,  //接收者
            'send_time'=>0, //短消息发送时间
            'reply_time'=>0,  //短消息回复更新时间
            'kind'=>1, //0为私信、1为系统消息
            'is_batch'=>1, //站内信是否为一条发给多个用户 0为否 1为多条
            'is_send'=>0,  //是否已发送 1:已发送 0:未发送
            'receiver_type'=>$receiver_type, //接收人类型  1所有用户和店铺 2所有用户 3所有和店铺 4批量用户 5批量店铺 6单个普通用户
            'title'=>$title, //短消息标题
            'content'=>$message,  //短消息内容
            'type_id'=>$type_id, //消息类型 1:通知消息 2:交易消息 3:物流消息
            'action_title'=>$action_title, //跳转按钮名
            'web_url'=>$web_url, //跳转网址
            'app_url'=>$app_url, //app跳转
            'status'=>1,  //发送者是否删除 1:正常 -1:删除
        );
        
        //保存至数据库
        $this->ci->Message_model->insert_string($data);
    
        $aReceiver = explode(',',$arrReturn['receiver']);
        foreach ($aReceiver as $key => $v) {
            $tada = array(
                'receiver_id'=>$v,  //接收者
                'message_id'=>$arrReturn['id'],  //消息id
                'is_read'=>0, //是否已读 1:已读 0:未读
                'type_id'       => $type_id,
                'read_time'=>time(),  //读信时间
                'is_del'=>1,  //1:正常  -1:已删除
                'push_status'=>0,//短信状态 0:无需发送 1:等待发送 2:已发送
                );
            //保存至数据库
            $this->ci->Message_receiver_model->insert_string($tada);
        }
        $this->ci->Message_model->update_by_id($arrReturn['id'],array('is_send'=>1,'send_time'=>time()));
    
        $arrReturn['code'] = 'SUCCESS';
        $arrReturn['message'] = '成功';
        return $arrReturn;
        
    }


    /**
     * 系统消息
     * tpl_id 消息模板id 1还款提醒 2逾期提醒 3审批贷款通知 4提现成功 5保证金返还客户
     * receiver 接受者为空
     * receiver_type 接收人类型  多客户端情况使用（如：1用户端app，2商家端app）
     * arrParam
     * type 消息类型 //消息模板id 1还款提醒 2逾期提醒 3审批贷款通知 4提现成功 5保证金返还客户 6自动还款通知
     */
    public function send_sys($tpl_id,$receiver,$receiver_type,$arrParam,$msginfo = array(),$smsArrParam = array(),$mobile=''){
        $sender_id = 0; //系统消息
        $arrReturn = array('code'=>'EMPTY','message'=>'');
        if(empty($receiver))
        {
            $arrReturn['code'] = 'Failure';
            $arrReturn['message'] = '接受者为空';
            return $arrReturn;
        }
        if (empty($msginfo['msgtype'])) {
            $arrReturn['code'] = 'Failure';
            $arrReturn['message'] = '消息类型不能为空';
            return $arrReturn;
        }
        if(empty($tpl_id))
        {
            $arrReturn['code'] = 'Failure';
            $arrReturn['message'] = '模板id不能为空';
            return $arrReturn;
        }
        if(empty($receiver_type))
        {
            $arrReturn['code'] = 'Failure';
            $arrReturn['message'] = '用户类型不能为空';
            return $arrReturn;
        }
        $aMessageDef = $this->ci->Message_def_model->get_by_id($tpl_id);
        log_message('debug','@@@@@@@@@$aMessageDef:'.json_encode($aMessageDef));
        //echo '<pre>';print_r($aMessageDef);exit;
    	if(empty($aMessageDef)){
    		$arrReturn['code'] = 'Failure';
        	$arrReturn['message'] = '消息模板不存在';
        	return $arrReturn;
    	}

        if ($aMessageDef['sms_switch']==1) { //如果需要发送短信
            if (empty($mobile)) {
                $arrReturn['code'] = 'Failure';
                $arrReturn['message'] = '手机号不能为空';
                return $arrReturn;
            }
            $bMobile = preg_match("/^1[34578]\d{9}$/", $mobile);
            if (!$bMobile) {
                $arrReturn['code'] = '-1';
                $arrReturn['message'] = '手机号格式不对！';
                return $arrReturn;
            }
        }

    	$title = $aMessageDef['message_title'];
    	$message = $aMessageDef['message_content'];
    	$action_title = $aMessageDef['action_title'];
    	$web_url = $aMessageDef['web_url'];
    	$app_url = $aMessageDef['app_url'];
        $need_push = $aMessageDef['need_push'];
        $type_id = $aMessageDef['type_id'];
        
    	if( strpos($message,'||')>0 ){
        	$arrMsg = explode('\|\|', $message);
        	if(count($arrMsg)>1){
        		$idx = rand(0,count($arrMsg)-1);
        		$message = $arrMsg[$idx];
        	}
        }
        if(!empty($arrParam)){
        	foreach ($arrParam as $key => $value) {
        		if(!empty($title))
        			$title = str_replace($key, $value, $title);
        		if(!empty($message))
        			$message = str_replace($key, $value, $message);
        		if(!empty($action_title))
        			$action_title = str_replace($key, $value, $action_title);
        		if(!empty($web_url))
        			$web_url = str_replace($key, $value, $web_url);
                if(!empty($app_url))
                    $app_url = str_replace($key, $value, $app_url);
            }
        }

        $arrSms = array();
        if(!empty($smsArrParam)){
            foreach ($smsArrParam as $key => $value) {
                array_push($arrSms, $value);
            }
        }

        $is_batch = 1;
        /*$pushinfo = array();
        if ($aMessageDef['need_push']==1) {
           $pushinfo = array('msgtype'=>$msginfo['msgtype'],'content'=>$message);
        }*/
        $data = array(
            'sender_id'=>$sender_id,  //短消息发送人
            'parent_id'=>0, //回复短消息id
            'tpl_id'=>$tpl_id, //模板_id
            'title'=>$title,  //短消息标题
            'content'=>$message, //短消息内容
            'receiver'=>$receiver, //接收者
            'send_time'=>0, //短消息发送时间
            'reply_time'=>0, //短消息回复更新时间
            'kind'=>1, //0为私信、1为系统消息
            'is_batch'=>0, //站内信是否为一条发给多个用户 0为否 1为多条
            'is_send'=>0, //是否已发送 1:已发送 0:未发送
            'receiver_type'=>$receiver_type, //接收人类型  1患者，2医生，3医院
            'type_id'=>$type_id,  //模板类型 1:通知消息 2:交易消息 3:物流消息
            'action_title'=>$action_title, //跳转按钮名
            'web_url'=>$web_url, //跳转网址
            'app_url'=>$app_url, //app跳转
            'status'=>1, //发送者是否删除 1:正常 -1:删除
            'is_push'=>$need_push,
            'type'=>$msginfo['msgtype'], //推送类型与消息类型
            'msginfo'=>json_encode($msginfo), //返回给用户内容
            //'pushinfo'=>json_encode($pushinfo), //推送内容
            'sms_parameter'=>json_encode($arrSms)//短信参数
        );

        //保存至数据库
        $message_id = $this->ci->Message_model->insert_string($data);
        //var_dump($data);
        $aReceiver = explode(',',$receiver);
        foreach ($aReceiver as $key => $v) {
            $data_receiver = array(
                'receiver_id'   => $v,
                'message_id'    => $message_id,
                'is_read'       => 0,
                'type_id'       => $type_id,
                'read_time'     => 0,
                'is_del'        => 1,
                'push_status'   =>($need_push==1?1:0),
                'sms_status'    =>($aMessageDef['sms_switch']==1?1:0),
            );
            $this->ci->Message_receiver_model->insert_string($data_receiver);

        }
        $this->ci->Message_model->update_by_id($message_id,array('is_send'=>1,'send_time'=>time()));
        /*******************发送短信开始********************/
        log_message('debug','send sms');
        //if ($aMessageDef['sms_switch']==1 && !empty($aMessageDef['sms_content'])) {
        log_message('debug','$aMessageDef:'.json_encode($aMessageDef));
        if ($aMessageDef['sms_switch']==1) {
            log_message('debug','send start');
            /*
            $testMobile = array('15757116427','15757116428','18757554362');
            if (!in_array($mobile, $testMobile)) {
                $this->send_ems($mobile,$smsArrParam,$aMessageDef['sms_content']);
            }
            */
            //$this->send_ems($mobile,$smsArrParam,$aMessageDef['sms_content']);

            //1还款提醒 2逾期提醒 3审批贷款通知 4提现成功 5保证金返还客户
            $smsType = '';
            switch($tpl_id)
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
                case 6:
                {
                    $smsType = 'AutoRepayment';
                }
                    break;
            }
            $arrSms = array();
            if(!empty($smsArrParam)){
                foreach ($smsArrParam as $key => $value) {
                    array_push($arrSms, $value);
                }
            }

            log_message('debug','sms type:'.$smsType.' arrSms:'.json_encode($arrSms));

            $this->ci->sms_service->sendSms($smsType,json_encode($arrSms), $mobile);
        }

        log_message('debug','send sms end');
        /*******************发送短信结束********************/
        $arrReturn['code'] = 'SUCCESS';
        $arrReturn['message'] = '成功';
        return $arrReturn;
    }

    public function send_sms($type, $arrSmsParam, $mobile)
    {
        $arrSms = array();
        if(!empty($arrSmsParam)){
            foreach ($arrSmsParam as $key => $value) {
                array_push($arrSms, $value);
            }
        }

        $smsType = '';
        switch($type)
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
            case 6:
            {
                $smsType = 'AutoRepayment';
            }
                break;
            case 7:
            {
                $smsType = 'WithdrawNotifyLoan';
            }
                break;
            case 8:
            {
                $smsType = 'RechargeNotifyLoan';
            }
                break;
        }

        log_message('debug', 'sendSms:'.json_encode($arrSms).'$smsType:'.$smsType);

        $this->ci->sms_service->sendSms($smsType,json_encode($arrSms), $mobile);
    }

    /*******************发送短信开始********************/
    private function send_ems($mobile,$smsArrParam,$messageContent) {
        $message = $messageContent;
        if(!empty($smsArrParam) && !empty($messageContent)){
            foreach ($smsArrParam as $key => $value) {
                $message = str_replace($key, $value, $message);
            }
        } 
        $code = '';
        //echo $message,$mobile;
        if ($this->sendMessgae($message, $mobile)) {
            $arrReturn['code'] = '1';
            $arrReturn['message'] = '发送成功！';
        } else {
            $arrReturn['code'] = '-1';
            $arrReturn['message'] = '发送失败！';
        }
    }

    /**
     * 发送短信消息
     * @param unknown_type $tel
     * @param unknown_type $name
     * @param unknown_type $type
     * @param unknown_type $username
     * @return mixed
     * XXX--X级代理商wy777081198
     */
    protected function sendMessgae($content, $tel){
        $url = "http://api.app2e.com/smsBigSend.api.php";
        $data = array(
            'pwd' => md5('wv1C13zO'),
            'username' => 'zhuoerwangluo',
            'p' => $tel,
            'isUrlEncode' => 'no',
            'charSetStr' => 'utf',
            'msg' => $content
        );
        $code = http_post_data($url, $data);
        return $code;
    }

}