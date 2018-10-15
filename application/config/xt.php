<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['cache_open'] = 0;

defined('SITE_URL') OR define('SITE_URL','http://'.$_SERVER['HTTP_HOST']);
$config['url'] = array(
    'base_site_url' => SITE_URL,
    'seller_site_url' => SITE_URL.'seller',
    'admin_site_url' => SITE_URL.'/admin',
    'wap_site_url' => SITE_URL.'/wap',
    'upload_site_url' => SITE_URL.'/',
);


$config['basic_info'] = array(
    'PLATFORM_ID' => 9,
    'TAKE_CASH_LIMIT_MAX' => 2000,
    'TAKE_CASH_LIMIT_MIN' => 1,
    'TAKE_CASH_EACH_FEE' => 2,
    'MD5_KEY' => 'd@36$hS('
);


$config['cookie_pre'] = 'borrowing';
$config['lang_type'] = 'zh_cn';
$config['url_model'] = false;

$config['cfg_path'] = array(
    'res' => '/res/',
    'css' => '/res/front/css/',
    'js' => '/res/front/js/',
    'images' => '/res/front/images/',
    'font' => '/res/font/',
    'lib' => '/res/lib/',
    'admin' => '/res/admin/',
    'admin_css' => '/res/admin/css/',
    'admin_js' => '/res/admin/js/',
    'admin_js_fileupload' => '/res/admin/js/fileupload/',
    'admin_images' => '/res/admin/images/',
    'seller' => '/res/seller/',
    'seller_css' => '/res/seller/css/',
    'seller_js' => '/res/seller/js/',
    'seller_images' => '/res/seller/images/',
);

$config['sex'] = array(
    '1' => '男',
    '2' => '女',
);

//银行列表
$config['bank_list'] = array(
    array(
        'id' => '1',
        'name' => '中国银行',
        'icon_url' => $config['url']['base_site_url'] . '/res/admin/images/1.png'
    ),
    array(
        'id' => '2',
        'name' => '中国农业银行',
        'icon_url' => $config['url']['base_site_url'] . '/res/admin/images/2.png'
    ),
    array(
        'id' => '3',
        'name' => '中国工商银行',
        'icon_url' => $config['url']['base_site_url'] . '/res/admin/images/3.png'
    ),
    array(
        'id' => '4',
        'name' => '中国建设银行',
        'icon_url' => $config['url']['base_site_url'] . '/res/admin/images/4.png'
    )
);

$config['SmsTemplate'] = array(
    'SmsCode' => '3031604',
    'RepaymentRemind' => '3029501',
    'DelayRemind' => '3029502',
    'BorrowingApproved' => '3029503',
    'Withdraw'=>'3033474',
    'RetentionMoneyReturn'=>'3032493',
    'AutoRepayment'=>'3057064',
    'WithdrawNotifyLoan'=>'3060020',
    'RechargeNotifyLoan'=>'3050096'
);

$config['PayMethod'] = array(
    'WeixinPayApp' => 'WeixinPayApp',
    'WeixinPayJs' => 'WeixinPayJs',
    'AlipayApp' => 'AlipayApp',
    'AliPayJs' => 'AlipayApp',
    'YinlianPay'=>'YinlianPay',
    'UnionPayDS'=>'UnionPayDS',
);
$config['PayMethodType'] = array(
    'WeixinPayApp' => 11,
    'WeixinPayJs' => 12,
    'AlipayApp' => 13,
    'AliPayJs' => 14,
    'YinlianPay' =>15,
    'UnionPayDS' => 16//代收

);

$config['PayMethodName'] = array(
    11 => '微信APP',
    12 => '微信Wap',
    13 => '支付宝app',
    14 => '支付宝Wap',
    15 => '银联',
    16 => '银联代收',
    17 => '账户余额'
);

$config['ProductType'] = array(
    1 => '现金借款',
    2 => '电瓶车借款',
    3 => '租房借款',
);

$config['RepaymentType'] = array(
    1 => '按月还',
    2 => '按周还',
    3 => '按天还',
);

$config['Education'] = array(
    1 => '成教',
    2 => '专科',
    3 => '本科',
    4 => '研究生'
);

$config['usage'] = array(
    1 => '旅游',
    2 => '培训助学',
    3 => '应急周转'
);

$config['pay_way'] = array(
    1 => '押一付一',
    2 => '押一付二',
    3 => '押一付三',
    4 => '押一付四',
    5 => '押一付五',
    6 => '押一付六'
);

#-1—审批被拒绝；0—申请中；1—通过申请；2-还款中 3-还款结束 4-因故被终止
$config['BorrowingStatus'] =array(
    '-1' => '审批被拒绝',
    '0' => '申请中',
    '1' => '通过申请',
    '2' => '还款中',
    '3' => '还款结束',
    '4' => '因故被终止',
);

$config['BorrowingRate'] = array(
    'CompensationRate' => 0.05,//提前还清补偿金率
    'ServiceFee' => 0.05,//服务费
    'LateInterestRate' => 0.01,//罚息
);

$config['company_nature'] = array(
    1 => '私营/民企',
    2 => '事业单位',
    3 => '国家机关'
);

$config['company_position'] = array(
    1 => '总经理',
    2 => '部门经理/总监',
    3 => '基层员工'
);

$config['notify_mobile_list'] = array(
    1 => '15067165009',
    2 => '15167182654',
    3 => '13777867377');

$config['share_info'] = array('share_title'=>'浙贷宝','share_description'=>'有了浙贷宝，资金周转不用愁'
);

define('SHARE_URL',$config['url']['base_site_url'].'/help/share.html');
define('HELP_URL',$config['url']['base_site_url'].'/help/articlelist.html');

define('BASE_SITE_URL', $config['url']['base_site_url']);
define('SELLER_SITE_URL', $config['url']['seller_site_url']);
define('RES_SITE_URL', $config['url']['base_site_url'] . $config['cfg_path']['res']);


define('ADMIN_SITE_URL', $config['url']['admin_site_url']);
//define('MOBILE_SITE_URL', $config['mobile_site_url']);
define('WAP_SITE_URL', $config['url']['wap_site_url']);
define('UPLOAD_SITE_URL', $config['url']['upload_site_url']);

define('BASE_UPLOAD_PATH', BASE_ROOT_PATH . '/upload');
define('LANG_TYPE', $config['lang_type']);
define('URL_MODEL', $config['url_model']);

define('COOKIE_PRE', $config['cookie_pre']);

define('TPL_ADMIN_NAME', 'templates/default/');

define('ATTACH_PATH', 'shop');
define('ATTACH_GOODS', ATTACH_PATH . '/goods');

/**
 * 商品图片
 */
define('GOODS_IMAGES_WIDTH', '60,240,360,1280');
define('GOODS_IMAGES_HEIGHT', '60,240,360,12800');
define('GOODS_IMAGES_EXT', '_60,_240,_360,_1280');


define('Failure', 'Failure');
define('Success', 'Success');
define('APPNAME', '浙贷宝');


