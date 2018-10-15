<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Finance extends MY_Admin_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->model('Borrowing_model');
	}

	public function index(){
		$this->load->view('admin/finance/orderlist',array('borrowingStatus'=>config_item('BorrowingStatus')));

	}

	public function details(){
		$id = $this->input->get('id');
		$work = $this->input->get_post('work');
		$this->load->model('Borrowing_model');
		$this->load->model('Ebike_info_model');
		$this->load->model('Renting_house_info');
		$this->load->model('User_model');

		$where = 'a.id ='.$id;
		$dbprefix = $this->Borrowing_model->db->dbprefix;
		$tb = $dbprefix.'borrowing a left join '.$dbprefix.'borrowing_product b on (a.borrowing_product_id=b.id) ';
		$field = 'a.*,b.product_name,b.productType';
		$order = $this->Borrowing_model->get_by_where_tb($where,$field,$tb);
		$order['pro_type'] = $order['productType'];
		if ($order['pro_type'] == 2) {
			$order['ebike_info'] = $this->Ebike_info_model->get_by_where('borrowing_id='.$order['id']);
		} elseif($order['pro_type'] == 3) {
			$order['renting_house_info'] = $this->Renting_house_info->get_by_where('borrowing_id='.$order['id']);
		}
		$order['productType'] = config_item('ProductType')[$order['productType']];
		$order['status_name'] = config_item('BorrowingStatus')[$order['status']];
		if(empty($order)){
			showMessage('没有找到记录',ADMIN_SITE_URL+'/finance/index');
		}
		$where = 'a.user_id = '.$order['user_id'].'';
		if($work=== '0'){
			$tb = $dbprefix.'user a left join '.$dbprefix.'student b on (a.user_id=b.user_id) ';
			$field = 'a.user_name as name,a.work,a.portrait,a.authentication_status,a.additional_info_status,b.*';
		}else if($work === '1'){
			$tb = $dbprefix.'user a left join '.$dbprefix.'employee b on (a.user_id=b.user_id)';
			$field = 'a.user_name as name,a.work,a.portrait,a.authentication_status,a.additional_info_status,b.*';
		}else{
			$tb = $dbprefix.'user a left join '.$dbprefix.'additional_user_info b on (a.user_id=b.user_id)';

			$field = 'a.user_name as name,a.work,a.portrait,a.authentication_status,a.additional_info_status,b.*';
		}
		$data = $this->User_model->get_by_where_tb($where,$field,$tb);
		$data['order'] = $order;
		$this->load->view('admin/finance/orderdetail',$data);
	}

	public function getOrderList(){
		$where = array();

		$page     = $this->input->get_post('page');
		if(empty($page)){
			$page = 1;
		}
		$pagesize = 10;
		$starttime = (int)$this->input->post('start_time');
		$endtime = (int)$this->input->post('end_time');
		$user_name = $this->input->post('user_name');
		$status = $this->input->post_get('status');

		if($starttime){
			$where['a.applied_time >= '] = $starttime;
		}
		if($endtime){
			$where['a.applied_time <'] = $endtime;
		}
		if( $user_name){
			$where['a.user_name like'] = "'%$user_name%'";
		}
//
		if(isset($status) && $status !== ''){
			if($status <1){
				$where['a.status'] = $status;
			}else{
				$where['a.status > '] = 0;
			}
		}
//




		$pagecfg = array();
		$dbprefix = $this->Borrowing_model->db->dbprefix;
		$tb = $dbprefix.'borrowing a left join '.$dbprefix.'user b on(a.user_id=b.user_id)';
		$prolist = $this->Borrowing_model->fetch_page(
			$page,
			$pagesize,
			$where,'a.*,b.work,b.user_name as mobile','a.applied_time desc',$tb);
		$pagecfg['total_rows']   = $prolist['count'];
		$pagecfg['cur_page'] = $page;
		$pagecfg['per_page'] = $pagesize;

		$this->pagination->initialize($pagecfg);
		$prolist['pages'] = $this->pagination->create_links();

        //$borrowCount = $this->Borrowing_model->get_count($where);
		$where = array();
		if($starttime){
			$where['applied_time >= '] = $starttime;
		}
		if($endtime){
			$where['applied_time <'] = $endtime;
		}
		if( $user_name){
			$where['user_name like '] = "'%.$user_name.%'";
		}
//
		if(isset($status) && $status !== ''){
			if($status <1){
				$where['status = '] = $status;
			}else{
				$where['status > '] = 0;
			}
		}
		$report = $this->Borrowing_model->get_by_where($where,'sum(apply_amount) as apply,sum(approve_amount) as approve,sum(detain_amount) as detain,sum(poundage) as poundage');
		$report['total_count'] =$prolist['count'] ;
		$report['apply'] = round($report['apply'] ,2);
		$report['approve'] = round($report['approve'] ,2);
		$report['detain'] = round($report['detain'] ,2);
        $prolist['report'] = $report;
		output_data($prolist);
	}

	public function orderdeal(){


		$id = $this->input->post('id');
		$type = $this->input->post('type');
		$money = (float)$this->input->post('money');
		$remark = $this->input->post('remark');
		$remark = substr($remark,0,100);

		if(!$id || !$type){
			output_error(-20,'参数错误');
		}

		$order= $this->Borrowing_model->get_by_id($id);
		if(!$order){
			output_error(-2,'借款不存在');
		}
		if($order['status'] != 0){
			output_error(-3,'订单已经处理过了');
		}
		if($type == 1){
			if(!$money || $money<= 0 || $money>(float)$order['apply_amount']){
				output_error(-3,'金额不对');
			}
			$data = array(
				'status' =>1,
				'approved_time' =>time(),
				'approve_amount' =>$money,
				'remarks' =>'',
				'detain_amount' => $money*C('deposit_rate')/100,
				'poundage' => $money*C('procedure_fee')/100,
			);
			$real_amount = round($money-$data['detain_amount']-$data['poundage'],2);//实际发放金额
			$data['real_amount'] = $real_amount;
			if($real_amount<=0){
				output_error(-11,'实际放款金额小于0');
			}
			$pay_data = array(
				'createtime' => time(),
				'updatetime' => time(),
				'order_type'=> 4,
				'title' => '放款',
				'order_id'=> $order['id'],
				'pay_type' => '',
				'status' => 1,
				'amount' => $real_amount,
				'user_id' => $order['user_id'],
				'order_num' => date('YmdHis').rand('1000','9999'),
			);
			$this->db = _get_db('default');
			$this->db->trans_begin();//启动事务
			$res = $this->Borrowing_model->update_by_id($id,$data);
			if($res){
				$this->load->model('User_model');
				$res1 = $this->User_model->setInc($order['user_id'],'balance',$real_amount);

				if($res1){
					$this->load->model('User_pay_model');
					$this->User_pay_model->insert_string($pay_data);
					$bj = round((float)($money/$order['credit_cycle']) ,2);
				 	$lx = round((float)($money*$order['rate']/100) ,2);
					$ctime = time();
					$yy = date('Y',$ctime);
					$mm = date('m',$ctime);
					$dd = date('d',$ctime);
					$this->load->model('Repayment_schedule');
					for($i = 0;$i < (int)$order['credit_cycle'];$i++){
						$data = array(
							'user_id' =>$order['user_id'],
							'borrowing_id' =>$id,
							'repayment_amount' =>$bj,
							'repayment_interest' =>$lx,
							'borrowing_period' =>($i+1),
							'repayment_status' =>0,
							'time' =>$ctime,
						);
						if($order['repaymentType'] == 1){
							$xm = (int)(($mm+$i)/12) ;//是否多了一年

							if(($mm+$i+1)%12 == 0){
								$cm = 12;
							}else{
								$cm =  ($mm+$i+1)%12;
							}
							//拼接是否有效日期
							if(checkdate($cm,$dd,$yy+$xm)){
								$data['repayment_deadline'] = strtotime(($yy+$xm).'-'.$cm.'-'.$dd)+86399;
							}else{
								$d = date('t',strtotime(($yy+$xm).'-'.$cm));
								$data['repayment_deadline'] = strtotime(($yy+$xm).'-'.$cm.'-'.$d)+86399;
							}


						}else if($order['repaymentType'] == 2 || $order['repaymentType'] == 3){
							$d=strtotime($order['repayment_cycle_days']*($i+1).' days');
							$dd = date('Y/m/d',$d);
							$d = strtotime($dd);
							$data['repayment_deadline'] = $d+86399;
						}

						$res2 = $this->Repayment_schedule->insert_string($data);
						if(!$res2){
							$this->db->trans_rollback();
							output_error(-11,'更新状态失败');
						}
					}
				}else{
					$this->db->trans_rollback();
					output_error(-10,'更新状态失败,无法放款');
				}
				$this->db->trans_commit();
				$this->load->model('user_model');
				$this->load->service('message_service');
				$msgTempId = 3;//还款通知 //消息模板id 1还款提醒 2逾期提醒 3审批贷款通知 4提现成功 5保证金返还客户
				$push_arr = array('{product}' => $order['product_name'],'{amount}' => $order['apply_amount']);
				$msgdata = array('msgtype' => '3', 'itemid' => $order['id']);
				$user_info = $this->user_model->fetch_row('user_id ='.$order['user_id'], 'user_name');
            	$res = $this->message_service->send_sys($msgTempId, $order['user_id'], 1, $push_arr, $msgdata, $push_arr, $user_info['user_name']);
				output_data();
			}else{
				$this->db->trans_rollback();
				output_error(-10,'更新状态失败,无法放款');
			}

		}else if($type ==2){
			$data = array(
				'status' =>-1,
				'remarks' =>$remark,
			);
			$res = $this->Borrowing_model->update_by_id($id,$data);
			if($res){
				output_data();
			}else{
				output_error(-11,'拒绝放款失败');
			}
		}else{
			output_error(-1,'参数错误');
		}

	}


	/*
	 * 提现
	 * */
	public function withdraw(){
		$this->load->view('admin/finance/withdrawlist');
	}

	public function withdrawDetail(){
		$this->load->view('admin/finance/withdrawdetail');
	}

	public function withdrawList(){

		$page     = $this->input->get_post('page');
		if(empty($page)){
			$page = 1;
		}
		$pagesize = 10;
		$starttime = $this->input->post('start_time');
		$endtime = $this->input->post('end_time');
		$user_name = $this->input->post('user_name');
		$status = $this->input->post('status');
		$where = array();
		if($starttime){
			$where['b.createtime >='] =$starttime;
		}
		if($endtime){
			$where['b.createtime < '] =$endtime;

		}
		if( $user_name){
			$where['b.user_name like'] ="'%$user_name%'";
		}

		if($status){
			$where['b.status'] = $status;
		}
		$this->load->model('Withdraw_model');
		$pagecfg = array();
		$dbprefix = $this->Withdraw_model->db->dbprefix;
		$tb = $dbprefix.'withdraw b left join '.
			$dbprefix.'user a on (b.user_id = a.user_id) ';

		$field = 'a.user_name as user_phone,b.* ';
		$prolist = $this->Withdraw_model->fetch_page(
			$page,
			$pagesize,
			$where,$field,'',$tb);


		$pagecfg['total_rows']   = $prolist['count'];
		$pagecfg['cur_page'] = $page;
		$pagecfg['per_page'] = $pagesize;

		$this->pagination->initialize($pagecfg);
		$prolist['pages'] = $this->pagination->create_links();


		$where = array();
		if($starttime){
			$where['createtime >='] =$starttime;
		}
		if($endtime){
			$where['createtime < '] =$endtime;

		}
		if( $user_name){
			$where['user_name like'] ="'%$user_name%'";
		}

		if($status){
			$where['status'] = $status;
		}
		$report = $this->Withdraw_model->get_by_where($where,'sum(amount) as amount,sum(fees) as fees');
		$report['total_count'] =$prolist['count'] ;
		$report['amount'] = round($report['amount'] ,2);
		$report['fees'] = round($report['fees'] ,2);
		$prolist['report'] = $report;
		output_data($prolist);
	}
	/*
	 * 还款
	 * */
	public function repayment(){
		$this->load->view('admin/finance/repaymentlist');
	}

	public function repaymentList(){
		$where = array();

		$page     = (int)$this->input->get_post('page');
		if(empty($page)){
			$page = 1;
		}
		$pagesize = 10;
		$starttime = (int)$this->input->post('start_time');
		$endtime =  (int)$this->input->post('end_time');
		$user_name = $this->input->post('user_name');
		$work =  $this->input->post('work');
		$a_status = $this->input->post('a_status');
		$where = array();
		if($starttime){
			$where['b.time >='] =$starttime;
		}
		if($endtime){
			$where['b.time < '] =$endtime;

		}
		if( $user_name){
			$where['c.user_name like'] ="'%$user_name%'";
		}

		if(isset($a_status) && $a_status!==''){
			$where['b.repayment_status'] =$a_status;
		}
		if($work !== null && $work!==''){
			$where['a.work'] =$work;
		}

		$this->load->model('Repayment_schedule');
		$pagecfg = array();
		$dbprefix = $this->Repayment_schedule->db->dbprefix;

		$tb = $dbprefix.'repayment_schedule b left join '.
			$dbprefix.'user a on (b.user_id = a.user_id) left join '.
			$dbprefix.'borrowing c on (b.borrowing_id=c.id) ';
		$field = 'a.work,a.user_name as mobile,b.* ,c.user_name,c.credit_cycle,c.applied_time,c.product_name';
		$prolist = $this->Repayment_schedule->fetch_page(
			$page,
			$pagesize,
			$where,$field,'',$tb);

		$pagecfg['total_rows']   = $prolist['count'];
		$pagecfg['cur_page'] = $page;
		$pagecfg['per_page'] = $pagesize;

		$this->pagination->initialize($pagecfg);
		$prolist['pages'] = $this->pagination->create_links();

		$where = array();
		if($starttime){
			$where['a.time >='] =$starttime;
		}
		if($endtime){
			$where['a.time < '] =$endtime;

		}

		if(isset($a_status) && $a_status!==''){
			$where['a.repayment_status ='] =$a_status;
		}
		if($work !== null && $work!==''){
			$where['b.work ='] =(int)$work;
		}

		if( $user_name){
			$where['c.user_name like '] ="'%$user_name%'";
		}



		$tb = $dbprefix.'repayment_schedule a left join '.
			$dbprefix.'user b on (a.user_id = b.user_id) left join '.
			$dbprefix.'borrowing c on (a.borrowing_id = c.id) ';;

		$wh = array();
		foreach($where as $k => $v){
			array_push($wh, $k.' '.$v);
		}
		if(count($wh)>0){
			$where = join(' and ',$wh);
		}
		$report = $this->Repayment_schedule->get_by_where_tb($where,'sum(a.repayment_amount) as bjtotal,sum(a.repayment_interest) as lxtotal,sum(a.delay_fine) as detain',$tb);
		$report['bjtotal'] = round($report['bjtotal'] ,2);
		$report['lxtotal'] = round($report['lxtotal'] ,2);
		$report['detain'] = round($report['detain'] ,2);
		$report['total_count'] =$prolist['count'] ;
		$prolist['report'] = $report;
		output_data($prolist);
	}

	public function repaymentDetail(){
		$id =  (int)$this->input->get('id');
		$this->load->model('Repayment_schedule');
		$this->load->model('Borrowing_model');
		$where = 'a.id ='.$id;
		$dbprefix = $this->Borrowing_model->db->dbprefix;
		$tb = $dbprefix.'borrowing a left join '.$dbprefix.'borrowing_product b on (a.borrowing_product_id=b.id) ';
		$field = 'a.*,b.product_name,b.productType';
		$order = $this->Borrowing_model->get_by_where_tb($where,$field,$tb);
		$order['productType'] = config_item('ProductType')[$order['productType']];
		$data = $this->Repayment_schedule->get_list(array('borrowing_id'=>$id));


		$where = 'a.user_id = '.$order['user_id'].'';

		$this->load->model('User_model');
		$user = $this->User_model->get_by_id($order['user_id'],'work');

 		if($user['work']=== '0'){
			$tb = $dbprefix.'user a left join '.$dbprefix.'student b on (a.user_id=b.user_id) ';
			$field = 'a.user_name as name,a.work,a.portrait,a.authentication_status,a.additional_info_status,b.*';
		}else if($user['work'] === '1'){
			$tb = $dbprefix.'user a left join '.$dbprefix.'employee b on (a.user_id=b.user_id)';
			$field = 'a.user_name as name,a.work,a.portrait,a.authentication_status,a.additional_info_status,b.*';
		}else{
			$tb = $dbprefix.'user a left join '.$dbprefix.'additional_user_info b on (a.user_id=b.user_id)';

			$field = 'a.user_name as name,a.work,a.portrait,a.authentication_status,a.additional_info_status,b.*';
		}
		$user = $this->User_model->get_by_where_tb($where,$field,$tb);

		$this->load->view('admin/finance/repaymentdetail',array('data' => $data,'order'=>$order,'user'=>$user));
	}

	public function withdrawdeal(){
		$id =  $this->input->get_post('id');
		$type =  $this->input->get_post('type');
		$this->load->model('Withdraw_model');
		$order = $this->Withdraw_model->get_by_id($id);
		if(!$order){
			output_error(-1,'订单不存在!');
		}
		if($order['status'] != 1){
			output_error(-2,'订单状态错误!');
		}
		if($order['status'] == 3){
			output_error(-5,'该订单银行处理中!');
		}
		if($type != 1 && $type != 2){
			output_error(-3,'订单操作出错');
		}
		$data = array('op_time' => time());
		if($type ==1){
			$data['status'] = 2;//打款成功
			$this->db = _get_db('default');
			$this->db->trans_begin();//启动事务
			$res = $this->Withdraw_model->update_by_id($id,$data);
			if(!$res){
				$this->db->trans_rollback();
				output_error(-4,'更新失败');
			}else{
				$this->db->trans_commit();
				output_data();
//				$this->load->service('union_df_service');
//				$o = array('merId'=>'777290058110097','orderId'=>'jiedaibaob'.$id,'txnTime'=>date("YmdHis",$data['op_time']),'txnAmt'=>$order['amount']);
////				$arr = $this->union_df_service->dfrequest($o,$order['card_id'],array('certifTp'=>'01','certifId' => $order['user_id_card'],'customerNm' => $order['user_name']));
//				$arr= $this->union_df_service->dfrequest($o,'6226388000000095',array('certifTp'=>'01','certifId' => '510265790128303','customerNm' => '张三'));
//
//				if($arr['code'] != 1){
//					$this->db->trans_rollback();
//					output_error(-10,$arr['errInfo']);
//				}else{
//					$this->db->trans_commit();
//					output_data();
//				}
			}



		}
		if($type ==2){
			$data['status'] = -1;
			$this->db = _get_db('default');
			$this->db->trans_begin();//启动事务
			$res = $this->Withdraw_model->update_by_id($id,$data);
			if(!$res){
				$this->db->trans_rollback();
				output_error(-4,'更新失败');
			}
			if($type == 2){
				$this->load->model('User_model');
				$res2 = $this->User_model->setInc($order['user_id'],'balance',$order['amount']);
				if(!$res2){
					$this->db->trans_rollback();
					output_error(-4,'更新失败');
				}
			}
			$this->db->trans_commit();
			output_data();
		}


	}

	public function export_borrow_list()
	{
		$starttime = $this->input->get('start');
		$endtime = $this->input->get('end');
		$title = '借款清单';
		$where = '';
		if($starttime){
			$where .='a.applied_time >= '.($starttime);
			$title.=date('Y/m/d',$starttime);
			if(!$endtime){
				$title.='-至今';
			}
		}
		if($endtime){

			if(!$starttime){
				$where.='a.applied_time < '.($endtime);
				$title.=date('Y/m/d',0);
			}else{
				$where.=' and a.applied_time < '.($endtime);
				$title.='-'.date('Y/m/d',$starttime);
			}

		}
		$field = 'b.user_name as mobile,a.*';
		$dbprefix = $this->Borrowing_model->db->dbprefix;
		$tb = $dbprefix.'borrowing a left join '.$dbprefix.'user b on (a.user_id=b.user_id) ';
		$order = $this->Borrowing_model->get_by_where_tb_list($where,$field,$tb);
		$data = array();
		foreach ($order as $key => $value) {
			$data[] = array(
				'user_name' => $value['user_name'],
				'mobile' => $value['mobile'],
				'product_name' => $value['product_name'],
				'apply_amount' => $value['apply_amount'],
				'usage' => $value['usage'],
				'applied_time' => !empty($value['applied_time'])?date('Y-m-d H:i',$value['applied_time']):'--',
				'approved_time' => !empty($value['approved_time'])?date('Y-m-d H:i',$value['approved_time']):'--',
				'approve_amount' => $value['approve_amount'],
				'status' => C('BorrowingStatus.'.$value['status']),
			);
		}

		$header = array('姓名','手机','申请产品','申请金额','用途','申请时间','通过时间','通过金额','状态');

		$this->load->library('excel');
		$this->excel->createExcel($title,$header,$data);
	}

	public function export_repayment_list()
	{
		$this->load->model('Repayment_schedule');
		$this->load->model('Borrowing_product_model');
		$starttime = $this->input->get('start');
		$endtime = $this->input->get('end');
//		$product = $this->Borrowing_product_model->get_list('status=1');
//		foreach ($product as $key => $value) {
//			$product[$value['id']] = $value;
//		}
		//echo '<pre>';print_r($product);
		$title = '还款清单';
		$time = microtime();
		$where = '';
		if($starttime){
			$where .='c.applied_time >= '.($starttime);
			$title.=date('Y/m/d',$starttime);
			if(!$endtime){
				$title.='-至今';
			}
		}
		if($endtime){
			if(!$starttime){
				$where.='c.applied_time < '.($endtime);
				$title.=date('Y/m/d',0);
			}else{
				$where.=' and c.applied_time < '.($endtime);
				$title.='-'.date('Y/m/d',$starttime);
			}
		}

		$field = 'b.user_name,c.product_name,a.repayment_amount,a.repayment_interest,a.borrowing_period,c.credit_cycle,a.repayment_deadline,a.repayment_time,a.repayment_status';
		$dbprefix = $this->Repayment_schedule->db->dbprefix;
		$tb = $dbprefix.'repayment_schedule a left join '.$dbprefix.'user b on (a.user_id=b.user_id) left join '.$dbprefix.'borrowing c on (a.borrowing_id = c.id)';
		$list = $this->Repayment_schedule->get_by_where_tb_list($where,$field,$tb);
		$data = array();
		foreach ($list as $key => $value) {
			$data[] = array(
				'user_name' => $value['user_name'],
				'borrowing_product' =>  $value['product_name'],
				'repayment_amount' => '￥'.$value['repayment_amount'],
				'repayment_interest' => '￥'.$value['repayment_interest'],
				'borrowing_period' => $value['borrowing_period'].'期/'.$value['credit_cycle'].'期',
				'repayment_deadline' => !empty($value['repayment_deadline'])?date('Y-m-d H:i',$value['repayment_deadline']):'--',
				'repayment_time' => !empty($value['repayment_time'])?date('Y-m-d H:i',$value['repayment_time']):'--',
				'repayment_status' => $value['repayment_status'] == 1?'已还款':'未还款',
			);
			unset($borrowing);
		}
//		$list = $this->Repayment_schedule->get_list($where);
//		$data = array();
//		foreach ($list as $key => $value) {
//			$borrowing = $this->Borrowing_model->get_by_id($value['borrowing_id']);
//			$data[] = array(
//				'user_id' => $value['user_id'],
//				'borrowing_product' => '',
//				'repayment_amount' => '￥'.$value['repayment_amount'],
//				'repayment_interest' => '￥'.$value['repayment_interest'],
//				'borrowing_period' => $value['borrowing_period'].'期/'.$borrowing['credit_cycle'].'期',
//				'repayment_deadline' => !empty($value['repayment_deadline'])?date('Y-m-d H:i',$value['repayment_deadline']):'--',
//				'repayment_time' => !empty($value['repayment_time'])?date('Y-m-d H:i',$value['repayment_time']):'--',
//				'repayment_status' => $value['repayment_status'] == 1?'已还款':'未还款',
//			);
//			unset($borrowing);
//		}
		$t = microtime() - $time;
		echo $t;
		$header = array('用户','产品','本金','利息','还款期数','到期时间','还款时间','状态');

		$this->load->library('excel');
		$this->excel->createExcel($title,$header,$data);


	}

	public function export_withdraw_list()
	{
		$this->load->model('Withdraw_model');
		$starttime = $this->input->get('start');
		$endtime = $this->input->get('end');
		$title = '提现清单';
		$where = array();
		if($starttime){
			$where['createtime >='] =($starttime);
			$title.=date('Y/m/d',$starttime);
			if(!$endtime){
				$title.='-至今';
			}
		}
		if($endtime){
			$where['createtime < '] =($endtime);
			if(!$starttime){
				$title.=date('Y/m/d',0);
			}
			$title.='-'.date('Y/m/d',$starttime);
		}

		$list = $this->Withdraw_model->get_list($where);
		$data = array();
		foreach ($list as $key => $value) {
			$data[] = array(
				'user_name' => $value['user_name'],
				'mobile' => $value['mobile'],
				'bank' => $value['bank'],
				'card_id' => $value['card_id'],
				'amount' => $value['amount'],
				'fees' => $value['fees'],
				'createtime' => !empty($value['createtime'])?date('Y-m-d H:i',$value['createtime']):'--',
				'op_time' => !empty($value['op_time'])?date('Y-m-d H:i',$value['op_time']):'--',
				'status' => C('BorrowingStatus.'.$value['status']),
			);
		}

		$header = array('姓名','手机号码','银行','银行卡号','提现金额','提现手续费','申请时间','操作时间','状态');

		$this->load->library('excel');
		$this->excel->createExcel($title,$header,$data);
	}


}