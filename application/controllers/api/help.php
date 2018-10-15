<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Help extends ApiController
{
	public function __construct()
    {
        parent::__construct();
        $this->load->model('Help_model');
    }

    //帮助列表
    public function index()
    {
    	$data = $this->Help_model->get_list(array('status'=>1,),'id,title question','sort desc');
    	output_data($data);
    }

    //帮助详情
    public function detail()
    {
    	$id = $this->input->post('id');
    	if (!is_numeric($id) || $id<=0) {
    		output_error(-1,'参数错误');
    	}
    	$data = $this->Help_model->get_by_id($id,'id,title question,content answer');
    	output_data($data);
    }
	
}