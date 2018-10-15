<?php
/**
 * Created by PhpStorm.
 * User: zhucheng
 * Date: 16/9/19
 * Time: 下午3:25
 */

class Home extends ApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('images_model');
        $this->load->model('Borrowing_product_model');
        $this->load->model('Borrowing_model');
    }

    public $tokenUser = array();
    public $loginUser = array();
    public function index(){

        /*
        //验证token信息是否正确
        $this->load->model('User_token_model');
        $token = $this->input->post('token');
        if(!empty($token))
        {
            $where['token'] = "'$token'";
            $where['status'] = 1;
            $this->tokenUser = $this->User_token_model->get_by_where($where);
            if (empty($this->tokenUser)) {
                output_error(-101, '请先登录');exit;
            }
            if ($this->tokenUser['status'] == -2) {
                output_error(-102, '登录已失效，请重新登陆');exit;
            }

            $this->load->model('user_model');
            $this->loginUser = $this->user_model->get_by_id($this->tokenUser['user_id'],'user_id,user_name,rongytoken,work,authentication_status,invite_code,status');
            if (empty($this->loginUser) || $this->loginUser['status'] != 1) {
                //output_error(-103, '无效账户，账户被锁定或已删除');exit;
            }

            //$this->loginUser['user_id'] = $this->loginUser['user_id'];
            //$this->loginUser['user_name'] = $this->loginUser['user_name'];

            $this->loginUser['token'] = $this->tokenUser['token'];
            //加入验证结束
            //print_r($arrRes);exit();

        }
        */


        /***- 轮播 -***/
        $arrBanner = $this->images_model->get_list(array('type' => 1,'status'=>1), 'picurl,weburl', 'id asc');
        if (!empty($arrBanner)) {
            foreach($arrBanner as & $bannerItem)
            {
                if (isset($bannerItem['picurl']) && !empty($bannerItem['picurl']))
                {
                    if (empty(stristr($bannerItem['picurl'], 'http')))
                    {
                        trim($bannerItem['picurl'], '/');
                        $bannerItem['picurl'] = '/'.$bannerItem['picurl'];
                        $bannerItem['picurl'] = BASE_SITE_URL.$bannerItem['picurl'];
                    }
                }
            }
        }

        //热点
        $arrHot = $this->images_model->get_list(array('type' => 2,'status'=>1), 'title,weburl', 'id asc');

        $applyBorrowingCount = 135689;
        $this->load->model('Borrowing_model');
        $applyBorrowingCount += $this->Borrowing_model->get_distinct_count('user_id');


        //借贷产品列表，一般一个
        $where = array('status'=>1, 'is_recommend'=>1);
        $borrowingList = $this->Borrowing_product_model->get_list($where);

        /*
        //个人借贷情况
        $userBorrowingInfo = array();
        if(!empty($this->loginUser))
        {
            $where = array('user_id'=>$this->loginUser['user_id'], 'status'=>0);
            $userBorrowingInfo = $this->Borrowing_model->get_by_where($where);
        }
        */
        $shareInfoConfig = config_item('share_info');
        $shareInfo = array('share_url'=>SHARE_URL, 'share_title'=>$shareInfoConfig['share_title'], 'share_description'=>$shareInfoConfig['share_description'] );
        $data = array(
            'banner' => $arrBanner,
            'apply_borrowing_count'=>$applyBorrowingCount,
            //'hot_list' => $arrHot,
            'borrowing_list'=>$borrowingList,
            'share_info'=>$shareInfo,
            'help_url'=>HELP_URL
            //'borrowing_info'=>$userBorrowingInfo
        );

        output_data($data);
    }

    public function getHomeData()
    {
        $where = array('status'=>1);
        $borrowingList = $this->Borrowing_product_model->get_list($where);

        if (!empty($borrowingList)) {
            $data = array(
                'borrowing_list' => $borrowingList
            );
            output_data($data);
        }else{
            output_error(-1,'暂无借贷产品');
        }
    }

    public function getBorrowingProductList()
    {
        $where = array('status'=>1);
        $order_by = 'is_recommend DESC,sort DESC';
        $borrowingList = $this->Borrowing_product_model->get_list($where, '*', $order_by);

        if (!empty($borrowingList)) {
            foreach($borrowingList as & $borrowingItem)
            {
                if (isset($borrowingItem['img_flag_url']) && !empty($borrowingItem['img_flag_url']))
                {
                    if (empty(stristr($borrowingItem['img_flag_url'], 'http')))
                    {
                        trim($borrowingItem['img_flag_url'], '/');
                        $borrowingItem['img_flag_url'] = '/'.$borrowingItem['img_flag_url'];
                        $borrowingItem['img_flag_url'] = BASE_SITE_URL.$borrowingItem['img_flag_url'];
                    }
                }
            }

            $data = array(
                'borrowing_list' => $borrowingList
            );
            output_data($data);
        }else{
            output_error(-1,'暂无借贷产品');
        }
    }

    public function getBorrowingProductInfo()
    {
        $productId = $this->input->post('borrowing_product_id');

        if(empty($productId))
        {
            output_error(-1, '参数不能为空');exit;
        }

        $where = array('id'=>$productId);
        $borrowingProduct = $this->Borrowing_product_model->get_by_where($where);

        if (!empty($borrowingProduct)) {

            $depositRate = C('deposit_rate')/100.000;
            $procedureFee = C('procedure_fee')/100.000;

            $borrowingProduct['deposit_rate'] = "$depositRate";//借款扣留金扣费比率
            $borrowingProduct['procedure_fee'] = "$procedureFee";//借款手续费费率
            $data = array(
                'borrowing_product_info' => $borrowingProduct
            );
            output_data($data);
        }else{
            output_error(-1,'无效的产品');
        }
    }
}