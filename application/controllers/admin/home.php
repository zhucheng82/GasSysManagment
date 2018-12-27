<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Admin_Controller
{
    public  function __construct()
    {
        parent::__construct();
        $this->load->model('Custom_company_model');
        $this->load->model('Meter_model');
        $this->load->model('Meter_data_model');
        $this->load->model('Meter_warn_info_model');
    }

    /**
     *
     */
    public function index()
    {   
        $this->lang->load('admin_layout');

        $this->getNav('', $top_nav, $left_nav, $map_nav);
        $admin_info = $this->getAdminInfo();

        $result = array(
            'output' => array(
                'html_title' => lang('login_index_title_02'),
                'map_nav' => $map_nav,
                'admin_info' => $admin_info,
                'top_nav' => $top_nav,
                'left_nav' => $left_nav,
                'type' => $this->admin_info['type']
            )
        );

        //$this->load->view('admin/home', $result);


        //$this->load->view('admin/new_home', $result);


        $admin_info = unserialize($this->encrypt->decode($this->session->userdata('sys_key'),C('basic_info.MD5_KEY') ) );
        $admin_id = $admin_info['admin_id'];
        $level = $admin_info['level'];

        $company_id = -1;

        $company = array();

        if ($level == 0){
            $where = 'level = 1';
            $company = $this->Custom_company_model->get_by_where_tb_list($where);
        }
        else
        {
            $where = 'user_id = '.$admin_id .' and level = '.$level;
            $company = $this->Custom_company_model->get_by_where_tb_list($where);
        }

        //echo json_encode($company);exit();

        foreach ($company as & $comp)
        {
            $id = $comp['id'];
            $level = $comp['level'];

            if ($level < 2)
            {
                $where = 'parent_company_id = '.$id .' and level = '.($level+1);
                $sub = $this->Custom_company_model->get_by_where_tb_list($where);

                foreach ($sub as & $subComp)
                {
                    $id = $subComp['id'];

                    $where = 'user_id = '.$id;
                    $subMeter = $this->Meter_model->get_by_where_tb_list($where);

                    $subComp['sub'] = $subMeter;

                }

            }
            else
            {
                $where = 'user_id = '.$id;
                $sub = $this->Meter_model->get_by_where_tb_list($where);

            }


            $comp['sub'] = $sub;

            //echo json_encode($comp).'<br>';
        }

        //echo '+++++++++++++++';

        //echo json_encode($company); exit();

        //echo json_encode($company);exit();


        $itemSelect = array('index'=>0);

        $this->load->view('admin/top', $itemSelect);

        $data['company_lists'] = $company;
        $data['level'] = $level;
        $data['company_id'] = $company_id;
        //$this->load->view('admin/meterD', $data);


        $this->load->view('admin/left_navigation_menu', $data);
        //$this->load->view('admin/meterContent', $itemSelect);

    }

    //根据type获取仪表统计信息。type:0-超级用户,1-公司,2-公司下级公司/用户,3-仪表
    public function getMeterStatisticalInfoWithLevel()
    {
        //echo json_encode($_POST);

        $type = $_POST['type'];
        $meterEui = $_POST['meter_eui'];
        $companyId = $_POST['company_id'];
        $data = array();
        $string = "";

        switch ($type)
        {
            case 0://

                $string = $this->load->view('admin/brief_intro_for_company_view', $data, TRUE);
                break;
            case 1://点击某个公司

                $data['type'] = $type;
                $data['company_id'] = $companyId;
                $string = $this->load->view('admin/meter_statistical_info_for_company', $data, TRUE);

                break;

            case 2://点击某个具体的终端用户

                $where = 'm.user_id = '.'"'.$companyId.'"';
                $dbprefix = $this->Meter_warn_info_model->db->dbprefix;

                $tb = $dbprefix.'meter_warninfo w left join '.$dbprefix.'meter_meter m on (w.meter_eui = m.meter_eui) 
                left join '.$dbprefix.'meter_custom_company u on  (u.id = m.user_id)
                left join '.$dbprefix.'meter_datawarntype warnType
                          on (warnType.data_warn = w.data_warn)';
                $field = 'w.data_warn,
                w.warn_date,
                w.meter_eui,
                m.meter_name,
                w.warn_other,
                warnType.data_warn_solution ,
                warnType.data_warn_level ,
                warnType.data_warn_reason ,
                u.name
                ';

                $data['type'] = $type;
                $data['company_id'] = $companyId;

                $string = $this->load->view('admin/meter_statistical_info_for_end_user', $data, TRUE);

                break;
            case 3://点击某个燃气仪表



                //燃气表信息
                $where = 'a.meter_eui = '.'"'.$meterEui.'"';
                $dbprefix = $this->Meter_model->db->dbprefix;

                $tb = $dbprefix.'meter_meter a left join '.$dbprefix.'meter_metertype b on (a.meter_type = b.meter_type) left join '.$dbprefix.'meter_identificationmeter c on (a.meter_eui=c.meter_eui)';
                $field = 'a.meter_name, a.meter_index, a.meter_eui, a.meter_version, a.meter_revisetype, a.meter_district, a.wrap_code, b.*, c.outputMin,c.outputMax,c.pressureMin,c.pressureMax,c.temperatureMin,c.temperatureMax,c.next_identify_date as valid_time';

                $meterInfo = $this->Meter_model->get_by_where_tb($where,$field,$tb);

                $data['meter'] = $meterInfo;
                $data['meter_type'] = $meterInfo['meter_type'];
                $data['meter_eui'] = $meterEui;

                //$data['meter_type'] = 0;

                $string = $this->load->view('admin/meter_statistical_info_for_meter', $data, TRUE);
                break;


            case 4://总警报信息
                $data = array();
                $string = $this->load->view('admin/meter_warn_info_view', $data, TRUE);
                break;
            default:
                $string = $this->load->view('admin/meter_statistical_info_for_company', $data, TRUE);
                break;
        }

        echo $string;
    }


    public function showtest()
    {
        $data = array();
        //$this->load->view('admin/meter_statistical_info_for_meter_test', $data);
        $this->load->view('admin/meter_statistical_info_for_end_user', $data);

    }

    public function test()
    {
        $admin_info = unserialize($this->encrypt->decode($this->session->userdata('sys_key'),C('basic_info.MD5_KEY') ) );
        $admin_id = $admin_info['admin_id'];
        $level = $admin_info['level'];

        $company_id = -1;

        $company = array();

        if ($level == 0){
            $where = 'level = 1';
            $company = $this->Custom_company_model->get_by_where_tb_list($where);
        }
        else
        {
            $where = 'user_id = '.$admin_id .' and level = '.$level;
            $company = $this->Custom_company_model->get_by_where_tb_list($where);
        }

        //echo json_encode($company);exit();

        foreach ($company as & $comp)
        {
            $id = $comp['id'];
            $level = $comp['level'];

            if ($level < 2)
            {
                $where = 'parent_company_id = '.$id .' and level = '.($level+1);
                $sub = $this->Custom_company_model->get_by_where_tb_list($where);

                foreach ($sub as & $subComp)
                {
                    $id = $subComp['id'];

                    $where = 'user_id = '.$id;
                    $subMeter = $this->Meter_model->get_by_where_tb_list($where);

                    $subComp['sub'] = $subMeter;

                }

            }
            else
            {
                $where = 'user_id = '.$id;
                $sub = $this->Meter_model->get_by_where_tb_list($where);

            }


            $comp['sub'] = $sub;

            //echo json_encode($comp).'<br>';
        }

        //echo '+++++++++++++++';

        //echo json_encode($company); exit();

        //echo json_encode($company);exit();


        $itemSelect = array('index'=>0);

        $this->load->view('admin/top', $itemSelect);

        $data['company_lists'] = $company;
        $data['level'] = $level;
        $data['company_id'] = $company_id;
        //$this->load->view('admin/meterD', $data);


        $this->load->view('admin/test_view', $data);

    }

    //获取用户用气量
    public function getUserGasConsumption()
    {
        $companyId = $this->input->get_post('company_id');
        $dateType = $this->input->get_post('date_type');//0-一周统计 1-一月统计 2-一年统计 3-所有统计

        if (empty($dateType)) {
            $dateType = 0;
        }

        $data = array();
        $data['com_id'] = $companyId;

        $curDate = date('Y-m-d', time());

        $dateLimite = '';


        switch ($dateType)
        {
            case 0://一周
                $dateLimite = date("Y-m-d", strtotime("-7 day"));
                break;
            case 1://一月
                $dateLimite = date("Y-m-d", strtotime("-1 month"));
                break;
            case 2://一年
                $dateLimite = date("Y-m-d", strtotime("-1 year"));
                break;
            case 3://所有
                $dateLimite = '';
                break;
            default:
                $dateLimite = date("Y-m-d", strtotime("-7 day"));
        }

        $dbprefix = $this->Meter_data_model->db->dbprefix;
        //获取饼图数据
        $where = 'u.id = '.'"'.$companyId.'"';

        if (!empty($dateLimite))
        {
            $where = $where.' and substr(d.data_date, 0, 11) > '.'"'.$dateLimite.'"';
        }

        $tb = $dbprefix.'meter_data d inner join '.$dbprefix.'meter_meter m on (d.meter_eui = m.meter_eui) inner join '.$dbprefix.'meter_custom_company u on (u.id = m.user_id) ';
        $field = 'substr(d.data_date, 0, 11) as date, max(d.data_vb) - min(d.data_vb) as data_vb';

        $sql ='SELECT date, sum(data_vb) as data_vb from'. '(select '.$field.' from '.$tb.' where '.$where.' group by d.meter_eui , substr(d.data_date, 0, 11)) GROUP BY substr(date, 0, 11)';

        $arrGasUsage = $this->Meter_data_model->execute_array($sql);

        $data['gas_usage'] = $arrGasUsage;

        $data['sql'] = $sql;


        output_data($data);
    }

    public function getInfoForCompany()
    {
        $companyId = $this->input->get_post('company_id');

        $data = array();
        $data = array("info"=>'test', 'com_id'=>$companyId);

        $dbprefix = $this->Meter_data_model->db->dbprefix;
        //获取饼图数据
        $where = 'u.parent_company_id = '.'"'.$companyId.'"';
        $tb = $dbprefix.'meter_data d inner join '.$dbprefix.'meter_meter m on (d.meter_eui = m.meter_eui) inner join '.$dbprefix.'meter_custom_company u on (u.id = m.user_id) left join '.$dbprefix.'meter_identificationmeter c on (m.meter_eui=c.meter_eui) ';
        $field = 'd.data_qm, c.outputMax, max(d.data_date)';

        $sql ='select '.$field.' from '.$tb.' where '.$where.' group by d.meter_eui';

        $arrMeterQm = $this->Meter_data_model->execute_array($sql);

        //$this->db->get_last


        $data['meter_qm'] = $arrMeterQm;
        $data['sql'] = $sql;

        output_data($data);
    }

    public function getInfoForEndUser()
    {
        $page = $this->input->get_post('page');
        if(empty($page)){
            $page = 1;
        }

        $type = $this->input->get_post('type');

        $companyId = $this->input->get_post('company_id');

        if(empty($type) || empty($companyId)){
            return;
        }

        $arrParam = array();

        if($page)
        {
            //$arrParam['page'] = $page;

        }

        if($type)
        {
            $arrParam['type'] = $type;

        }

        if($companyId)
        {
            $arrParam['company_id'] = $companyId;

        }

        $where = 'm.user_id = '.'"'.$companyId.'"';
        $dbprefix = $this->Meter_warn_info_model->db->dbprefix;

        $tb = $dbprefix.'meter_warninfo w left join '.$dbprefix.'meter_meter m on (w.meter_eui = m.meter_eui) 
                left join '.$dbprefix.'meter_custom_company u on  (u.id = m.user_id)
                left join '.$dbprefix.'meter_datawarntype warnType
                          on (warnType.data_warn = w.data_warn)';
        $field = 'w.data_warn,
                w.warn_date,
                w.meter_eui,
                m.meter_name,
                w.warn_other,
                warnType.data_warn_solution ,
                warnType.data_warn_level ,
                warnType.data_warn_reason ,
                u.name
                ';

        $pagesize = 10;
        $pagecfg = array();
        $meterWarnInfoList = $this->Meter_warn_info_model->fetch_page(
            $page,
            $pagesize,
            $where,$field,'w.warn_date desc',$tb);

        $pagecfg['base_url'] = _create_url(ADMIN_SITE_URL.'/home/getInfoForEndUser', $arrParam);
        $pagecfg['total_rows']   = $meterWarnInfoList['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;

        //echo "查询结果:".json_encode($meterWarnInfoList)."\n";

        //echo $this->db->last_query()."\n";

        $pagecfg['total_rows']   = $meterWarnInfoList['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;

        $this->pagination->initialize($pagecfg);

        $meterWarnInfoList['pages'] = $this->pagination->create_links();
        output_data($meterWarnInfoList);
    }

    //获取燃气表采集实时数据
    public function getMeterDataList()
    {
        //燃气表采集信息
        $page = $this->input->get_post('page');
        if(empty($page)){
            $page = 1;
        }

        $meterEui = $this->input->get_post('meter_eui');
        $meterType = $this->input->get_post('meter_type');

        $arrParam = array();

        if($meterEui)
        {
            $arrParam['meter_eui'] = $meterEui;
        }

        $pagesize = 10;
        $pagecfg = array();

        $dbprefix = $this->Meter_data_model->db->dbprefix;
        $tb = $dbprefix.'meter_data';

        $where = array('meter_eui' => $meterEui);
        $where = 'meter_eui = '.'"'.$meterEui.'"';
        $field = "*";

        //echo "准备执行sql查询.".'page:'.$page." where:".$where;
        $meterDataList = $this->Meter_data_model->fetch_page(
            $page,
            $pagesize,
            $where,$field,'data_date desc',$tb);

        $sql = $this->db->last_query();

        $pagecfg['base_url'] = _create_url(ADMIN_SITE_URL.'/home/getMeterData', $arrParam);
        $pagecfg['total_rows']   = $meterDataList['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;

        $this->pagination->initialize($pagecfg);

        $meterDataList['pages'] = $this->pagination->create_links();

        $meterDataList['meter_type'] = $meterType;

        $meterDataList['sql'] = $sql;

        output_data($meterDataList);
    }

    public function showWarnInfoView()
    {
        $itemSelect = array('index'=>1);
        $this->load->view('admin/top', $itemSelect);

        $data = array('param1'=>"test1", 'param2'=>"test2");
        $this->load->view('admin/meter_warn_info_view', $data);

        //$this->load->view('admin/layout_test_view', $data);
        //$this->load->view('admin/layout_test_view2', $data);

    }


    public function getWarnInfoForCompany()
    {
        $page = $this->input->get_post('page');
        if(empty($page)){
            $page = 1;
        }

        $type = $this->input->get_post('type');

        $companyId = $this->input->get_post('company_id');

        if(empty($companyId)){
            return;
        }

        if(empty($type)){
            $type = 1;
        }

        $where = "m.id = 10";
        if ($type == 1) {//公司
            $where = 'u.parent_company_id = ' . '"' . $companyId . '"';

        }
        else//客户
        {
            $where = 'u.id = ' . '"' . $companyId . '"';
        }


        $dbprefix = $this->Meter_model->db->dbprefix;


        $tb = $dbprefix.'meter_warninfo w left join '.$dbprefix.'meter_meter m on (w.meter_eui = m.meter_eui)
                left join '.$dbprefix.'meter_custom_company u on  (u.id = m.user_id)
                left join '.$dbprefix.'meter_datawarntype warnType on (warnType.data_warn = w.data_warn)';


        $field = 'w.data_warn,
                w.warn_date,
                w.meter_eui,
                m.meter_name,
                w.warn_other,
                warnType.data_warn_solution ,
                warnType.data_warn_level ,
                warnType.data_warn_reason ,
                u.name
                ';

        $field = '*';


        $pagesize = 10;
        $pagecfg = array();

        $meterWarnInfoList = $this->Meter_model->fetch_page($page, $pagesize, $where, $field, 'w.warn_date desc', $tb);

        $lastQuery = $this->db->last_query();

        //echo json_encode(array('code'=>'0', 'last_query'=>$lastQuery));
        //exit();

        $pagecfg['total_rows']   = $meterWarnInfoList['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;

        $this->pagination->initialize($pagecfg);

        //echo json_encode(array('code'=>'0'));
        //exit();

        //$data['meter_warn_info_pages']=$pagecfg;

        //echo json_encode($data)."\n";

        //$string = $this->load->view('admin/meter_warn_info_view', $data, TRUE);
        //$this->load->view('admin/meter_warn_info_view', $data);

        $meterWarnInfoList['pages'] = $this->pagination->create_links();
        output_data($meterWarnInfoList);

        //echo json_encode($meterWarnInfoList);
    }

    //获取账号下所有报警信息
    public function getAllWarnInfoForAdmin()
    {

        $admin_info = unserialize($this->encrypt->decode($this->session->userdata('sys_key'),C('basic_info.MD5_KEY') ) );
        $admin_id = $admin_info['admin_id'];
        $level = $admin_info['level'];

        $company = array();
        $companyId = "";

        $page = $this->input->get_post('page');
        if(empty($page)){
            $page = 1;
        }

        $where = "m.id = 10";
        if ($level > 0) {
            $where1 = 'user_id = ' . $admin_id . ' and level = ' . $level;
            $company = $this->Custom_company_model->get_by_where_tb($where1);

            if (!empty($company)) {
                $companyId = $company['id'];
                $where = 'u.parent_company_id = ' . '"' . $companyId . '"';
            }

        }
        else
        {
            $where = "m.id > 0";
        }


        $dbprefix = $this->Meter_model->db->dbprefix;

        $tb = $dbprefix.'meter_warninfo w inner join '.$dbprefix.'meter_meter m on (w.meter_eui = m.meter_eui)
                inner join '.$dbprefix.'meter_custom_company u on  (u.id = m.user_id)
                left join '.$dbprefix.'meter_custom_company parent_u on (u.parent_company_id = parent_u.id) left join '.$dbprefix.'meter_datawarntype warnType on (warnType.data_warn = w.data_warn)';


        $field = 'w.data_warn,
                w.warn_date,
                w.meter_eui,
                m.meter_name,
                w.warn_other,
                warnType.data_warn_solution ,
                warnType.data_warn_level ,
                warnType.data_warn_reason ,
                u.name as user,
                parent_u.name as company
                ';

        $pagesize = 10;
        $pagecfg = array();

        $meterWarnInfoList = $this->Meter_model->fetch_page($page, $pagesize, $where, $field, 'w.warn_date desc', $tb);

        $lastQuery = $this->db->last_query();

        $pagecfg['total_rows']   = $meterWarnInfoList['count'];
        $pagecfg['cur_page'] = $page;
        $pagecfg['per_page'] = $pagesize;

        $this->pagination->initialize($pagecfg);


        $meterWarnInfoList['pages'] = $this->pagination->create_links();

        output_data($meterWarnInfoList);


    }



    public function tmp_send()
    {
        $this->load->service('Message_service');
        $this->message_service->tmp_send();
    }


    /**
     * 取得后台菜单
     *
     * @param string $permission
     * @return
     */
    protected final function getNav($permission = '', &$top_nav, &$left_nav, &$map_nav)
    {

        $act = $this->input->get_post('act');
        $op = $this->input->get_post('op');

        if ($this->admin_info['type'] != 1 && empty($this->permission)) {

            $this->permission = explode(',', $this->admin_info['limits']);
            // foreach ($arrPermit as $key => $value) {
            //     $this->permission[$value['parent_id']][] = $value['menu_id'];
            // }
        }
        $this->lang->load('common');
        //Language::read('common');
        $array = $this->get_menu();

        $array = $this->parseMenu($array);
        //管理地图
        $map_nav = $array['left'];
        unset($map_nav[0]);

        $model_nav = "<li><a class=\"link actived\" id=\"nav__nav_\" href=\"javascript:;\" onclick=\"openItem('_args_');\"><span>_text_</span></a></li>\n";
        $top_nav = '';
        //顶部菜单
        foreach ($array['top'] as $k => $v) {
            $v['nav'] = $v['args'];
            $top_nav .= str_ireplace(array('_args_', '_text_', '_nav_'), $v, $model_nav);
        }
        $top_nav = str_ireplace("\n<li><a class=\"link actived\"", "\n<li><a class=\"link\"", $top_nav);

        //左侧菜单
        $model_nav = "
          <ul id=\"sort__nav_\">
            <li>
              <dl>
                <dd>
                  <ol>
                    list_body
                  </ol>
                </dd>
              </dl>
            </li>
          </ul>\n";
        $left_nav = '';
        foreach ($array['left'] as $k => $v) {
            $left_nav .= str_ireplace(array('_nav_'), array($v['nav']), $model_nav);
            $model_list = "<li nc_type='_pkey_'><a href=\"JavaScript:void(0);\" name=\"item__opact_\" id=\"item__opact_\" onclick=\"openItem('_args_');\">_text_</a></li>";
            $tmp_list = '';

            $current_parent = '';//当前父级key
            if (!empty($v['list'])) {
                foreach ($v['list'] as $key => $value) {
                    $model_list_parent = '';
                    $args = explode(',', $value['args']);
                    if ($this->admin_info['type'] != 1) {
                        if (!@in_array($args[1], $permission)) {
                            //continue;
                        }
                    }

                    if (!empty($value['parent'])) {
                        if (empty($current_parent) || $current_parent != $value['parent']) {
                            $model_list_parent = "<li nc_type='parentli' dataparam='{$value['parent']}'><dt>{$value['parenttext']}</dt><dd style='display:block;'></dd></li>";
                        }
                        $current_parent = $value['parent'];
                    }

                    $value['op'] = $args[0];
                    $value['act'] = $args[1];
                    //$tmp_list .= str_ireplace(array('_args_','_text_','_op_'),$value,$model_list);
                    $tmp_list .= str_ireplace(array('_args_', '_text_', '_opact_', '_pkey_'), array($value['args'], $value['text'], $value['op'] . $value['act'], !empty($value['parent']) ? $value['parent'] : 0), $model_list_parent . $model_list);
                }
            }
            

            $left_nav = str_replace('list_body', $tmp_list, $left_nav);

        }
    }


    public function get_menu()
    {
        $menuList = array();
        $this->load->model('Menu_model');
        $menu = $this->Menu_model->get_menu();

        foreach ($menu['type-'.$this->admin_info['type']]['children'][0] as $key => $value) {
            $args = $menu['type-'.$this->admin_info['type']][$value]['act'];
            $menuList['top'][$menu['type-'.$this->admin_info['type']][$value]['menu_id']]['args'] = $args;
            $menuList['top'][$menu['type-'.$this->admin_info['type']][$value]['menu_id']]['text'] = $menu['type-'.$this->admin_info['type']][$value]['menu_title'];
        }
        foreach ($menuList['top'] as $key => $value) {
            $menuList['left'][$key]['nav'] = $value['args'];
            $menuList['left'][$key]['text'] = $value['text'];
            if (!empty($menu['type-'.$this->admin_info['type']]['children'][$key])) {
                foreach ($menu['type-'.$this->admin_info['type']]['children'][$key] as $k => $v) {
                $menuList['left'][$key]['list'][$v] = array(
                    'args' => $menu['type-'.$this->admin_info['type']][$v]['op'].','.$menu['type-'.$this->admin_info['type']][$v]['act'].','.$value['args'],
                    'text' => $menu['type-'.$this->admin_info['type']][$v]['menu_title']
                );
            }
            }
            
        }
        //var_dump($menuList['left']);exit;

        return $menuList;
    }

    /**
     * 过滤掉无权查看的菜单
     *
     * @param array $menu
     * @return array
     */
    private final function parseMenu($menu = array())
    {
        if ($this->admin_info['type'] != 1)
        {
           foreach ($menu['top'] as $key => $value) {
                if (!in_array($key, $this->permission)) {
                    unset($menu['top'][$key]);
                }
            }
            foreach ($menu['left'] as $k => $v) {
                if (!empty($v['list'])) {
                    foreach ($v['list'] as $xk => $xv) {
                        if (!in_array($xk, $this->permission)) {
                            unset($menu['left'][$k]['list'][$xk]);
                        }
                    }
                }
                
            } 
        }
        foreach ($menu['left'] as $key => $value) {
            if(empty($value['list'])) {
                unset($menu['top'][$key]);
                unset($menu['left'][$key]);
            }
        }
        return $menu;
    }

}
