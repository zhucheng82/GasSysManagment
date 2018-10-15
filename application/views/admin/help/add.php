<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>帮助中心</title>
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
      <h3>帮助中心</h3>
      <ul class="tab-base">
      <li><a href="<?php echo ADMIN_SITE_URL.'/help';?>"><span>帮助中心列表</span></a></li>
      <li><a href="JavaScript:void(0);" class="current"><span><?php echo empty($data)?'新增':'编辑';?></span></a></li>
      </ul>
    </div>
  </div>
  <div class="fixed-empty"></div>
  <form id="add_form" method="post" action="<?php echo ADMIN_SITE_URL.'/help/add_do'?>">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" name="id" value="<?php echo empty($data)?'':$data['id']?>" />
    <table class="table tb-type2 nobdb">
      <tbody>
        <tr class="noborder">
          <td colspan="2" class="required"><label class="validation" for="question">问题:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><textarea id="question" name="question" class="txt" ><?php if(!empty($data)){echo $data['title'];}?></textarea></td>
          <td class="vatop tips"><?php echo lang('admin_add_username_tip');?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="answer">回答:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><textarea id="answer" name="answer" ><?php if(!empty($data)){echo $data['content'];}?></textarea></td>
          <td class="vatop tips"><?php echo lang('admin_add_password_tip');?></td>
        </tr>
        <tr>
          <td colspan="2" class="required"><label class="validation" for="sort">排序:</label></td>
        </tr>
        <tr class="noborder">
          <td class="vatop rowform"><input type="text" id="sort" name="sort" class="txt" value="<?php if(!empty($data)){echo $data['sort'];}?>"></td>
          <td class="vatop tips"><?php echo lang('admin_add_password_tip');?></td>
        </tr>
        
      </tbody>
      <tfoot>
        <tr class="tfoot">
          <td colspan="2"><a href="JavaScript:void(0);" class="btn" id="submitBtn"><span>提交</span></a></td>
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
            question : {
                required : true
            },
            answer : {
                required : true
            }
       
        },
        messages : {
            question : {
                required : '不能为空'
            },
            answer : {
                required : '不能为空'
            }
        }
	});
});
</script>
</body>
</html>
