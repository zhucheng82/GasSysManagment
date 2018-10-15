<?php defined('BASEPATH') OR exit('No direct script access allowed');


class User extends MY_Admin_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('User_model');
	}

	public function index(){
		$this->load->view('admin/user/userlist');

	}

	public function details(){

		$id     = $this->input->get_post('id');
		$work = (int)$this->input->get_post('work');
		$where = 'a.user_id = '.$id;
		$dbprefix = $this->User_model->db->dbprefix;

		if($work=== 0){
			$tb = $dbprefix.'user a left join '.$dbprefix.'student b on (a.user_id=b.user_id) left join '.$dbprefix.'additional_user_info c on (a.user_id=c.user_id)';
			$field = 'a.user_name as name,a.work,a.portrait,a.authentication_status,a.additional_info_status,b.*,c.*';
		}else if($work === 1){
			$tb = $dbprefix.'user a left join '.$dbprefix.'employee b on (a.user_id=b.user_id) left join '.$dbprefix.'additional_user_info c on (a.user_id=c.user_id)';
			$field = 'a.user_name as name,a.work,a.portrait,a.authentication_status,a.additional_info_status,b.*,c.*';
		}else{
			$tb = $dbprefix.'user a left join '.$dbprefix.'additional_user_info b on (a.user_id=b.user_id)';

			$field = 'a.user_name as name,a.work,a.portrait,a.authentication_status,a.additional_info_status,b.*';
		}

		$data = $this->User_model->get_by_where_tb($where,$field,$tb);

		foreach($data as $k =>&$v){
			if(!$data[$k]){
				if (!is_numeric($data[$k])) {
					$data[$k] = '';
				}
			}
		}
		if(!isset($data['user_name'])){
			$data['user_name'] = '';
			$data['user_identify_card'] = '';
			$data['school_name'] = '';
			$data['company_name'] = '';
			$data['company_name'] = '';
			$data['education'] = '';
			$data['entry_time'] = '';
		}

		$this->load->model('Borrowing_model');
		$where.= ' and a.status > -1';
		$tb = $dbprefix.'borrowing a left join '.$dbprefix.'borrowing_product b on (a.borrowing_product_id=b.id)';
		$field = 'a.*,b.product_name';
		$list = $this->Borrowing_model->get_by_where_tb_list($where,$field,$tb);
		foreach($list as $k =>&$v){
			$v['status'] = config_item('BorrowingStatus')[$v['status']];
		}
		$data['blist'] = $list;
 		$this->load->view('admin/user/userdetail',$data);
	}

	public function resetAuthenticationStatus(){
		$user_id =(int)$this->input->post('user_id');
		$status =  (int)$this->input->post('status');
		if($status ==0 || $status==1){
			$res  = $this->User_model->update_by_id($user_id,array('authentication_status'=>$status));
			if($res){
				output_data();
			}else{
				output_error(-1,'状态修改失败');
			}
		}else{
			output_error(-2,'参数错误');
		}
	}

	public function getUserList(){
		$where = array();

		$page     = $this->input->get_post('page');
		if(empty($page)){
			$page = 1;
		}
		$pagesize = 10;
		$work = $this->input->post('work');
		$a_status = $this->input->post('a_status');
		$user_name = $this->input->post('user_name');
		if($work !== '' && $work !== null){
			$where['a.work'] = $work;
		}
		if($a_status !== ''&& $a_status !== null){
			$where['a.authentication_status'] = $a_status;
		}
		if( $user_name){
			$where['a.user_name like'] = "'%$user_name%'";
		}



		$pagecfg = array();

		$dbprefix = $this->User_model->db->dbprefix;


		$tb = $dbprefix.'user a left join '.
			$dbprefix.'student c on (a.user_id=c.user_id) left join '.
			$dbprefix.'employee b on (a.user_id=b.user_id) left join '.
			$dbprefix.'additional_user_info d on (a.user_id=d.user_id)';
		$field = 'a.user_id as uid,a.user_name as name,a.work,a.authentication_status,a.additional_info_status,a.createtime,a.longitude,a.latitude,a.lacation_addr,a.location_time,a.balance,'.
			'b.user_name as work_user_name,b.user_identify_card as work_user_identify_card,b.company_name as work_company_name,'.
			'b.company_type as work_company_type,b.post as work_post,b.entry_time as work_entry_time,d.*,'.
			'c.user_name,c.user_identify_card,c.school_name,c.education,c.entry_time';

		$prolist = $this->User_model->fetch_page(
			$page,
			$pagesize,
			$where,$field,'uid desc',$tb);

		$pagecfg['total_rows']   = $prolist['count'];
		$pagecfg['cur_page'] = $page;
		$pagecfg['per_page'] = $pagesize;

		$this->pagination->initialize($pagecfg);
		$prolist['pages'] = $this->pagination->create_links();
		output_data($prolist);
	}

	public function export()
	{
		$dbprefix = $this->User_model->db->dbprefix;


			$tb = $dbprefix.'user a left join '.
				$dbprefix.'student c on (a.user_id=c.user_id) left join '.
				$dbprefix.'employee b on (a.user_id=b.user_id) left join '.
				$dbprefix.'additional_user_info d on (a.user_id=d.user_id)';
			$field = 'a.user_name as name,a.work,a.authentication_status,a.additional_info_status,a.createtime,'.
			'b.user_name as work_user_name,b.user_identify_card as work_user_identify_card,b.company_name as work_company_name,'.
			'b.company_type as work_company_type,b.post as work_post,b.entry_time as work_entry_time,c.*,d.*';


		$list = $this->User_model->get_by_where_tb_list('',$field,$tb);
		$work = array('0'=>'学生','1'=>'白领');
		$data = array();
		foreach ($list as $key => &$value) {

				$b = array(
				'user_name' => $value['name'],
				'work' => array_key_exists($value['work'], $work)?$work[$value['work']]:'未知',
				'authentication_status' => $value['authentication_status'] == 1?'已认证':($value['authentication_status'] == 2?'审核中':'未认证'),
				'createtime' => date('Y-m-d H:i',$value['createtime']),
				
			);
            if($value['work'] == 0 || $value['work'] == 1){
                if($value['work'] == 0){
                    $b['name'] = $value['user_name'];//真实姓名
                    $b['id_card'] = "'".$value['user_identify_card'];//身份证
                    $b['work_at'] = $value['school_name'];//学校
                    $b['position'] = $value['education'];//学历
//					$b['entry_time'] =  date('Y-m-d H:i',strtotime($value['entry_time']));//入学时间
                    $b['entry_time'] =  $value['entry_time'];//入学时间
                }else if($value['work'] == 1){
                    $b['name'] = $value['work_user_name'];//真实姓名
                    $b['id_card'] = "'".$value['work_user_identify_card'];//身份证
                    $b['work_at'] = $value['work_company_name'];//学校
                    $b['position'] = $value['work_post'];//学历
//					$b['entry_time'] = date('Y-m-d H:i',$value['work_entry_time']);//入学时间
                    $b['entry_time'] = $value['work_entry_time'];
                }
				$b['linkman_name1'] = $value['linkman_name1'];//联系人1 父亲
				$b['linkman_tel1'] = $value['linkman_tel1'];//
				$b['linkman_name2'] = $value['linkman_name2'];//联系人1 母亲
				$b['linkman_tel2'] = $value['linkman_tel2'];//
				$b['linkman_name3'] = $value['linkman_name3'];//联系人1 同事/同学
				$b['linkman_tel3'] = $value['linkman_tel3'];//
				$b['linkman_name4'] = $value['linkman_name4'];//联系人1 朋友
				$b['linkman_tel4'] = $value['linkman_tel4'];//
				$b['address1'] = $value['province_name'].$value['city_name'].$value['district_name'].$value['address'];//
				$b['address2'] = $value['cur_province_name'].$value['cur_city_name'].$value['cur_district_name'].$value['cur_address'];//
				$b['com_phone'] = $value['company_tel'];//
			}else{
				$b['name'] = '';//真实姓名
				$b['id_card'] = '';//身份证
				$b['work_at'] = '';//学校
				$b['position'] = '';//学历
				$b['entry_time'] = '';//入学时间
				$b['linkman_name1'] = '';//联系人1 父亲
				$b['linkman_tel1'] = '';//
				$b['linkman_name2'] = '';//联系人1 母亲
				$b['linkman_tel2'] = '';//
				$b['linkman_name3'] = '';//联系人1 同事/同学
				$b['linkman_tel3'] = '';//
				$b['linkman_name4'] = '';//联系人1 朋友
				$b['linkman_tel4'] = '';//
				$b['address1'] = '';//
				$b['address2'] = '';//
				$b['com_phone'] = '';//
			}
			$data[] = $b;
		}
		$header = array('手机号码','职业','认证状态','注册时间','真实姓名','身份证号码','学校/公司','学历/职位','入学时间/入职时间',
			'父亲姓名',
			'父亲电话',
			'母亲姓名',
			'母亲电话',
			'同学/同事姓名',
			'同学/同事电话',
			'朋友姓名',
			'朋友电话',
			'家庭住址',
			'现住址',
			'学校/公司电话',
			);

		$this->load->library('excel');
		$this->excel->createExcel('用户',$header,$data);
	}


}