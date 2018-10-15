<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Message_Def_model extends XT_Model {

	protected $mTable = 'inter_message_def';

	protected $cachedData;

	public function get_by_id($id,$field = '*')
	{
		$data = $this->getCache();

		$arrReturn = isset($data[$id])?$data[$id]:array();

		return $arrReturn;

	}

	public function insert($data)
	{
		dkcache('message_def');

		$result = parent::insert($data);

		return $result;
	}

	public function update_by_id($id,$data)
	{
		dkcache('message_def');

		$result = parent::update_by_id($id,$data);

		return $result;
	}


	public function getCache()
	{/*
		// 对象属性中有数据则返回
		if ($this->cachedData !== null) {
			return $this->cachedData;
		}

		// 缓存中有数据则返回
		if ($data = rkcache('message_def')) {
			$this->cachedData = $data;
			return $data;
		}
*/
		// 查库
		$data = array();
		$array = $this->get_list();
		foreach ($array as $key => $value) {
			$data[$value['id']] = $value;
		}
		wkcache('message_def', $data);
		$this->cachedData = $data;

		return $data;
	}


}