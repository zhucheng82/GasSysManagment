<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Create CAPTCHA
 *
 */
 
 if ( ! function_exists('check_captcha'))
{
    function check_captcha($randval,$verifyName='verify'){
        $CI =& get_instance();
        $rs = $randval && $CI->session->userdata($verifyName) == md5(strtoupper($randval)) ? true : false;
        $CI->session->unset_userdata($verifyName);
        return $rs;
    }
}
if ( ! function_exists('create_captcha'))
{
    function create_captcha($length=4,$width=64,$height=28,$verifyName='verify')
    {
        if ( ! extension_loaded('gd'))
        {
            echo "extension_loaded('gd') is false!";

            phpinfo();
            return FALSE;
        }

        $pool = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';//abcdefghijklmnopqrstuvwxyz
        
        $randval = '';
        for ($i = 0; $i < $length; $i++)
        {
            $randval .= substr($pool, mt_rand(0, strlen($pool) -1), 1);
        }
        $CI =& get_instance(); 
        $CI->session->set_userdata($verifyName, md5($randval));
        
        $type = 'jpeg';
        $width = ($length*10+10)>$width?$length*10+10:$width;
        if ( $type!='gif' && function_exists('imagecreatetruecolor')) {
            $im = @imagecreatetruecolor($width,$height);
        }else {
            $im = @imagecreate($width,$height);
        }
        $r = Array(225,255,255,223);
        $g = Array(225,236,237,255);
        $b = Array(225,236,166,125);
        $key = mt_rand(0,3);
        $backColor = imagecolorallocate($im, $r[$key], $g[$key], $b[$key]);
        $borderColor = imagecolorallocate($im, 100, 100, 100); 
        $pointColor = imagecolorallocate($im,rand(0,255),rand(0,255),rand(0,255));//����ɫ
        $stringColor = imagecolorallocate($im,rand(0,200),rand(0,120),rand(0,120));
        imagefilledrectangle($im, 0, 0, $width-1, $height-1, $backColor);
        imagerectangle($im,0,0,$width-1,$height-1,$borderColor);
        for ($i=0;$i<2;$i++){
            $fontColor = imagecolorallocate($im,rand(0,255),rand(0,255),rand(0,255));
            imagearc($im,rand(10, $width), rand(10, $height), rand(30,300), rand(20,200),55,44, $fontColor);
        }
        for ($i=0;$i<25;$i++){
            imagesetpixel($im, rand(0,$width), rand(0, $height), $pointColor);
        }
        for ($i=0;$i<$length;$i++){
            imagestring($im,5,$i*15+5,5,$randval{$i}, $stringColor);
        }
        header("Content-type: image/jpeg");
        ImageJPEG($im);
        ImageDestroy($im);
    }
}

// ------------------------------------------------------------------------

/* End of file MY_captcha_helper.php */
/* Location: ./application/frontend/heleprs/MY_captcha_helper.php */