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
      <li><a href="<?php echo ADMIN_SITE_URL.'/admin';?>"><span><?php echo lang('limit_admin');?></span></a></li>
      <li><a href="JavaScript:void(0);" class="current"><span><?php echo lang('admin_add_limit_admin');?></span></a></li>
      <li><a href="<?php echo ADMIN_SITE_URL.'/admin/admin_role';?>"><span><?php echo lang('limit_gadmin');?></span></a></li>
      <li><a href="<?php echo ADMIN_SITE_URL.'/admin/admin_role_add';?>"><span><?php echo lang('admin_add_limit_gadmin');?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="add_form" method="post" action="<?php echo ADMIN_SITE_URL.'/admin/save'?>">
    <input type="hidden" name="form_submit" value="ok" />
    <table class="table tb-type2 nobdb">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="admin_name"><?php echo lang('admin_index_username');?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="admin_name" name="admin_name" class="txt" ></td>
          <td class="vatop tips"><?php echo lang('admin_add_username_tip');?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="admin_password"><?php echo lang('admin_index_password');?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="password" id="admin_password" name="admin_password" class="txt"></td>
          <td class="vatop tips"><?php echo lang('admin_add_password_tip');?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="admin_password"><?php echo lang('admin_rpassword');?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="password" id="admin_rpassword" name="admin_rpassword" class="txt"></td>
          <td class="vatop tips"><?php echo lang('admin_add_password_tip');?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="gadmin_name"><?php echo lang('gadmin_name');?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform">
          <select name="role_id">
          <?php foreach($role as $v){?>
          <option value="<?php echo $v['id'];?>"><?php echo $v['role_name'];?></option>
          <?php }?>
          </select>
          </td>
          <td class="vatop tips"><?php echo lang('admin_add_gid_tip');?></td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="2"><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span><?php echo lang('nc_submit');?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script>
//按钮先执行验证再提交表
$(document).ready(function(){
	//按钮先执行验证再提交表单
	$("#submitBtn").click(function(){
	    if($("#add_form").valid()){
	     $("#add_form").submit();
		}
	});
	
	$("#add_form").validate({
		errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },
        rules : {
            admin_name : {
                required : true,
        				minlength: 3,
        				maxlength: 20,
                remote  : {
                    url :'<?php echo ADMIN_SITE_URL?>/admin/ajax_check_name',
                    type:'get',
                    data:{
                      role_name : function(){
                            return $('#role_name').val();
                        }
                    }
                }
            },
            admin_password : {
                required : true,
				minlength: 6,
				maxlength: 20
            },
            admin_rpassword : {
                required : true,
                equalTo  : '#admin_password'
            },
            gid : {
                required : true
            }        
        },
        messages : {
            admin_name : {
                required : '<?php echo lang('admin_add_username_null');?>',
        				minlength: '<?php echo lang('admin_add_username_max');?>',
        				maxlength: '<?php echo lang('admin_add_username_max');?>',
        				remote	 : '该账号已存在'
            },
            admin_password : {
                required : '<?php echo lang('admin_add_password_null');?>',
        				minlength: '<?php echo lang('admin_add_password_max');?>',
        				maxlength: '<?php echo lang('admin_add_password_max');?>'
            },
            admin_rpassword : {
                required : '<?php echo lang('admin_add_password_null');?>',
                equalTo  : '<?php echo lang('admin_edit_repeat_error');?>'
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
