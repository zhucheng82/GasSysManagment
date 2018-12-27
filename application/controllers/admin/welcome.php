<?php defined('BASEPATH') OR exit('No direct script access allowed');


class Welcome extends MY_Admin_Controller{

	public function __construct(){
		parent::__construct();
        $this->load->service('cron_service');
        $this->load->model('Repayment_schedule');
        $this->load->service('message_service');
        $this->load->model('user_model');
        $this->load->model('Borrowing_model');
	}

	public function index()
	{
        $this->load->view('admin/home/meter',array('borrowingStatus'=>config_item('BorrowingStatus')));
	}

    public function delayStatistics()
    {
        $this->load->view('admin/home/delay_statistics');

    }

    public function getApplyBorrowingList()
    {
        $time = time();//当前时间

        $where = array();
        $page = $this->input->get_post('page');
        if(empty($page)){
            $page = 1;
        }
        $pagesize = 10;
        $starttime = strtotime(date('Y-m-d'));
        $endtime = strtotime(date('Y-m-d',strtotime('+1 day')));

        if($starttime){
            $where['applied_time >= '] = $starttime;
        }
        if($endtime){
            $where['applied_time <'] = $endtime;
        }


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
        //$borrowAmount = $this->Borrowing_model->sum($where,'apply_amount');

        //逾期记录
        $where = array('repayment_status'=>0,'repayment_deadline<'=>$time);
        $delayCount = $this->Repayment_schedule->get_count($where);

        $prolist['total_apply'] = $prolist['count'];
        $prolist['total_delay'] = $delayCount;
        output_data($prolist);
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

    public function delayRepaymentList(){
        $time = time();//当前时间
        $page = $this->input->get_post('page');
        if(empty($page)){
            $page = 1;
        }
        $pagesize = 10;
        //逾期记录
        $where = array('b.repayment_status'=>0,'b.repayment_deadline<'=>$time);

        //$this->load->model('Repayment_schedule');
        $pagecfg = array();
        $dbprefix = $this->Repayment_schedule->db->dbprefix;

        $tb = $dbprefix.'repayment_schedule b left join '.
            $dbprefix.'user a on (b.user_id = a.user_id) left join '.
            $dbprefix.'borrowing c on (b.borrowing_id=c.id) left join '.
            $dbprefix.'borrowing_product d on (c.borrowing_product_id = d.id)';
        $field = 'a.work,a.user_name as mobile,b.* ,c.user_name,c.credit_cycle,c.applied_time,d.product_name';
        $prolist = $this->Repayment_schedule->fetch_page(
            $page,
            $pagesize,
            $where,$field,'',$tb);

        $pagecfg['total_rows']   = $prolist['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;

        $this->pagination->initialize($pagecfg);
        $prolist['pages'] = $this->pagination->create_links();

        //申请人数
        $where = array();
        $starttime = strtotime(date('Y-m-d'));//今日凌晨
        $endtime = strtotime(date('Y-m-d',strtotime('+1 day')));//明日凌晨

        if($starttime){
            $where['applied_time >= '] = $starttime;
        }
        if($endtime){
            $where['applied_time <'] = $endtime;
        }

        $applyCount = $this->Borrowing_model->get_count($where);



        $prolist['total_apply'] = $applyCount;
        $prolist['total_delay'] = $prolist['count'];

        output_data($prolist);
    }

    public function repaymentDetail(){
        $id =  $this->input->get('id');
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

}