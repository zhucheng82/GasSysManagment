<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends ApiController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->service('user_service');
    }
    
/**
     * @param 用户登录
     *
     * @param $_POST['user_name']
     * @param $_POST['pwd']
     * @param $_POST['client_type']
     *
     * @param $_POST['uin']
     * @param $_POST['a1']
     * @param $_POST['client_type']
     * @param $POST['platform_id']
     *
     * @return
     * $arrRes = array{
     *      "data":{"user_id":"40","user_name":"user21","name":"sdfuhiu","mobile":"iredhgerr","mobile_verify":null,"logo":"5.png",
     *           	"sign":null,"sex":"1","reg_time":"1458005923","reg_ip":"127.0.0.1","update_time":"1458005923","status":"2","platform_id":"1"
     *           	},
     *       "code":"USER_LOGIN_SUCCESS",
     *       "msg":"\u767b\u5f55\u6210\u529f"
     *       }
     */
    public function login()
    {
        $user_account = $this->input->post('user_account');
        $pwd_md5 = $this->input->post('pwd_md5');
        $client_type = $this->input->post('client_type');
        $platform_id = $this->input->post('platform_id');
        $arrRes['action'] = 'user_login';

        $config = array(
            array(
                'field'=>'user_account',
                'label'=>'user_account',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'pwd_md5',
                'label'=>'pwd_md5',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'client_type',
                'label'=>'client_type',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'platform_id',
                'label'=>'platform_id',
                'rules'=>'trim|required',
            ),
        );
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() === TRUE)
        {
            $arrUserData['user_account'] = $user_account;
            $arrUserData['pwd_md5'] = $pwd_md5;
            $arrUserData['client_type'] = $client_type;
            $arrRes = $this->user_service->login($arrUserData);
            /*
            if(!empty($arrRes['logo']))
                $arrRes['logo'] = BASE_SITE_URL.'/'.$arrRes['logo'];
            else
                $arrRes['logo'] = '';
            */
            $where = array('user_id'=>$arrRes['user_id']);
            $this->load->model('User_bank_card_info');
            $bankList = $this->User_bank_card_info->get_list($where);
            if (empty($bankList))
            {
                $arrRes['band_bankcard_flag'] = 0;
            }
            else
            {
                $arrRes['band_bankcard_flag'] = 1;
            }

            output_data($arrRes);exit;
            //echo json_encode($arrRes);exit();
        }
        else
        {
            if (empty($user_account))
            {
                //output_error('-1','USER_USERNAME_NULL');exit;
                output_error('-1','用户名为空');exit;
            }
            if (empty($pwd_md5))
            {
                //output_error('-1','USER_A1_NULL');exit;
                output_error('-1','密码为空');exit;
            }
            if (empty($client_type))
            {
                //output_error('-1','USER_CLIENT_TYPE_NULL');exit;
                output_error('-1','客户端类型为空');exit;
            }
            if (empty($platform_id))
            {
                //output_error('-1','USER_PLATFORM_ID_NULL');exit;
                output_error('-1','平台ID为空');exit;
            }
        }
    }

    
    /**
     * 获取授权用户信息
     */
    public function get_info()
    {
        $this->load->model('User_model');
        $this->load->model('User_auth_model');
        $this->load->model('User_token_model');
        $unionid = $this->input->post('unionid');
        $client_type = $this->input->post('client_type');
        $platform_id = $this->input->post('platform_id');
        $where['unionid'] = "'$unionid'";
        $info = $this->User_auth_model->get_by_where($where);
        unset($where);
        $where['user_id'] = $info['user_id'];
        $userInfo = $this->User_model->get_by_where($where);
        if (empty($userInfo))
        {
            //output_error(-1,'USER_NOT_EXIST');exit;
            output_error('-1','用户不存在');exit;
        }
        $where['status <>'] = -1;
        $val['status'] = -2 ;
        $this->User_token_model->update_by_where($where,$val);
        $tokenData = array(
            'user_id' => $userInfo['user_id'],
            'user_name' => $userInfo['user_name'],
            'token' => md5(time().mt_rand(0,1000)),
            'refresh_token' => md5(time().mt_rand(1000,2000)),
            'addtime' => time(),
            'expire_time' => time()+86400*7,
            'client_type' => $client_type,
        );
        $userInfo['token'] = $tokenData['token'];
        $userInfo['refresh_token'] = $tokenData['refresh_token'];
        $this->User_token_model->insert($tokenData);
        $userInfo['logo'] = BASE_SITE_URL.'/'.$userInfo['logo'];
        output_data($userInfo);
    }
}
