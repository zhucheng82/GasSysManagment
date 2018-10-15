<?php
/**
 * Created by PhpStorm.
 * User: zhucheng
 * Date: 16/9/18
 * Time: 下午1:14
 */

class ZhimaAuth
{
    //芝麻信用网关地址
    public $gatewayUrl = "https://zmopenapi.zmxy.com.cn/openapi.do";
    //商户公钥文件
    //芝麻公钥文件
    public $privateKeyFile = "../application/libraries/zmxy-sdk/rsa_private_key.pem";
    public $zmPublicKeyFile = "../application/libraries/zmxy-sdk/rsa_public_key.pem";

    //数据编码格式
    public $charset = "UTF-8";
    //芝麻分配给商户的appId
    public $appId = "1000003";

    public function __construct()
    {
        //parent::__construct();

        $this->ci = &get_instance();

        //$gatewayUrl = '', $appId = '', $charset = 'UTF-8', $privateKeyFilePath, $zhiMaPublicKeyFilePath
        $config = array('gatewayUrl'=>$this->gatewayUrl, 'appId'=>$this->appId, 'charset'=>$this->charset, 'privateKeyFilePath'=>$this->privateKeyFile, 'zhiMaPublicKeyFilePath'=>$this->zmPublicKeyFile);
        $this->ci->load->library('zmxy-sdk/zmop/ZmopClient', $config,'ZmopClient');

        $this->ci->load->library('zmxy-sdk/zmop/request/ZhimaAuthInfoAuthorizeRequest', '', 'ZhimaAuthInfoAuthorizeRequest');

    }

    //生成移动端SDK 集成需要的sign 参数 ，并进行urlEncode
    public function generateSign($certNo, $name, $certType = 'IDENTITY_CARD')
    {


        $this->ci->ZhimaAuthInfoAuthorizeRequest->setScene("test");
        // 授权来源渠道设置为appsdk
        $this->ci->ZhimaAuthInfoAuthorizeRequest->setChannel("appsdk");
        // 授权类型设置为2标识为证件号授权见“章节4中的业务入参说明identity_type”
        $this->ci->ZhimaAuthInfoAuthorizeRequest->setIdentityType("2");
        // 构造授权业务入参证件号，姓名，证件类型;“章节4中的业务入参说明identity_param”
        $this->ci->ZhimaAuthInfoAuthorizeRequest->setIdentityParam("{\"certNo\":\"$certNo\",\"certType\":\"IDENTITY_CARD\", \"name\":\"$name\"}");
        // 构造业务入参扩展参数“章节4中的业务入参说明biz_params”
        $this->ci->ZhimaAuthInfoAuthorizeRequest->setBizParams("{\"auth_code\":\"M_APPSDK\"}");

        $params = $this->ci->ZmopClient->generateEncryptedParamWithUrlEncode($this->ci->ZhimaAuthInfoAuthorizeRequest);
        $sign = $this->ci->ZmopClient->generateSignWithUrlEncode($this->ci->ZhimaAuthInfoAuthorizeRequest);

        $data['gatewayUrl'] = $this->gatewayUrl;
        $data['appId'] = $this->appId;
        $data['charset'] = $this->charset;
        $data['params'] = $params;
        $data['sign'] = $sign;
        return $data;
    }


    // 解密
    public function zhimacallback($params)
    {
        //$this->privateKeyFile = "path/rsa_private_key.pem";
        //$client = new ZmopClient($this->gatewayUrl, $this->appId, $this->charset, $this->privateKeyFile, $this->zmPublicKeyFile);
        $result = $this->ZmopClient->generateSignCallBack($params, $this->privateKeyFile);
        return $result;
    }

}

class Authorization extends TokenApiController {
    public $user_id;
    protected $mPkId = 'user_id';
    public function __construct()
    {
        parent::__construct();
        /*
        $this->load->service('sms_service');
        $this->load->service('user_service');
        $this->load->model('User_pwd_model');
        $this->load->model('User_token_model');
        */
        $this->load->model('User_model');
        $this->load->model('student_model');
        $this->load->model('employee_model');
        $this->load->model('additional_user_info_model');
        $this->load->model('id_extract_info_model');
        $this->load->model('Company_nature_model');
        $this->load->model('Company_position_model');
        $this->user_id = $this->loginUser['user_id'];

    }

    public function auth()
    {
        $token = $this->input->post('token');
        $type = $this->input->post('type');
        $user_id = $this->loginUser['user_id'];
        $real_name = $this->input->post('real_name');
        $identity_card = $this->input->post('identity_card');

        if (!isset($type) || !isset($user_id) || !isset($real_name) || !isset($identity_card))
        {
            output_error(-1,'参数不能为空');exit;
        }
        if(strlen($identity_card) < 15)
        {
            output_error(-1,'身份证长度不合法');exit;
        }

        $provinceCode = (int)substr($identity_card, 0, 2)*10000;
        $cityCode = (int)substr($identity_card, 0, 4)*100;
        $districtCode = substr($identity_card, 0, 6);
        $birthday = substr($identity_card, 6, 8);
        $gender = 0;
        $genderInfo = -1;
        if(strlen($identity_card) == 15)
        {
            $genderInfo = substr($identity_card, 14, 1);
        }
        else if(strlen($identity_card) == 18)
        {
            $genderInfo = substr($identity_card, 16, 1);
        }
        if ($genderInfo > -1)
        {
            if ($genderInfo % 2 == 0)//基数为男（1），偶数为女（2）
            {
                $gender = 2;
            }
            else
            {
                $gender = 1;
            }
        }

        $iBirthday = 0;
        if (!empty($birthday))
        {
            $iBirthday = strtotime($birthday);
        }

        $IDExtractData = array('user_id'=>$user_id, 'province_code'=>$provinceCode, 'city_code'=>$cityCode,
            'district_code'=>$districtCode, 'birthday'=>$iBirthday, 'gender'=>$gender,'work'=>$type);

        if($type == 0)
        {
            $school = $this->input->post('school');
            $education = $this->input->post('education');
            $entry_time = $this->input->post('entry_time');

            $examNumbers = $this->input->post('exam_registration_numbers');
            $reqAccount = $this->input->post('request_account');
            $reqPwd = $this->input->post('request_password');
            $reqUrl = $this->input->post('request_url');

            //$enrollPhoto = $this->input->post('enroll_photo_url');
            //$entrancePhoto = $this->input->post('entrance_photo_url');

            $isNewStudent = 0;
            // 获取今天的日期，格式为 YYYY-MM-DD
            $year = date('Y');
            // 使用IF当作字符串判断是否相等
            if($entry_time==$year){
                $isNewStudent = 1;
            }

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
                'is_new_student'=>$isNewStudent
                //'photo1'=>$enrollPhoto,
                //'photo2'=>$entrancePhoto
            );

            $data = array('studentData'=> $studentData);
            //output_data($data);exit;

            //var_dump($this->student_model->insert_string($studentData));exit;

            $this->student_model->db->trans_strict(FALSE);
            $this->student_model->db->trans_begin();
            $result = $this->student_model->insert($studentData);
            //$authType = array('work'=>$type,'authentication_status'=>1);
            $authType = array('work'=>$type,'authentication_status'=>2);
            $this->User_model->update_by_id($user_id, $authType);
            $this->id_extract_info_model->insert($IDExtractData);
            //$this->student_model->db->trans_complete();
            if ($this->student_model->db->trans_status() === FALSE)
            {

                $this->student_model->db->trans_rollback();
                //echo $this->student_model->db->last_query();
                output_error(-1,'认证失败');exit;

            }
            else
            {
                $this->student_model->db->trans_commit();

                $data = array('real_name'=>$real_name);
                output_data($data);exit;
            }

        }
        else
        {
            $company_name = $this->input->post('company_name');
            $company_type = $this->input->post('company_type');
            $post = $this->input->post('post');//职位
            $entry_time = $this->input->post('entry_time');

            $employeeData = array(
                'user_id' => $user_id,
                'user_name' => $real_name,
                'user_identify_card'=>$identity_card,
                'company_name'=>$company_name,
                'company_type'=>$company_type,
                'post'=>$post,
                'entry_time'=>$entry_time
            );

            $this->employee_model->db->trans_strict(FALSE);
            $this->employee_model->db->trans_start();
            $this->employee_model->insert($employeeData);
            $authType = array('work'=>$type,'authentication_status'=>2);
            $this->User_model->update_by_id($user_id, $authType);
            $this->id_extract_info_model->insert($IDExtractData);
            $this->employee_model->db->trans_complete();

            if ($this->employee_model->db->trans_status())
            {
                $data = array('real_name'=>$real_name);
                output_data($data);exit;
            }
            else
            {
                output_error(-1,'认证失败');exit;
            }
        }
    }

    public function addIDCardInfo()
    {
        $bankName = $this->input->post('bank_name');
        $bankCardId = $this->input->post('bank_card_id');
        $phone = $this->input->post('phone_num');
        $smsCode = $this->input->post('sms_code');
    }

    //弃用，完善资料用userinfo.php中的相关接口
    public function completeUserInfo()
    {
        $linkmanName1 = $this->input->post('linkman_name1');
        $linkmanTel1 = $this->input->post('linkman_tel1');
        $linkmanName2 = $this->input->post('linkman_name2');
        $linkmanTel2 = $this->input->post('linkman_tel2');
        $linkmanName3 = $this->input->post('linkman_name3');
        $linkmanTel3 = $this->input->post('linkman_tel3');
        $linkmanName4 = $this->input->post('linkman_name4');
        $linkmanTel4 = $this->input->post('linkman_tel4');

        $bankName = $this->input->post('bank_name');
        $bankCardId = $this->input->post('bank_card_id');
        $photoUrl = $this->input->post('id_photo_url');

        $provinceId = $this->input->post('province_id');
        $provinceName = $this->input->post('province_name');
        $cityId = $this->input->post('city_id');
        $cityName = $this->input->post('city_name');
        $districtId = $this->input->post('district_id');
        $districtName = $this->input->post('district_name');
        $addr = $this->input->post('addr');

        $zhima = $this->input->post('zhima');

        $additionalInfo = array('user_id'=>$this->user_id,
            'linkman_name1'=>$linkmanName1
        , 'linkman_tel1'=>$linkmanTel1
        , 'linkman_name2'=>$linkmanName2
        , 'linkman_tel2'=>$linkmanTel2
        , 'linkman_name3'=>$linkmanName3
        , 'linkman_tel3'=>$linkmanTel3
        , 'linkman_name4'=>$linkmanName4
        , 'linkman_tel4'=>$linkmanTel4
        , 'bank_name'=>$bankName
        , 'bank_card_id'=>$bankCardId
        , 'id_photo_url'=>$photoUrl
        , 'province_id'=>$provinceId
        , 'province_name'=>$provinceName
        , 'city_id'=>$cityId
        , 'city_name'=>$cityName
        , 'district_id'=>$districtId
        , 'district_name'=>$districtName
        , 'address'=>$addr
        , 'zhima'=>$zhima
        );

        $this->additional_user_info_model->db->trans_strict(FALSE);
        $this->additional_user_info_model->db->trans_start();
        $this->additional_user_info_model->insert($additionalInfo);
        $authType = array('additional_info_status'=>1);
        $this->User_model->update_by_id($this->user_id, $authType);
        $this->additional_user_info_model->db->trans_complete();

        if ($this->additional_user_info_model->db->trans_status())
        {
            output_data();exit;
        }
        else
        {
            output_error(-1,'完善资料失败');exit;
        }
    }

    public function getZhimaSignAndParams()
    {
        $IdNumber = $this->input->post('id_number');
        $name = $this->input->post('name');
        $zhima = new ZhimaAuth();
        $data = $zhima->generateSign($IdNumber, $name);

        if(!empty($data))
        {
            output_data($data);
        }
        else
        {
            output_error(-1, '获取芝麻信用sign参数失败');
        }
    }

    public function getComplayTypeAndPositionInfo()
    {
        $order = 'sort DESC';
        $arrCompanyNatureInfo = $this->Company_nature_model->get_list(array(), 'company_nature',$order);
        $arrCompanyNature = array();
        if(!empty($arrCompanyNatureInfo))
        {
            foreach($arrCompanyNatureInfo as $companyNature )
            {
                $arrCompanyNature[] = $companyNature['company_nature'];
            }

        }
        else
        {
            $arrCompanyNature = array_values($this->config->item("company_nature"));
        }

        $arrCompanyPositionInfo = $this->Company_position_model->get_list(array(), 'company_position',$order);
        $arrCompanyPosition = array();
        if(!empty($arrCompanyPositionInfo))
        {
            foreach($arrCompanyPositionInfo as $companyPosition )
            {
                $arrCompanyPosition[] = $companyPosition['company_position'];
            }
        }
        else
        {
            $arrCompanyPosition = array_values($this->config->item("company_position"));
        }
        $data = array('company_nature'=>$arrCompanyNature, 'company_position'=>$arrCompanyPosition);
        output_data($data);
    }

    public function getEducationList()
    {
        $arrRes['education_list'] = array_values($this->config->item("Education"));
        output_data($arrRes);exit;
    }

    public function getUserAuthInfo()
    {
        $work = $this->loginUser['work'];
        if($work == 0)
        {
            $where = array('user_id'=>$this->user_id);
            $data = $this->student_model->get_by_where($where);
            output_data($data);exit;
        }
        else
        {
            $where = array('user_id'=>$this->user_id);
            $data = $this->employee_model->get_by_where($where);
            output_data($data);exit;
        }
    }

}