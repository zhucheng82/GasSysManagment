<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ServiceAPI extends TokenApiController{
	public function __construct(){
		parent::__construct();
		//$this->load->model('User_third_model');
		$this->load->library('RonghubApi');
		//$this->targetId='KEFU148117535368935';//开发环境
        $this->targetId='KEFU148775764422893';//生产环境
		//$this->appSecret = '4dR5Mzy7y4DH';//开发环境
        $this->appSecret = 'L36D5cU9Oc';//生产环境
		//$this->appKey = 'z3v5yqkbzcnf0';//开发环境
        $this->appKey = 'vnroth0kvp9qo';//生产环境

	}
	
	//请求token
	public function getToken(){
        $bRefresh = $this->input->post('isRefresh');
		$user_id = $this->loginUser['user_id'];
		if(empty($user_id)){
			output_error(-1,'没有找到用户信息');exit;
		}else{
			$userName = empty($this->loginUser['user_name'])?'用户'.$this->loginUser['user_id']:$this->loginUser['user_name'];
			if (empty($this->loginUser['rongytoken']) || $bRefresh) {
				//请求融云token
				$ronghubApi = new RonghubApi();

                $portraitUrl = $this->loginUser['portrait'];
				if (empty($portraitUrl))
                    $portraitUrl = BASE_SITE_URL.'defaultPortrait.png';//可以设置默认头像
				//$res = $ronghubApi->getToken($this->loginUser['user_id'],$userName, BASE_SITE_URL.$this->loginUser['portrait']);

                if (strpos($portraitUrl, 'http') !== 0)
                {
                    $portraitUrl = BASE_SITE_URL.$portraitUrl;
                }
                $res = $ronghubApi->getToken($this->loginUser['user_id'],$userName,$portraitUrl);
                $res_Arr = json_decode($res,true);
				if ($res_Arr['code']=='200') {
					$data = array('user_id'=>$this->loginUser['user_id'],'rongytoken'=> $res_Arr['token']);
					$update_res = $this->user_model->insert($data);//更新融云token到数据库

				}
				$rongToken = $res_Arr['token'];
			}else{
				$rongToken = $this->loginUser['rongytoken'];
			}

			$result = array('status'=>1,'userId'=>$this->loginUser['user_id'],'userName'=>$userName,'token'=>$rongToken, 'service_id'=>$this->targetId);
		}
		output_data($result);exit;
	}

	//无用???刷新token (当status值为1时，代表刷新成功)
	public function refreshToken(){
		$user_id = (int)$this->input->get('user_id');
		if (empty($user_id)) {
			output_error(-1,'参数错误');exit;
		}

		if (empty($this->loginUser)) {
			output_error(-1,'没有找到用户信息');exit;
		}

		$userName = empty($this->loginUser['name'])?'用户'.$this->loginUser['user_id']:$this->loginUser['name'];
		if (!empty($this->loginUser['rong_token'])) {//如果没有token,则请求
			//请求刷新融云token
			$ronghubApi = new RonghubApi();
			$res = $ronghubApi->userRefresh($this->loginUser['id'],$userName,$this->loginUser['id'].$userName);
			$res_Arr = json_decode($res,true);
			if($res_Arr['code']=='200'){
				$result = array('status'=>1,'userId'=>$this->loginUser['id'],'userName'=>$userName,'token'=>$this->loginUser['rong_token']);
			}
		}else{
			output_error(-1,'没有找到用户信息');exit;
		}
		
		output_data($result);exit;
	}

	//生成签名
	public function createAutoGraph(){
		srand((double)microtime()*1000000);
		$appSecret = $this->appSecret; // 开发者平台分配的 App Secret。
		$nonce = rand(); // 获取随机数。
		$timestamp = time(); // 获取时间戳。
		$signature = sha1($appSecret.$nonce.$timestamp);
		//echo $nonce.','.$timestamp.',';
		echo $signature;exit();
	}

	//校验签名
	public function checkAutoGraph(){
		$appSecret = $this->appSecret; // 开发者平台分配的 App Secret。
		$nonce = $_GET['nonce']; // 获取随机数。
		$timestamp = $_GET['timestamp']; // 获取时间戳。
		$signature = $_GET['signature']; // 获取数据签名。
		$local_signature = sha1($appSecret.$nonce.$timestamp); // 生成本地签名。
		if(strcmp($signature, $local_signature)===0){
			//相关处理
		    echo 'OK';
		} else {
		    echo 'Error';
		}
	}
	
}