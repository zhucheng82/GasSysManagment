<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//还款

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

class repayment_service
{
    public function __construct(){  
		$this->ci = & get_instance();
        $this->ci->load->model('Borrowing_model');
        $this->ci->load->model('Repayment_schedule');
	}

    //还款（用户提交还款列表，服务端计算每笔还款金额，总金额等信息）
    /*
     * 每笔延期罚金：当期本息*5%（后台可调费用服务费）+当期本息*1%（后台可调罚息）
     * 提前还款：不全部结清，每笔按本息算;全部结清，5%补偿金+当期本息+剩余几期本金
     *
     * $borrowingId:贷款id
     * $repaymentScheduleIds:某笔贷款的每期还款id列表，用;分隔开
     */
    public function calculateRepaymentAmount($borrowingId, $repaymentScheduleIds)
    {
        if (!isset($borrowingId,$repaymentScheduleIds))
        {
            //output_error(-1, '参数不能为空');exit;
            return -1;
        }

        rtrim($repaymentScheduleIds, ";");

        $cur_time = time();//当前时间
        $repaymentAmountInfo = $this->PreRepaymentCalculate($borrowingId, $repaymentScheduleIds, $cur_time, true);
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
        $repaymentAmount = number_format($repaymentAmount, 2, '.', '');
        $arrRes['total_repayment_amount'] = $repaymentAmount;
        $arrRes['repayment_status_type'] = $repaymentStatusType;
        $arrRes['compensation_rate'] = $compensationRate;

        //output_data($arrRes);exit;

        return $repaymentAmount;
    }

    //预还款计算
    //$isPrerepayment 为true，则需要把每笔还款的金额都算出记在还款记录表中
    //
    private function PreRepaymentCalculate($borrowingId, $repaymentScheduleIds, $cur_time, $isPreRepayment = false)
    {
        if (!isset($borrowingId,$repaymentScheduleIds))
        {
            //output_error(-1, '参数不能为空');exit;
            return null;
        }

        $where = array('id'=>$borrowingId, 'status>'=>0 ,'status<'=>3);
        $borrowingItem = $this->ci->Borrowing_model->get_by_where($where,'id,borrowing_product_id,apply_amount,usage,status,credit_cycle,approve_amount,applied_time,approved_time,remarks');
        if (empty($borrowingItem)) {
            //output_error(-1, '贷款不存在');exit;
            return null;
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

        $arrToRepaymentScheduleId = explode(',',$repaymentScheduleIds);

        //$cur_time = time();//当前时间

        //还款状态 0:未还款 1：已还款
        $repayment_status = 0;
        $where = array('borrowing_id'=>$borrowingId, 'repayment_status'=>$repayment_status);
        //'id,borrowing_id,repayment_amount,repayment_interest,borrowing_period,repayment_deadline,repayment_time,repayment_status'
        $repaymentScheduleList = $this->ci->Repayment_schedule->get_list($where);

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
                $where = array('borrowing_id'=>$borrowingId, 'id'=>$repaymentId, 'repayment_status'=>$repayment_status);
                //'id,borrowing_id,repayment_amount,repayment_interest,borrowing_period,repayment_deadline,repayment_time,repayment_status'
                $repaymetInfo = $this->ci->Repayment_schedule->get_by_where($where);

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
                        $this->ci->Repayment_schedule->update_by_id($repaymentId, $data);
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
                        $this->ci->Repayment_schedule->update_by_id($repaymentId, $data);
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
                $this->ci->Borrowing_model->update_by_id($borrowingId, $data);

                $repaymentTotal = $repaymentAmount + $repaymentInterest;
                $data = array('real_repayment_amount'=> $repaymentTotal);
                $this->ci->Repayment_schedule->update_by_id($theNearestSchedule, $data);

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
                $where = array('borrowing_id'=>$borrowingId, 'id'=>$repaymentId, 'repayment_status'=>$repayment_status);
                //'id,borrowing_id,repayment_amount,repayment_interest,borrowing_period,repayment_deadline,repayment_time,repayment_status'
                $repaymetInfo = $this->ci->Repayment_schedule->get_by_where($where);

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
                        $this->ci->Repayment_schedule->update_by_id($repaymentId, $data);
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
                        $this->ci->Repayment_schedule->update_by_id($repaymentId, $data);
                    }
                }
            }
        }

        $repaymentAmountInfo = array('amount'=>$allRepaymentAmount, 'repayment_status_type'=>$repaymentStatusType, 'compensation_rate'=>$compensationRate);
        //return $allRepaymentAmount;
        return $repaymentAmountInfo;
    }


}