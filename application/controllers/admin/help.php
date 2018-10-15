<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Help extends MY_Admin_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('Help_model');
	}

	public function index()
	{
		
		$help_status = $this->input->post_get('help_status');
		$help_name = $this->input->post_get('help_name');
		$page = _get_page();
        $pagesize = 20;

        $arrWhere = array();
        $arrParam = array();
        if ($help_status) {
        	$arrWhere['status'] = "$help_status";
        	$arrParam['help_status'] = $help_status;
        }
        if ($help_name) {
        	$arrWhere['title like '] = "'%$help_name%'";
        	$arrParam['help_name'] = $help_name;
        }
		$list = $this->Help_model->fetch_page($page,$pagesize,$arrWhere,'*','sort desc');
		 //分页
        $pagecfg = array();
        $pagecfg['base_url']     = _create_url(ADMIN_SITE_URL.'/help', $arrParam);
        $pagecfg['total_rows']   = $list['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;
         
        $this->pagination->initialize($pagecfg);
        $list['pages'] = $this->pagination->create_links();

        $result = array(
        	'list' => $list,
        	'arrParam' => $arrParam,
        );
		$this->load->view('admin/help/lists',$result);
	}

	public function add()
	{
		$id = $this->input->get('id');

		if (is_numeric($id) && $id>0) {
			$data = $this->Help_model->get_by_id($id);
		} else {
			$data = array();
		}
		$result = array(
			'data'=>$data,
			);
		$this->load->view('admin/help/add',$result);
	}

	public function add_do()
	{
		$title = $this->input->post('question');
		$content = $this->input->post('answer');
		$id = $this->input->post('id');
		$sort = $this->input->post('sort');
		$data = array(
			'title' => $title,
			'content' => $content,
			'sort' => $sort,
		);
		if ($id) {
			if ($this->Help_model->update_by_id($id,$data)) {
				showMessage('操作成功',ADMIN_SITE_URL.'/help');
			} else {
				showMessage('操作失败');
			}
		} else {
			$data['createtime'] = time();
			if ($this->Help_model->insert($data)) {
				showMessage('操作成功',ADMIN_SITE_URL.'/help');
			} else {
				showMessage('操作失败');
			}
		}
	}

	public function update_status()
	{
		$id = $this->input->post('id');
		$status = $this->input->post('status');

		$arrReturn = array('status'=>'','msg'=>'');
		if ($this->Help_model->update_by_id($id,array('status' => $status))) {
			$arrReturn = array('status'=>'1','msg'=>'操作成功');
		} else {
			$arrReturn = array('status'=>'-1','msg'=>'操作失败');
		}

		echo json_encode($arrReturn);
	}

}