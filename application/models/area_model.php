<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Area_model extends XT_Model {

	protected $mTable = 'sys_area';

	protected $cachedData;

	public function add($sort, $name, $parent_id, $child_deep) {
		$new_area = array();
		foreach ($sort as $key => $value) {
			$new_area[] = array('sort' => $value, 'name' => $name[$key], 'parent_id' => $parent_id, 'deep' => $child_deep);
		}
		foreach ($new_area as $value) {
			$this->Area_model->insert($value);
		}
		dkcache('area');
	}

	public function del($del_id) {
		$ids = explode('|', $del_id);
		array_shift($ids);
		$cache = rkcache('area');
		$this->deleteone($ids);
		$this->delete_by_id($ids);
		dkcache('area');
	}

	protected function deleteone($ids) {
		$cache = rkcache('area');
		foreach ($ids as $key => $value) {
			if (isset($cache['children'][$value]) && is_array($cache['children'][$value])) {
				$this->deleteone($cache['children'][$value]);
				$this->delete_by_id($cache['children'][$value]);
			} elseif (isset($cache['children'][$value])) {
				$this->delete_by_id($value);
			}
		}

	}

	public function update_area_by_id($id,$type,$data){
		dkcache('area');
		if ($this->Area_model->update_by_id($id,array("$type"=>$data))) {
			return true;
		}else{
			return false;
		}
		
	}

	/**
	 * 获取地址列表
	 *
	 * @return mixed
	 */
	public function getAreaList($condition = array(), $fields = '*', $order_by = '') {
		// return $this->where($condition)->field($fields)->limit(false)->group($group)->select();
		return $this->get_list($condition, $fields, $order_by);
	}

	/**
	 * 根据地区ID获取地区名称
	 */
	public function getAreaId($id){
		$areaInfo = $this->get_by_id($id);
		if(!empty($areaInfo)){
			return $areaInfo['name'];
		}
		return '';
	}

	/**
	 * 获取地址详情
	 *
	 * @return mixed
	 */
	public function getAreaInfo($condition = array(), $fileds = '*') {
		return $this->where($condition)->field($fileds)->find();
	}

	/**
	 * 获取一级地址（省级）名称数组
	 *
	 * @return array 键为id 值为名称字符串
	 */
	public function getTopLevelAreas() {
		$data = $this->getCache();
		$arr = array();
		foreach ($data['children'][0] as $i) {
			$arr[$i] = $data['name'][$i];
		}

		return $arr;
	}

	/**
	 * 获取获取市级id对应省级id的数组
	 *
	 * @return array 键为市级id 值为省级id
	 */
	public function getCityProvince() {
		$data = $this->getCache();

		$arr = array();
		foreach ($data['parent'] as $k => $v) {
			if ($v && $data['parent'][$v] == 0) {
				$arr[$k] = $v;
			}
		}

		return $arr;
	}

	/**
	 * 获取地区缓存
	 *
	 * @return array
	 */
	public function getAreas() {
		return $this->getCache();
	}

	/**
	 * 获取全部地区名称数组
	 *
	 * @return array 键为id 值为名称字符串
	 */
	public function getAreaNames() {
		$data = $this->getCache();

		return $data['name'];
	}

	/**
	 * 获取用于前端js使用的全部地址数组
	 *
	 * @return array
	 */
	public function getAreaArrayForJson() {
		$data = $this->getCache();
		$arr = array();
		foreach ($data['children'] as $k => $v) {

			foreach ($v as $vv) {
				$arr[$k][] = array($vv, $data['name'][$vv], $data['sort'][$vv]);

			}
		}
		return $arr;
	}

	public function getAreasList(){

		return $this->getCache();
	}

	/**
	 * 获取地区数组 格式如下
	 * array(
	 *   'name' => array(
	 *     '地区id' => '地区名称',
	 *     // ..
	 *   ),
	 *   'parent' => array(
	 *     '子地区id' => '父地区id',
	 *     // ..
	 *   ),
	 *   'children' => array(
	 *     '父地区id' => array(
	 *       '子地区id 1',
	 *       '子地区id 2',
	 *       // ..
	 *     ),
	 *     // ..
	 *   ),
	 *   'region' => array(array(
	 *     '华北区' => array(
	 *       '省级id 1',
	 *       '省级id 2',
	 *       // ..
	 *     ),
	 *     // ..
	 *   ),
	 * )
	 *
	 * @return array
	 */
	protected function getCache() {
		// 对象属性中有数据则返回
		if ($this->cachedData !== null) {
			return $this->cachedData;
		}

		// 缓存中有数据则返回
		if ($data = rkcache('area')) {
			$this->cachedData = $data;
			return $data;
		}

		// 查库
		$data = array();
		$area_all_array = $this->get_list();
		foreach ((array) $area_all_array as $a) {
			$data['name'][$a['id']] = $a['name'];
			$data['parent'][$a['id']] = $a['parent_id'];
			$data['children'][$a['parent_id']][] = $a['id'];
			$data['sort'][$a['id']] = $a['sort'];

			if ($a['deep'] == 1 && $a['region']) {
				$data['region'][$a['region']][] = $a['id'];
			}

		}
		wkcache('area', $data);
		$this->cachedData = $data;

		return $data;
	}

}