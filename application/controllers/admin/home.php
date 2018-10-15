<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends MY_Admin_Controller
{

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
        
        $this->load->view('admin/home', $result);
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
