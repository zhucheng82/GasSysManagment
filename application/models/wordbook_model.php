<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wordbook_model extends XT_Model {

	protected $mTable = 'sys_wordbook';

	/**
	 * 读取系统设置信息
	 *
	 * @param string $name 系统设置信息名称
	 * @return array 数组格式的返回结果
	 */
	public function getVal($key)
	{

		$o = $this->get_by_where("k=$key");
		if(!empty($o))
		{
			return $o['val'];
		}
		else
			return false;
	}
	
	public function getList(){
		$result = $this->get_list();
		$list = array();
		foreach ($result as $key=>$value)
		{
		    $list[$value['k']] = $value['val'];
		}

		return $list;
	}

	public function updateSetting($arr)
	{
		if (empty($arr)){
			return false;
		}

		if (is_array($arr)){
			foreach ($arr as $k => $v){
				$tmp = array();
				$specialkeys_arr = array('statistics_code');
				$tmp['val'] = (in_array($k,$specialkeys_arr) ? htmlentities($v,ENT_QUOTES) : $v);

	            $this->update_by_where("k='$k'",$tmp);
			}
			dkcache('setting');
			return true;
		}else {
			return false;
		}
	}
}