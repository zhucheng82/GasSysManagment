<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class DES
{
    private $key='xshop@m';
    private $iv; //偏移量
    private $mode   = MCRYPT_MODE_ECB;
    private $cipher = MCRYPT_DES;
    
    public function __construct(){}

    
    /**
     +----------------------------------------------------------
     * 加密字符串
     +----------------------------------------------------------
     * @access static
     +----------------------------------------------------------
     * @param string $str 字符串
     * @param string $key 加密key
     * @return string
     */
    public function encrypt($str, $key=''){
        if ($str == '')return;
        return $this->_des($key, $str, 1);      
    }
    
    /**
     +----------------------------------------------------------
     * 解密字符串
     +----------------------------------------------------------
     * @access static
     +----------------------------------------------------------
     * @param string $str 字符串
     * @param string $key 加密key
     * @return string
     */
    public function decrypt($str, $key=''){
        if ($str == '')return;
        return $this->_des($key, $str);     
    }
    
    private function _des($key, $str, $mode=0, $iv=0){
        if($key)$this->key = $key;
        if( $iv == 0 ) {
            $this->iv = $key; //默认以$key 作为 iv
        } else {        
            $iv = mcrypt_create_iv(mcrypt_get_iv_size($this->cipher, $this->mode), MCRYPT_RAND);
            $this->iv = $iv; 
        }
        if ($mode){
            $size = mcrypt_get_block_size ( $this->cipher, $this->mode );
            $str = $this->pkcs5Pad ( $str, $size );
            return bin2hex( mcrypt_encrypt($this->cipher, $this->key, $str, $this->mode, $this->iv) ); 
        }else{
            $strBin = $this->hex2bin(  $str  );
            $str = mcrypt_decrypt($this->cipher, $this->key, $strBin, $this->mode, $this->iv); 
            return $this->pkcs5Unpad ( $str);
        }
    }    
    
//    function encrypt($str) {
//        $size = mcrypt_get_block_size ( MCRYPT_DES, $this->mode );
//        $str = $this->pkcs5Pad ( $str, $size );
//        return bin2hex( mcrypt_ecb(MCRYPT_DES, $this->key, $str, MCRYPT_ENCRYPT, $this->iv ) );
//    }
//    
//    function decrypt($str) {
//        $strBin = $this->hex2bin(  $str  );
//        $str = mcrypt_ecb( MCRYPT_DES, $this->key, $strBin, MCRYPT_DECRYPT, $this->iv );
//        return $this->pkcs5Unpad( $str );
//    }
    
    private function hex2bin($hexData) {
        $binData = "";
        for($i = 0; $i < strlen ( $hexData ); $i += 2) {
            $binData .= chr ( hexdec ( substr ( $hexData, $i, 2 ) ) );
        }
        return $binData;
    }

    private function pkcs5Pad($text, $blocksize) {
        $pad = $blocksize - (strlen ( $text ) % $blocksize);
        return $text . str_repeat ( chr ( $pad ), $pad );
    }
    
    private function pkcs5Unpad($text) {
        $pad = ord ( $text {strlen ( $text ) - 1} );
        if ($pad > strlen ( $text ))
            return false;
        if (strspn ( $text, chr ( $pad ), strlen ( $text ) - $pad ) != $pad)
            return false;
        return substr ( $text, 0, - 1 * $pad );
    }
    
}

