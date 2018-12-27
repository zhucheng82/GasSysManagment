<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu_model extends XT_Model {

	protected $mTable = 'change_sys_menu';

	protected $cachedData;
	
	public function get_menu()
	{
		dkcache('menu');
		// 对象属性中有数据则返回
		if ($this->cachedData !== null) {
			return $this->cachedData;
		}

		// 缓存中有数据则返回
		if ($data = rkcache('menu')) {
			$this->cachedData = $data;
			return $data;
		}

		// 查库
		$data = array();
		$menu_all_array = $this->get_list(array('status' => '1'));
		foreach ((array) $menu_all_array as $a) {
			$data['type-'.$a['type']][$a['menu_id']] = array(
				'menu_id' => $a['menu_id'],
				'menu_title' => $a['menu_title'],
				'menu_level' => $a['menu_level'],
				'menu_url' => $a['menu_url'],
				'parent_id' => $a['parent_id'],
				'act' => $a['act'],
				'op' => $a['op'],
				'type' => $a['type'],
				'status' => $a['status'],
				'desc' => $a['desc'],

			);
			$data['type-'.$a['type']]['parent'][$a['menu_id']] = $a['parent_id'];
			$data['type-'.$a['type']]['children'][$a['parent_id']][] = $a['menu_id'];
		}
		wkcache('menu', $data);
		$this->cachedData = $data;

		return $data;
	}

}
