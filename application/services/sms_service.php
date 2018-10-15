<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sms_service
{
    //短信接口配置
    const APPID='qxuid_582ebfec42c81';
    const APPKEY='955e4d3199e97ec54b2fe977769b0171';
    const URL='qxsms.qixiangnet.com/api/public/sms';
    public function __construct()
    {
        $this->ci = & get_instance();
        $this->ci->load->model('Smscode_model');
        $this->ci->load->model('user_model');
        $this->ci->load->model('User_token_model');
    }

    /*
    type_id
    */
    public function check_code($mobile, $code, $type_id, $platform_id)
    {
        $where = array(
            'mobile' => $mobile,
            'code' => $code,
            'type_id' => $type_id,
            'platform_id' =>$platform_id,
        );//'expiretime >=' => time()
        $aSmscode = $this->ci->Smscode_model->get_by_where($where);
        if(!empty($aSmscode)){
            if($aSmscode['expiretime']>time()){
                $this->ci->Smscode_model->update_by_id($aSmscode['id'],array('is_valid'=>1));
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
        return false;
    }


    /**
     * 验证通过后，验证码置为失效
     */
    public function setCodeValid($id){
        $this->ci->Smscode_model->update_by_id($id, array('is_valid' => 1));
    }


    /**
     * @param $code
     * @param $mobile
     * @return mixed|string
     */
    public function sendMsg($code, $mobile)
    {
        $msg = '【浙贷宝】验证码' . $code;
        $data = array(
            'msg'=>$msg,
            'mobile'=>$mobile,
            'appkey'=>self::APPKEY,
            'timestamp'=>time(),
        );

        $data['sign']= $this->makeSign($data);
        $code = http_post_data(self::URL, $data);
        $result=json_decode($code,true);
        if($result['code']==100){
            return true;
        }else{
            exit($code);
            return false;
        }

    }

    /**
     * 发送其他类型消息
     * @param $code
     * @param $mobile
     * @return mixed|string
     */
    public function sendMessgae($msg, $mobile)
    {
        $data = array(
            'msg'=>$msg,
            'mobile'=>$mobile,
            'appkey'=>self::APPKEY,
            'timestamp'=>time(),
        );

        $data['sign']= $this->makeSign($data);
        $code = http_post_data(self::URL, $data);
        $result=json_decode($code,true);
        if($result['code']==100){
            return true;
        }else{
            //exit($code);
            return false;
        }

    }

    public function sendSms($type, $msgVar, $mobile)
    {
        $templateId = C('SmsTemplate.'.$type);
        if (isset($templateId))
        {
            $data = array(
                'jsonVar'=>$msgVar,
                'mobile'=>$mobile,
                'templateId'=>$templateId,
                'appkey'=>self::APPKEY,
                'timestamp'=>time(),
            );

            $data['sign']= $this->makeSign($data);
            $code = http_post_data(self::URL, $data);
            $result=json_decode($code,true);

            log_message('debug','sendSms result'.json_encode($result).'$data:'.json_encode($data));

            if($result['code']==1){
                log_message('debug','sendSms succeed');
                return true;
            }else{
                //exit($code);
                log_message('debug','sendSms failed');
                return false;
            }
        }
        return false;
    }



    /**
     * 生成签名算法
     * @param $data
     * @return string
     */
    private function makeSign($data){
        //验证签名是否正确
        $second_str = 'appid=' . self::APPID. '&timestamp=' . $data['timestamp'];
        unset($data['timestamp'],$data['appkey']);
        ksort($data);
        $str=http_build_query($data);
        if (empty($data)) {
            $str = $second_str;
        } else {
            $str .= '&'.$second_str;
        }
        return md5($str);
    }

    /**
     * 短信余量
     * @return string(json)
     */
    public function left_num(){
        $data = array(
            'appkey'=>self::APPKEY,
            'timestamp'=>time(),
        );
        $data['sign']= $this->makeSign($data);
        return http_post_data(self::URL.'/stat', $data);
    }
}