<?php
if ( ! defined('BASEPATH')){
	exit('No direct script access allowed');
}

/**
 * Service基类
 */
abstract class CI_Service{
	/**
	 * 构造
	 */
	public function __construct(){}

	/**
	 * 复制CI的属性
	 *
	 * @param $key
	 *
	 * @return mixed
	 */
	public function __get($key){
		$CI =& get_instance();
		return $CI->$key;
	}
}

/**
 * MY Service
 */
class MY_Service extends CI_Service{
	public function __construct(){
	}
}//END Service class

/**
 * 会员中心基础Service(构造时判断 是否登录 否则抛20000异常)
 */
class User_base_service extends MY_Service{
	/**
	 *@var int|null 登录的会员id
	 */
	protected $user_id=null;
	/**
	 * @var string|null 登录的会员名称
	 */
	protected $user_info=array();

	/**
	 * 判断是否登录
	 */
	public function __construct(){
		parent::__construct();
		$login_user=$this->loginUser;
		if(!$login_user||!isset($login_user['id'])||!$login_user['id']){
			throw new ServiceException(20000); //抛会员未登录异常
		}
		$this->user_id=$login_user['id'];
		$this->user_info=array(
			'user_name'=>isset($login_user['username'])?$login_user['username']:'',
			'usertype'=>isset($login_user['usertype'])?$login_user['usertype']:'',
			'nickname'=>isset($login_user['nicknamenickname'])?$login_user['nickname']:'',
			'realname'=>isset($login_user['realname'])?$login_user['realname']:'',
			'sex'=>isset($login_user['sex'])?$login_user['sex']:'',
			'mobile'=>isset($login_user['mobile'])?$login_user['mobile']:'',
		);
		
	}
}

/**
 * 异常基类
 */
abstract class MY_Exception extends Exception{
	/**
	 * @var array 异常相关数据
	 */
	private $_data;

	/**
	 * 构造
	 *
	 * @param int    $code 异常代码
	 * @param string $msg  自定义消息内容(不填，根据错误代码自动获取)
	 * @param array $data 异常相关数据
	 * @param bool   $is_log 是否记录日志
	 */
	public function __construct($code=9999,$msg='',$data=array(),$is_log=false){
		$this->_data=$data?$data:array();
		$err=$this->getErrorMsg($code);
		if($err&&$msg){
			if(is_array($msg)){
				$message=call_user_func_array('sprintf', $msg);
			}
			else{
				$message=sprintf($err,$msg);
			}
		}
		elseif($err){
			$message=$err;
		}
		parent::__construct($message,$code);
		if($is_log){
			$this->_log();
		}
	}

	/**
	 * 获取异常相关数据
	 *
	 * @return array
	 */
	public function getData(){
		return $this->_data;
	}

	/**
	 * 获取错误消息内容
	 *
	 * @param int $err_code
	 *
	 * @return string
	 */
	protected function getErrorMsg($err_code=9999){
		$cfg =&load_class('Config', 'core');
		$cfg->load('trj_errors', TRUE);
		$errors = $cfg->item('trj_errors','trj_errors');
		$errors=is_array($errors)?$errors:array();
		return isset($errors[$err_code])?$errors[$err_code]:'对不起，发生未知错误了(代码'.$err_code.')';
	}

	/**
	 * 记录异常日志
	 *
	 * @return bool
	 */
	private function _log(){
		$content=$this->getCode()."\t".$this->getMessage()."\t".json_encode($this->_data,true);
		return _trj_log('error',$content);
	}
}

/**
 * Service异常类
 */
class ServiceException extends MY_Exception{}
/**
 * Model异常类
 */
class ModelException extends MY_Exception{}

if(!function_exists('_trj_log')){
	/**
	 * 写入日志
	 *
	 * @param string $type 日志类型
	 * @param string $msg 日志内容
	 *
	 * @return bool
	 */
	function _trj_log($type,$msg){
		//获取日志记录路径
		$config =& get_config();
		$log_path = ($config['log_path'] != '') ? $config['log_path'] : APPPATH.'logs/';
		$file_path = $log_path.$type.'_'.date('Ymd').'.log';
		if ( ! $fp = @fopen($file_path, FOPEN_WRITE_CREATE)){
			return FALSE;
		}
		$message = date('Y-m-d H:i:s'). "\t".$msg."\n";
		flock($fp, LOCK_EX);
		fwrite($fp, $message);
		flock($fp, LOCK_UN);
		fclose($fp);
		return TRUE;
	}
}
if(!function_exists('array_empty_filter')){
	/**
	 * 过滤数组中值为空的元素
	 *
	 * @param array $a
	 *
	 * @return array
	 */
	function array_empty_filter($a){
		if(!$a){
			return array();
		}
		return array_filter($a,function($v){
			if($v===''||$v===FALSE||$v===null){
				return FALSE;
			}
			return TRUE;}
		);
	}
}
/* End of file MY_Service.php */
/* Location: ./application/core/MY_Service.php */