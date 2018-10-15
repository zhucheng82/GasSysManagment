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
  <form id="add_form" method="post" name="adminForm" action="<?php echo ADMIN_SITE_URL.'/admin/admin_role_set'?>">
    <input type="hidden" name="role_id" value="<?php echo $info['id']?>" />
    <table class="table tb-type2">
		<tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="admin_name"><?php echo lang('gadmin_name');?>:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="role_name" value="<?php echo $info['role_name']?>" maxlength="40" name="role_name" class="txt"></td>
          <td class="vatop tips"></td>
        </tr>		
        <tr>
          <td colspan="2"><table class="table tb-type2 nomargin">
              <thead>
                <tr class="space">
                  <th> <input id="limitAll" id="limitAll" value="1" type="checkbox">&nbsp;&nbsp;<?php echo lang('admin_set_limt');?>
                  </th>
                </tr>
              </thead>
              <tbody>
                <?php foreach((array)$limit as $k => $v) { ?>
                <tr>
                  <td>
                  <label style="width:100px"><?php echo (!empty($v['nav'])) ? $v['nav'] : '&nbsp;'; ?></label>
                  <input id="limit<?php echo $k;?>" type="checkbox" onclick="selectLimit('limit<?php echo $k;?>')">
                    <label for="limit<?php echo $k;?>"><b><?php echo $v['name'];?></b>&nbsp;&nbsp;</label>
                      <?php foreach($v['child'] as $xk => $xv) { ?>
                        <label><input nctype='limit' class="limit<?php echo $k;?>"<?php if ($xv['op'] ==1){echo 'checked';}?> type="checkbox" name="permission[]" value="<?php echo $xv['act'];?>">
                        <?php echo $xv['name'];?>&nbsp;</label>
                      <?php } ?>
                    </td>
                </tr>
                <?php } ?>
              </tbody>
            </table></td>
        </tr>
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td><a href="JavaScript:void(0);" class="btn" onclick="document.adminForm.submit()"><span><?php echo lang('nc_submit');?></span></a></td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<script>
function selectLimit(name){
    if($('#'+name).attr('checked')) {
        $('.'+name).attr('checked',true);
    }else {
       $('.'+name).attr('checked',false);
    }
}
$(function(){
	//按钮先执行验证再提交表单
	$("#submitBtn").click(function(){
	    if($("#add_form").valid()){
	     $("#add_form").submit();
		}
	});

	$('#limitAll').click(function(){
		$('input[type="checkbox"]').attr('checked',$(this).attr('checked') == 'checked');
	});
	$("#add_form").validate({
		errorPlacement: function(error, element){
			error.appendTo(element.parent().parent().prev().find('td:first'));
        },
        rules : {
            role_name : {
                required : true,
				remote	: {
                    url :'<?php echo ADMIN_SITE_URL?>/admin/ajax?id=<?php echo $info['id']?>',
                    type:'get',
                    data:{
                    	role_name : function(){
                            return $('#role_name').val();
                        }
                    }
                }
            }
        },
        messages : {
        	role_name : {
                required : '<?php echo lang('nc_none_input');?>',
                remote	 : '<?php echo lang('admin_add_admin_not_exists');?>'
            }
        }
	});	
})
</script>
</body>
</html>