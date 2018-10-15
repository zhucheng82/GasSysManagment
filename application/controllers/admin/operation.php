<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Operation extends MY_Admin_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('Images_model');
		$this->load->model('Feedback_model');
	}

	public function images_list()
	{
		$page = _get_page();
		$pagesize = 20;//已分页
		$arrParam = array();

		$images_list = $this->Images_model->fetch_page($page,$pagesize,$arrWhere = array('status <>' => -1,'type'=>1),'*','sort desc');

		$pagecfg = array();
		$pagecfg['base_url']     = _create_url(ADMIN_SITE_URL.'/operation/images_list', $arrParam);
		$pagecfg['total_rows']   = $images_list['count'];
		$pagecfg['cur_page'] = $page;
		$pagecfg['per_page'] = $pagesize;

		$this->pagination->initialize($pagecfg);
		$images_list['pages'] = $this->pagination->create_links();

		$result = array('images_list' => $images_list);
		$this->load->view('admin/operation/images_list',$result);
	}

	public function images_add()
	{
		$id = $this->input->get('id');

		$info = array();
		if ($id) {
			$info = $this->Images_model->get_by_id($id);
		}
		$result = array('info' => $info);
		$this->load->view('admin/operation/images_add',$result);
	}

	public function images_add_do()
	{
		$arrReturn = array('status' => '', 'msg' => '' );
		$id = $this->input->post('id');
		$data = array(
			'type' => 1,
			'item_id' => $this->input->post('item_id'),
			'picurl' => $this->input->post('picurl'),
			'weburl' => $_POST['weburl'],
			'sort' => $this->input->post('sort'),
			'status' => 1,
		);
		if ($id) {
			if ($this->Images_model->update_by_id($id,$data)) {
				$arrReturn=array('status' => '1', 'msg' => '编辑成功');
				echo json_encode($arrReturn);exit;
			} else {
				$arrReturn=array('status' => '0', 'msg' => '编辑失败');
				echo json_encode($arrReturn);exit;
			}
		} else {
			if ($this->Images_model->insert($data)) {
				$arrReturn=array('status' => '1', 'msg' => '新增成功');
				echo json_encode($arrReturn);exit;
			} else {
				$arrReturn=array('status' => '0', 'msg' => '新增失败');
				echo json_encode($arrReturn);exit;
			}
		}

	}

	public function delete(){
		$id = $this->input->post('id');
		$data = array(
			'status' => -1,
		);
		if ($this->Images_model->update_by_id($id,$data)) {
			output_data();
		}else{
			output_error(-1,'删除失败');
		}
	}

	public function setting(){
		$this->load->model('Wordbook_model');
		$result = $this->Wordbook_model->get_list();
		$this->load->view('admin/operation/setting',array('list'=>$result));
	}

	public function submitsetting(){
		$ids = $this->input->post('ids');
		$vals = $this->input->post('vals');
		$this->load->model('Wordbook_model');
		foreach($vals as $k => $v){
			if($v >100 || $v < 0){
				output_error(-1,'参数错误');
			}
		}
		try{
			foreach($ids as $k => $v){
				$this->Wordbook_model->update_by_id($v,array('val'=>(float)$vals[$k]));
			}
			dkcache('setting');
			output_data();
		}catch(Exception $e){
			output_error(-1,$e->getMessage());
		}

	}

	public function feedback(){
		$cKey = $this->input->get_post('txtKey');
		$user_id = $this->input->get_post('user_id');
		$user_name = $this->input->get_post('user_name');

		$page     = _get_page();

		$pagesize = 8;
		$arrParam = array();
		$arrWhere = array();

		if($cKey)
		{
			$arrParam['txtKey'] = $cKey;
			$arrWhere['desc_txt like '] = "'%$cKey%'";
		}

		if($user_id)
		{
			$arrParam['user_id'] = $user_id;
			$arrWhere['user_id'] = $user_id;
		}

		if($user_name)
		{
			$arrParam['user_name'] = $user_name;
			$arrWhere['user_phone like '] = "'%$user_name%'";
		}

		$strOrder = ' createtime desc';
		$arrWhere['status <>'] = -1;

		$list = $this->Feedback_model->fetch_page($page, $pagesize, $arrWhere,'*',$strOrder);

		//分页
		$pagecfg = array();
		$pagecfg['base_url']     = _create_url(ADMIN_SITE_URL.'/operation/feedback', $arrParam);
		$pagecfg['total_rows']   = $list['count'];
		$pagecfg['cur_page'] = $page;
		$pagecfg['per_page'] = $pagesize;
		$this->pagination->initialize($pagecfg);
		$list['pages'] = $this->pagination->create_links();
		$result = array(
			'list' => $list,
			'arrParam' => $arrParam,
		);
		//var_dump($list);die;
		$this->load->view('admin/operation/feedback',$result);
	}

	public function position(){
		$result = array();
		$this->load->view('admin/operation/position',$result);
	}

	public function positionList(){
		$this->load->model('Company_position_model');
		$page     = _get_page();
		$pagesize = 10;
		$prolist = $this->Company_position_model->fetch_page($page, $pagesize, array(),'*','id asc');
		$pagecfg = array();
		$pagecfg['total_rows']   = $prolist['count'];
		$pagecfg['cur_page'] = $page;
		$pagecfg['per_page'] = $pagesize;
		$this->pagination->initialize($pagecfg);
		$prolist['pages'] = $this->pagination->create_links();
		output_data($prolist);
	}

	public function deletePosition(){
		$id = $this->input->post('id');
		$this->load->model('Company_position_model');
		$res = $this->Company_position_model->delete_by_id($id);
		if($res){
			output_data();
		}else{
			output_error(-1,'删除出错');
		}
	}

	public function addPosition(){
		$config = array(
			array(
				'field' => 'position',
				'label' => '性质',
				'rules' => 'required|min_length[2]|max_length[30]',
			),
			array(
				'field' => 'sort',
				'label' => '排序',
				'rules' => 'integer',

			),
		);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() === false) {
			output_error(-2,'参数格式错误');
		}

		$data = array(
			'company_position' => $this->input->post('position'),
			'sort' =>$this->input->post('sort')?$this->input->post('sort'):0,
		);
		$this->load->model('Company_position_model');
		$res = $this->Company_position_model->insert_string($data);
		if($res){
			output_data();
		}else{
			output_error(-1,'添加失败');
		}
	}

	public function useage(){
		$result = array();
		$this->load->view('admin/operation/useage',$result);
	}

	public function deleteUseage(){
		$id = $this->input->post('id');
		$this->load->model('borrowing_usage_model');
		$res = $this->borrowing_usage_model->delete_by_id($id);
		if($res){
			output_data();
		}else{
			output_error(-1,'删除出错');
		}
	}

	public function useageList(){
		$this->load->model('borrowing_usage_model');
		$page     = _get_page();
		$pagesize = 10;
		$prolist = $this->borrowing_usage_model->fetch_page($page, $pagesize, array(),'*','id asc');
		$pagecfg = array();
		$pagecfg['total_rows']   = $prolist['count'];
		$pagecfg['cur_page'] = $page;
		$pagecfg['per_page'] = $pagesize;
		$this->pagination->initialize($pagecfg);
		$prolist['pages'] = $this->pagination->create_links();
		output_data($prolist);
	}

	public function addUseage(){
		$config = array(
			array(
				'field' => 'useage',
				'label' => '性质',
				'rules' => 'required|min_length[2]|max_length[30]',
			),
			array(
				'field' => 'sort',
				'label' => '排序',
				'rules' => 'integer',

			),
		);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() === false) {
			output_error(-2,'参数格式错误');
		}

		$data = array(
			'usage' => $this->input->post('useage'),
			'sort' =>$this->input->post('sort')?$this->input->post('sort'):0,
		);
		$this->load->model('borrowing_usage_model');
		$res = $this->borrowing_usage_model->insert_string($data);
		if($res){
			output_data();
		}else{
			output_error(-1,'添加失败');
		}
	}

	public function nature(){
		$result = array();
		$this->load->view('admin/operation/nature',$result);
	}

	public function deleteNature(){
		$id = $this->input->post('id');
		$this->load->model('Company_nature_model');
		$res = $this->Company_nature_model->delete_by_id($id);
		if($res){
			output_data();
		}else{
			output_error(-1,'删除出错');
		}
	}


	public function natureList(){
		$this->load->model('Company_nature_model');
		$page     = _get_page();
		$pagesize = 10;
		$prolist = $this->Company_nature_model->fetch_page($page, $pagesize, array(),'*','id asc');
		$pagecfg = array();
		$pagecfg['total_rows']   = $prolist['count'];
		$pagecfg['cur_page'] = $page;
		$pagecfg['per_page'] = $pagesize;
		$this->pagination->initialize($pagecfg);
		$prolist['pages'] = $this->pagination->create_links();
		output_data($prolist);
	}

	public function addNature(){
		$config = array(
			array(
				'field' => 'nature',
				'label' => '性质',
				'rules' => 'required|min_length[2]|max_length[30]',
			),
			array(
				'field' => 'sort',
				'label' => '排序',
				'rules' => 'integer',

			),
		);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() === false) {
			output_error(-2,'参数格式错误');
		}

		$data = array(
			'company_nature' => $this->input->post('nature'),
			'sort' =>$this->input->post('sort')?$this->input->post('sort'):0,
		);
		$this->load->model('Company_nature_model');
		$res = $this->Company_nature_model->insert_string($data);
		if($res){
			output_data();
		}else{
			output_error(-1,'添加失败');
		}
	}

}