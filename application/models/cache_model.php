<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cache_model extends XT_Model {

	//protected $mTable = 'sys_wordbook';

	
	public function call($method){
		$method = '_'.strtolower($method);
		if (method_exists($this,$method)){
			return $this->$method();
		}else{
			return false;
		}
	}

	/**
	 * 基本设置
	 *
	 * @return array
	 */
	private function _setting(){
		$this->set_table('sys_wordbook');
		$list =$this->get_list();
		$array = array();
		foreach ((array)$list as $v) {
			$array[$v['k']] = $v['val'];
		}
		unset($list);
		return $array;
	}

    public function getServiceFeeRate()
    {
        $rate = 0;
        $k = 'deposit_rate';
        $where = array('k like'=>"'%$k%'");
        $feeRateInfo = $this->get_by_where($where);
        if (isset($feeRateInfo))
        {
            $rate = $feeRateInfo['val'];
        }
        return $rate;
    }


}