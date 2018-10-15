<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//工具服务mao
class Tools_service{
    public function __construct(){
        $this->ci = &get_instance();
    }

    /*
     * 创建二维码
     * $orderid 订单ID
     * $orderType 订单类型
     * $url 浏览url
     * $logo 二维码logo
     */
    public function createQrcode($orderid,$orderType,$url='',$oldUrl,$logo=''){
        $ecc = 'H';
        $errorCorrectionLevel = $ecc;
        $size = 10;
        $matrixPointSize = $size;
        require('../application/libraries/qrcode/qrlib.php');
        $filename = '/upload/oqrcode/'.date("Y/m/d").'/'.$orderType.$orderid.time().'qrcode'.'.png';//文件上传目录
        if(!file_exists("./upload/oqrcode/".date("Y/m/d"))){  
            mkdir("./upload/oqrcode/".date("Y/m/d"),0777,true);//原图路径
        }
        $file= (dirname(dirname(dirname(__FILE__)))).'/www'.$filename;
        $file=str_replace("/",DIRECTORY_SEPARATOR,$file);
        if (file_exists('.'.$oldUrl)) {//如果存在则删除更新
            @unlink('.'.$oldUrl);
        }
        \QRcode::png($url,$file,$errorCorrectionLevel,$matrixPointSize);
        return $filename;
    }

    /*
     * 创建邀请二维码并更新到数据库
     * $inviteCode 推广ID
     * $userType 用户类型
     * $url 浏览url
     * $logo 二维码logo
     */
    public function createUserQrcode($inviteCode,$userType,$url='',$logo=''){
        $this->ci->load->model('user_model');
        $userInfo = $this->ci->user_model->get_by_where("invite_code = '$inviteCode'",'user_id,qrcode');
        if (empty($userInfo)) {
            return false;
        }elseif (!empty($userInfo['qrcode'])) {
            $codeUrl = BASE_SITE_URL.'/'.$userInfo['qrcode'];
            return $codeUrl;
        }
        $ecc = 'H';
        $errorCorrectionLevel = $ecc;
        $size = 10;
        $matrixPointSize = $size;
        require('../application/libraries/qrcode/qrlib.php');
        $filename = '/upload/invite_code/'.date("Y/m/d").'/'.$inviteCode.$userType.'qrcode'.'.png';//文件上传目录
        if(!file_exists("./upload/invite_code/".date("Y/m/d"))){
            mkdir("./upload/invite_code/".date("Y/m/d"),0777,true);//原图路径
        }
        $file= (dirname(dirname(dirname(__FILE__)))).'/www'.$filename;
        $file=str_replace("/",DIRECTORY_SEPARATOR,$file);
        if (file_exists('.'.$filename)) {//如果存在则删除更新
            unlink('.'.$filename);
        }
        \QRcode::png($url,$file,$errorCorrectionLevel,$matrixPointSize);
        $codeUrl = BASE_SITE_URL.'/'.$filename;
        $this->ci->user_model->update_by_id($userInfo['user_id'],array('qrcode'=>$filename));
        return $codeUrl;

    }

    //创建推广码
    public  function createInviteCode(){
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $inviteCode = substr($charid, 0, 2).substr($charid, 2, 2).substr($charid,4, 2);
        return $inviteCode;
    }
}