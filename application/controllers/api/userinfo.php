<?php
/**
 * Created by PhpStorm.
 * User: zhucheng
 * Date: 16/10/26
 * Time: 下午1:34
 */

class Userinfo extends TokenApiController
{
    public $user_id;
    public $user_name;
    private $userInfo;
    public function __construct()
    {
        parent::__construct();
        $this->load->service('user_service');
        $this->price_type = $this->config->item('price_type');
        $this->userInfo = $this->loginUser;
        $this->user_id = $this->loginUser['user_id'];
        $this->user_name = $this->loginUser['user_name'];
        $this->load->model('additional_user_info_model');
        $this->load->model('feedback_model');
        //$this->load->library('verity/Verity');
    }

    public function getQRCode()
    {
        $inviteCode = $this->userInfo['invite_code'];
        $this->load->service('tools_service');
        //$qrcodeUrl = $this->tools_service->createQrcode($aFundOrder['order_id'],$aFundOrder['order_type'],$urlOrPage['code_url'],$aFundOrder['code_url']);

        $url = BASE_SITE_URL.'/help/share.html';
        if (!empty($inviteCode))
        {
            $url = $url.'?twid='.$inviteCode;
        }

        log_message('debug', 'getQRCode');
        log_message('debug', $url);

        $qrcodeUrl = $this->tools_service->createUserQrcode($inviteCode,1,$url,$logo='');
        $result['QrCode_url'] = $qrcodeUrl;
        output_data($result);
    }

    /*
    //用户余额
    public function balance()
    {
        $this->load->model("Withdraw_account_model");
        $balanceInfo = $this->user_service->getUserBalance($this->user_id);
        $count = $this->Withdraw_account_model->count(array('user_id'=>$this->user_id));
        if($count > 0){
            $balanceInfo['data']['is_set_account'] = "1";
        }else{
            $balanceInfo['data']['is_set_account'] = "0";
        }
        if ($balanceInfo['code'] != 1) {
            output_error(-1, $balanceInfo['msg']);
        } else {
            output_data($balanceInfo['data']);
        }
    }
    */

    //我的邀请
    public function myinvite()
    {
        $inviteCode  = $this->loginUser['invite_code'];
        $page = $this->input->get_post('page');
        $pagesize = $this->input->get_post('pagesize');
        if (empty($page)) {
            $page = 1;
        }
        if (empty($pagesize)) {
            $pagesize = 20;
        }
        $where = array('a.from_invite_code' => $inviteCode, 'a.status' => 1);
        $result = array(
            'page' => $page,
            'pagesize' => $pagesize,
            'invite_Code' => $inviteCode
        );

        $where = array('from_invite_code' => $inviteCode, 'status' => 1);
        $res = $this->user_model->fetch_page($page, $pagesize, $where, 'id,user_name,createtime');
        $result['lists'] = $res;
        output_data($result);
    }

    //判断用户是否设置支付密码
    public function checkPaypsw(){
        if ($this->input->is_post()){
            if (!empty($this->userInfo['paypsw'])) {
                output_data(array('paypsw_status'=>1));
            }else{
                output_data(array('paypsw_status'=>0));
            }
        }
    }

    public function completeLinkmanInfo()
    {
        $linkmanName1 = $this->input->post('linkman_name1');
        $linkmanTel1 = $this->input->post('linkman_tel1');
        $linkmanName2 = $this->input->post('linkman_name2');
        $linkmanTel2 = $this->input->post('linkman_tel2');
        $linkmanName3 = $this->input->post('linkman_name3');
        $linkmanTel3 = $this->input->post('linkman_tel3');
        $linkmanName4 = $this->input->post('linkman_name4');
        $linkmanTel4 = $this->input->post('linkman_tel4');
        $companyTel = $this->input->post('company_tel');
        $additionalInfo = array('user_id'=>$this->user_id,
            'linkman_name1'=>$linkmanName1
        , 'linkman_tel1'=>$linkmanTel1
        , 'linkman_name2'=>$linkmanName2
        , 'linkman_tel2'=>$linkmanTel2
        , 'linkman_name3'=>$linkmanName3
        , 'linkman_tel3'=>$linkmanTel3
        , 'linkman_name4'=>$linkmanName4
        , 'linkman_tel4'=>$linkmanTel4
        , 'company_tel'=>$companyTel
        );

        if ($this->additional_user_info_model->insert($additionalInfo))
        {
            output_data();exit;
        }
        else
        {
            output_error(-1,'完善联系人信息失败');exit;
        }
    }

    public function completeIDPhotoInfo()
    {
        $photoUrl1 = $this->input->post('id_photo1_url');
        $photoUrl2 = $this->input->post('id_photo2_url');
        $photoUrl3 = $this->input->post('id_photo3_url');
        if(!isset($photoUrl1) ||!isset($photoUrl2) || !isset($photoUrl3))
        {
            output_error(-1,'参数不能为空');exit;
        }
        $additionalInfo = array('user_id'=>$this->user_id
        , 'id_photo_url1'=>$photoUrl1
        , 'id_photo_url2'=>$photoUrl2
        , 'id_photo_url3'=>$photoUrl3
        );

        if ($this->additional_user_info_model->insert($additionalInfo))
        {
            output_data();exit;
        }
        else
        {
            output_error(-1,'完善身份证信息失败');exit;
        }
    }

    public function completeAddrInfo()
    {
        $addrTypeFlag = $this->input->post('addr_type');
        $provinceId = $this->input->post('province_id');
        $provinceName = $this->input->post('province_name');
        $cityId = $this->input->post('city_id');
        $cityName = $this->input->post('city_name');
        $districtId = $this->input->post('district_id');
        $districtName = $this->input->post('district_name');
        $addr = $this->input->post('addr');

        if(!isset($provinceId) || !isset($provinceName) || !isset($cityId) || !isset($cityName) || !isset($districtId) || !isset($districtName) || !isset($addr)) {
            output_error(-1,'参数不能为空');exit;
        }

        if(!isset($addrTypeFlag) || $addrTypeFlag == 0)
        {
            $additionalInfo = array('user_id'=>$this->user_id
            , 'province_id'=>$provinceId
            , 'province_name'=>$provinceName
            , 'city_id'=>$cityId
            , 'city_name'=>$cityName
            , 'district_id'=>$districtId
            , 'district_name'=>$districtName
            , 'address'=>$addr
            );
        }
        else
        {
            $additionalInfo = array('user_id'=>$this->user_id
            , 'cur_province_id'=>$provinceId
            , 'cur_province_name'=>$provinceName
            , 'cur_city_id'=>$cityId
            , 'cur_city_name'=>$cityName
            , 'cur_district_id'=>$districtId
            , 'cur_district_name'=>$districtName
            , 'cur_address'=>$addr
            );
        }


        if ($this->additional_user_info_model->insert($additionalInfo))
        {
            output_data();exit;
        }
        else
        {
            output_error(-1,'完善地址信息失败');exit;
        }
    }

    public function completeZhimaInfo()
    {
        $zhima = $this->input->post('zhima');

        $additionalInfo = array('user_id'=>$this->user_id
        , 'zhima'=>$zhima
        );

        if ($this->additional_user_info_model->insert($additionalInfo))
        {
            output_data();exit;
        }
        else
        {
            output_error(-1,'完善芝麻信用信息失败');exit;
        }
    }

    public function finishCompleteInfo()//完善证明照片，同时完成完善资料操作
    {
        $photo1 = $this->input->post('photo_url1');
        $photo2 = $this->input->post('photo_url2');

        $role = $this->userInfo['work'];
        $isNewStudent = 0;
        if($role == 0) {
            $arrWhere = array('user_id' => $this->user_id);
            $this->load->model('student_model');
            $studentInfo = $this->student_model->get_by_where($arrWhere, 'is_new_student');
            $isNewStudent = $studentInfo['is_new_student'];
        }

        //检查是否完善资料
        $this->checkAdditionalInfo($role);

        if (0 == $role && $isNewStudent == 0)//大学生且非新生，不用提交照片，直接判断是否完善所有资料，直接标示
        {
            $this->checkAdditionalInfo($role);

            $authType = array('additional_info_status'=>1);

            if ($this->user_model->update_by_id($this->user_id, $authType))
            {
                output_data();exit;
            }
            else
            {
                $where = array('user_id' => $this->user_id);
                $res = $this->user_model->get_by_where($where, 'additional_info_status');
                if (!empty($res) && $res['additional_info_status'] == 1)
                {
                    output_data();exit;
                }
                log_message('debug','大学生（非新生）完善资料失败');
                output_error(-1,'完善资料失败');exit;
            }
        }
        else
        {
            if (!isset($photo1)) {
                output_error(-1,'请先上传第一张图片');
            }
            if (!isset($photo2)) {
                output_error(-1,'请先上传第二张图片');
            }

            $additionalInfo = array('user_id'=>$this->user_id
            , 'photo_url1'=>$photo1
            , 'photo_url2'=>$photo2
            );

            if (!$this->additional_user_info_model->insert($additionalInfo))
            {
                log_message('debug','保存图片地址失败');
                output_error(-1,'完善资料失败');exit;
            }

            $authType = array('additional_info_status'=>1);

            if ($this->user_model->update_by_id($this->user_id, $authType))
            {
                output_data();exit;
            }
            else
            {
                $where = array('user_id' => $this->user_id);
                $res = $this->user_model->get_by_where($where, 'additional_info_status');
                if (!empty($res) && $res['additional_info_status'] == 1)
                {
                    output_data();exit;
                }

                log_message('debug','完善资料失败');
                output_error(-1,'完善资料失败');exit;
            }
        }
    }

    public function checkAdditionalInfo($role)
    {
        $where = array('user_id' => $this->user_id);
        $additionalInfo = $this->additional_user_info_model->get_by_where($where);

        if (empty($additionalInfo))
        {
            output_error(-1,'请完善资料');exit;
        }
        if(empty($additionalInfo['linkman_name1']) || empty($additionalInfo['linkman_tel1'])
                || empty($additionalInfo['linkman_name2']) || empty($additionalInfo['linkman_tel2'])
                || empty($additionalInfo['linkman_name3']) || empty($additionalInfo['linkman_tel3'])
                || empty($additionalInfo['linkman_name4']) || empty($additionalInfo['linkman_tel4']))
        {
            output_error(-1,'联系人信息未完善');exit;
        }

        if ($role == 1)
        {
            if(empty($additionalInfo['company_tel']))
            {
                output_error(-1,'公司电话信息未完善');exit;
            }
        }

        $this->load->model('User_bank_card_info');
        $bankList = $this->User_bank_card_info->get_list($where);
        if(empty($bankList))
        {
            output_error(-1,'银行卡信息未完善');exit;
        }

        if(empty($additionalInfo['id_photo_url1']) || empty($additionalInfo['id_photo_url1']) || empty($additionalInfo['id_photo_url1']))
        {
            output_error(-1,'身份证信息未完善');exit;
        }
        if(empty($additionalInfo['province_id']) || empty($additionalInfo['province_name'])
                || empty($additionalInfo['city_id']) || empty($additionalInfo['city_name'])
                || empty($additionalInfo['district_id']) || empty($additionalInfo['district_name'])
                || empty($additionalInfo['address']))
        {
            output_error(-1,'家庭住址信息未完善');exit;
        }
        if(empty($additionalInfo['cur_province_id']) || empty($additionalInfo['cur_province_name'])
                || empty($additionalInfo['cur_city_id']) || empty($additionalInfo['cur_city_name'])
                || empty($additionalInfo['cur_district_id']) || empty($additionalInfo['cur_district_name'])
                || empty($additionalInfo['cur_address']))
        {
            output_error(-1,'现居住地址信息未完善');exit;
        }

        if(empty($additionalInfo['zhima']) || $additionalInfo['zhima'] <= 0)
        {
            output_error(-1,'芝麻信用分信息未完善');exit;
        }
    }

    public function getAdditionalInfo()
    {
        $role = $this->userInfo['work'];

        $where = array('user_id' => $this->user_id);
        $additionalInfo = $this->additional_user_info_model->get_by_where($where);

        $data = array('linkman_info_status'=>0, 'bank_card_info_status'=>0, 'id_card_photo_status'=>0, 'home_addr_status'=>0,'cur_addr_status'=>0,'zhima_info_status'=>0);

        if (!empty($additionalInfo))
        {
            if(!empty($additionalInfo['linkman_name1']) && !empty($additionalInfo['linkman_tel1'])
                && !empty($additionalInfo['linkman_name2']) && !empty($additionalInfo['linkman_tel2'])
                && !empty($additionalInfo['linkman_name3']) && !empty($additionalInfo['linkman_tel3'])
                && !empty($additionalInfo['linkman_name4']) && !empty($additionalInfo['linkman_tel4']))
            {
                if ($role == 1)
                {
                    if(!empty($additionalInfo['company_tel']))
                    {
                        $data['linkman_info_status'] = 1;
                    }
                }
                else
                {
                    $data['linkman_info_status'] = 1;
                }

            }

            if(!empty($additionalInfo['id_photo_url1']) && !empty($additionalInfo['id_photo_url1']) && !empty($additionalInfo['id_photo_url1']))
            {
                $data['id_card_photo_status'] = 1;
            }
            if(!empty($additionalInfo['province_id']) && !empty($additionalInfo['province_name'])
                && !empty($additionalInfo['city_id']) && !empty($additionalInfo['city_name'])
                && !empty($additionalInfo['district_id']) && !empty($additionalInfo['district_name'])
                && !empty($additionalInfo['address']))
            {
                $data['home_addr_status'] = 1;
            }
            if(!empty($additionalInfo['cur_province_id']) && !empty($additionalInfo['cur_province_name'])
                && !empty($additionalInfo['cur_city_id']) && !empty($additionalInfo['cur_city_name'])
                && !empty($additionalInfo['cur_district_id']) && !empty($additionalInfo['cur_district_name'])
                && !empty($additionalInfo['cur_address']))
            {
                $data['cur_addr_status'] = 1;
            }

            if(!empty($additionalInfo['zhima']) && $additionalInfo['zhima'] > 0)
            {
                $data['zhima_info_status'] = 1;
            }

            $data['addtionnalInfo'] = $additionalInfo;
        }

        $this->load->model('User_bank_card_info');
        $where = array('user_id' => $this->user_id,'is_default'=>1);
        $bankList = $this->User_bank_card_info->get_by_where($where);
        if(!empty($bankList))
        {
            $data['bank_card_info_status'] = 1;
            $data['bank_card_list'] = $bankList;
        }
        else
        {

            $where = array('user_id'=>$this->user_id);
            $defaultBankCard = $this->User_bank_card_info->get_by_where($where);
            if (!empty($defaultBankCard))//获取一个存在的卡，设为默认卡
            {
                $where = array('user_id'=>$this->user_id,'id'=>$defaultBankCard['id']);
                $defultBankValue = array('is_default'=>1);
                $this->User_bank_card_info->update_by_where($where, $defultBankValue);

                $defaultBankCard['is_default'] = '1';

                $data['bank_card_info_status'] = 1;
                $data['bank_card_list'] = $defaultBankCard;
            }
        }

        output_data($data);exit;
    }

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
        $this->user_model->update_by_id($this->user_id, $authType);
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

    public function setGesturePassword()
    {
        $gesturePwd = $this->input->post('gesture_password');

        if (!isset($gesturePwd))
        {
            output_error(-1,'参数为空');exit;
        }

        $gesturePwdInfo = array('gesture_pwd'=>$gesturePwd);
        $res = $this->user_model->update_by_id($this->user_id, $gesturePwdInfo);
        if ($res)
        {
            $data = array('gesture_password'=>$gesturePwd);
            output_data($data);exit;
        }
        else
        {
            output_error(-1,'设置手势密码失败');exit;
        }

    }

    public function changeGesturePassword()
    {
        $gesturePwd = $this->input->post('gesture_password');
        $originalPwd = $this->input->post('original_password');

        if (!isset($gesturePwd) || !isset($originalPwd))
        {
            output_error(-1,'参数为空');exit;
        }

        $originalGesPwd = $this->user_model->get_by_id($this->user_id,'gesture_pwd');
        if (!empty($originalGesPwd))
        {
            if (strcmp($originalPwd, $originalGesPwd['gesture_pwd']) != 0)
            {
                output_error(-1,'原手势密码输入错误');exit;
            }

        }

        if (strcmp($originalPwd, $gesturePwd) == 0)
        {
            output_error(-1,'新设密码与原手势密码相同');exit;
        }

        $gesturePwdInfo = array('gesture_pwd'=>$gesturePwd);
        $res = $this->user_model->update_by_id($this->user_id, $gesturePwdInfo);
        if ($res)
        {
            $data = array('gesture_password'=>$gesturePwd);
            output_data($data);exit;
        }
        else
        {
            output_error(-1,'修改手势密码失败');exit;
        }

    }

    public function resetGesturePassword()
    {
        $gesturePwd = $this->input->post('gesture_password');
        $loginPwd = $this->input->post('login_password');

        if (!isset($gesturePwd) || !isset($loginPwd))
        {
            output_error(-1,'参数为空');exit;
        }

        $password = $this->user_model->get_by_id($this->user_id,'password');
        if (!empty($password))
        {
            if (strcmp($password['password'], $loginPwd) != 0)
            {
                output_error(-1,'登录密码输入错误');exit;
            }

        }

        $gesturePwdInfo = array('gesture_pwd'=>$gesturePwd);
        $res = $this->user_model->update_by_id($this->user_id, $gesturePwdInfo);
        if ($res)
        {
            $data = array('gesture_password'=>$gesturePwd);
            output_data($data);exit;
        }
        else
        {
            output_error(-1,'重置手势密码失败');exit;
        }

    }

    public function getGesturePassword()
    {
        $data = $this->user_model->get_by_id($this->user_id,'gesture_pwd');
        output_data($data);
    }

    public function suggest()
    {
        $suggest = $this->input->post('suggest');

        if (!isset($suggest))
        {
            output_error(-1,'参数为空');exit;
        }

        $suggestInfo = array('user_id'=>$this->user_id, 'user_phone'=>$this->loginUser['user_name'],'desc_txt'=>$suggest,'createtime'=>time());
        $res = $this->feedback_model->insert($suggestInfo);
        if ($res)
        {
            output_data();exit;
        }
        else
        {
            output_error(-1,'提交建议失败');exit;
        }
    }

    public function updateLocation()
    {
        $lng = $this->input->post('lng');//经度
        $lat = $this->input->post('lat');//纬度
        $locationAddr = $this->input->post('location_addr');//

        $locationInfo = array('longitude'=>$lng, 'latitude'=>$lat, 'lacation_addr'=>$locationAddr,'location_time'=>time());
        $res = $this->user_model->update_by_id($this->user_id, $locationInfo);
        output_data();exit;
    }

    public function getPersonalCenterInfo()
    {
        $personalCenterInfo = $this->user_service->getPersonalCenterInfo($this->user_id);
        if(!empty($personalCenterInfo))
        {
            output_data($personalCenterInfo);
        }
        else
        {
            output_error(-1,'获取用户信息失败');exit;
        }

    }
}