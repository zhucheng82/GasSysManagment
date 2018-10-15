<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>big city</title>
<?php echo _get_html_cssjs('admin_js','jquery.js,jquery.validation.min.js,admincp.js,jquery.cookie.js,common.js','js');?>
<link href="<?php echo _get_cfg_path('admin').TPL_ADMIN_NAME;?>css/skin_0.css" type="text/css" rel="stylesheet" id="cssfile" />
<?php echo _get_html_cssjs('admin_css','perfect-scrollbar.min.css','css');?>

<?php echo _get_html_cssjs('admin',TPL_ADMIN_NAME.'css/font-awesome.min.css','css');?>

<!--[if IE 7]>
  <?php echo _get_html_cssjs('admin',TPL_ADMIN_NAME.'css/font-awesome-ie7.min.css','css');?>
<![endif]-->
<?php echo _get_html_cssjs('admin_js','perfect-scrollbar.min.js','js');?>

</head>
<body>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <h3><?php echo lang('nc_limit_manage');?></h3>
      <ul class="tab-base">
      <li><a href="JavaScript:void(0);" class="current"><span><?php echo lang('limit_admin');?></span></a></li>
      <li><a href="<?php echo ADMIN_SITE_URL.'/admin/admin_add';?>"><span><?php echo lang('admin_add_limit_admin');?></span></a></li>
      <li><a href="<?php echo ADMIN_SITE_URL.'/admin/admin_role';?>"><span><?php echo lang('limit_gadmin');?></span></a></li>
      <li><a href="<?php echo ADMIN_SITE_URL.'/admin/admin_role_add';?>"><span><?php echo lang('admin_add_limit_gadmin');?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form method="post" id='form_admin' action="<?php echo ADMIN_SITE_URL.'/admin/del'?>">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2">
      <thead>
        <tr class="space">
          <th colspan="15" class="nobg"><?php echo lang('nc_list');?></th>
        </tr>
        <tr class="thead">
          <th></th>
          <th><?php echo lang('admin_index_username');?></th>
          <th class="align-center"><?php echo lang('admin_index_last_login');?></th>
          <th class="align-center"><?php echo lang('admin_index_login_times');?></th>
          <th class="align-center"><?php echo lang('gadmin_name');?></th>
          <th class="align-center"><?php echo lang('nc_handle');?></th>
        </tr>
      </thead>
      <tbody>
        <?php if(!empty($list['rows']) && is_array($list['rows'])){ ?>
        <?php foreach($list['rows'] as $k => $v){ ?>
        <tr class="hover">
          <td class="w24"><?php if ($v['is_super'] != 1){?>
            <input type="checkbox" name="del_id[]" value="<?php echo $v['id']; ?>" class="checkitem" onclick="javascript:chkRow(this);">
            <?php }else { ?>
            <input name="del_id[]" type="checkbox" value="<?php echo $v['id']; ?>" disabled="disabled">
            <?php }?></td>
          <td><?php echo $v['name'];?></td>
          <td class="align-center"><?php echo $v['login_time'] ? date('Y-m-d H:i:s',$v['login_time']) : lang('admin_index_login_null'); ?></td>
          <td class="align-center"><?php echo $v['login_num']; ?></td>
          <td class="align-center"><?php echo $v['role_id']; ?></td>
          <td class="w150 align-center"><?php if($v['is_super'] == 1){?>
            <?php echo lang('admin_index_sys_admin_no');?>
            <?php }else{?>
            <a href="javascript:void(0)" onclick="if(confirm('<?php echo lang('nc_ensure_del');?>')){location.href='<?php echo ADMIN_SITE_URL.'/admin/del?id='.$v['id']; ?>'}"><?php echo lang('admin_index_del_admin');?></a> | <a href="<?php echo ADMIN_SITE_URL.'/admin/admin_edit?id='.$v['id']; ?>"><?php echo lang('admin_index_edit');?></a>
            <?php }?></td>
        </tr>
        <?php } ?>
        <?php }else { ?>
        <tr class="no_data">
          <td colspan="10"><?php echo lang('nc_no_record');?></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <?php if(!empty($list) && is_array($list)){ ?>
        <tr class="tfoot">
          <td><input type="checkbox" class="checkall" id="checkallBottom" name="chkVal"></td>
          <td colspan="16"><label for="checkallBottom"><?php echo lang('nc_select_all'); ?></label>
            &nbsp;&nbsp;<a href="JavaScript:void(0);" class="btn" onclick="if(confirm('<?php echo lang('nc_ensure_del');?>')){$('#form_admin').submit();}"><span><?php echo lang('nc_del_all');?></span></a>
            <div class="pagination"> <?php echo $list['pages'];?> </div></td>
        </tr>
        <?php } ?>
      </tfoot>
    </table>
  </form>
</div>
</body>
</html>
