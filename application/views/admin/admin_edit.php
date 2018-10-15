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
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="admin_form" method="post" action='<?php echo ADMIN_SITE_URL.'/admin/admin_edit?id='.$info['id']?>'>
    <input type="hidden" name="user_id" value="<?php echo $info['id']?>" />
    <table class="table tb-type2">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label><?php echo lang('admin_index_username');?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><?php echo $info['name']?></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="new_pw"><?php echo lang('admin_edit_admin_pw'); ?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input id="new_pw" name="new_pw" class="txt" type="password"></td>
           <td class="vatop tips"><?php echo lang('admin_edit_pwd_tip1');?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label for="new_pw2"><?php echo lang('admin_edit_admin_pw2'); ?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input id="new_pw2" name="new_pw2" class="txt" type="password"></td>
          <td class="vatop tips"></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="gadmin_name"><?php echo lang('gadmin_name');?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
          <select name="role_id">
          <?php foreach((array)$role as $v){?>
          <option <?php if ($v['id'] == $info['role_id']) echo 'selected';?> value="<?php echo $v['id'];?>"><?php echo $v['role_name'];?></option>
          <?php }?>
          </select>
          </td>
          <td class="vatop tips"><?php echo lang('admin_add_gid_tip');?></td>
        </tr>        
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="2" ><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo lang('nc_submit');?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script>
//按钮先执行验证再提交表单
$(function(){$("#submitBtn").click(function(){
    if($("#admin_form").valid()){
     $("#admin_form").submit();
	}
	});
});
$(document).ready(function(){
	$("#admin_form").validate({
		errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },
        rules : {
        	new_pw : {
				minlength: 6,
				maxlength: 20
            },
            new_pw2 : {
				minlength: 6,
				maxlength: 20,
				equalTo: '#new_pw'
            },
            gid : {
                required : true
            }
        },
        messages : {
        	new_pw : {
				minlength: '<?php echo lang('admin_add_password_max');?>',
				maxlength: '<?php echo lang('admin_add_password_max');?>'
            },
            new_pw2 : {
				minlength: '<?php echo lang('admin_add_password_max');?>',
				maxlength: '<?php echo lang('admin_add_password_max');?>',
				equalTo:   '<?php echo lang('admin_edit_repeat_error');?>'
            },
            gid : {
                required : '<?php echo lang('admin_add_gid_null');?>'
            }
        }
	});
});
</script>
</body>
</html>
