<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//评价服务 maoweihua
class comment_service
{   
    public function __construct(){  
		$this->ci = & get_instance();
        $this->ci->load->model('user_comment_model');
        $this->ci->load->model('doctor_model');
        $this->ci->load->model('hospital_model');
	}
	
	//添加评论内容
    public function addCommentOrder($data){
        if (!empty($data) && is_array($data)) {
            $dat = array('mobile'=>$data['mobile'],'type'=>$data['type'],'item_id'=>$data['item_id'],'item_name'=>$data['item_name'],'comments'=>$data['comments'],'score'=>$data['score'],'order_type'=>$data['order_type'],'order_id'=>$data['order_id'],'createtime'=>time());
            $res = $this->ci->user_comment_model->insert_string($dat);
            if (!empty($res)) {
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    } 
    
    //计算医生所得订单金额
    public function calc_doctor_getprice($price,$score){
        if ($price && $score) {
            $returnPrice = 0;
            switch ($score) {
                case '1':$returnPrice = ($price * 0.6);
                    break;
                case '2':
                    $returnPrice = ($price * 0.7);
                    break;
                case '3':
                    $returnPrice = ($price * 0.8);
                    break;
                case '4':
                    $returnPrice = ($price * 0.9);
                    break;
                case '5':
                    $returnPrice = $price;
                    break;    
            }
            return $returnPrice;
        }else{
            return false;
        }
    }

    //修改医生的score
    public function modify_doctor_score($doctor_id,$score){
        if (!empty($doctor_id) || !empty($score)) {
            $count_score = $this->ci->user_comment_model->count(array('type'=>1,'item_id'=>$doctor_id));
            $sum_score = $this->ci->user_comment_model->sum(array('type'=>1,'item_id'=>$doctor_id),'score');
            if (empty($count_score) || empty($sum_score)) {
                $avg_score = $score;
            }else{
                $avg_score = ($sum_score+$score)/($count_score+1);
            }
            $res  = $this->ci->doctor_model->update_by_id($doctor_id,array('score'=>round($avg_score,1)));
            return true;
        }else{
            return false;
        }
    }

    //修改医院的score
    public function modify_hospital_score($hospital_id,$score){
        if (!empty($hospital_id) || !empty($score)) {
            $count_score = $this->ci->user_comment_model->count(array('type'=>2,'item_id'=>$hospital_id));
            $sum_score = $this->ci->user_comment_model->sum(array('type'=>2,'item_id'=>$hospital_id),'score');
            if (empty($count_score) || empty($sum_score)) {
                $avg_score = $score;
            }else{
                $avg_score = ($sum_score+$score)/($count_score+1);
            }
            $res  = $this->ci->hospital_model->update_by_id($hospital_id,array('score'=>round($avg_score,1)));
            
            return true;
            
        }else{
            return false;
        }
    }

}