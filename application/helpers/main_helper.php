<?php
/*公用函数库*/

function _request_format($request_data, $method = 'post')
{
    if ($method == 'post') {
        foreach ($request_data as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $kk => $vv) {
                    $a = $k . '[' . $kk . ']';
                    $request_data[$a] = $vv;
                }
                unset($request_data[$k]);

                if (is_array($vv)) {
                    return _request_format($request_data);
                }
            }
        }
    }
    return $request_data;
}

function _get_userlogo_url($userlogo)
{

    return $userlogo ? BASE_SITE_URL.'/' . trim($userlogo, '/') : _get_cfg_path('admin_images') . 'default_portrait.png';

}

function _get_doctorlogo_url($logo)
{

    return $logo ? BASE_SITE_URL.'/' . trim($logo, '/') : _get_cfg_path('admin_images') . 'default_doctor_portrait.png';

}

function _get_hospitallogo_url($logo)
{

    return $logo ? BASE_SITE_URL.'/' . trim($logo, '/') : _get_cfg_path('admin_images') . 'default_hospital_portrait.png';

}


function _get_login_agent_user()
{
    $CI =& get_instance();

    $agentUser = $CI->cache->get('agentUser');
    if (!empty($agentUser))
        return $agentUser;
    else
        return $CI->loginUser;
}

function _get_image_url($img)
{

    return $img ? BASE_SITE_URL.'/' . trim($img, '/') : '';

}

function _check_password_safe($pwd)
{
    $res = 1;
    if (preg_match('/^[0-9]{1,6}$/', $pwd))
        $res = 1;
    else if (preg_match('/^([a-z]+(?=[0-9])|[0-9]+(?=[a-z]))[a-z0-9]+$/i', $pwd))
        $res = 3;
    else
        $res = 2;

    return $res;
}

function _sendSms($mobile, $message, $sendTime, $smsUrl)
{
    //todo:
    return true;
}




/**
 * 取上一步来源地址
 *
 * @param
 * @return string 字符串类型的返回结果
 */
function getReferer()
{
    return empty($_SERVER['HTTP_REFERER']) ? '' : $_SERVER['HTTP_REFERER'];
}

/**
 * 抛出异常
 *
 * @param string $error 异常信息
 */
function throw_exception($error)
{
    if (!defined('IGNORE_EXCEPTION')) {
        showMessage($error, '', 'exception');
    } else {
        exit();
    }
}




/**
 * 构造API返回action参数
 * @return string
 */
function get_api_action()
{
    $path_info = !empty($_SERVER['PATH_INFO']) ? str_replace('/api/', '', $_SERVER['PATH_INFO']) : '';
    return trim(str_replace('/', '_', $path_info), '_');
}


/**
 * 输出成功JSON
 * @param array $datas
 * @param array $extend_data
 * @return string
 */
function output_data(array $datas = array())
{
    $data = array();
    $data['code'] = 1;
    $data['msg'] = 'SUCCESS';
    $data['action'] = get_api_action();
    if (empty($datas)) {
        $data['data'] = new stdClass();
    } else {
        $data['data'] = $datas;
    }
    if (!empty($_GET['callback'])) {
        echo $_GET['callback'] . '(' . json_encode($data) . ')';
        exit;
    } else {
        echo json_encode($data);
        exit;
    }
}


/**
 * 输出失败JSON
 * @param int $errCode
 * @param string $message
 * @return string
 */
function output_error($errCode, $message,array $datas=array())
{
    $data['code'] = $errCode;
    $data['msg'] = $message;
    $data['action'] = get_api_action();
    if(empty($datas)){
        $data['data'] = new stdClass();
    }else{
        $data['data'] = $datas;
    }
    if (!empty($_GET['callback'])) {
        echo $_GET['callback'] . '(' . json_encode($data) . ')';
        exit;
    } else {
        echo json_encode($data);
        exit;
    }
}



/**
 * 输出信息
 *
 * @param string $msg 输出信息
 * @param string /array $url 跳转地址 当$url为数组时，结构为 array('msg'=>'跳转连接文字','url'=>'跳转连接');
 * @param string $show_type 输出格式 默认为html
 * @param string $msg_type 信息类型 succ 为成功，error为失败/错误
 * @param string $is_show 是否显示跳转链接，默认是为1，显示
 * @param int $time 跳转时间，默认为2秒
 * @return string 字符串类型的返回结果
 */
function showMessage($msg, $url = '', $show_type = 'html', $msg_type = 'succ', $is_show = 1, $time = 2000)
{
    $CI =& get_instance();
    $CI->lang->load('core_index');
    /**
     * 如果默认为空，则跳转至上一步链接
     */
    $url = ($url != '' ? $url : getReferer());

    $msg_type = in_array($msg_type, array('succ', 'error')) ? $msg_type : 'error';

    /**
     * 输出类型
     */
    switch ($show_type) {
        case 'json':
            $return = '{';
            $return .= '"msg":"' . $msg . '",';
            $return .= '"url":"' . $url . '"';
            $return .= '}';
            echo $return;
            break;
        case 'exception':
            echo '<!DOCTYPE html>';
            echo '<html>';
            echo '<head>';
            echo '<meta http-equiv="Content-Type" content="text/html; charset=' . CHARSET . '" />';
            echo '<title></title>';
            echo '<style type="text/css">';
            echo 'body { font-family: "Verdana";padding: 0; margin: 0;}';
            echo 'h2 { font-size: 12px; line-height: 30px; border-bottom: 1px dashed #CCC; padding-bottom: 8px;width:800px; margin: 20px 0 0 150px;}';
            echo 'dl { float: left; display: inline; clear: both; padding: 0; margin: 10px 20px 20px 150px;}';
            echo 'dt { font-size: 14px; font-weight: bold; line-height: 40px; color: #333; padding: 0; margin: 0; border-width: 0px;}';
            echo 'dd { font-size: 12px; line-height: 40px; color: #333; padding: 0px; margin:0;}';
            echo '</style>';
            echo '</head>';
            echo '<body>';
            echo '<h2>' . lang('error_info') . '</h2>';
            echo '<dl>';
            echo '<dd>' . $msg . '</dd>';
            echo '<dt><p /></dt>';
            echo '<dd>' . lang('error_notice_operate') . '</dd>';
            echo '<dd><p /><p /><p /><p /></dd>';
            echo '<dd><p /><p /><p /><p /></dd>';
            echo '</dl>';
            echo '</body>';
            echo '</html>';
            exit;
            break;
        case 'javascript':
            echo "<script>";
            echo "alert('" . $msg . "');";
            echo "location.href='" . $url . "'";
            echo "</script>";
            exit;
            break;
        case 'tenpay':
            echo "<html><head>";
            echo "<meta name=\"TENCENT_ONLINE_PAYMENT\" content=\"China TENCENT\">";
            echo "<script language=\"javascript\">";
            echo "window.location.href='" . $url . "';";
            echo "</script>";
            echo "</head><body></body></html>";
            exit;
            break;
        default:
            /**
             * 不显示右侧工具条
             */
            Tpl::output('hidden_nctoolbar', 1);
            if (is_array($url)) {
                foreach ($url as $k => $v) {
                    $url[$k]['url'] = $v['url'] ? $v['url'] : getReferer();
                }
            }
            /**
             * 读取信息布局的语言包
             */
            $CI->lang->load('msg');
            /**
             * html输出形式
             * 指定为指定项目目录下的error模板文件
             */
            Tpl::setDir('admin');
            Tpl::output('html_title', lang('nc_html_title'));
            Tpl::output('msg', $msg);
            Tpl::output('url', $url);
            Tpl::output('msg_type', $msg_type);
            Tpl::output('is_show', $is_show);
            Tpl::showpage('msg', 'msg_layout', '', $time);
    }
    exit;
}

/**
 * 消息提示，主要适用于普通页面AJAX提交的情况
 *
 * @param string $message 消息内容
 * @param string $url 提示完后的URL去向
 * @param stting $alert_type 提示类型 error/succ/notice 分别为错误/成功/警示
 * @param string $extrajs 扩展JS
 * @param int $time 停留时间
 */
function showDialog($message = '', $url = '', $alert_type = 'error', $extrajs = '', $time = 2)
{
    if (empty($_GET['inajax'])) {
        if ($url == 'reload') $url = '';
        showMessage($message . $extrajs, $url, 'html', $alert_type, 1, $time * 1000);
    }
    $message = str_replace("'", "\\'", strip_tags($message));

    $paramjs = null;
    if ($url == 'reload') {
        $paramjs = 'window.location.reload()';
    } elseif ($url != '') {
        $paramjs = 'window.location.href =\'' . $url . '\'';
    }
    if ($paramjs) {
        $paramjs = 'function (){' . $paramjs . '}';
    } else {
        $paramjs = 'null';
    }
    $modes = array('error' => 'alert', 'succ' => 'succ', 'notice' => 'notice', 'js' => 'js');
    $cover = $alert_type == 'error' ? 1 : 0;
    $extra .= 'showDialog(\'' . $message . '\', \'' . $modes[$alert_type] . '\', null, ' . ($paramjs ? $paramjs : 'null') . ', ' . $cover . ', null, null, null, null, ' . (is_numeric($time) ? $time : 'null') . ', null);';
    $extra = $extra ? '<script type="text/javascript" reload="1">' . $extra . '</script>' : '';
    if ($extrajs != '' && substr(trim($extrajs), 0, 7) != '<script') {
        $extrajs = '<script type="text/javascript" reload="1">' . $extrajs . '</script>';
    }
    $extra .= $extrajs;
    ob_end_clean();
    @header("Expires: -1");
    @header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
    @header("Pragma: no-cache");
    @header("Content-type: text/xml; charset=" . CHARSET);

    $string = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\r\n";
    $string .= '<root><![CDATA[' . $message . $extra . ']]></root>';
    echo $string;
    exit;
}


/**
 * 不显示信息直接跳转
 *
 * @param string $url
 */
function go_redirect($url = '')
{
    if (empty($url)) {
        if (!empty($_REQUEST['ref_url'])) {
            $url = $_REQUEST['ref_url'];
        } else {
            $url = getReferer();
        }
    }
    header('Location: ' . $url);
    echo $url;
    die;
    exit();
}


/**
 * 返回模板文件所在完整目录
 *
 * @param str $tplpath
 * @return string
 */
function template($tplpath)
{
    return VIEWPATH . '/' . $tplpath . '.php';
}

/**
 * 编辑器内容
 *
 * @param int $id 编辑器id名称，与name同名
 * @param string $value 编辑器内容
 * @param string $width 宽 带px
 * @param string $height 高 带px
 * @param string $style 样式内容
 * @param string $upload_state 上传状态，默认是开启
 */
function showEditor($id, $value = '', $width = '700px', $height = '300px', $style = 'visibility:hidden;', $upload_state = "true", $media_open = false, $type = 'all')
{
    //是否开启多媒体
    $media = '';
    if ($media_open) {
        $media = ", 'flash', 'media'";
    }
    switch ($type) {
        case 'basic':
            $items = "['source', '|', 'fullscreen', 'undo', 'redo', 'cut', 'copy', 'paste', '|', 'about']";
            break;
        case 'simple':
            $items = "['source', '|', 'fullscreen', 'undo', 'redo', 'cut', 'copy', 'paste', '|',
            'fontname', 'fontsize', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
            'removeformat', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
            'insertunorderedlist', '|', 'emoticons', 'image', 'link', '|', 'about']";
            break;
        default:
            $items = "['source', '|', 'fullscreen', 'undo', 'redo', 'print', 'cut', 'copy', 'paste',
            'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
            'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
            'superscript', '|', 'selectall', 'clearhtml','quickformat','|',
            'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
            'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 'image'" . $media . ", 'table', 'hr', 'emoticons', 'link', 'unlink', '|', 'about']";
            break;
    }
    //图片、Flash、视频、文件的本地上传都可开启。默认只有图片，要启用其它的需要修改resource\kindeditor\php下的upload_json.php的相关参数
    echo '<textarea id="' . $id . '" name="' . $id . '" style="width:' . $width . ';height:' . $height . ';' . $style . '">' . $value . '</textarea>';
    echo '
<script src="' . _get_cfg_path('lib') . '/kindeditor/kindeditor-min.js" charset="utf-8"></script>
<script src="' . _get_cfg_path('lib') . '/kindeditor/lang/zh_CN.js" charset="utf-8"></script>
<script>
	var KE;
  KindEditor.ready(function(K) {
        KE = K.create("textarea[name=\'' . $id . '\']", {
						items : ' . $items . ',
						cssPath : "' . _get_cfg_path('lib') . '/kindeditor/themes/default/default.css",
						allowImageUpload : ' . $upload_state . ',
						allowFlashUpload : false,
						allowMediaUpload : false,
						allowFileManager : false,
						syncType:"form",
						afterCreate : function() {
							var self = this;
							self.sync();
						},
						afterChange : function() {
							var self = this;
							self.sync();
						},
						afterBlur : function() {
							var self = this;
							self.sync();
						}
        });
			KE.appendHtml = function(id,val) {
				this.html(this.html() + val);
				if (this.isCreated) {
					var cmd = this.cmd;
					cmd.range.selectNodeContents(cmd.doc.body).collapse(false);
					cmd.select();
				}
				return this;
			}
	});
</script>
	';
    return true;
}


/**
 * 拼接动态URL，参数需要小写
 *
 * 调用示例
 *
 * 若指向网站首页，可以传空:
 * url() => 表示act和op均为index，返回当前站点网址
 *
 * url('search,'index','array('cate_id'=>2)); 实际指向 index.php?act=search&op=index&cate_id=2
 * 传递数组参数时，若act（或op）值为index,则可以省略
 * 上面示例等同于
 * url('search','',array('act'=>'search','cate_id'=>2));
 *
 * @param string $act control文件名
 * @param string $op op方法名
 * @param array $args URL其它参数
 * @param boolean $model 默认取当前系统配置
 * @param string $site_url 生成链接的网址，默认取当前网址
 * @return string
 */
function url($act = '', $op = '', $args = array(), $model = false, $site_url = '')
{
    //伪静态文件扩展名
    $ext = '.html';
    //入口文件名
    //$file = 'index.php';
//    $site_url = empty($site_url) ? SHOP_SITE_URL : $site_url;
    $act = trim($act);
    $op = trim($op);
    $args = !is_array($args) ? array() : $args;
    //定义变量存放返回url
    $url_string = '';
    if (empty($act) && empty($op) && empty($args)) {
        return $site_url;
    }
    //$act = !empty($act) ? $act : 'index';
    $op = !empty($op) ? $op : 'index';

    $model = $model ? URL_MODEL : $model;

    if ($model) {
        //伪静态模式
        $url_perfix = "{$act}-{$op}";
        if (!empty($args)) {
            $url_perfix .= '-';
        }
        $url_string = $url_perfix . http_build_query($args, '', '-') . $ext;
        $url_string = str_replace('=', '-', $url_string);
    } else {
        //默认路由模式
        $url_path = "{$act}/{$op}";
        $url_string = $url_path;
        if (!empty($args))
            $url_string .= '?' . http_build_query($args);
    }
    //将商品、店铺、分类、品牌、文章自动生成的伪静态URL使用短URL代替
    $reg_match_from = array(
        '/^category-index\.html$/',
        '/^goods-index-goods_id-(\d+)\.html$/',
        '/^show_store-index-store_id-(\d+)\.html$/',
        '/^show_store-goods_all-store_id-(\d+)-stc_id-(\d+)-key-([0-5])-order-([0-2])-curpage-(\d+)\.html$/',
        '/^article-show-article_id-(\d+)\.html$/',
        '/^article-article-ac_id-(\d+)\.html$/',
        '/^document-index-code-([a-z_]+)\.html$/',
        '/^search-index-cate_id-(\d+)-b_id-([0-9_]+)-a_id-([0-9_]+)-key-([0-3])-order-([0-2])-type-([0-1])-gift-([0-1])-area_id-(\d+)-curpage-(\d+)\.html$/',
        '/^brand-list-brand-(\d+)-key-([0-3])-order-([0-2])-type-([0-1])-gift-([0-1])-area_id-(\d+)-curpage-(\d+)\.html$/',
        '/^brand-index\.html$/',

        '/^show_groupbuy-index\.html$/',
        '/^show_groupbuy-groupbuy_detail-group_id-(\d+)\.html$/',

        '/^show_groupbuy-groupbuy_list-class-(\d+)-s_class-(\d+)-groupbuy_price-(\d+)-groupbuy_order_key-(\d+)-groupbuy_order-(\d+)-curpage-(\d+)\.html$/',
        '/^show_groupbuy-groupbuy_soon-class-(\d+)-s_class-(\d+)-groupbuy_price-(\d+)-groupbuy_order_key-(\d+)-groupbuy_order-(\d+)-curpage-(\d+)\.html$/',
        '/^show_groupbuy-groupbuy_history-class-(\d+)-s_class-(\d+)-groupbuy_price-(\d+)-groupbuy_order_key-(\d+)-groupbuy_order-(\d+)-curpage-(\d+)\.html$/',

        '/^show_groupbuy-vr_groupbuy_list-vr_class-(\d+)-vr_s_class-(\d+)-vr_area-(\d+)-vr_mall-(\d+)-groupbuy_price-(\d+)-groupbuy_order_key-(\d+)-groupbuy_order-(\d+)-curpage-(\d+)\.html$/',
        '/^show_groupbuy-vr_groupbuy_soon-vr_class-(\d+)-vr_s_class-(\d+)-vr_area-(\d+)-vr_mall-(\d+)-groupbuy_price-(\d+)-groupbuy_order_key-(\d+)-groupbuy_order-(\d+)-curpage-(\d+)\.html$/',
        '/^show_groupbuy-vr_groupbuy_history-vr_class-(\d+)-vr_s_class-(\d+)-vr_area-(\d+)-vr_mall-(\d+)-groupbuy_price-(\d+)-groupbuy_order_key-(\d+)-groupbuy_order-(\d+)-curpage-(\d+)\.html$/',

        '/^pointshop-index.html$/',
        '/^pointprod-plist.html$/',
        '/^pointprod-pinfo-id-(\d+).html$/',
        '/^pointvoucher-index.html$/',
        '/^pointgrade-index.html$/',
        '/^pointgrade-exppointlog-curpage-(\d+).html$/',
        '/^goods-comments_list-goods_id-(\d+)-type-([0-3])-curpage-(\d+).html$/'
    );
    $reg_match_to = array(
        'category.html',
        'item-\\1.html',
        'shop-\\1.html',
        'shop_view-\\1-\\2-\\3-\\4-\\5.html',
        'article-\\1.html',
        'article_cate-\\1.html',
        'document-\\1.html',
        'cate-\\1-\\2-\\3-\\4-\\5-\\6-\\7-\\8-\\9.html',
        'brand-\\1-\\2-\\3-\\4-\\5-\\6-\\7.html',
        'brand.html',

        'groupbuy.html',
        'groupbuy_detail-\\1.html',

        'groupbuy_list-\\1-\\2-\\3-\\4-\\5-\\6.html',
        'groupbuy_soon-\\1-\\2-\\3-\\4-\\5-\\6.html',
        'groupbuy_history-\\1-\\2-\\3-\\4-\\5-\\6.html',

        'vr_groupbuy_list-\\1-\\2-\\3-\\4-\\5-\\6-\\7-\\8.html',
        'vr_groupbuy_soon-\\1-\\2-\\3-\\4-\\5-\\6-\\7-\\8.html',
        'vr_groupbuy_history-\\1-\\2-\\3-\\4-\\5-\\6-\\7-\\8.html',

        'integral.html',
        'integral_list.html',
        'integral_item-\\1.html',
        'voucher.html',
        'grade.html',
        'explog-\\1.html',
        'comments-\\1-\\2-\\3.html'
    );
    $url_string = preg_replace($reg_match_from, $reg_match_to, $url_string);
    return rtrim($site_url, '/') . '/' . $url_string;
}


?>