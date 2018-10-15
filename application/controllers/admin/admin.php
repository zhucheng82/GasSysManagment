<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 权限管理
 */

class Admin extends MY_Admin_Controller {

    public function __construct()
    {

        parent::__construct();
        $this->load->model('Admin_model');
    }

    //权限管理首页
    public function index() {

        $this->lang->load('admin_layout');
        $this->lang->load('admin_admin');
        $this->load->model('Admin_role_model');
        $page     = _get_page();
        $pagesize = 5;
        $arrParam = array();
        $arrWhere = array();
        $arrWhere['status <>'] = -1;
        $list = $this->Admin_model->fetch_page($page, $pagesize, $arrWhere,'*');

        foreach($list['rows'] as $k => $v){

            $roleName = $this->Admin_role_model->get_by_id($v['role_id'],'role_name');

            $list['rows'][$k]['role_name'] = $roleName['role_name'];
        }

        //分页
        $pagecfg = array();
        $pagecfg['base_url']     = _create_url(ADMIN_SITE_URL.'/admin', $arrParam);
        $pagecfg['total_rows']   = $list['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;

        $this->pagination->initialize($pagecfg);
        $list['pages'] = $this->pagination->create_links();

        $result = array(
            'list' =>$list,
        );

        $this->load->view('admin/admin',$result);
    }

    //新增管理员
    public function admin_add()
    {
        $this->lang->load('admin_layout');
        $this->lang->load('admin_admin');

        $this->load->model('Admin_role_model');
        $role_list = $this->Admin_role_model->get_list();

        $result = array(
            'role' => $role_list,
        );

        $this->load->view('admin/admin_add',$result);
    }

    //编辑已存在的管理员
    public function admin_edit()
    {
        $this->lang->load('admin_layout');
        $this->lang->load('admin_admin');
        //如果有post数据则判断之后修改表
        if ($this->input->post())
        {

            $config = array(
                array(
                    'field'   => 'user_id',
                    'label'   => '管理员id',
                    'rules'   => 'trim|required'
                ),
                array(
                    'field'   => 'role_id',
                    'label'   => '所属分组的id',
                    'rules'   => 'trim|required'
                ),
            );
            $this->form_validation->set_rules($config);
            if ($this->form_validation->run() === TRUE)
            {
                $new_pwd = $this->input->post('new_pw');
                if (!empty($new_pwd))
                {
                    $new_pwd2 = $this->input->post('new_pw2');

                    if ($new_pwd == $new_pwd2)
                    {

                        $data['password'] = md5(trim($new_pwd));
                    }
                }
                $id = $this->input->post('user_id');
                $data['role_id'] = $this->input->post('role_id');

                if($this->Admin_model->update_by_id($id,$data))
                {
                    redirect(ADMIN_SITE_URL.'/admin');
                }
            }

        }

        //如果只有get数据则进入管理员编辑页面
        if ($this->input->get('id'))
        {
            $id = $this->input->get('id');
            $info = $this->Admin_model->get_by_id($id);
        }
        $this->load->model('Admin_role_model');
        $role_list = $this->Admin_role_model->get_list();

        $result = array(
            'info' => $info,
            'role' => $role_list,
        );
        $this->load->view('admin/admin_edit',$result);
    }

    //权限组列表页
    public function admin_role()
    {
        $this->lang->load('admin_layout');
        $this->lang->load('admin_admin');
        $this->load->model('Admin_role_model');

        $page     = _get_page();
        $pagesize = 5;
        $arrParam = array();
        $arrWhere = array();
        $list = $this->Admin_role_model->fetch_page($page, $pagesize, $arrWhere,'*');

        //分页
        $pagecfg = array();
        $pagecfg['base_url']     = _create_url(ADMIN_SITE_URL.'/admin/gadmin', $arrParam);
        $pagecfg['total_rows']   = $list['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;

        $this->pagination->initialize($pagecfg);
        $list['pages'] = $this->pagination->create_links();

        $result = array(
            'list' =>$list,
        );
        $this->load->view('admin/admin_role',$result);
    }

    //新增权限组
    public function admin_role_add()
    {
        $this->lang->load('admin_layout');
        $this->lang->load('admin_admin');
        $config['limit'] = $this->permission();
        $result = array(
            'limit' => $config['limit'],
        );
        $this->load->view('admin/admin_role_add',$result);
    }

    public function admin_role_set()
    {
        $this->lang->load('admin_layout');
        $this->lang->load('admin_admin');

        //如果有post数据则验证之后修改表
        if ($this->input->is_post())
        {
            $config = array(
                array(
                    'field'   => 'role_name',
                    'label'   => '用户名',
                    'rules'   => 'trim|required'
                ),
            );

            $this->form_validation->set_rules($config);
            if ($this->form_validation->run() === TRUE)
            {
                $data = array(
                    'role_name' => $this->input->post('role_name'),
                    'limits' => $this->input->post('permission'),
                );

                //将数组转化为字符串之后加密
                $limits = implode('|',$data['limits']);
                $this->load->library('encrypt');
                $data['limits'] = $this->encrypt->encode($limits);

                $this->load->model('Admin_role_model');
                $id = $this->input->post('role_id');
                if ($this->Admin_role_model->update_by_id($id,$data))
                {
                    redirect(ADMIN_SITE_URL.'/admin/admin_role');
                }
            }
        }

        $config['limit'] = $this->permission();

        $this->load->library('encrypt');
        $this->load->model('Admin_role_model');
        $limits = $this->Admin_role_model->get_by_id($this->input->get('id'));
        $permission = $this->encrypt->decode($limits['limits']);
        $permission = explode('|',$permission);
        foreach ($config['limit'] as $key=>$value)
        {
            foreach ($value['child'] as $k =>$v)
            {
                if (in_array($v['act'],$permission))
                {
                    $config['limit'][$key]['child'][$k]['op'] = 1;
                }
            }
        }

        $result = array(
            'limit' => $config['limit'],
            'info' =>$limits,
        );
        $this->load->view('admin/admin_role_set',$result);

    }


    //审核管理员编辑
    public function save()
    {
        if ($this->input->is_post())
        {
            $config = array(
                array(
                    'field'   => 'admin_name',
                    'label'   => '用户名',
                    'rules'   => 'trim|required'
                ),
                array(
                    'field'   => 'admin_password',
                    'label'   => '密码',
                    'rules'   => 'trim|required'
                ),
                array(
                    'field'   => 'role_id',
                    'label'   => '所属权限组id',
                    'rules'   => 'trim|required'
                ),
            );
            $this->form_validation->set_rules($config);

            if ($this->form_validation->run() === TRUE)
            {
                $data = array(
                    'name' => $this->input->post('admin_name'),
                    'role_id' => $this->input->post('role_id'),
                    'login_time' => time(),
                    'login_num' => 0,
                    'is_super' => 0,
                    'status' => 2,
                );
                $pwd = $this->input->post('admin_password');
                $data['password'] = md5(trim($pwd));
                $this->Admin_model->insert($data);

                redirect(ADMIN_SITE_URL.'/admin');
            }
        }
    }

    //审核权限组编辑
    public function role_save()
    {
        if ($this->input->is_post())
        {
            $config = array(
                array(
                    'field'   => 'role_name',
                    'label'   => '用户名',
                    'rules'   => 'trim|required'
                ),
            );

            $this->form_validation->set_rules($config);
            if ($this->form_validation->run() === TRUE)
            {
                $data = array(
                    'role_name' => $this->input->post('role_name'),
                    'limits' => $this->input->post('permission'),
                );

                //将数组转化为字符串之后加密
                $limits = implode('|',$data['limits']);
                $this->load->library('encrypt');
                $data['limits'] = $this->encrypt->encode($limits);

                $this->load->model('Admin_role_model');

                if ($this->Admin_role_model->insert($data))
                {
                    redirect(ADMIN_SITE_URL.'/admin/admin_role');
                }
            }
        }
    }

    //管理员删除操作
    public function del()
    {
        if ($this->input->is_post())
        {
            $id = $this->input->post('del_id');
        }
        else
        {
            $id	= $this->input->get('id');
        }

        $data['status'] = -1;
        $where['id'] = $id;
        $this->Admin_model->delete_by_id($id);
        redirect( ADMIN_SITE_URL.'/admin' );
    }

    //权限组删除操作
    public function role_del()
    {

        if ($this->input->is_post())
        {
            $id = $this->input->post('del_id');
        }
        else
        {
            $id	= $this->input->get('id');
        }
        $this->load->model('Admin_role_model');
        $this->Admin_role_model->delete_by_id($id);
        redirect( ADMIN_SITE_URL.'/admin/admin_role' );
    }

    //ajax判断输入的权限组名称是否已存在
    public function ajax(){
        if ($this->input->get('role_name'))
        {
            $where['role_name'] = $this->input->get('role_name');
            $this->load->model('Admin_role_model');
            $id = $this->input->get('id');
            $orig_name = $this->Admin_role_model->get_by_id($id);

            if ($orig_name['role_name'] == $where['role_name'])
            {
                exit('true');
            }
            if ($this->Admin_role_model->get_list($where))
            {
                exit('false');
            }
            else
            {
                exit('true');
            }
        }

    }

    //ajax判断登录名是否重复
    public function ajax_check_name()
    {
        $id = $this->input->get('id');
        $where['name'] = $this->input->get('admin_name');
        $res = $this->Admin_model->get_list($where);
        if (!empty($id))
        {
            $info = $this->Admin_model->get_by_id($id);
            if ($info['name'] == $where['name'])
            {
                exit('true');
            }
        }
        if (empty($res[0]))
        {
            exit('true');
        }
        else
        {
            exit('false');
        }
    }

}
