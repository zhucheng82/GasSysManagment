<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Product extends MY_Admin_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('Borrowing_product_model');
		$this->load->model('Product_type_model');
	}

	public function productlist(){
		$pro_type = $this->Product_type_model->get_list();
		$this->load->view('admin/product/productlist',array('pro_type' =>$pro_type,'repay_type'=>config_item('RepaymentType')));

	}

	public function addproducttype(){
		$id = $this->input->get_post('id');
		$protype = array();
		if($id){
			$protype = $this->Product_type_model->get_by_id($id);
		}
		$this->load->view('admin/product/addprotype',array('protype' =>$protype));
	}

	public function producttype(){
		$this->load->view('admin/product/protypelist');
	}

	public function getProTypeList(){
		$where = array();

		$page     = _get_page();



		$pagesize = 10;
		$pagecfg = array();

		$prolist = $this->Product_type_model->fetch_page(
			$page,
			$pagesize,
			$where,'*','id asc');

		$pagecfg['total_rows']   = $prolist['count'];
		$pagecfg['cur_page'] = $page;
		$pagecfg['per_page'] = $pagesize;

		$this->pagination->initialize($pagecfg);
		$prolist['pages'] = $this->pagination->create_links();
		output_data($prolist);
	}

	public function deleteProductType(){

	}

	public function addProductTypeSubmit(){
		$config = array(
			array(
				'field' => 'product_name',
				'label' => '产品名称',
				'rules' => 'trim|required|max_length[50]',
			),
			array(
				'field' => 'desc',
				'label' => '描述',
				'rules' => 'trim|max_length[300]',
			),
			array(
				'field' => 'pro_id',
				'label' => 'id',
				'rules' => 'integer',
			)
		);
		$data = array(
			'name' => $this->input->post('product_name'),
			'desc' => $this->input->post('desc'),
			'createtime' => time(),
		);

		$id = $this->input->post('pro_id');

		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() === TRUE) {
			if($id){
				$res = $this->Product_type_model->update_by_id($id,$data);
				if($res){
					output_data();
				}else{
					output_error(-1,'修改产品类型失败!');
				}
			}else{
				$res = $this->Product_type_model->insert_string($data);
				if($res){
					output_data();
				}else{
					output_error(-1,'添加产品类型失败!');
				}
			}

		}else{
			output_error(-10,'数据格式错误');
		}
	}

	public function productdetail(){
		$id = $this->input->get('id');
		$res = $this->Borrowing_product_model->get_by_id($id);
		if(!$res){
			showMessage('没有这个产品',ADMIN_SITE_URL+'/product/productlist');
		}
		$res['pro_type'] =  $this->Product_type_model->get_list();;
		$res['repay_type'] = config_item('RepaymentType');
		$this->load->view('admin/product/prodetail',$res);
	}

	public function eidtproduct(){
		$id = $this->input->post('id');
		$config = array(
			array(
				'field' => 'product_name',
				'label' => '产品名称',
				'rules' => 'trim|required|max_length[100]',
			),
			array(
				'field' => 'pro_type',
				'label' => '产品类型',
				'rules' => 'required|greater_than[0]',
			),
			array(
				'field' => 'repay_type',
				'label' => '还款周期类型',
				'rules' => 'required|in_list[1,2,3]',
			),

			array(
				'field' => 'credit_limit',
				'label' => '最少额度',
				'rules' => 'required|integer|less_than_equal_to[10000]|greater_than_equal_to[0]',

			),
			array(
				'field' => 'credit_upper_limit',
				'label' => '最多额度',
				'rules' => 'integer|less_than_equal_to[100000]|greater_than_equal_to["credit_limit"]',

			),
			array(
				'field' => 'repayment_cycle_limit',
				'label' => '最少额度',
				'rules' => 'required|integer|less_than_equal_to[120]|greater_than_equal_to[1]',

			),
			array(
				'field' => 'repayment_cycle_upper_limit',
				'label' => '最多额度',
				'rules' => 'integer|less_than_equal_to[120]|greater_than_equal_to["repayment_cycle_limit"]',
			),
			array(
				'field' => 'rate',
				'label' => '利率',
				'rules' => 'required|numeric|less_than_equal_to[100]|greater_than[0]',
			),
			array(
				'field' => 'sort',
				'label' => '排序',
				'rules' => 'integer|less_than[10000]|greater_than_equal_to[0]',
			),
			array(
				'field' => 'bannar',
				'label' => '标语',
				'rules' => 'trim|required',
			),
			array(
				'field' => 'repayment_cycle_days',
				'label' => '周期',
				'rules' => 'integer|less_than[1000]|greater_than[0]',

			)
		,
			array(
				'field' => 'desc',
				'label' => '描述',
				'rules' => 'trim|max_length[200]',
			)

		);
		$this->form_validation->set_rules($config);



		if ($this->form_validation->run() === TRUE) {

			$p = $_POST;
			$day = $p['repayment_cycle_days'];
			if($p['repay_type'] == 1){
				$day = 30;
			}else if($p['repay_type'] == 2){
				$day = 7;
			}else{
				if(!$day){
					output_error(-2,'没有产品周期天数');
				}
			}
			$data = array(
				'product_name' => $p['product_name'],
				'productType' => $p['pro_type'],
				'credit_limit' => $p['credit_limit'],
				'credit_upper_limit' => $p['credit_upper_limit'],
				'repayment_cycle_limit' => $p['repayment_cycle_limit'],
				'repayment_cycle_upper_limit' => $p['repayment_cycle_upper_limit'],
				'rate' => $p['rate'],
				'describe' => $p['desc'],
				'sort' => $p['sort'],
				'product_banner' =>$p['bannar'],
				'createtime' =>time(),
				'repaymentType' => $p['repay_type'],
				'repayment_cycle_days'=>$day,
				'img_flag_url'=> $p['picture_url'],
			);
			$res = $this->Borrowing_product_model->update_by_id($id,$data);
			if($res){
				output_data();
			}else{
				output_error(-1,'修改产品失败!');
			}
		}else{
			log_message('debug',json_encode($this->form_validation->error_array()));
			output_error(-10,'数据格式错误');
		}
	}


	public function newproduct(){
		$pro_type = $this->Product_type_model->get_list();
		$this->load->view('admin/product/addpro',array('pro_type' =>$pro_type,'repay_type'=>config_item('RepaymentType')));
	}

	public function submitproduct(){
		$config = array(
			array(
				'field' => 'product_name',
				'label' => '产品名称',
				'rules' => 'trim|required|max_length[100]',
			),
			array(
				'field' => 'pro_type',
				'label' => '产品类型',
				'rules' => 'required|greater_than[0]',
			),
			array(
				'field' => 'repay_type',
				'label' => '还款周期类型',
				'rules' => 'required|in_list[1,2,3]',
			),

			array(
				'field' => 'credit_limit',
				'label' => '最少额度',
				'rules' => 'required|integer|less_than_equal_to[10000]|greater_than_equal_to[0]',

			),
			array(
				'field' => 'credit_upper_limit',
				'label' => '最多额度',
				'rules' => 'integer|less_than_equal_to[100000]|greater_than_equal_to["credit_limit"]',

			),
			array(
				'field' => 'repayment_cycle_limit',
				'label' => '最少额度',
				'rules' => 'required|integer|less_than_equal_to[120]|greater_than_equal_to[1]',

			),
			array(
				'field' => 'repayment_cycle_upper_limit',
				'label' => '最多额度',
				'rules' => 'integer|less_than_equal_to[120]|greater_than_equal_to["repayment_cycle_limit"]',
			),
			array(
				'field' => 'rate',
				'label' => '利率',
				'rules' => 'required|numeric|less_than_equal_to[100]|greater_than[0]',
			),
			array(
				'field' => 'sort',
				'label' => '排序',
				'rules' => 'integer|less_than[10000]|greater_than_equal_to[0]',
			),
			array(
				'field' => 'bannar',
				'label' => '标语',
				'rules' => 'trim|required',
			),
			array(
				'field' => 'repayment_cycle_days',
				'label' => '周期',
				'rules' => 'trim|integer|less_than[1000]|greater_than[0]',

			)
		,
			array(
				'field' => 'desc',
				'label' => '描述',
				'rules' => 'trim|max_length[200]',
			)

		);

		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() === TRUE) {
			$p = $_POST;
			$day = $p['repayment_cycle_days'];
			if($p['repay_type'] == 1){
				$day = 30;
			}else if($p['repay_type'] == 2){
				$day = 7;
			}else{
				if(!$day){
					output_error(-2,'没有产品周期天数');
				}
			}
			$data = array(
				'product_name' => $p['product_name'],
				'productType' => $p['pro_type'],
				'credit_limit' => $p['credit_limit'],
				'credit_upper_limit' => $p['credit_upper_limit'],
				'repayment_cycle_limit' => $p['repayment_cycle_limit'],
				'repayment_cycle_upper_limit' => $p['repayment_cycle_upper_limit'],
				'rate' => $p['rate'],
				'describe' => $p['desc'],
				'sort' => $p['sort'],
				'product_banner' =>$p['bannar'],
				'createtime' =>time(),
				'repaymentType' => $p['repay_type'],
				'repayment_cycle_days'=>$day,
				'img_flag_url'=> $p['picture_url'],
			);
			$res = $this->Borrowing_product_model->insert_string($data);

			if($res){
				output_data();
			}else{
				output_error(-1,'添加产品失败!');
			}
		}else{
			log_message('debug',validation_errors());
			output_error(-10,'数据格式错误');
		}



	}


	public function getProList(){
//        output_data();
		$where = array();

		$page     = $this->input->get_post('page');
		if(empty($page)){
			$page = 1;
		}
		$pagesize = 10;
		$pro_type = $this->input->post('pro_type');
		$status = $this->input->post('pro_status');
		$pro_name = $this->input->post('pro_name');
		if($pro_type){
			$where['productType'] = $pro_type;
		}
		if($status){
			$where['status'] = $status;
		}
		if( $pro_name){
			$where['product_name like'] = "'%$pro_name%'";
		}



		$pagecfg = array();

		$prolist = $this->Borrowing_product_model->fetch_page(
			$page,
			$pagesize,
			$where,'*','is_recommend desc,sort desc');

		$pagecfg['total_rows']   = $prolist['count'];
		$pagecfg['cur_page'] = $page;
		$pagecfg['per_page'] = $pagesize;

		$this->pagination->initialize($pagecfg);
		$prolist['pages'] = $this->pagination->create_links();
		output_data($prolist);

	}

	public function changeStatus(){
		$id = $this->input->post('id');
		$status = (int)$this->input->post('status');
		if(!in_array($status,[-1,1]) ){
			output_error(-1,'非法的状态');
		}
		$res = $this->Borrowing_product_model->update_by_id($id,array('status'  => $status));
		if($res){
			output_data(array('id' => $id,'status' =>$status));
		}else{
			output_error(-1,'状态修改失败');
		}
	}

	public function changeRecommend(){
		$id = $this->input->post('id');
		$status = (int)$this->input->post('recommend_status');
		if(!in_array($status,[0,1]) ){
			output_error(-1,'非法的状态');
		}
		$res = $this->Borrowing_product_model->update_by_id($id,array('is_recommend'  => $status));
		if($res){
			output_data(array('id' => $id,'is_recommend' =>$status));
		}else{
			output_error(-1,'推荐设置失败');
		}
	}

	public function export()
	{
		$list = $this->Borrowing_product_model->get_list();
		$data = array();
		foreach ($list as $key => $value) {
			$data[$key] = array(
				'product_name' => $value['product_name'],
				'productType' => C('ProductType.'.$value['productType']),
				'credit_limit' => '￥'.$value['credit_limit'].' - ￥'.$value['credit_upper_limit'],
				'repayment_cycles' => $value['repayment_cycle_limit'].'期 - '.$value['repayment_cycle_upper_limit'].'期',
				'repaymentType' =>  C('RepaymentType.'.$value['repaymentType']),
			);
			switch ($value['repaymentType']) {
				case '1':
					$data[$key]['repayment_date'] = '一个月';
					break;
				case '2':
					$data[$key]['repayment_date'] = '一周';
					break;
				case '3':
					$data[$key]['repayment_date'] = $value['repayment_cycle_days'];
					break;
				default :
					$data[$key]['repayment_date'] = '';
					break;
			}
			$data[$key]['rate'] = $value['rate'].'%';
			$data[$key]['status'] = $value['status']==-1?'下线':'上线';
		}
		$header = array('产品名称','产品类型','贷款金额','还款周期','周期类型','周期天数','利率','状态');

		$this->load->library('excel');
		$this->excel->createExcel('产品清单',$header,$data);
	}

}