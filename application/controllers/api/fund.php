<?php
/**
 * Created by PhpStorm.
 * User: zhucheng
 * Date: 16/9/20
 * Time: 下午3:03
 */

class Fund extends TokenApiController
{
    public $user_id;
    public $user_name;
    public $userInfo;
    public function __construct()
    {
        parent::__construct();
        $this->userInfo = $this->loginUser;
        $this->user_id = $this->loginUser['user_id'];
        $this->user_name = $this->loginUser['user_name'];
        $this->load->model('User_bank_card_info');
        $this->load->service('sms_service');
        $this->load->service('message_service');
        $this->load->service('user_service');
        $this->load->model('Cache_model');
        $this->load->model('Notify_mobile_list_model');
    }

    public function addBankCard()
    {
        $bankName = $this->input->post('bank_name');
        $bankCard = $this->input->post('bank_card');
        $mobile = $this->input->post('mobile');
        $smsCode = $this->input->post('sms_code');

        if (!isset($bankName)) {
            output_error(-1,'请输入银行名称');
        }
        if (!isset($bankCard)) {
            output_error(-1,'请输入银行卡号');
        }
        if (!isset($mobile)) {
            output_error(-1,'请输入手机号');
        }
        if (!isset($smsCode)) {
            output_error(-1,'请输入验证码');
        }

        $user_code = $this->sms_service->check_code($mobile,$smsCode,6,9);
        if (empty($user_code)) {
            output_error(-1,'验证码错误');
        }

        //判断银行卡是否存在
        $bankFind = array('user_id'=>$this->user_id, 'bank_card_id'=>$bankCard);
        $bankCardExist = $this->User_bank_card_info->get_by_where($bankFind);
        if (!empty($bankCardExist))
        {
            output_error(-2, '该银行卡已添加');
        }

        $isDefault = 0;
        $whereDefault = array('user_id'=>$this->user_id, 'is_default'=>1);
        $bankCardDefault = $this->User_bank_card_info->get_by_where($whereDefault);
        if (empty($bankCardDefault))
        {
            $isDefault = 1;
        }

        $where = array('user_id'=>$this->user_id, 'bank_name'=>$bankName, 'bank_card_id'=>$bankCard, 'mobile'=>$mobile, 'is_default'=>$isDefault);

        $result = $this->User_bank_card_info->insert_string($where);
        if ($result)
        {
            output_data();exit;
        }
        else
        {
            output_error(-1, '添加银行卡失败');
        }
    }

    //解除银行卡
    public function relieveBandBankCard()
    {
        $id = $this->input->post('bank_card_info_id');

        if (!isset($id)) {
            output_error(-1,'参数为空');
        }

        //判断银行卡是否存在
        $bankFind = array('user_id'=>$this->user_id, 'id'=>$id);
        $bankCardExist = $this->User_bank_card_info->get_by_where($bankFind);
        if (empty($bankCardExist))
        {
            output_error(-1, '该银行卡不存在');
        }

        $where = array('user_id'=>$this->user_id, 'id'=>$id);
        $result = $this->User_bank_card_info->delete_by_where($where);
        if ($result)
        {
            $where = array('user_id'=>$this->user_id);
            $count = $this->User_bank_card_info->get_count($where);
            $data = array('count'=>$count);
            output_data($data);exit;
        }
        else
        {
            output_error(-1, '解除银行卡失败');
        }
    }

    //设置默认银行卡(更改默认银行卡)
    public function setDefaultBankCard()
    {
        $id = $this->input->post('bank_card_info_id');

        if (!isset($id)) {
            output_error(-1,'参数为空');
        }

        //判断银行卡是否存在
        $bankFind = array('user_id'=>$this->user_id, 'id'=>$id);
        $bankCardExist = $this->User_bank_card_info->get_by_where($bankFind);
        if (empty($bankCardExist))
        {
            output_error(-1, '该银行卡不存在');
        }

        $where = array('user_id'=>$this->user_id, 'is_default'=>1);
        $undefultBankValue = array('is_default'=>0);
        $this->User_bank_card_info->update_by_where($where, $undefultBankValue);

        $where = array('user_id'=>$this->user_id, 'id'=>$id);
        $defultBankValue = array('is_default'=>1);
        $result = $this->User_bank_card_info->update_by_where($where, $defultBankValue);

        if ($result)
        {
            output_data();exit;
        }
        else
        {
            output_error(-1, '设置默认银行卡失败');
        }
    }

    public function getBankCardList()
    {
        //先查询默认银行卡存不存在
        $where = array('user_id'=>$this->user_id,'is_default'=>1);
        $defaultBankCard = $this->User_bank_card_info->get_by_where($where);
        if (empty($defaultBankCard))//默认卡不存在
        {
            $where = array('user_id'=>$this->user_id);
            $defaultBankCard = $this->User_bank_card_info->get_by_where($where, 'id, bank_name, bank_card_id, fee_rate');
            if (!empty($defaultBankCard))//获取一个存在的卡，设为默认卡
            {
                $where = array('user_id'=>$this->user_id,'id'=>$defaultBankCard['id']);
                $defultBankValue = array('is_default'=>1);
                $this->User_bank_card_info->update_by_where($where, $defultBankValue);
            }
            else
            {
                output_error(-5,'银行卡列表为空');exit;
            }
        }

        $where = array('user_id'=>$this->user_id);
        $bankList = $this->User_bank_card_info->get_list($where, '*','is_default desc');

        $data = array('bank_list'=>$bankList);
        output_data($data);
    }

    public function  getFundFlowList()
    {

    }

    public function getBalance()
    {
        $data = $this->user_model->get_by_id($this->tokenUser['user_id'],'balance');
        output_data($data);
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

    /*
    public function recharege(){
        $uid = $this->user_id;
        $amount = (float)$this->input->post('amount');
        $pay_type = (int)$this->input->post('paymethod');
        if($amount<=0){
            output_error(-1,'充值金额不能小于0！');
        }
        if($amount>100000){
            output_error(-2,'充值金额不能大于100000！');
        }
        $PayMethodName = config_item('PayMethodName');
        if (empty($PayMethodName["$pay_type"])) {
            output_error(-3,'支付方式不存在！');//交易订单创建失败
        }
        //PayConfig::$USER_TYPE = $utype;
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
    */
    //充值
    public function recharge()
    {
        $uid = $this->user_id;
        $amount = (float)$this->input->post('amount');
        $pay_type = (int)$this->input->post('pay_type');
        /*
         * 11 => '微信APP',
         * 12 => '微信Wap',
         * 13 => '支付宝app',
         * 14 => '支付宝Wap',
         * 15 => '银联',
         * 16 => '银联代收'
         */

        $order_id = $this->input->post('order_id');
        if ($amount <= 0) {
            output_error(-1, '充值金额不能小于0！');
        }
        if ($amount > 100000) {
            output_error(-2, '充值金额不能大于100000！');
        }
        $PayMethodName = config_item('PayMethodName');
        if (empty($PayMethodName["$pay_type"])) {
            output_error(-3, '支付方式不存在！');//交易订单创建失败
        }
        $amount_price = $amount;
        $this->load->service('pay_service');
        $this->load->service('trd_service');
        $this->load->model('User_recharge_model');

        if (empty($order_id)) {
            $data = array(
                'amount' => $amount,
                'amount_price' => $amount_price,//对应虚拟货币
                'reward' => 0,//充值附赠
                'user_id' => $uid,
                'ip' => '',
                'create_time' => time(),
                'pay_status' => 0,
            );
            $order_id = $this->User_recharge_model->insert_string($data);
        }

        $order_type = 2;//1还款 2充值
        $res = $this->trd_service->createTrd($order_id,$amount, $uid, $pay_type, $order_id, $order_type);

        if ($res['code'] < 0) {
            output_error($res['code'], '交易订单创建失败！');//交易订单创建失败
            log_message('debug', 'create pay order failed:' + $res['code']);
        } else {
            $res = $this->trd_service->doNextStep($res['order_id'], $res['order_type'],$this->user_id, BASE_SITE_URL.'/wap/member/balance-tup.html?order_id='.$order_id);
            output_data($res);//交易订单创建成功
        }
    }

    public function prepareWithdrawInfo()
    {
        // 获得余额
        $balanceInfo = $this->user_model->get_by_id($this->user_id,'balance');
        //获取默认银行卡信息
        $where = array('user_id'=>$this->user_id,'is_default'=>1);
        $defaultBankCard = $this->User_bank_card_info->get_by_where($where, 'id, bank_name, bank_card_id, fee_rate,mobile');
        if (empty($defaultBankCard))
        {
            $where = array('user_id'=>$this->user_id);
            $defaultBankCard = $this->User_bank_card_info->get_by_where($where, 'id, bank_name, bank_card_id, fee_rate,mobile');
            if (!empty($defaultBankCard))
            {
                $where = array('user_id'=>$this->user_id,'id'=>$defaultBankCard['id']);
                $defultBankValue = array('is_default'=>1);
                $this->User_bank_card_info->update_by_where($where, $defultBankValue);
            }
        }
        $balance = 0;
        if (isset($balanceInfo) && isset($balanceInfo['balance']))
        {
            $balance = $balanceInfo['balance'];
        }
        $data = array('balance'=>$balance,'default_bank_info'=>$defaultBankCard);
        output_data($data);
    }

    //提现
    public function withdraw()
    {
        $uid = $this->user_id;
        $amount = (float)$this->input->post('amount');
        $bankCardInfoId = $this->input->post('bank_info_id');
        $payPassword = $this->input->post('pay_password');

        if (!isset($amount))
        {
            output_error(-1, '提现金额不能为空');exit;
        }
        if (!isset($bankCardInfoId))
        {
            output_error(-1, '银行信息不能为空');exit;
        }
        if ($amount <= 0) {
            output_error(-1, '提现金额不能小于0！');exit;
        }
        if ($amount > 100000) {
            output_error(-2, '提现金额不能大于100000！');exit;
        }

        $where = array('id'=>$bankCardInfoId, 'user_id'=>$this->user_id);
        $bankCardInfo = $this->User_bank_card_info->get_by_where($where);
        if (empty($bankCardInfo)) {
            output_error(-1, '您未绑定该银行卡');exit;
        }

        $userAddtionInfo = $this->user_service->getUserRealName($this->user_id);
        $userRealName = '';
        if(!empty($userAddtionInfo) && isset($userAddtionInfo['real_name']))
        {
            $userRealName = $userAddtionInfo['real_name'];
        }

        $this->load->model('Withdraw_model');
        $data = array('user_id'=>$this->user_id,
            'bank'=>$bankCardInfo['bank_name'],
            'user_name'=>$userRealName,
            'card_id'=>$bankCardInfo['bank_card_id'],
            'mobile'=>$bankCardInfo['mobile'],
            'fees'=>$bankCardInfo['fee_rate'],
            'amount' => $amount,
            'status'=>1,
            'createtime' => time()
            );

        $this->load->service('pay_service');
        $this->load->service('trd_service');

        $this->Withdraw_model->db->trans_strict(FALSE);
        $this->Withdraw_model->db->trans_begin();//启动事务

        log_message('debug', '~~~~~~~~~~~~withdraw data:'.json_encode($data));

        $withdrawInsert = $this->Withdraw_model->insert_string($data);
        if (!$withdrawInsert) {
            $this->Withdraw_model->db->trans_rollback();
            log_message('debug', '提现写入记录失败');
            output_error(-1, '提现失败');
        }

        $order_type = 3;//1还款 2充值 3提现
        $order_id = $withdrawInsert;
        $res = $this->trd_service->createTrd($order_id,$amount, $uid, 0, $order_id, $order_type);

        // 更新余额
        $updateBalance = $this->user_model->setInc($this->user_id, 'balance', (float)$amount * (float)(-1));
        if (!$updateBalance) {
            $this->Withdraw_model->db->trans_rollback();
            log_message('debug', '提现更新余额失败');
            output_error(-1, '提现失败');
        }

        if ($this->Withdraw_model->db->trans_status() === FALSE)
        {
            $this->Withdraw_model->db->trans_rollback();
            //echo $this->student_model->db->last_query();
            output_error(-1,'提现失败');exit;
        }
        else
        {
            $this->Withdraw_model->db->trans_commit();

            //执行提现申请成功后，给放款方发送短信通知提醒
            $push_arr = array('{amount}' => $amount);
            $msgTempId = 7;//还款通知 //消息模板id 1还款提醒 2逾期提醒 3审批贷款通知 4提现成功 5保证金返还客户 6自动还款通知 7提现申请通知客户

            //$notifyMobileList = C('withdraw_notify_list');

            $arrMobile = $this->Notify_mobile_list_model->get_list(array(), 'mobile');
            log_message('debug', '(withdraw)$arrMobile:'.json_encode($arrMobile).' msg_temp_id:'.$msgTempId);
            foreach ($arrMobile as $mobile)
            {
                log_message('debug', '$mobile:'.json_encode($mobile).' num:'.$mobile['mobile']);
                $this->message_service->send_sms($msgTempId, $push_arr, $mobile['mobile']);
            }


            output_data();

        }

    }

    public function getFundRecord()
    {
        $uid = $this->user_id;
        $type = (float)$this->input->post('type');//0全部 1收入 2支出k
        $page = $this->input->post('page');
        $pagesize = (int)$this->input->post('pagesize');

        $page = isset($page) ? max(intval($page), 1) : 1;
        $pagesize = !empty($pagesize) ? $pagesize:10;


        $this->load->model('User_pay_model');

        $where = array();
        if (!isset($type) || $type == 0)
        {
            $where = array('user_id'=>$uid);//order_type 1还款，2充值，3提现，4放款
        }
        elseif ($type == 1)//1收入
        {
            $where = "user_id = $uid and status=1 and (order_type = 2 or order_type = 4)";//order_type 1还款，2充值，3提现，4放款
            //$where = array('user_id'=>$uid,'order_type'=>2,'order_type'=>4);//order_type 1还款，2充值，3提现，4放款
        }
        elseif ($type == 2)//2支出
        {
            $where = "user_id = $uid and status=1 and (order_type = 1 or order_type = 3)";//order_type 1还款，2充值，3提现，4放款
            //$where = array('user_id'=>$uid,'order_type'=>1,'order_type'=>3);//order_type 1还款，2充值，3提现，4放款
        }
        //$fundRecord = $this->User_pay_model->get_list($where);

        $fundRecord = $this->User_pay_model->fetch_page($page,$pagesize,$where,'amount,order_type,title,pay_type,updatetime,status','updatetime desc');

        if (!empty($fundRecord)) {

            $totalpage = ceil($fundRecord['count'] / $pagesize);
            $data = array('page'=>$page,'pagesize'=>$pagesize,'totalpage'=>$totalpage,'fund_record'=>$fundRecord);
            output_data($data);
        }else{
            output_error(-1, '暂无数据');
        }

    }

}