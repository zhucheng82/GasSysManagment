<?php

class Reg extends ApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->service('sms_service');
        $this->load->service('user_service');
        $this->load->model('User_pwd_model');
        $this->load->model('User_model');
        $this->load->model('User_token_model');
        $this->load->model('student_model');
        $this->load->model('employee_model');

    }

    /**
     * @param 用户注册
     *
     * @param $_POST ['uin']
     * @param $_POST ['code']
     * @param $_POST ['pwd']
     * @param $_POST ['name']
     * @param $_POST ['platform_id']
     *
     * @return{
     *              "data":"",
     *              "code":"SUCCESS",
     *              "msg":"\u6ce8\u518c\u6210\u529f",
     *               }
     */

    public function reg()
    {
        $uin = $this->input->post('mobile');
        $pwd = $this->input->post('pwd');
        $code = $this->input->post('code');
        $client_type = $this->input->post('client_type');
        $platform_id = $this->input->post('platform_id');
        $from_invite = $this->input->post('from_invite_code');//邀请码

        $user_name = $uin;
        $arrRes['action'] = 'user_reg';
        $config = array(
            array(
                'field'=>'mobile',
                'label'=>'手机号码',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'pwd',
                'label'=>'密码',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'code',
                'label'=>'验证码',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'platform_id',
                'label'=>'平台id',
                'rules'=>'trim|required',
            ),

        );
        $this->form_validation->set_rules($config);
    
        if($this->form_validation->run() === TRUE)
        {

            //1:Resgister 2:ForgetPassword 3:BankCert 4:ModifyUserName 5:TiedUserName 6:DeliverResgister
            //验证码验证
            $check_code = $this->sms_service->check_code($uin, $code, 1, $platform_id);
            if (!$check_code)
            {
                //echo json_encode($check_code);exit();
                output_error('-1','验证码错误');exit;
            }
            $userData['mobile'] = $uin;
            $userData['pwd'] = $pwd;
            $userData['user_name'] = $user_name;
            $userData['platform_id'] = $platform_id;
            $userData['from_invite_code']   = $from_invite;
            $arrRes = $this->user_service->reg($userData);
            if ($arrRes['code'] == 'SUCCESS')
            {

                $tokenData = array(
                    'user_id' => $arrRes['data']['user_id'],
                    'user_name' => $arrRes['data']['user_name'],
                    'token' => md5(time().mt_rand(0,1000)),
                    'refresh_token' => md5(time().mt_rand(1000,2000)),
                    'addtime' => time(),
                    'expire_time' => time()+86400*7,
                    'client_type' => $client_type,
                );
                if ($this->User_token_model->insert_string($tokenData))
                {
                    $arrRes['data']['token'] = $tokenData['token'];
                    $arrRes['data']['refresh_token'] = $tokenData['refresh_token'];
                    output_data($arrRes['data']);exit;
                }
                else
                {
                    output_error(-1,'用户自动登录错误');exit;//USER_AUTO_LOGIN_ERROR
                }
            }
            else
            {
                output_error(-1,$arrRes['message']);exit;
            }
        }
        else
        {
            if (empty($uin))
            {
                output_error(-1,'手机号不能为空');exit;    //USER_PHONE_NULL
            }
            if (empty($pwd))
            {
                output_error(-1,'密码不能为空');exit;  //USER_PWD_NULL
            }
            if (empty($platform_id)) {
                output_error(-1, '平台id不能为空');
                exit;   //PLATFORM_ID_NULL
            }
        }
    }

    public function authorization()
    {
        $token = $this->input->post('token');
        $type = $this->input->post('type');
        $user_id = $this->loginUser['user_id'];
        $real_name = $this->input->post('real_name');
        $identity_card = $this->input->post('identity_card');

        //需要先判断用户是否存在

        if($type == 0)
        {
            $school = $this->input->post('school');
            $education = $this->input->post('education');
            $entry_time = $this->input->post('entry_time');

            $examNumbers = $this->input->post('exam_registration_numbers');
            $reqAccount = $this->input->post('request_account');
            $reqPwd = $this->input->post('request_password');
            $reqUrl = $this->input->post('request_url');

            $enrollPhoto = $this->input->post('enroll_photo_url');
            $entrancePhoto = $this->input->post('entrance_photo_url');

            $studentData = array(
                'user_id' => $user_id,
                'user_name' => $real_name,
                'user_identify_card'=>$identity_card,
                'school_name'=>$school,
                'education'=>$education,
                'entry_time'=>$entry_time,

                'exam_registration_numbers'=>$examNumbers,
                'request_account'=>$reqAccount,
                'request_password'=>$reqPwd,
                'request_url'=>$reqUrl,
                'photo1'=>$enrollPhoto,
                'photo2'=>$entrancePhoto,
            );
            if ($this->Student_model->insert_string($studentData))
            {
                output_data(1,"succeed");exit;
            }
            else
            {
                output_error(-1,'认证失败');exit;
            }
        }
        else
        {
            $company_name = $this->input->post('company_name');
            $company_type = $this->input->post('company_type');
            $post = $this->input->post('post');//职位

            $employeeData = array(
                'user_id' => $user_id,
                'user_name' => $real_name,
                'user_identify_card'=>$identity_card,
                'company_name'=>$company_name,
                'company_type'=>$company_type,
                'post'=>$post,
            );
            if ($this->Employee_model->insert_string($employeeData))
            {
                output_data(1,"succeed");exit;
            }
            else
            {
                output_error(-1,'认证失败');exit;
            }
        }



    }

    /**
     * @param 忘记密码
     *
     * @param $_POST['code']
     * @param $_POST['mobile']
     * @param $_POST['pwd']
     * @param $_POST['platform_id']
     * @param
     *
     * @return {"data":"","code":"USER_PWD_UPDATED","msg":"\u5bc6\u7801\u4fee\u6539\u6210\u529f"}
     */
    public function newpwd()
    {
        $user_name = $this->input->post('mobile');
        $code = $this->input->post('code');
        $pwd = $this->input->post('pwd');
        $platform_id = $this->input->post('platform_id');
    
        $config = array(
            array(
                'field'=>'mobile',
                'label'=>'手机号',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'code',
                'label'=>'验证码',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'pwd',
                'label'=>'pwd',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'platform_id',
                'label'=>'platform_id',
                'rules'=>'trim|required',
            ),
        );
        $this->form_validation->set_rules($config);
        if($this->form_validation->run() === TRUE)
        {
            /*$arrData = array(
                'mobile' => $user_name,
                'code' => $code,
                'type_id' => 2,
                'platform_id' =>$platform_id,
            );*/

            $check_code = $this->sms_service->check_code($user_name, $code, 2, $platform_id);
            if (!$check_code)
            {
                $result = array('data'=>null,
                    'code'=>-1,
                    'msg'=>'验证码错误',
                    'action' =>'sms_check_code',
                );
                echo json_encode($result);exit();
            }
            $where['user_name'] = "'".$user_name."'";
            $userInfo = $this->User_model->get_by_where($where);
            if (empty($userInfo))
            {
                output_error('-1','用户不存在');exit;   //USER_NOT_EXIST
            }
            if (md5($pwd) == $userInfo['password'])
            {
                output_error('-1','新旧密码不能相同');exit; //USER_PWD_NO_SAME
            }
            if (preg_match("/^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,20}$/",$pwd))
            {
                $data['password'] = md5($pwd);
                $where2['user_name'] = $user_name;
                if ($this->User_model->update_by_where($where2,$data))
                {
                    output_data();
                }
                else
                {
                    output_error('-1','FAILED');exit;
                }
            }
            else
            {
                output_error('-1','密码格式错误');exit;    //USER_PWD_FORMAT_ERROR
            }
        }
        else
        {
            if (empty($user_name))
            {
                output_error('-1','手机号不能为空');exit;
            }
            if (empty($pwd))
            {
                output_error('-1','密码不能为空');exit;
            }
            if (empty($code))
            {
                output_error('-1','验证码不能为空');exit;
            }
            if (empty($latform_id))
            {
                output_error('-1','平台id不能为空');exit;
            }
        }
    }
}