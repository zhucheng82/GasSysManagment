<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_service
{
    private $randstr = "12345678910111213141516171816202122232425262728293031323334353637383940";

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('user_model');
        $this->ci->load->model('User_token_model');
        $this->ci->load->library('encryption');
        $this->ci->load->model('student_model');
        $this->ci->load->model('employee_model');
        //$this->ci->load->model('Account_model'); //账户模块
    }


    /**
     * @param $arrUserData =array('uin'=>'uin','a1'=>'a1')
     *
     * @return $arrRes = array('data'=>array('user_id'=>1,'username'=>'用户名','name'=>'昵称'.....) ,'code'=>'SUCCESS','msg'=>'登录成功')
     */
    public function login($arrUserData)
    {
        $user_account = $arrUserData['user_account'];
        $pwd_md5 = $arrUserData['pwd_md5'];
        $client_type = $arrUserData['client_type'];
        if (empty($user_account)) {
            output_error(-1, '用户名不能为空');
            exit;
        }
        $arrWhere1 = array('user_name' => "'$user_account'");
        $aUserInfo1 = $this->ci->user_model->get_by_where($arrWhere1);

        //echo $this->ci->user_model->db->last_query();

        if (empty($aUserInfo1)) {
            output_error(-1, '用户不存在');
            exit;
        }

        $arrWhere = array('user_name' => "'$user_account'", 'password' => "'$pwd_md5'");
        $aUserInfo = $this->ci->user_model->get_by_where($arrWhere);
        if (empty($aUserInfo)) {
            output_error(-1, '密码不正确');
            exit;
        }

        $arrWhere = array('user_name' => $user_account, 'status' => 1);
        $this->ci->User_token_model->update_by_where($arrWhere, array('status' => -2));

        if ($aUserInfo['status'] == 2) {
            output_error(2, 'USER_LOCKED');
            exit;
        }

        $arrWhere = array('user_name' => "'$user_account'");
        $data = $this->ci->user_model->get_by_where($arrWhere, 'user_id,user_name,rongytoken,work,authentication_status,additional_info_status,status,invite_code,balance,portrait,gesture_pwd');
        //print_r($data);exit;
        $authStautus = $data['authentication_status'];//认证状态 0，未认证；1，已认证
        if ($authStautus == 1)
        {
            $work = $data['work'];//职业：-1，未知；0，学生；1，白领
            if($work == 0)
            {
                $arrWhere = array('user_id' => $data['user_id']);
                $studentInfo = $this->ci->student_model->get_by_where($arrWhere, 'user_name,is_new_student');
                $data['real_name'] = $studentInfo['user_name'];
                $data['is_new_student'] = $studentInfo['is_new_student'];
            }
            else if($work == 1)
            {
                $arrWhere = array('user_id' => $data['user_id']);
                $userRealName = $this->ci->employee_model->get_by_where($arrWhere, 'user_name');
                $data['real_name'] = $userRealName['user_name'];
            }
        }
        $tokenData = array(
            'user_id' => $data['user_id'],
            'user_name' => $data['user_name'],
            'token' => md5(time() . mt_rand(0, 1000)),
            'refresh_token' => md5(time() . mt_rand(1000, 2000)),
            'addtime' => time(),
            'expire_time' => time() + 86400 * 7,
            'client_type' => $arrUserData['client_type'],
        );
        $data['token'] = $tokenData['token'];
        $data['refresh_token'] = $tokenData['refresh_token'];
        if ($this->ci->User_token_model->insert($tokenData)) {
            $arrRes = $data;
            return $arrRes;
        } else {
            output_error(-1, 'FAILED');
            exit;
        }
    }

    /**
     * @param $arrUserInfo =array('user_name'=>'用户名','mobile'=>'手机号码','pwd'=>'密码','name'=>'昵称','platform_id' =>1,'ip'=>'ip')
     *
     * @return
     * $arrRes = Array (
     * [code] => SUCCESS
     * [msg] => 注册成功
     * )
     */
    public function reg($arrUserData)
    {

        $arrReturn = array('code' => 'EMPTY', 'message' => '', 'data' => 0);
        if (!preg_match("/^1[34578]\d{9}$/", $arrUserData['mobile'])) {
            $arrReturn = array('code' => 'USER_PHONE_FORMAT_ERROR', 'message' => '手机号格式', 'data' => 0);
            return $arrReturn;
        }

        if (!preg_match("/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,20}$/", $arrUserData['pwd'])) {
            $arrReturn = array('code' => 'USER_PWD_FORMAT_ERROR', 'message' => '密码格式不正确，须为数字+英文(大小写)', 'data' => 0);
            return $arrReturn;
        }
        $arrReturn = $this->reg_user($arrUserData);
        if ($arrReturn['code'] == 'SUCCESS') {
            $where = array('user_name' => "'" . $arrUserData['user_name'] . "'");
            $aUser = $this->ci->user_model->get_by_where($where);
            $arrReturn['data'] = $aUser;
        }

        return $arrReturn;

    }

    /**
     * @param $arrUserData =array('user_name'=>'用户名','mobile'=>'手机号','pwd'=>'密码','name'=>'昵称','platform_id' =>1,'ip'=>'ip')
     * @return
     * $arrReturn = Array (
     * [code] => SUCCESS
     * [message] => 注册成功
     * )
     */
    public function reg_user($arrUserData)
    {
        $arrReturn = array('code' => 'EMPTY', 'message' => '', 'data' => 0);
        //$this->ci->load->model('Account_model');
        $where = array('user_name' => "'" . $arrUserData['user_name'] . "'");
        $aUser = $this->ci->user_model->get_by_where($where);
        //echo $this->ci->user_model->db->last_query();exit();
        if (!empty($aUser)) {
            $arrReturn = array('code' => 'USER_NAME_EXIST', 'message' => '用户名已存在', 'data' => $aUser['user_id']);
            return $arrReturn;
        }
        if (!empty($arrUserData['from_invite_code'])) {
            $invite_info = $this->getUserinfoByInviteCode($arrUserData['from_invite_code']);
            if (!empty($invite_info['data'])) {
                $invite_info = $invite_info['data'];
            }
        }

        $data = array(
            'user_name' => $arrUserData['user_name'],
            'platform' => $arrUserData['platform_id'],
            'invite_code' => $this->randstr{rand(0, 70)} . $this->randstr{rand(0, 70)} . $this->randstr{rand(0, 70)} . $this->randstr{rand(0, 70)} . $this->randstr{rand(0, 70)} . $this->randstr{rand(0, 70)},
            'from_invite_code' => !empty($arrUserData['from_invite_code']) ?$arrUserData['from_invite_code']: '',
            'password' => md5($arrUserData['pwd']),
            'invite_id' => !empty($invite_info) && !empty($invite_info['user_id']) ? $invite_info['user_id'] : '',
            'status' => 1,
            'authentication_status'=>0,
            'createtime' => time(),
        );//'paypsw' => md5('123456'),
        $user_id = $this->ci->user_model->insert_string($data);
        if (!empty($user_id)) {
            $rongytoken = $this->getUserRongyToken($user_id,$arrUserData['user_name']);
            $arrReturn = array('code' => 'SUCCESS', 'message' => '注册成功', 'data' => $user_id);
            return $arrReturn;
        } else {
            $arrReturn = array('code' => 'FAILED', 'message' => '注册失败', 'data' => 0);
            return $arrReturn;
        }
    }

    //请求融云token
    public function getUserRongyToken($userId,$userName){
        $this->ci->load->library('server');
        //请求融云token
        $userName = substr($userName,0,3).'****'.substr($userName,-4);
        $Server = new Server();
        $res = $Server->getToken($userId,$userName,_get_userlogo_url(''));
        $res_Arr = json_decode($res,true);
        if ($res_Arr['code']=='200') {
            $dat['rongytoken'] = $res_Arr['token'];
            $update_res = $this->ci->user_model->update_by_id($userId,$dat);//更新融云token到数据库
            $returnArr = array('user_id'=>$userId,'rongy_token'=>$res_Arr['token']);
        }else{
            $returnArr = array('user_id'=>$userId,'rongy_token'=>'');
        }
        return $returnArr;
    }

    /**
     * @param 根据token获取用户信息
     */
    public function get_userid($token)
    {
        $arrRes = array('data' => '', 'code' => '', 'msg' => '');
        $a['token'] = "'" . $token . "'";
        $loginUser = $this->ci->User_token_model->get_by_where($a);
        if (empty($loginUser)) {
            return array();
        } else {
            return $loginUser;
        }
    }

    //根据用户InviteCode获取用户信息
    public function getUserinfoByInviteCode($inviteCode)
    {
        if (empty($inviteCode)) {
            $returnArr = array('code' => 0, 'msg' => '用户ID不能为空');
        } else {
            $userInfo = $this->ci->user_model->get_by_where(array('invite_code' => '"' . $inviteCode . '"', 'status' => 1), 'user_id,invite_code');
            if (empty($userInfo)) {
                $returnArr = array('code' => 0, 'msg' => '用户信息不存在');
            } else {
                $returnArr = array('code' => 1, 'msg' => '获取成功', 'data' => $userInfo);
            }
        }
        return $returnArr;
    }

    //根据用户ID获取用户对应的余额(maoweihua)
    public function getUserBalance($user_id)
    {
        if (empty($user_id)) {
            $returnArr = array('code' => 0, 'msg' => '用户ID不能为空');
        } else {
            $userInfo = $this->ci->user_model->get_by_where(array('id' => $user_id, 'status' => 1), 'balance');
            if (empty($userInfo)) {
                $returnArr = array('code' => 0, 'msg' => '用户信息不存在');
            } else {
                $returnArr = array('code' => 1, 'msg' => '获取成功', 'data' => $userInfo);
            }
        }
        return $returnArr;
    }

    //根据用户ID获取用户中心信息
    public function getPersonalCenterInfo($user_id)
    {
        $userInfo = array();
        if (empty($user_id)) {
            return $userInfo;
        } else {
            $userInfo = $this->ci->user_model->get_by_where(array('user_id' => $user_id, 'status' => 1), 'balance, authentication_status, work');
            $work = $userInfo['work'];//职业：-1，未知；0，学生；1，白领
            if($work == 0) {
                $arrWhere = array('user_id' => $user_id);
                $studentInfo = $this->ci->student_model->get_by_where($arrWhere, 'is_new_student');
                $userInfo['is_new_student'] = $studentInfo['is_new_student'];
            }
        }
        return $userInfo;
    }

    //根据用户ID获取用户中心信息
    public function getUserRealName($user_id)
    {
        $userInfo = array();
        if (empty($user_id)) {
            return $userInfo;
        } else {
            $data = $this->ci->user_model->get_by_where(array('user_id' => $user_id, 'status' => 1), 'work, authentication_status');

            $authStautus = $data['authentication_status'];//认证状态 0，未认证；1，已认证
            if ($authStautus == 1)
            {
                $work = $data['work'];//职业：-1，未知；0，学生；1，白领
                if($work == 0)
                {
                    $arrWhere = array('user_id' => $user_id);
                    $studentInfo = $this->ci->student_model->get_by_where($arrWhere, 'user_name');
                    $userInfo['real_name'] = $studentInfo['user_name'];
                }
                else if($work == 1)
                {
                    $arrWhere = array('user_id' => $user_id);
                    $userRealName = $this->ci->employee_model->get_by_where($arrWhere, 'user_name');
                    $userInfo['real_name'] = $userRealName['user_name'];
                }
            }


        }
        return $userInfo;
    }


    public function get_user_status($user_type, $user_id){
        if (empty($user_id) || empty($user_type)) {
            $returnArr = array('code' => 0, 'msg' => '用户ID或者用户类型不能为空');
        } else {
            $this->ci->load->model('user_model');
            $receiverInfo = $this->ci->user_model->get_by_id($user_id);
            if (empty($receiverInfo)) {
                $returnArr = array('code' => 0, 'msg' => '用户不存在');
            } else {
                if (empty($receiverInfo['card_id']) || empty($receiverInfo['card_photo_front']) || empty($receiverInfo['card_photo_opposite'])) {
                    $returnArr = array('code' => 0, 'msg' => '用户未完善信息');
                } else {
                    if (intval($user_type) == 2) {//医生
                        $this->ci->load->model('doctor_model');
                        $doctorInfo = $this->ci->doctor_model->get_by_where(array('user_id' => $user_id), 'id,user_id,status');//, 'status' => 1
                        if (!empty($doctorInfo)) {
                            if ($doctorInfo['status']==0) {
                                $returnArr = array('code' => 1, 'msg' => '用户未审核');
                            }elseif($doctorInfo['status']==1){
                                $returnArr = array('code' => 2, 'msg' => '已通过审核', 'data' => $doctorInfo);
                            }else{
                                $returnArr = array('code' => 3, 'msg' => '未通过审核', 'data' => $doctorInfo);
                            }
                        }else{
                            $returnArr = array('code' => 1, 'msg' => '用户未审核通过');
                        }
                    } elseif (intval($user_type) == 3) {//医院
                        $hospitalInfo = $this->ci->hospital_model->get_by_where(array('user_id' => $user_id), 'id,user_id,status');//, 'status' => 1
                        if (!empty($hospitalInfo)) {
                            if ($hospitalInfo['status']==0) {
                                $returnArr = array('code' => 1, 'msg' => '用户未审核');
                            }elseif($hospitalInfo['status']==1){
                                $returnArr = array('code' => 2, 'msg' => '已通过审核', 'data' => $hospitalInfo);
                            }else{
                                $returnArr = array('code' => 3, 'msg' => '未通过审核', 'data' => $hospitalInfo);
                            }
                        }else{
                            $returnArr = array('code' => 1, 'msg' => '用户未审核通过');
                        }
                    }
                }
            }
        }
        return $returnArr;
    }

    //根据地址获取经纬度
    public function convertAddress($address){
        $url = "http://api.map.baidu.com/geocoder/v2/?address=$address&output=json&ak=MU3NreHexTG9wvfCv0zjddLeEIbzLPCX";
        $res = http_get_data($url);
        if (!empty($res['result']['location'])) {
            $result = array('status'=>1,'info'=>$res['result']['location']);
        }else{
            $result = array('status'=>0,'info'=>"");
        }
        return $result;
    }

}