<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>帮助中心</title>
    <?php echo _get_html_cssjs('admin_js', 'jquery.js,jquery.validation.min.js,admincp.js,jquery.cookie.js,common.js', 'js'); ?>
    <link href="<?php echo _get_cfg_path('admin') . TPL_ADMIN_NAME; ?>css/skin_0.css" type="text/css" rel="stylesheet"
          id="cssfile"/>
    <?php echo _get_html_cssjs('admin_css', 'perfect-scrollbar.min.css', 'css'); ?>

    <?php echo _get_html_cssjs('admin', TPL_ADMIN_NAME . 'css/font-awesome.min.css', 'css'); ?>

    <!--[if IE 7]>
    <?php echo _get_html_cssjs('admin',TPL_ADMIN_NAME.'css/font-awesome-ie7.min.css','css');?>
    <![endif]-->
    <?php echo _get_html_cssjs('admin_js', 'perfect-scrollbar.min.js', 'js'); ?>
    <style>
        a{
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>帮助中心</h3>
            <ul class="tab-base">
                <li><a class="current"><span>帮助中心列表</span></a></li>
                <li><a href="<?php echo BASE_SITE_URL?>/admin/help/add"><span>新增</span></a></li>

            </ul>
        </div>
    </div>
</div>
<div class="fixed-empty"></div>
<form method="post" name="formSearch" id="formSearch">
    <table class="tb-type1 noborder search">
        <tbody>
        <tr>
            <th>状态:</th>
            <td><select name="help_status" id="help_status">
                    <option value="">请选择...</option>
                    <option value="1" <?php if(isset($arrParam['help_status']) && $arrParam['help_status'] == 1) echo "selected";?>>显示</option>
                    <option value="-1" <?php if(isset($arrParam['help_status']) && $arrParam['help_status'] == -1) echo "selected";?>>隐藏</option>
                </select></td>
            <th>标题:</th>
            <td><input type="text"  name="help_name" id="help_name" class="txt" value="<?php if(isset($arrParam['help_name'])) echo $arrParam['help_name'];?>"></td>
            <td><button type="submit" class="btn">搜 索</button></td>
        </tr>

        </tbody>
    </table>
</form>

<input type="hidden" id="voucher_price_id" name="voucher_price_id" value=""/>
<table class="table tb-type2">
    <thead>
    <tr class="thead">
        <th></th>
        <th>id</th>
        <th>问题</th>
        <th>内容</th>
        <th>排序</th>
        <th>操作</th>

    </tr>
    </thead>
    <tbody id="list_content">
        <?php if (!empty($list['rows']) && is_array($list['rows'])) {?>
        <?php foreach($list['rows'] as $key => $value){?>
        <tr>
            <td></td>
            <td><?php echo $value['id'];?></td>
            <td><?php echo $value['title'];?></td>
            <td><?php echo mb_strlen($value['content'])>14?mb_substr($value['content'], 0,14).'...':$value['content'];?></td>
            <td><?php echo $value['sort'];?></td>
            <td><a href="<?php echo ADMIN_SITE_URL.'/help/add?id='.$value['id'];?>">编辑</a>　|
                <?php if($value['status'] == 1){?>
                　<a href="javascript:void(0)" val="<?php echo $value['id'];?>" onclick="updatestatus(<?php echo $value['id'];?>,-1)">隐藏</a>
                <?php }else{?>
                　<a href="javascript:void(0)" val="<?php echo $value['id'];?>" onclick="updatestatus(<?php echo $value['id'];?>,1)">显示</a>
                <?php }?>
            </td>
        </tr>
        <?php }}?>
    </tbody>
    <tfoot>
    <tr class="tfoot">
        <div class="pagination"></div>
        </td>
    </tr>
    </tfoot>
</table>
</body>
<script type="text/javascript">
function updatestatus(id,status){
    var id = id;
    var status = status;

    var data = {
        id:id,
        status:status
    };
    
    $.post('<?php echo ADMIN_SITE_URL.'/help/update_status'?>', data, function(response){
            if (response.status==1) {
              alert(response.msg);
              location.href='<?php echo ADMIN_SITE_URL.'/help'?>';
            }else{
              alert(response.msg);
              return false;
            }
          }, 'json');
}
</script>
</html>