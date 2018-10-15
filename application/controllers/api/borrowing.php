<?php
/**
 * Created by PhpStorm.
 * User: zhucheng
 * Date: 16/9/20
 * Time: 下午5:57
 */

/**
 * 求两个日期之间相差的天数
 * (针对1970年1月1日之后，求之前可以采用泰勒公式)
 * @param int $begin_time
 * @param int $end_time
 * @return number
 */
function daysBetweenTwoTimes($begin_time, $end_time)
{
    $days = 0;
    //计算天数
    $timediff = $end_time-$begin_time;
    $days = intval($timediff/86400);
    //计算小时数
    $remain = $timediff%86400;
    if ($remain > 0)
    {
        $days++;
    }

    return $days;
}

class Borrowing extends TokenApiController
{
    public $user_id;

    public function __construct()
    {
        parent::__construct();
        /*
        $this->load->service('sms_service');
        $this->load->service('user_service');
        $this->load->model('User_pwd_model');
        $this->load->model('User_model');
        $this->load->model('User_token_model');
        */
        $this->load->model('Borrowing_product_model');
        $this->load->model('Borrowing_model');
        $this->load->model('Repayment_schedule');
        $this->load->model('student_model');
        $this->load->model('employee_model');
        $this->load->model('additional_user_info_model');
        $this->load->model('addmin_info_model');
        $this->load->model('ebike_info_model');
        $this->load->model('renting_house_info');
        $this->load->model('Cache_model');
        $this->load->model('Borrowing_usage_model');

        $this->user_id = $this->loginUser['user_id'];

    }

    public function getUsageList()
    {
        $order = 'sort ASC';
        $arrUsage = $this->Borrowing_usage_model->get_list(array(), 'usage',$order);
        if(!empty($arrUsage))
        {
            $arrRes['usage'] = $arrUsage;
            output_data($arrRes);exit;
        }
        else
        {
            log_message('debug', 'Borrowing_usage_model empty');
            $arrRes['usage'] = array_values($this->config->item("usage"));
            output_data($arrRes);exit;
        }
    }

    //用途和扣除咨询费比例信息
    public function getBorrowingPreInfo()
    {
        $serviceFeeRate = 0;
        //$serviceFeeRate = $this->Cache_model->getServiceFeeRate();
        $serviceFeeRate = C('deposit_rate');

        $order = 'sort DESC';
        $arrUsageInof = $this->Borrowing_usage_model->get_list(array(), 'usage',$order);
        $arrUsage = array();
        if(!empty($arrUsageInof))
        {
            foreach($arrUsageInof as $usage )
            {
                $arrUsage[] = $usage['usage'];
            }
        }
        else
        {
            $arrUsage = array_values($this->config->item("usage"));
        }

        $data = array('usage'=>$arrUsage, 'service_fee_rate'=>$serviceFeeRate);
        output_data($data);
    }

    public function getRentingHousePayWayList()
    {
        $arrRes['pay_way'] = array_values($this->config->item("pay_way"));
        output_data($arrRes);exit;
    }

    //申请借贷
    public function applyBorrowing()
    {
        $productId = $this->input->post('borrowing_product_id');
        $amount = $this->input->post('amount');
        $usage = $this->input->post('usage');
        $creditCycle = $this->input->post('credit_cycle');
        $extraInfoId = $this->input->post('extra_info_id');

        if(empty($productId) || empty($amount) || empty($usage) || empty($creditCycle))
        {
            output_error(-1, '参数不能为空');exit;
        }
        if($amount <= 0)
        {
            output_error(-1, '贷款金额不能小于0');exit;
        }
        if($creditCycle <= 0)
        {
            output_error(-1, '还款期数不能小于0');exit;
        }

        //判断产品是否有效
        $where = array('id'=>$productId,'status'=>1);
        $borrowingProductItem = $this->Borrowing_product_model->get_by_where($where);
        if (empty($borrowingProductItem)) {
            output_error(-1,'无效的借贷产品');exit;
        }
        //判断用户是否认证
        $work = $this->loginUser['work'];//职业：-1，未知；0，学生；1，白领
        $authStatus = $this->loginUser['authentication_status'];//认证状态 0，未认证；1，已认证
        if($work<0 || $authStatus == 0)
        {
            output_error(-1,'用户未认证');exit;
        }

        $userName = '';
        if ($work == 0)//学生
        {
            $where = array('user_id'=>$this->user_id);
            $student = $this->student_model->get_by_where($where);
            if (!empty($student) && isset($student['user_name']))
            {
                $userName = $student['user_name'];
            }
        }
        else if ($work == 1)//白领
        {
            $where = array('user_id'=>$this->user_id);
            $student = $this->employee_model->get_by_where($where);
            if (!empty($student) && isset($student['user_name']))
            {
                $userName = $student['user_name'];
            }
        }

        //判断用户是否完善资料
        $where = array('user_id'=>$this->user_id);
        $additionalInfo = $this->additional_user_info_model->get_list($where);
        /*if (empty($additionalInfo)) {
            output_error(-1,'用户未完善资料');exit;
        }
        */

        //判断当前是否有贷款
        /*
         **************
        */
        /*
        $where = array('user_id'=>$this->user_id, 'borrowing_product_id'=>$productId);
        $borrowing = $this->Borrowing_model->get_list($where);
        if (!empty($borrowing)) {
            //output_error(-1,'您已经有一笔未还完款的借贷，不可重复借贷。');exit;
        }
        */

        $borrowingProductType = $borrowingProductItem['productType'];//贷款类型 1：普通现金贷款 2：电动车贷款 3：租房贷款

        $res = false;

        if ($borrowingProductType == 2 || $borrowingProductType == 3)
        {
            log_message('debug', '+++++++ebike or renting！');
            $this->Borrowing_model->db->trans_strict(FALSE);
            $this->Borrowing_model->db->trans_begin();//启动事务
        }

        $borrowingData = array('user_id'=>$this->user_id,
            'user_name'=>$userName,
            'borrowing_product_id'=>$productId,
            'apply_amount'=>$amount,
            'usage'=>$usage,
            'status'=>0,
            'credit_cycle'=>$creditCycle,
            'applied_time'=>time(),
            'rate'=>$borrowingProductItem['rate'],
            'repaymentType'=>$borrowingProductItem['repaymentType'],
            'repayment_cycle_days'=>$borrowingProductItem['repayment_cycle_days'],
            'productType'=>$borrowingProductItem['productType'],
            'product_name'=>$borrowingProductItem['product_name']
            );

        $res = $this->Borrowing_model->insert_string($borrowingData);

        if ($borrowingProductType == 2)
        {
            log_message('debug', '+++++++ebike！');
            $data = array('borrowing_id'=>$res);
            $this->ebike_info_model->update_by_id($extraInfoId, $data);
        }
        elseif ($borrowingProductType == 3)
        {
            $data = array('borrowing_id'=>$res);
            $this->renting_house_info->update_by_id($extraInfoId, $data);
        }

        if ($borrowingProductType == 2 || $borrowingProductType == 3)
        {
            $res = $this->Borrowing_model->db->trans_status();

            if ($res === FALSE)
            {
                log_message('debug', '+++++++trans_begin failed！');
                $this->Borrowing_model->db->trans_rollback();
                //output_error(-1,'认证失败');exit;
            }
            else
            {

                log_message('debug', '+++++++trans_begin succeed！');
                $this->Borrowing_model->db->trans_commit();
                //output_data();exit;
            }

        }

        if ($res)
        {
            log_message('debug', '+++++++borrowing succeed！');

            $additionalInfoStatus = 0;
            $additionalInfoFlag = 0;
            if(isset($this->loginUser['additional_info_status']))
            {
                $additionalInfoFlag = $this->loginUser['additional_info_status'];
            }
            if (!empty($additionalInfo) &&  $additionalInfoFlag == 1) {
                //用户未完善资料
                $additionalInfoStatus = 1;
            }

            $arrRes['additional_info_status'] = $additionalInfoStatus;
            output_data($arrRes);exit;
        }
        else
        {
            output_error(-1, '申请借贷失败');exit;
        }
    }

    //申请借贷之电动车信息
    public function submitEBikeBorrowingInfo()
    {
        $productId = $this->input->post('borrowing_product_id');
        $brand = $this->input->post('brand');
        $price = $this->input->post('price');
        $merchantName = $this->input->post('merchant_name');
        $merchantAddr = $this->input->post('merchant_addr');
        $merchantTel = $this->input->post('merchant_tel');
        $photoUrl = $this->input->post('photo_url');

        if(empty($productId))
        {
            output_error(-1, '借贷产品id不能为空');exit;
        }
        if(empty($brand))
        {
            output_error(-1, '电动车品牌不能为空');exit;
        }
        if(empty($price))
        {
            output_error(-1, '电动车价格不能为空');exit;
        }
        if(empty($merchantName))
        {
            output_error(-1, '商家名称不能为空');exit;
        }
        if(empty($merchantAddr))
        {
            output_error(-1, '商家地址不能为空');exit;
        }
        if(empty($merchantTel))
        {
            output_error(-1, '商家电话不能为空');exit;
        }
        if(empty($photoUrl))
        {
            output_error(-1, '电动车图片不能为空');exit;
        }
        /*
        if(empty($brand) || empty($price) || empty($merchantName) || empty($merchantAddr) || empty($merchantTel) || empty($photo_url))
        {
            output_error(-1, '参数不能为空');exit;
        }
        */
        if($price <= 0)
        {
            output_error(-1, '价格不能小于0');exit;
        }

        //判断产品是否有效
        $where = array('id'=>$productId,'status'=>1);
        $borrowingProductItem = $this->Borrowing_product_model->get_by_where($where);
        if (empty($borrowingProductItem)) {
            output_error(-1,'无效的借贷产品');exit;
        }
        //判断用户是否认证
        $work = $this->loginUser['work'];//职业：-1，未知；0，学生；1，白领
        $authStatus = $this->loginUser['authentication_status'];//认证状态 0，未认证；1，已认证
        if($work<0 || $authStatus == 0)
        {
            output_error(-1,'用户未认证');exit;
        }
        //判断用户是否完善资料

        $where = array('user_id'=>$this->user_id);
        $additionalInfo = $this->additional_user_info_model->get_list($where);
        /*if (empty($additionalInfo)) {
            output_error(-1,'用户未完善资料');exit;
        }
        */

        //判断当前是否有贷款
        /*
         **************
        */
        /*
        $where = array('user_id'=>$this->user_id, 'borrowing_product_id'=>$productId);
        $borrowing = $this->Borrowing_model->get_list($where);
        if (!empty($borrowing)) {
            //output_error(-1,'您已经有一笔未还完款的借贷，不可重复借贷。');exit;
        }
        */

        $ebikeInfo = array('user_id'=>$this->user_id,
            'borrowing_product_id'=>$productId,
            'price'=>$price,
            'brand'=>$brand,
            'merchant_name'=>$merchantName,
            'merchant_addr'=>$merchantAddr,
            'merchant_tel'=>$merchantTel,
            'photo_url'=>$photoUrl,
            'create_time'=>time()
        );
        $id = $this->ebike_info_model->insert_string($ebikeInfo);
        if ($id)
        {
            $data = array('id'=>$id);
            output_data($data);exit;
        }
        else
        {
            output_error(-1, '提交电动车资料失败');exit;
        }
    }

    //申请借贷之租房信息
    public function submitRendingHouseBorrowingInfo()
    {
        $productId = $this->input->post('borrowing_product_id');

        $communityName = $this->input->post('community_name');
        $address = $this->input->post('address');
        $price = $this->input->post('price');
        $payWay = $this->input->post('pay_way');
        $landlordName = $this->input->post('landlord_name');
        $landlordTel = $this->input->post('landlord_tel');

        $hasIntermediary = $this->input->post('has_intermediary');
        $intermediaryName = $this->input->post('intermediary_name');
        $intermediaryTel = $this->input->post('intermediary_tel');

        if(empty($productId))
        {
            output_error(-1, '借贷产品id不能为空');exit;
        }
        if(empty($communityName))
        {
            output_error(-1, '小区名不能为空');exit;
        }
        if(empty($address))
        {
            output_error(-1, '小区地址不能为空');exit;
        }
        if(empty($price))
        {
            output_error(-1, '房租价格不能为空');exit;
        }
        if(empty($payWay))
        {
            output_error(-1, '支付方式不能为空');exit;
        }
        if(empty($landlordName))
        {
            output_error(-1, '房东姓名不能为空');exit;
        }
        if(empty($landlordTel))
        {
            output_error(-1, '房东电话不能为空');exit;
        }
        if (isset($hasIntermediary) && $hasIntermediary == 1)
        {
            if(empty($intermediaryName))
            {
                output_error(-1, '中介联系人姓名不能为空');exit;
            }
            if(empty($intermediaryTel))
            {
                output_error(-1, '中介联系人电话不能为空');exit;
            }
        }

        /*
        if(empty($brand) || empty($price) || empty($merchantName) || empty($merchantAddr) || empty($merchantTel) || empty($photo_url))
        {
            output_error(-1, '参数不能为空');exit;
        }
        */
        if($price <= 0)
        {
            output_error(-1, '房租价格不能小于0');exit;
        }

        //判断产品是否有效
        $where = array('id'=>$productId,'status'=>1);
        $borrowingProductItem = $this->Borrowing_product_model->get_by_where($where);
        if (empty($borrowingProductItem)) {
            output_error(-1,'无效的借贷产品');exit;
        }
        //判断用户是否认证
        $work = $this->loginUser['work'];//职业：-1，未知；0，学生；1，白领
        $authStatus = $this->loginUser['authentication_status'];//认证状态 0，未认证；1，已认证
        if($work<0 || $authStatus == 0)
        {
            output_error(-1,'用户未认证');exit;
        }
        //判断用户是否完善资料

        $where = array('user_id'=>$this->user_id);
        $additionalInfo = $this->additional_user_info_model->get_list($where);
        /*if (empty($additionalInfo)) {
            output_error(-1,'用户未完善资料');exit;
        }
        */

        //判断当前是否有贷款
        /*
         **************
        */
        /*
        $where = array('user_id'=>$this->user_id, 'borrowing_product_id'=>$productId);
        $borrowing = $this->Borrowing_model->get_list($where);
        if (!empty($borrowing)) {
            //output_error(-1,'您已经有一笔未还完款的借贷，不可重复借贷。');exit;
        }
        */

        $communityName = $this->input->post('community_name');
        $address = $this->input->post('address');
        $price = $this->input->post('price');
        $payWay = $this->input->post('pay_way');
        $landlordName = $this->input->post('landlord_name');
        $landlordTel = $this->input->post('landlord_tel');

        $hasIntermediary = $this->input->post('has_intermediary');
        $intermediaryName = $this->input->post('intermediary_name');
        $intermediaryTel = $this->input->post('intermediary_tel');

        $rentingHouseInfo = array('user_id'=>$this->user_id,
            'borrowing_product_id'=>$productId,
            'community_name' => $communityName,
            'address' => $address,
            'price'=>$price,
            'pay_way' => $payWay,
            'landlord_name' => $landlordName,
            'landlord_tel' => $landlordTel,
            'has_intermediary' => $hasIntermediary,
            'intermediary_name' => $intermediaryName,
            'intermediary_tel'=>$intermediaryTel,
            'create_time'=>time()
        );

        $id = $this->renting_house_info->insert_string($rentingHouseInfo);
        if ($id)
        {
            $data = array('id'=>$id);
            output_data($data);exit;
        }
        else
        {
            output_error(-1, '提交租房资料失败');exit;
        }
    }

    //审核贷款申请
    public function approveBorrowing()
    {
        $id = $this->input->post('id');//申请贷款id
        $aprove_status = $this->input->post('approve_status');
        $approve_amount = $this->input->post('approve_amount');
        $approverId = $this->input->post('approver_id');

        if(empty($id) || empty($aprove_status) || empty($approverId))
        {
            output_error(-1, '参数不能为空');exit;
        }
        if (empty($approve_amount))
        {
            $approve_amount = 0;
        }

        //判断申请单是否存在
        $where = array('id'=>$id);
        $applyBorrowing = $this->Borrowing_model->get_list($where);
        if (empty($applyBorrowing)) {
            output_error(-1,'该借款申请不存在');exit;
        }
        //判断操作用户是否存在
        $where = array('id'=>$approverId);
        $appoverInfo = $this->addmin_info_model->get_list($where);
        if (empty($additionalInfo)) {
            output_error(-1,'用户未完善资料');exit;
        }

        $borrowingData = array('id'=>$id,
            'approver_id'=>$approverId,
            'approve_amount'=>$approve_amount,
            'status'=>$aprove_status,
            'approved_time'=>time()
        );

        if ($this->Borrowing_model->insert_string($borrowingData))
        {
            //审批成功，需要把审批结果通知到用户（短信，通知）

            output_data();exit;
        }
        else
        {
            output_error(-1, '审批借贷失败');exit;
        }
    }

    public function borrowingList()
    {
        $status = $this->input->post('status');//-1—审批被拒绝；0—申请中；1—通过申请（还款中） 2-还款结束 3-因故被终止

        $where = array('user_id'=>$this->user_id);
        if(isset($status))
        {
            $where = array('user_id'=>$this->user_id, 'status'=>$status);
        }
        $borrowingList = $this->Borrowing_model->get_list($where,'id,borrowing_product_id,apply_amount,usage,status,credit_cycle,approve_amount,applied_time,approved_time,remarks');
        if (!empty($borrowingList)) {
            $data = array(
                'borrowing_list'=>$borrowingList
            );

            output_data($data);exit;
        }
        else
        {
            output_error(-1, '无借贷记录');exit;
        }
    }

    public function borrowingListEx()
    {
        $type = $this->input->post('type');//0：我的申请（申请中和通过的以及未通过的） 1：我的贷款（通过的）
        if(!isset($type))
        {
            output_error(-1, '参数不能为空');exit;
        }

        $where = array();
        if ($type == 0)//status -1—审批被拒绝；0—申请中；1—通过申请（还款中） 2-还款结束 3-因故被终止
        {
            $where = array('user_id'=>$this->user_id, 'status<'=>2);
        }
        else
        {
            $where = array('user_id'=>$this->user_id, 'status>'=>0);
        }

        //$borrowingList = $this->Borrowing_model->get_list($where,'id,borrowing_product_id,apply_amount,usage,status,credit_cycle,approve_amount, rate,applied_time,approved_time,remarks');
        $orderBy = 'applied_time DESC';
        if ($type == 0)
        {
            $orderBy = 'applied_time DESC';
        }
        else
        {
            $orderBy = 'approved_time DESC';
        }
        $borrowingList = $this->Borrowing_model->get_list($where, '*', $orderBy);
        if (!empty($borrowingList)) {
            foreach($borrowingList as & $borrowingItem)
            if (!isset($borrowingItem['product_name']))
            {
                if (isset($borrowingItem['borrowing_product_id']))
                {
                    $where = array('id'=>$borrowingItem['borrowing_product_id']);
                    $borrowingProductList = $this->Borrowing_product_model->get_by_where($where);
                    if (!empty($borrowingProductList) && isset($borrowingProductList['product_name']))
                    {
                        $borrowingItem['product_name'] = $borrowingProductList['product_name'];
                    }

                }
            }

            $data = array(
                'borrowing_list'=>$borrowingList
            );

            output_data($data);exit;
        }
        else
        {
            output_error(-1, '无借贷记录');exit;
        }
    }

    public function borrowingInfo()
    {
        $id = $this->input->post('id');
        if(!isset($id))
        {
            output_error(-1, '参数不能为空');exit;
        }
        $where = array('user_id'=>$this->user_id, 'id'=>$id);
        $borrowingInfo = $this->Borrowing_model->get_by_where($where);
        if (!empty($borrowingInfo)) {
            output_data($borrowingInfo);exit;
        }
        else
        {
            output_error(-1, '无该借款信息');exit;
        }
    }

    public function getRepaymentSchedule()
    {
        $borrowId = $this->input->post('borrowing_id');
        if (!isset($borrowId))
        {
            output_error(-1, '参数不合法');exit;
        }
        $where = array('user_id'=>$this->user_id, 'borrowing_id'=>$borrowId);

        $cur_time = time();
        $orderBy = 'repayment_deadline asc';
        $repaymentScheduleList = $this->Repayment_schedule->get_list($where,'id,borrowing_id,repayment_amount,repayment_interest,borrowing_period,repayment_deadline,repayment_time,repayment_status', $orderBy);
        if (!empty($repaymentScheduleList)) {

            foreach ($repaymentScheduleList as &$repaymentSchedule) {
                $deadlineTime = $repaymentSchedule['repayment_deadline'];
                $status = $repaymentSchedule['repayment_status'];
                if ($deadlineTime < $cur_time && $status == 0)//逾期
                {
                    $daysDelay = daysBetweenTwoTimes($deadlineTime, $cur_time);
                    $repaymentSchedule['delayDays'] = $daysDelay;
                }
            }
            $data = array(
                'repayment_schedule_list'=>$repaymentScheduleList
            );

            output_data($data);exit;
        }
        else
        {
            output_error(-1, '无还款列表');exit;
        }
    }

    //还款（用户提交还款列表，服务端计算每笔还款金额，总金额等信息）
    /*
     * 每笔延期罚金：当期本息*5%（后台可调费用服务费）+当期本息*1%（后台可调罚息）
     * 提前还款：不全部结清，每笔按本息算;全部结清，5%补偿金+当期本息+剩余几期本金
     */
    public function calculateRepaymentAmount()
    {
        $borrowingId = $this->input->post('borrowing_id');//贷款id
        $repaymentScheduleIds = $this->input->post('repayment_schedule_id_list');//某笔贷款的每期还款id，用;分隔开

        if (!isset($borrowingId,$repaymentScheduleIds))
        {
            output_error(-1, '参数不能为空');exit;
        }

        rtrim($repaymentScheduleIds, ";");

        $cur_time = time();//当前时间
        //$repaymentAmount = $this->PreRepaymentCalculate($borrowingId, $repaymentScheduleIds, $cur_time);
        $repaymentAmountInfo = $this->PreRepaymentCalculate($borrowingId, $repaymentScheduleIds, $cur_time);
        $repaymentAmount = 0;
        $repaymentStatusType = 0;
        $compensationRate = 0;
        if(!empty($repaymentAmountInfo))
        {
            if (isset($repaymentAmountInfo['amount']))
            {
                $repaymentAmount = $repaymentAmountInfo['amount'];
            }
            if (isset($repaymentAmountInfo['repayment_status_type']))
            {
                $repaymentStatusType = $repaymentAmountInfo['repayment_status_type'];
            }
            if (isset($repaymentAmountInfo['compensation_rate']))
            {
                $compensationRate = $repaymentAmountInfo['compensation_rate'];
            }
        }
        $arrRes['total_repayment_amount'] = $repaymentAmount;
        $arrRes['repayment_status_type'] = $repaymentStatusType;
        $arrRes['compensation_rate'] = $compensationRate;
        output_data($arrRes);exit;
    }

    public function repayment()
    {
        $uid = $this->user_id;
        $borrowingId = $this->input->post('borrowing_id');//贷款id
        $repaymentScheduleIds = $this->input->post('repayment_schedule_id_list');//某笔贷款的每期还款id，用;分隔开
        $repaymentAmount = $this->input->post('repayment_amount');//还款金额

        $pay_type = (int)$this->input->post('pay_type');
        $order_id = $repaymentScheduleIds;


        if (!isset($borrowingId,$repaymentScheduleIds, $repaymentAmount))
        {
            output_error(-1, '参数不能为空');exit;
        }

        log_message('debug', '@@@@@@@ repayment-1'.'repayment_schedule_id_list:'.$repaymentScheduleIds);

        $repaymentScheduleIds = rtrim($repaymentScheduleIds, ",");

        log_message('debug', '@@@@@@@ repayment-2'.'repayment_schedule_id_list:'.$repaymentScheduleIds);

        $cur_time = time();
        $repaymentAmountInfo = $this->PreRepaymentCalculate($borrowingId, $repaymentScheduleIds, $cur_time, true);//计算金额同时，把每笔还款的具体还款金额记录到表中
        $amount = 0;
        if(!empty($repaymentAmountInfo))
        {
            if (isset($repaymentAmountInfo['amount']))
            {
                $amount = $repaymentAmountInfo['amount'];
            }
        }

        if ($repaymentAmount != $amount)
        {
            $diff = $repaymentAmount - $amount;
            $diff = abs($diff);
            if ($diff >= 0.01)//忽略小于一分的差额，否则会因为类似0.13不等于0.1309而出现无法还款的情况！
            {
                output_error(-1, '还款数额有误'."'$amount'");exit;
            }

        }

        //还款

        if ($repaymentAmount <= 0) {
            output_error(-1, '还款金额不能小于0！');
        }

        $PayMethodName = config_item('PayMethodName');
        if (empty($PayMethodName["$pay_type"])) {
            output_error(-3, '支付方式不存在！');//交易订单创建失败
        }

        $amount_price = $repaymentAmount;
        $this->load->service('pay_service');
        $this->load->service('trd_service');

        $order_type = 1;//1还款 2充值
        $fund_id = 0;
        $res = $this->trd_service->createTrd($fund_id,$repaymentAmount, $uid, $pay_type, $repaymentScheduleIds, $order_type);

        if ($res['code'] < 0) {
            output_error($res['code'], '交易订单创建失败！');//交易订单创建失败
            log_message('debug', 'create pay order failed:' + $res['code']);
        } else {
            $res = $this->trd_service->doNextStep($res['order_id'], $res['order_type'],$this->user_id, BASE_SITE_URL.'/wap/member/balance-tup.html?order_id='.$order_id);
            output_data($res);//交易订单创建成功
        }
    }

    //预还款计算
    //$isPrerepayment 为true，则需要把每笔还款的金额都算出记在还款记录表中
    private function PreRepaymentCalculate($borrowingId, $repaymentScheduleIds, $cur_time, $isPreRepayment = false)
    {
        if (!isset($borrowingId,$repaymentScheduleIds))
        {
            output_error(-1, '参数不能为空');exit;
        }

        $where = array('id'=>$borrowingId ,'user_id'=>$this->user_id, 'status>'=>0 ,'status<'=>3);
        $borrowingItem = $this->Borrowing_model->get_by_where($where,'id,borrowing_product_id,apply_amount,usage,status,credit_cycle,approve_amount,applied_time,approved_time,remarks');
        if (empty($borrowingItem)) {
            output_error(-1, '贷款不存在');exit;
        }

        $repaymentStatusType = 0;//0:正常还款 1：有延期还款 2：提前还款 3：提前还款且有延期
        $allRepaymentAmount = 0;//多笔还款总计金额
        $approveAmount = (float)$borrowingItem['approve_amount'];//借款总额

        $BorrowingRate = config_item('BorrowingRate');

        $compensationRate = $BorrowingRate['CompensationRate'];//提前还清补偿金率
        $serviceFee = $BorrowingRate['ServiceFee'];//服务费
        $lateInterestRate = $BorrowingRate['LateInterestRate'];//罚息

        //从数据库获取配置
        $compensationRate = C('compensation_rate')/100;
        $serviceFee = C('service_fee')/100;
        $lateInterestRate = C('lateInterest_rate')/100;

        $periodType = 0;//贷款类型 （还款类型） 1：按月还 2：按周还 3：按天（周期）还
        $periodDays = 0;
        $rate = 2;//利率

        $arrToRepaymentScheduleId = explode(',',$repaymentScheduleIds);

        //$cur_time = time();//当前时间

        //还款状态 0:未还款 1：已还款
        $repayment_status = 0;
        $where = array('user_id'=>$this->user_id, 'borrowing_id'=>$borrowingId, 'repayment_status'=>$repayment_status);
        //'id,borrowing_id,repayment_amount,repayment_interest,borrowing_period,repayment_deadline,repayment_time,repayment_status'
        $repaymentScheduleList = $this->Repayment_schedule->get_list($where);

        //判断逻辑非常复杂，比如要还款id列表是否重复，是否存在等都要判断，先简单处理等。这边先做简答处理
        $arrToRepaymentScheduleId = array_filter($arrToRepaymentScheduleId);
        $arrToRepaymentScheduleId = array_unique($arrToRepaymentScheduleId);//去掉重复id
        $theNearestSchedule = '';//未延期的最近的一笔还款
        $theNearestTime = 0;
        if(count($repaymentScheduleList) == count($arrToRepaymentScheduleId) && count($arrToRepaymentScheduleId) > 1)//提前还款（全结清）
        {
            //output_error(-99, '@@@@@@@@@@----All repayment----@@@@@@@@@'.'$repaymentScheduleList:'.count($repaymentScheduleList).'$arrToRepaymentScheduleId:'.count($arrToRepaymentScheduleId));
            //全部结清：5%补偿金+当期本息+剩余几期本金
            $compensationAmount = $approveAmount * $compensationRate;//5%(服务端配置可调)补偿金
            $allRepaymentAmount = $compensationAmount;
            $aRepaymentInterest = 0;

            $repaymentAmount = 0;
            $repaymentInterest = 0;

            $undelayCount = 0;//未延期笔数，如果未延期笔数小于2，则不算是提前还款

            foreach ($arrToRepaymentScheduleId as $repaymentId){
                $where = array('user_id'=>$this->user_id, 'borrowing_id'=>$borrowingId, 'id'=>$repaymentId, 'repayment_status'=>$repayment_status);
                //'id,borrowing_id,repayment_amount,repayment_interest,borrowing_period,repayment_deadline,repayment_time,repayment_status'
                $repaymetInfo = $this->Repayment_schedule->get_by_where($where);

                if (empty($repaymetInfo))
                {
                    continue;
                }

                $repaymentAmount = (float)$repaymetInfo['repayment_amount'];//本金
                $repaymentInterest = (float)$repaymetInfo['repayment_interest'];//利息
                $aRepaymentInterest = $repaymentInterest;
                $borrowingPeriod = $repaymetInfo['borrowing_period'];//第几期
                $repaymentDeadline = $repaymetInfo['repayment_deadline'];
                //判断是否是本期以及是否延期
                if ($repaymentDeadline < $cur_time)//延期
                {
                    $daysDelay = daysBetweenTwoTimes($repaymentDeadline, $cur_time);

                    //每笔延期罚金：当期本息*5%（后台可调费用服务费）+当期本息*1%（后台可调罚息）
                    $repaymentTotal = $repaymentAmount + $repaymentInterest;//本息总和
                    $fine = $repaymentTotal*$serviceFee + $repaymentTotal*$lateInterestRate * $daysDelay;
                    $repaymentTotal += $fine;

                    $allRepaymentAmount += $repaymentTotal;

                    if ($isPreRepayment)
                    {
                        $data = array('real_repayment_amount'=> $repaymentTotal, 'delay_fine'=> $fine);
                        $this->Repayment_schedule->update_by_id($repaymentId, $data);
                    }

                    $repaymentStatusType = 1;//0:正常还款 1：有延期还款 2：提前还款 3：提前还款且有延期
                }
                else
                {
                    //本期计算本息
                    //$days = daysBetweenTwoTimes($cur_time, $repaymentDeadline);

                    $undelayCount++;

                    if ($theNearestTime == 0)
                    {
                        $theNearestTime = $repaymentDeadline;
                        $theNearestSchedule = $repaymentId;
                    }
                    elseif ($theNearestTime > $repaymentDeadline)
                    {
                        $theNearestTime = $repaymentDeadline;
                        $theNearestSchedule = $repaymentId;
                    }

                    $allRepaymentAmount += $repaymentAmount;//加上所有本金

                    if ($isPreRepayment)
                    {
                        $data = array('real_repayment_amount'=> $repaymentAmount);
                        $this->Repayment_schedule->update_by_id($repaymentId, $data);
                    }

                }
            }

            $allRepaymentAmount += $aRepaymentInterest;//其中未延期的第一期还款，需要还利息
            //if (empty($theNearestSchedule))//表明全是延期的还款，则不应该算是提前还清贷款
            if ($undelayCount<2)//表明全是延期的还款或者一笔当前还款加上N笔逾期还款，则不应该算是提前还清贷款
            {
                $allRepaymentAmount -= $compensationAmount;//5%(服务端配置可调)补偿金
                if($undelayCount < 1)//全是逾期，则没有“未延期的第一期还款，需要还利息”
                {
                    $allRepaymentAmount -= $aRepaymentInterest;
                }
            }
            else
            {
                $data = array('compensation_amount'=> $compensationAmount);
                $this->Borrowing_model->update_by_id($borrowingId, $data);

                $repaymentTotal = $repaymentAmount + $repaymentInterest;
                $data = array('real_repayment_amount'=> $repaymentTotal);
                $this->Repayment_schedule->update_by_id($theNearestSchedule, $data);

                if ($repaymentStatusType == 0)
                {
                    $repaymentStatusType = 2;//0:正常还款 1：有延期还款 2：提前还款 3：提前还款且有延期
                }
                else
                {
                    $repaymentStatusType = 3;//0:正常还款 1：有延期还款 2：提前还款 3：提前还款且有延期
                }
            }
        }
        else//非全部结清逻辑
        {
            //output_error(-100, 'Not all repayment');
            foreach ($arrToRepaymentScheduleId as $repaymentId){
                $where = array('user_id'=>$this->user_id, 'borrowing_id'=>$borrowingId, 'id'=>$repaymentId, 'repayment_status'=>$repayment_status);
                //'id,borrowing_id,repayment_amount,repayment_interest,borrowing_period,repayment_deadline,repayment_time,repayment_status'
                $repaymetInfo = $this->Repayment_schedule->get_by_where($where);

                $repaymentAmount = (float)$repaymetInfo['repayment_amount'];//本金
                $repaymentInterest = (float)$repaymetInfo['repayment_interest'];//利息
                $aRepaymentInterest = $repaymentInterest;
                $borrowingPeriod = $repaymetInfo['borrowing_period'];//第几期
                $repaymentDeadline = $repaymetInfo['repayment_deadline'];
                //判断是否是本期以及是否延期
                if ($repaymentDeadline < $cur_time)//延期
                {
                    $daysDelay = daysBetweenTwoTimes($repaymentDeadline, $cur_time);

                    //每笔延期罚金：当期本息*5%（后台可调费用服务费）+当期本息*1%（后台可调罚息）
                    $repaymentTotal = $repaymentAmount + $repaymentInterest;
                    $fine = $repaymentTotal*$serviceFee + $repaymentTotal*$lateInterestRate*$daysDelay;
                    $repaymentTotal += $fine;

                    $allRepaymentAmount += $repaymentTotal;


                    if ($isPreRepayment)
                    {
                        /*
                        * real_repayment_amount//实际还款金额
                        * delay_fine//延期还款罚金
                        */
                        $data = array('real_repayment_amount'=> $repaymentTotal, 'delay_fine'=> $fine);
                        $this->Repayment_schedule->update_by_id($repaymentId, $data);
                    }


                    $repaymentStatusType = 1;//0:正常还款 1：有延期还款 2：提前还款 3：提前还款且有延期

                }
                else
                {
                    //本期计算本息
                    $repaymentTotal = $repaymentAmount + $repaymentInterest;
                    $allRepaymentAmount += $repaymentTotal;//加上所有本息

                    if ($isPreRepayment) {
                        $data = array('real_repayment_amount' => $repaymentTotal);
                        $this->Repayment_schedule->update_by_id($repaymentId, $data);
                    }
                }
            }
        }

        $repaymentAmountInfo = array('amount'=>$allRepaymentAmount, 'repayment_status_type'=>$repaymentStatusType, 'compensation_rate'=>$compensationRate);
        //return $allRepaymentAmount;
        return $repaymentAmountInfo;
    }

    public function loan()
    {

    }

    public function withDraw()
    {

    }
}