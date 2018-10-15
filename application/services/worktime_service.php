<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class worktime_service
{   
    public function __construct(){  
		$this->ci = & get_instance();
        $this->ci->load->model('doctor_worktime_model');
        $this->ci->load->model('doctor_worktime_detail_model');
	}
	
	//计算排版时间
    public function calc_worktime($worktimeInfo){
        if (!empty($worktimeInfo)) {
            if (!empty($worktimeInfo['am_start_time']) && !empty($worktimeInfo['am_end_time'])) {
                //排班时间(上午)
                $am_worktime_arr = array();
                $am_start_time   = intval($worktimeInfo['am_start_time']);//上午开始时间
                $am_end_time     = intval($worktimeInfo['am_end_time']); //下午开始时间
                $am_start_date   = date('H:i',$am_start_time);
                $am_end_date     = date('H:i',$am_end_time);
                //print_r($am_end_date);exit();
                $am_pelple_time = (($am_end_time - $am_start_time) / $worktimeInfo['am_register_num']);//   /60
                for ($i=0; $i < $worktimeInfo['am_register_num']; $i++) { 
                    $am_start_temp[$i][] = $am_start_time + ($am_pelple_time*$i);
                    $am_start_temp[$i][] = $am_start_time + ($am_pelple_time*($i+1)); 
                }
            }
            if (!empty($worktimeInfo['pm_start_time']) && !empty($worktimeInfo['pm_end_time'])) {   
                //下午
                $pm_worktime_arr = array();
                $pm_start_time   = intval($worktimeInfo['pm_start_time']);//上午开始时间
                $pm_end_time     = intval($worktimeInfo['pm_end_time']); //下午开始时间
                $pm_start_date   = date('H:i',$pm_start_time);
                $pm_end_date     = date('H:i',$pm_end_time);
                //print_r($pm_end_date);exit();
                $pm_pelple_time = (($pm_end_time - $pm_start_time) / $worktimeInfo['pm_register_num']);//   /60
                for ($i=0; $i < $worktimeInfo['pm_register_num']; $i++) { 
                    $pm_start_temp[$i][] = $pm_start_time + ($pm_pelple_time*$i);
                    $pm_start_temp[$i][] = $pm_start_time + ($pm_pelple_time*($i+1)); 
                }
            }
            if (!empty($worktimeInfo['am_start_time']) && !empty($worktimeInfo['am_end_time']) && !empty($worktimeInfo['pm_start_time']) && !empty($worktimeInfo['pm_end_time'])) {
                $list = array_merge($am_start_temp,$pm_start_temp);    
            }elseif (!empty($worktimeInfo['am_start_time']) && !empty($worktimeInfo['am_end_time']) && empty($worktimeInfo['pm_start_time']) && empty($worktimeInfo['pm_end_time'])) {
                $list = $am_start_temp ;
            }elseif (empty($worktimeInfo['am_start_time']) && empty($worktimeInfo['am_end_time']) && !empty($worktimeInfo['pm_start_time']) && !empty($worktimeInfo['pm_end_time'])) {
                $list = $pm_start_temp ; 
            }     
            
            $returnArr = array('code'=>1,'msg'=>'正常','list'=>$list);
        }else{
            $returnArr = array('code'=>0,'msg'=>'数据错误');
        }

        return $returnArr;
    } 
    
    //判断时间段是否已被患者预约
    public function is_choice($work_time_id,$time){
        if ($time && $work_time_id) {
            $is_check = $this->ci->doctor_worktime_detail_model->get_by_where(array('work_time_id'=>$work_time_id,'registered_time'=>"'".$time."'",'status >='=>0));
            if (!empty($is_check)) {
                if ($is_check['status']==0 && $is_check['createtime']<(time()-30*60)) {
                    $returnArr = array('code'=>0,'msg'=>'正常');
                }else{
                    $returnArr = array('code'=>1,'msg'=>'已预约','info'=>$is_check);
                }
            }else{
                $returnArr = array('code'=>0,'msg'=>'正常');
            }
        }else{
            $returnArr = array('code'=>0,'msg'=>'数据错误');
        }
        return $returnArr;
    }

    //判断用户是否已挂某排版
    public function checkWorktimeDetail($work_time_id,$userId){
        if ($work_time_id) {
            $is_check = $this->ci->doctor_worktime_detail_model->get_by_where(array('work_time_id'=>$work_time_id,'user_id'=>$userId,'status >='=>0));
            if (!empty($is_check)) {
                if ($is_check['status']==0 && $is_check['createtime']<(time()-30*60)) {
                    $returnArr = array('code'=>0,'msg'=>'正常');
                }else{
                    $returnArr = array('code'=>1,'msg'=>'已预约','info'=>$is_check);
                }
            }else{
                $returnArr = array('code'=>0,'msg'=>'正常');
            }
        }else{
            $returnArr = array('code'=>0,'msg'=>'数据错误');
        }
        return $returnArr;
    }

}