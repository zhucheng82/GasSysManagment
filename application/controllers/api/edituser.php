<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Edituser extends TokenApiController
{
    public $user_id;
    public $user_name;
    public $user_type;
    public $userInfo;
    public function __construct()
    {
        parent::__construct();
        $this->load->service('sms_service');
        //$this->price_type = $this->config->item('price_type');
        //$this->doctor_title = $this->config->item('doctor_title');
        $this->userInfo = $this->loginUser;
        $this->user_id = $this->loginUser['user_id'];
        $this->user_name = $this->loginUser['user_name'];
        //$this->user_type = $this->loginUser['user_type'];
    }

    //用户基本信息
    /*public function info(){
        if ($this->user_type==1) {//患者
            $data = array('real_name'=>$this->loginUser['real_name'],'user_name'=>$this->loginUser['username']);
        }elseif ($this->user_type==2) {//医生
            $this->load->model('doctor_model');
            $doctorInfo = $this->doctor_model->get_by_where(array('user_id'=>$this->user_id),'real_name,portrait,doctor_title');
            get_icon_url_full_path($doctorInfo, 'portrait');
            $data = array('real_name'=>$doctorInfo['real_name'],'portrait'=>$doctorInfo['portrait'],'doctor_title'=>$this->doctor_title[$doctorInfo['doctor_title']]);
        }elseif($this->user_type==3){
            $this->load->model('hospital_model');
            $hospitalInfo = $this->hospital_model->get_by_where(array('user_id'=>$this->user_id),'name,icon');
            get_icon_url_full_path($hospitalInfo, 'icon');
            $data = array('real_name'=>$hospitalInfo['name'],'icon'=>$hospitalInfo['icon']);
        }
        if (!empty($data)) {
            output_data($data);
        }else{
            output_error(-1,'获取用户信息失败');
        }
    }
*/

    public function setPortrait()
    {
        $portraitUrl = $this->input->post('portrait');
        $dat['portrait'] = $portraitUrl;
        $res = $this->user_model->update_by_id($this->user_id,$dat);
        if (!empty($res)) {
            //修改验证码

            output_data();
        }else{
            output_error(-1,'修改头像失败');
        }
    }

    //修改登录手机号
    public function editUserName(){
        if ($this->input->is_post()) {
            $this->load->model('user_model');
            $code = $this->input->post('code');
            $newMobile = $this->input->post('newMobile');
            $userPsw   = $this->input->post('userPsw');
            $bMobile = preg_match("/^1[34578]\d{9}$/", $newMobile);
            if(!$bMobile){
                output_error(-1,'手机号格式不对');
            }
            $user_code = $this->sms_service->check_code($newMobile,$code,3,9);
            if (empty($user_code)) {
                output_error(-1,'验证码错误');
            }
            $userInfo = $this->user_model->get_by_where(array('user_name'=>"'".$newMobile."'"));
            if (!empty($userInfo) && $userInfo['user_id']!=$this->user_id) {
                output_error(-1,'该手机号已被其他用户使用');
            }
            //print_r($userInfo);exit();
            if (!empty($userInfo) && $userInfo['user_name']==$newMobile && $userInfo['user_id']==$this->user_id) {
                output_error(-1,'用户手机号未改变');
            }
            if (empty($userInfo)) {
                $userInfoPsw = $this->user_model->get_by_id($this->user_id,'password');
                if (md5($userPsw)!=$userInfoPsw['password']) {
                    output_error(-1,'用户登录密码错误');
                }
            }
            $dat['user_name'] = $newMobile;
            $res = $this->user_model->update_by_id($this->user_id,$dat);
            if (!empty($res)) {
                //修改验证码
                
                output_data();
            }else{
                output_error(-1,'修改失败');
            }
        }
    }

    //修改登录密码
    public function changeUserPassword(){
        if ($this->input->is_post()) {
            $this->load->model('user_model');
            $oldpsw = $this->input->post('oldpsw');
            $newpsw = $this->input->post('newpsw');
            if (!preg_match("/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,20}$/", $newpsw)) {
                output_error(-1,'密码格式不正确，须为数字+英文(大小写)');
            }
            $userInfoPsw = $this->user_model->get_by_id($this->user_id,'user_name,password');
            if (empty($userInfoPsw)) {
                output_error(-1,'用户不存在');
            }
            if (!empty($userInfoPsw) && $userInfoPsw['password']!= md5($oldpsw))
            //if (!empty($userInfoPsw) && $userInfoPsw['password']!= $oldpsw)
            {
                output_error(-1,'原登录密码错误');
            }
            $dat['password'] = md5($newpsw);
            //$dat['password'] = $newpsw;
            $res = $this->user_model->update_by_id($this->user_id,$dat);
            if (!empty($res)) {
                //修改验证码
                output_data();
            }else{
                output_error(-1,'修改失败');
            }
        }
    }

    //设置支付密码
    public function setPaypsw(){
       if ($this->input->is_post()) {
            $this->load->model('userinfo_model');
            $Paypsw = $this->input->post('paypsw');
            /*$loginPsw = $this->input->post('loginPsw');*/
            if (empty($Paypsw)) {
                output_error(-1,'必要参数为空');
            }
            if (!preg_match("/^[0-9]*$/", $Paypsw)) {
                output_error(-1,'密码格式不正确，须为纯数字');
            }
            if (strlen($Paypsw)!=6) {
                output_error(-1,'密码长度只能为6位数字');
            }
            $userInfoPsw = $this->userinfo_model->get_by_id($this->user_id,'username,paypsw,password');
            if (empty($userInfoPsw)) {
                output_error(-1,'用户不存在');
            }
            if (!empty($userInfoPsw['paypsw'])) {
                output_error(-1,'您已设置过支付密码，不可重复设置');
            }
            /*if (!empty($userInfoPsw) && $userInfoPsw['password']!= md5($loginPsw)) {
                output_error(-1,'登录密码错误');exit;
            }*/
            $dat['paypsw'] = md5($Paypsw);
            $res = $this->userinfo_model->update_by_id($this->user_id,$dat);
            if (!empty($res)) {
                //修改验证码
                output_data();
            }else{
                output_error(-1,'修改失败');
            }
        } 
    }

    //修改支付密码
    public function editUserPaypsw(){
        if ($this->input->is_post()) {
            $this->load->model('userinfo_model');
            $oldPaypsw = $this->input->post('oldPaypsw');
            $newPaypsw = $this->input->post('newPaypsw');
            if (empty($oldPaypsw)|| empty($newPaypsw)) {
                output_error(-1,'必要参数为空');
            }
            if (!preg_match("/^[0-9]*$/", $newPaypsw)) {
                output_error(-1,'密码格式不正确，须为纯数字');
            }
            if (strlen($newPaypsw)!=6) {
                output_error(-1,'密码长度只能为6位数字');
            }
            $userInfoPsw = $this->userinfo_model->get_by_id($this->user_id,'username,paypsw');
            if (empty($userInfoPsw)) {
                output_error(-1,'用户不存在');
            }
            if (!empty($userInfoPsw) && $userInfoPsw['paypsw']!= md5($oldPaypsw)) {
                output_error(-1,'原支付密码错误');
            }
            $dat['paypsw'] = md5($newPaypsw);
            $res = $this->userinfo_model->update_by_id($this->user_id,$dat);
            if (!empty($res)) {
                //修改验证码
                output_data();
            }else{
                output_error(-1,'修改失败');
            }
        }
    }

    //忘记支付密码
    public function forgetPaypsw(){
        if ($this->input->is_post()) {
            $this->load->model('userinfo_model');
            $code = $this->input->post('code');
            $newPaypsw = $this->input->post('newPaypsw');
            if (empty($code)|| empty($newPaypsw)) {
                output_error(-1,'必要参数为空');
            }
            $user_code = $this->sms_service->check_code($this->user_name,$code,4,9);
            if (empty($user_code)) {
                output_error(-1,'验证码错误');
            }
            if (!preg_match("/^[0-9]*$/", $newPaypsw)) {
                output_error(-1,'密码格式不正确，须为纯数字');
            }
            $userInfoPsw = $this->userinfo_model->get_by_id($this->user_id,'user_name,paypsw');
            if (empty($userInfoPsw)) {
                output_error(-1,'用户不存在');
            }
            $dat['paypsw'] = md5($newPaypsw);
            $this->userinfo_model->update_by_id($this->user_id,$dat);
            output_data();
        }
    }

    /**
     * @param 用户注销
     * 
     * @param $_POST['token'];
     * @param $_POST['client_type'];
     * 
     * @return 
     */
    public function logout()
    {
        $user = $this->loginUser;
        $user_id = $user['user_id'];
        $token = $user['token'];
        $client_type = $this->input->post('client_type');
                
        if($client_type==1){
            $where['token'] = $user['token'];
            $where['client_type'] = 1;
        }else{
            $where['client_type >'] = 1;
            $where['token'] = $token;
        }
        $data['status'] = -1;
        $this->User_token_model->update_by_where($where,$data);
        output_data();
    }

}
