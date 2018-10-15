<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package        CodeIgniter
 * @author        ExpressionEngine Dev Team
 * @copyright    Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license        http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since        Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Application Controller Class
 *
 * This class object is the super class that every library in
 * CodeIgniter will be assigned to.
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    Libraries
 * @author        ExpressionEngine Dev Team
 * @link        http://codeigniter.com/user_guide/general/controllers.html
 */
class MY_Controller extends CI_Controller
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
}

// END Controller class
/* End of file Controller.php */
/* Location: ./system/core/Controller.php */

/**
 * 管理员
 */
class MY_Admin_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('encrypt');
        $this->load->library('session');
        $this->admin_info = $this->systemLogin();
        if (empty($this->admin_info['admin_id']) || !$this->checkPermission()) {
            // 验证权限
            redirect(ADMIN_SITE_URL . '/login');
        }
    }

    /**
    查询短信余量
     */
    public function get_sms_quantity(){
        $this->load->service('sms_service');
        $result = $this->sms_service->left_num();
        echo $result;
    }


    /**
     * 取得当前管理员信息
     *
     * @param
     * @return 数组类型的返回结果
     */
    final protected function getAdminInfo()
    {
        return $this->admin_info;
    }

    /**
     * 验证当前管理员权限是否可以进行操作
     */
    function checkPermission($link_nav = null)
    {
        if ($this->admin_info['type'] == 1) return true;
        $act = $this->router->fetch_class();
        $op = $this->router->fetch_method();

        if (empty($this->permission)) {
//            $permission = $this->encrypt->decode(this->['limits']);

            $this->permission = $permission = explode(',', $this->admin_info['limits']);
        } else {
            $permission = $this->permission;
        }
        //显示隐藏小导航，成功与否都直接返回
        if (is_array($link_nav)) {
            if (!in_array("{$link_nav['act']}.{$link_nav['op']}", $permission) && !in_array($link_nav['act'], $permission)) {
                return false;
            } else {
                return true;
            }
        }

        //以下几项不需要验证
        $tmp = array('index', 'dashboard', 'login', 'common', 'home');
        if (in_array($act, $tmp)) return true;
        if (in_array($act, $permission) || in_array("$act.$op", $permission)) {
            return true;
        } else {
            $extlimit = array('ajax', 'export_step1');
            if (in_array($op, $extlimit) && (in_array($act, $permission) || strpos(serialize($permission), '"' . $act . '.'))) {
                return true;
            }
            $bResult = false;
            //带前缀的都通过
            foreach ($permission as $v) {
                if (!empty($v) && strpos("$act.$op", $v . '_') !== false) {
                    $bResult = true;
                    break;
                }
            }
            return $bResult;
        }
        return false;
    }

    /**
     * 系统后台登录验证
     *
     * @param
     * @return array 数组类型的返回结果
     */
    function systemLogin()
    {
        //取得cookie内容，解密，和系统匹配
        $user = unserialize($this->encrypt->decode($this->session->userdata('sys_key'), C('basic_info.MD5_KEY')));
        if (!key_exists('limits', (array)$user) || !key_exists('role_id', (array)$user) || !isset($user['type']) || (empty($user['admin_name']) || empty($user['admin_id']))) {
            @header('Location: ' . ADMIN_SITE_URL . '/login');
            exit;
        } else {
            $this->systemSetKey($user);
        }
        return $user;
    }

    /**
     * 系统后台 会员登录后 将会员验证内容写入对应cookie中
     *
     * @param string $name 用户名
     * @param int $id 用户ID
     * @return bool 布尔类型的返回结果
     */
    protected final function systemSetKey($user)
    {
        $this->session->set_userdata('sys_key', $this->encrypt->encode(serialize($user), C('basic_info.MD5_KEY')), 36000);
        //$this->input->set_cookie('sys_key',$this->encrypt->encode(serialize($user),C('basic_info.MD5_KEY')),3600,'',null);
    }

    /**
     * 取得所有权限项
     *
     * @return array
     */
    public function permission()
    {

        $limit = array(
            array('name' => '设置', 'child' => array(
                array('name' => '权限设置', 'op' => null, 'act' => 'admin'),
            )),
            array('name' => '药品', 'child' => array(
                array('name' => '药品分类', 'op' => null, 'act' => 'category'),
                array('name' => '药品管理', 'op' => null, 'act' => 'goods'),
            )),

            array('name' => '患者', 'child' => array(
                array('name' => '患者管理', 'op' => null, 'act' => 'user'),
                array('name' => '问诊单管理', 'op' => null, 'act' => 'inquery'),
                array('name' => '挂号单管理', 'op' => null, 'act' => 'register'),
                array('name' => '紧急患者', 'op' => null, 'act' => 'emergency'),
            )),
            array('name' => '医生', 'child' => array(
                array('name' => '医生管理', 'op' => null, 'act' => 'doctor'),
                array('name' => '文章管理', 'op' => null, 'act' => 'article'),
                array('name' => '提现管理', 'op' => null, 'act' => 'cash'),
            )),
            array('name' => '医院', 'child' => array(
                array('name' => '医院管理', 'op' => 'null', 'act' => 'hospital'),
                array('name' => '科室管理', 'op' => 'null', 'act' => 'department'),
                array('name' => '提现管理', 'op' => 'null', 'act' => 'cash'),
            )),
            array('name' => '运营', 'child' => array(
                array('name' => '首页管理', 'op' => null, 'act' => 'homemanage'),
                array('name' => '充值记录', 'op' => null, 'act' => 'recharge'),
                array('name' => '账户明细', 'op' => null, 'act' => 'ammount'),
                array('name' => '评价管理', 'op' => null, 'act' => 'comment'),
                array('name' => '地区管理', 'op' => null, 'act' => 'area'),
                array('name' => '消息推送', 'op' => null, 'act' => 'message'),
            )),
        );

        if (is_array($limit)) {
            foreach ($limit as $k => $v) {

                if (is_array($v['child'])) {
                    $tmp = array();
                    foreach ($v['child'] as $key => $value) {
                        $act = (!empty($value['act'])) ? $value['act'] : '';
                        if (strpos($act, '|') == false) {//act参数不带|
                            $op = empty($value['op']) ? '' : $value['op'];
                            $limit[$k]['child'][$key]['op'] = rtrim($act . '.' . str_replace('|', '|' . $act . '.', $op), '.');
                        } else {//act参数带|
                            $tmp_str = '';
                            if (empty($value['op'])) {
                                $limit[$k]['child'][$key]['op'] = $act;
                            } elseif (strpos($value['op'], '|') == false) {//op参数不带|
                                foreach (explode('|', $act) as $v1) {
                                    $tmp_str .= "$v1.{$value['op']}|";
                                }
                                $limit[$k]['child'][$key]['op'] = rtrim($tmp_str, '|');
                            } elseif (strpos($value['op'], '|') != false && strpos($act, '|') != false) {//op,act都带|，交差权限
                                foreach (explode('|', $act) as $v1) {
                                    foreach (explode('|', $value['op']) as $v2) {
                                        $tmp_str .= "$v1.$v2|";
                                    }
                                }
                                $limit[$k]['child'][$key]['op'] = rtrim($tmp_str, '|');
                            }
                        }
                    }
                }
            }

            return $limit;
        } else {
            return array();
        }
    }
}




/**
 * 无需登录token的Api父类
 */
class ApiController extends CI_Controller
{
    const APPKEY = 'lkjsdfk#@&';

    public function __construct()
    {
        parent::__construct();

        //$flag = 0;
        $flag = 1;

        //验证签名是否正确
        $get_sign = $this->input->post('sign');
        $timestamp = $this->input->post('timestamp');
        $post = $_POST;
        //创建日志文件开始
        $post_url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
        $post_data = $_POST;
        $log_data = array('url'=>$post_url,'form_data'=>$post_data);
        $log_data = json_encode($log_data);
        //output_log('request-------'.$log_data);
        log_message('debug',$log_data);
        //创建日志文件结速
        $post = _request_format($post);
        ksort($post);
        unset($post['timestamp']);
        unset($post['sign']);
        foreach ($post as $key => $value) {
            $post[$key] = $key . '=' . $value;
        }
        $str = implode('&', $post);
        if (empty($post)) {
            $str = 'appkey=' . self::APPKEY . '&timestamp=' . $timestamp;
        } else {
            $str .= '&appkey=' . self::APPKEY . '&timestamp=' . $timestamp;
        }

        //$sign = md5(urlencode($str));
        $sign = md5($str);
        if ($flag == 1) {
            if ($sign != $get_sign) {
                output_error(-100, '签名出错-'.$sign);
                exit;
            }
        }

    }

}

/**
 * 需要登录token信息的Api父类
 */
class TokenApiController extends ApiController
{
    public $tokenUser = array();
    public $loginUser = array();

    public function __construct()
    {
        parent::__construct();

        //验证token信息是否正确
        $this->load->model('User_token_model');

        $token = $this->input->post('token');
        $where['token'] = "'$token'";
        $where['status'] = 1;
        $this->tokenUser = $this->User_token_model->get_by_where($where);
        if (empty($this->tokenUser)) {
            output_error(-101, '请先登录');
        }
        if ($this->tokenUser['status'] == -2) {
            output_error(-102, '登录已失效，请重新登陆');
        }

        $this->load->model('user_model');
        $this->loginUser = $this->user_model->get_by_id($this->tokenUser['user_id'],'user_id,user_name,portrait,rongytoken,work,authentication_status,additional_info_status,invite_code,status');

        if(!empty($this->loginUser))
        {
            $this->loginUser['user_id'] = $this->loginUser['user_id'];
            $this->loginUser['user_name'] = $this->loginUser['user_name'];
            $this->loginUser['token'] = $this->tokenUser['token'];
        }
        //加入验证结束
        //print_r($arrRes);exit();
        if (empty($this->loginUser) || $this->loginUser['status'] != 1) {
            output_error(-103, '无效账户，账户被锁定或已删除');
        }
    }
}

