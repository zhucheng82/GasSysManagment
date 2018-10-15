<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Report extends MY_Admin_Controller{

	public function __construct(){
		parent::__construct();
	}

	public function index(){
		$this->load->view('admin/report/index');

	}


	public function productReport(){
		$this->load->model('Borrowing_model');
		$sql = 'select count(a. borrowing_product_id) as value , b.product_name as name,b.id  from change_borrowing as a left join change_borrowing_product as b on (a.borrowing_product_id = b.id) group by b. id';
		$arr = $this->Borrowing_model->execute_array($sql);
		output_data($arr);

	}
/*
 * 	年龄分布统计
 * */
	public function getBrithdayReport(){
		$mytime1 = date('Y-m-d',strtotime("-18 year"));
		$mytime1 = strtotime($mytime1);

		$mytime2 = date('Y-m-d',strtotime("-18 year -3month"));
		$mytime2 = strtotime($mytime2);

		$mytime3 = date('Y-m-d',strtotime("-18 year -6month"));
		$mytime3 = strtotime($mytime3);

		$mytime4 = date('Y-m-d',strtotime("-18 year -12month"));
		$mytime4 = strtotime($mytime4);
		$this->load->model('ID_extract_info_Model');
		$work = $this->input->get_post('work');
		if(in_array($work,array('0','1'))){

		}else{
			$work = 0;
		}
		$arr = $this->ID_extract_info_Model->execute('select
		count(if(birthday < '.$mytime1.',true,null))as value1 ,
		count(if(birthday <'.$mytime2.' and birthday >='.$mytime1.',true,null)) as value2,
		count(if(birthday <'.$mytime3.' and birthday >='.$mytime2.',true,null)) as value3,
		count(if(birthday <'.$mytime4.' and birthday >='.$mytime3.',true,null)) as value4,
		count(if(birthday >='.$mytime4.',true,null)) as value5
		from change_ID_extract_info  where work = '.$work);
		$data = array();
		$namearr = array(
			'1' => '18周岁以上',
			'2' => '3月内满18周岁',
			'3' => '3-6月内满18周岁',
			'4' => '6-12月内满18周岁',
			'5' => '其他',
		);
		for($i = 1 ;$i < 6;$i++){
			$a = array(
				'name'=>$namearr[$i.''],
				'value'=>$arr['value'.$i],
				'type' => $i
			);
			array_push($data,$a);
		}

		output_data($data);

	}
	/*
	 * 入学时间,或者入职时间
	 * */
	public function getEnterSchoolReport(){
		$year = array();
		$time = array();
		$work = $this->input->get_post('work');
		$ty = '';
		if(in_array($work,array('0','1'))){
			if($work == 0){
				$ty = '入学';
			}else{
				$ty = '入职';
			}
		}else{
			$work = 0;
		}
		for($i = 0;$i < 8;$i++){
			$mytime1 = date('Y',strtotime("-$i year"));
			array_push($year,$mytime1.'年'.$ty);
			array_push($time,$mytime1);
		}
		array_push($year,'其他年份'.$ty);


		$ex = 'select ';
		foreach($time as $k => $v){
			$ex.='count(if(entry_time = '.$v.',true,null)) as value'.$k.' ,';
		}
		$ex.= 'count(if(entry_time < '.$v.',true,null)) as value'.($k+1);
		$ex .= ' from change_user as a left join change_student as b on(a.user_id = b.user_id) where a.work = '.$work;
		$this->load->model('ID_extract_info_Model');
		$arr = $this->ID_extract_info_Model->execute($ex);
		$data = array();
		for($i = 0 ;$i <= 8;$i++){
			$a = array(
				'name'=>$year[$i.''],
				'value'=>$arr['value'.$i],
				'year'=>$i
			);
			array_push($data,$a);
		}
		output_data($data);
	}

	public function getGrenderReport(){
		$this->load->model('ID_extract_info_Model');
		$work = $this->input->get_post('work');
		if(in_array($work,array('0','1'))){

		}else{
			$work = 0;
		}
		$arr = $this->ID_extract_info_Model->execute_array('select count(a.user_id) as count,a.gender from change_ID_extract_info as a where a.work = '.$work.' group by a.gender  ');
		foreach($arr as $k => &$v){
			$v['value'] = $v['count'];
			if($v['gender'] == 1){
				$v['name'] = '男';
			}else if($v['gender'] == 2){
				$v['name'] = '女';
			}else{
				$v['name'] = '未知';
			}
		}
		output_data($arr);
	}

	//家庭住址分布
	public function getProvinceReportlist(){
		$this->load->model('Additional_user_info_model');
		$work = $this->input->get_post('work');
		if(in_array($work,array('0','1'))){

		}else{
			$work = 0;
		}
//		$arr = $this->Additional_user_info_model->execute_array('select count(a.user_id) as count,a.province_id,b.name from change_additional_user_info as a left join change_sys_area b on (a.province_id = b.id) group by a.province_id ');
		$arr = $this->Additional_user_info_model->execute_array('select count(a.user_id) as count,b.province_id,c.name from change_user as a left join change_additional_user_info as b on(a.user_id = b.user_id) left join change_sys_area c on (b.province_id = c.id) where a.work = '.$work.' group by b.province_id ');


		foreach($arr as $k => &$v){
			$v['value'] = $v['count'];
			$v['id'] = $v['province_id'];
		}
		output_data($arr);
	}

	public function getCityReportlist(){
		$this->load->model('Additional_user_info_model');
		$this->load->model('Area_model');
		$pid = $this->input->post_get('pid');
		if(!(int)$pid){
//			$arr = $this->Additional_user_info_model->execute_array('select count(a.user_id) as count,b.city_id,c.name from change_user as a left join change_additional_user_info as b on(a.user_id = b.user_id) left join change_sys_area c on (b.city_id = c.id) where b.province_id = '.$pid.' and a.work = '.$work.' group by b.city_id ')
			output_error(-1,'参数错误');
		}
		$this->load->model('ID_areacode_model');
		$arr = $this->Area_model->get_by_id($pid);
		if(!$arr){
			output_error(-2,'没有这个市');
		}
		$work = $this->input->get_post('work');
		if(in_array($work,array('0','1'))){

		}else{
			$work = 0;
		}

//		$arr = $this->Additional_user_info_model->execute_array('select count(a.user_id) as count,a.city_id,b.name from change_additional_user_info as a left join change_sys_area b on (a.city_id = b.id) where a.province_id = '.$pid.' group by a.city_id ');
		$arr = $this->Additional_user_info_model->execute_array('select count(a.user_id) as count,b.city_id,c.name from change_user as a left join change_additional_user_info as b on(a.user_id = b.user_id) left join change_sys_area c on (b.city_id = c.id) where b.province_id = '.$pid.' and a.work = '.$work.' group by b.city_id ');



		foreach($arr as $k => &$v){
			$v['value'] = $v['count'];
			$v['id'] = $v['city_id'];
		}
		output_data($arr);
	}


	public function getDistrictReportlist(){
		$this->load->model('Additional_user_info_model');
		$this->load->model('Area_model');
		$pid = $this->input->post_get('pid');
		if(!(int)$pid){
			output_error(-1,'参数错误');
		}
		$this->load->model('ID_areacode_model');
		$arr = $this->Area_model->get_by_id($pid);
		if(!$arr){
			output_error(-2,'没有这个区');
		}
		$work = $this->input->get_post('work');
		if(in_array($work,array('0','1'))){

		}else{
			$work = 0;
		}
//		$arr = $this->Additional_user_info_model->execute_array('select count(a.user_id) as count,a.district_id,b.name from change_additional_user_info as a left join change_sys_area b on (a.district_id = b.id) where a.city_id = '.$pid.' group by a.district_id ');
		$arr = $this->Additional_user_info_model->execute_array('select count(a.user_id) as count,b.district_id,c.name from change_user as a left join change_additional_user_info as b on(a.user_id = b.user_id) left join change_sys_area c on (b.district_id = c.id) where b.city_id = '.$pid.' and a.work = '.$work.' group by b.district_id ');

		foreach($arr as $k => &$v){
			$v['value'] = $v['count'];
			$v['id'] = $v['district_id'];
		}
		output_data($arr);
	}

	//身份证地区分布
	public function getIDProvinceReportlist(){
		$this->load->model('ID_extract_info_Model');
		$work = $this->input->get_post('work');
		if(in_array($work,array('0','1'))){

		}else{
			$work = 0;
		}
		$arr = $this->ID_extract_info_Model->execute_array('select count(a.user_id) as count,a.province_code,b.desc from change_ID_extract_info as a left join change_id_areacode b on (a.province_code = b.zone) where a.work = '.$work.' group by a.province_code ');
		foreach($arr as $k => &$v){
			$v['value'] = $v['count'];
			$v['name'] = $v['desc'];
			$v['id'] = $v['province_code'];
		}
		output_data($arr);
	}

	public function getIDCityReportlist(){
		$this->load->model('ID_extract_info_Model');
		$this->load->model('ID_areacode_model');
		$pid = $this->input->post_get('pid');
		if(!(int)$pid){
			output_error(-1,'参数错误');
		}
		$this->load->model('ID_areacode_model');
		$arr = $this->ID_areacode_model->get_by_where(array('zone' =>$pid));
		if(!$arr){
			output_error(-2,'没有这个省');
		}
		$work = $this->input->get_post('work');
		if(in_array($work,array('0','1'))){

		}else{
			$work = 0;
		}
		$this->load->model('ID_extract_info_Model');
		$arr = $this->ID_extract_info_Model->execute_array('select count(a.user_id) as count,a.city_code,b.desc from change_ID_extract_info as a left join change_id_areacode b on (a.city_code = b.zone) where a.province_code = '.$pid.' and a.work = '.$work.'  group by a.city_code ');
		foreach($arr as $k => &$v){
			$v['value'] = $v['count'];
			$v['name'] = $v['desc'];
			$v['id'] = $v['city_code'];
		}
		output_data($arr);
	}

	public function getIDDistrictReportlist(){
		$this->load->model('ID_extract_info_Model');
		$this->load->model('ID_areacode_model');
		$pid = $this->input->post_get('pid');
		if(!(int)$pid){
			output_error(-1,'参数错误');
		}
		$arr = $this->ID_areacode_model->get_by_where(array('zone' =>$pid));
		if(!$arr){
			output_error(-2,'没有这个市');
		}

		$this->load->model('ID_extract_info_Model');

		$work = $this->input->get_post('work');
		if(in_array($work,array('0','1'))){

		}else{
			$work = 0;
		}
		$arr = $this->ID_extract_info_Model->execute_array('select count(a.user_id) as count,a.district_code,b.desc from change_ID_extract_info as a left join change_id_areacode b on (a.district_code = b.zone) where a.city_code = '.$pid.' and a.work = '.$work.' group by a.district_code ');
		foreach($arr as $k => &$v){
			$v['value'] = $v['count'];
			$v['name'] = $v['desc'];
			$v['id'] = $v['district_code'];
		}
		output_data($arr);
	}







	public function apply_statistics(){
        $this->load->view('admin/report/apply_statistics',array('borrowingStatus'=>config_item('BorrowingStatus')));
    }


	public function getUserList(){
		$where = array();
		$this->load->model('User_model');
		$page     = $this->input->get_post('page');
		if(empty($page)){
			$page = 1;
		}
		$pagesize = 10;
		$config = array(
			array(
				'field' => 'room_id',
				'label' => '职业',
				'rules' => 'in_list[0,1]',
			),
			array(
				'field' => 'cur_pid',
				'label' => '家庭住址省份',
				'rules' => 'integer',

			),
			array(
				'field' => 'cur_cid',
				'label' => '家庭住址市',
				'rules' => 'integer',

			),
			array(
				'field' => 'id_pid',
				'label' => '祖籍省份',
				'rules' => 'integer',

			),
			array(
				'field' => 'id_cid',
				'label' => '祖籍市',
				'rules' => 'integer',

			),
			array(
				'field' => 'gender',
				'label' => '性别',
				'rules' => 'in_list[0,1,2]',

			),
			array(
				'field' => 'btype',
				'label' => '年龄段',
				'rules' => 'in_list[1,2,3,4,5]',

			),
			array(
				'field' => 'ytype',
				'label' => '入学时间',
				'rules' => 'in_list[0,1,2,3,4,5,6,7,8]',

			),

		);
		$this->form_validation->set_rules($config);
		if ($this->form_validation->run() === false) {
			output_error(-2,'参数格式错误');
		}
		$work = $this->input->post('work');
		$cur_pid = $this->input->post('cur_pid');
		$cur_cid = $this->input->post('cur_cid');
		$id_pid = $this->input->post('id_pid');
		$id_cid = $this->input->post('id_cid');
		$gender = $this->input->post('gender');
		$btype = $this->input->post('btype');
		$ytype = $this->input->post('ytype');
		$product = $this->input->post('product');
		if($work !== '' && $work !== null){
			$where['a.work'] = $work;
		}
		if($cur_pid !== '' && $cur_pid !== null){
			$where['d.province_id'] = $cur_pid;
		}

		if($cur_cid !== '' && $cur_cid !== null){
			$where['d.city_id'] = $cur_cid;
		}

		if($id_pid !== '' && $id_pid !== null){
			$where['e.province_code'] = $id_pid;
		}

		if($id_cid !== '' && $id_cid !== null){
			$where['e.city_code'] = $id_cid;
		}

		if($gender !== '' && $gender !== null){
			$where['e.gender'] = $gender;
		}
		if($work == 0){
			if($ytype <=7 && $ytype>0){
				$year = date('Y');
				$where['c.entry_time'] = $year -$ytype;
			}else if($ytype == 8){

			}
		}





		$mytime1 = date('Y-m-d',strtotime("-18 year"));
		$mytime1 = strtotime($mytime1);

		$mytime2 = date('Y-m-d',strtotime("-18 year -3month"));
		$mytime2 = strtotime($mytime2);

		$mytime3 = date('Y-m-d',strtotime("-18 year -6month"));
		$mytime3 = strtotime($mytime3);

		$mytime4 = date('Y-m-d',strtotime("-18 year -12month"));
		$mytime4 = strtotime($mytime4);

		if($btype == 1){
			$where['e.birthday <'] = $mytime1;
		}else if($btype ==2){
			$where['e.birthday >='] = $mytime1;
			$where['e.birthday <'] = $mytime2;
		}else if($btype ==3){
			$where['e.birthday >='] = $mytime2;
			$where['e.birthday <'] = $mytime3;
		}else if($btype == 4){
			$where['e.birthday >='] = $mytime3;
			$where['e.birthday <'] = $mytime4;
		}else if($btype == 5){
			$where['e.birthday >='] = $mytime4;
		}

		$pagecfg = array();

		$dbprefix = $this->User_model->db->dbprefix;


		$tb = $dbprefix.'user a left join '.
			$dbprefix.'student c on (a.user_id=c.user_id) left join '.
			$dbprefix.'employee b on (a.user_id=b.user_id) left join '.
			$dbprefix.'additional_user_info d on (a.user_id=d.user_id) left join '.
			$dbprefix.'ID_extract_info e on (a.user_id=e.user_id)';
//			if($product){
//				$tb.= ' left join change_borrowing as f on ('
//			}
		$field = 'a.user_name as name,a.work,a.authentication_status,a.additional_info_status,a.createtime,'.
			'b.user_name as work_user_name,b.user_identify_card as work_user_identify_card,b.company_name as work_company_name,'.
			'b.company_type as work_company_type,b.post as work_post,b.entry_time as work_entry_time,d.*,'.
			'c.user_name,c.user_identify_card,c.school_name,c.education,c.entry_time,e.province_code,e.city_code,e.district_code,e.birthday,e.gender';

		$prolist = $this->User_model->fetch_page(
			$page,
			$pagesize,
			$where,$field,'',$tb);

		$pagecfg['total_rows']   = $prolist['count'];
		$pagecfg['cur_page'] = $page;
		$pagecfg['per_page'] = $pagesize;

		$this->pagination->initialize($pagecfg);
		$prolist['pages'] = $this->pagination->create_links();
		output_data($prolist);
	}





}