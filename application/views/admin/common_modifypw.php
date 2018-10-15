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
    <form id="admin_form" method="post" action='<?php echo ADMIN_SITE_URL.'/common/modifypw'?>' name="adminForm">
        <input type="hidden" name="form_submit" value="ok" />
        <table class="table tb-type2">
            <tbody>
            <tr class="noborder">
                <td colspan="2" class="required"><label class="validation" for="old_pw">原密码<!-- 原密码 -->:</label></td>
            </tr>
            <tr class="noborder">
                <td class="vatop rowform"><input id="old_pw" name="old_pw" class="infoTableInput" type="password"></td>
                <td class="vatop tips"></td>
            </tr>
            <tr>
                <td colspan="2" class="required"><label class="validation" for="new_pw">新密码<!-- 新密码 -->:</label></td>
            </tr>
            <tr class="noborder">
                <td class="vatop rowform"><input id="new_pw" name="new_pw" class="infoTableInput" type="password"></td>
                <td class="vatop tips"></td>
            </tr>
            <tr>
                <td colspan="2" class="required"><label class="validation" for="new_pw2">确认密码<!-- 确认密码-->:</label></td>
            </tr>
            <tr class="noborder">
                <td class="vatop rowform"><input id="new_pw2" name="new_pw2" class="infoTableInput" type="password"></td>
                <td class="vatop tips"></td>
            </tr>
            </tbody>
            <tfoot>
            <tr class="tfoot">
                <td colspan="2" >
                    <!--<a href="JavaScript:void(0);" class="btn" id="submitBtn"><span>提交</span></a>-->
                    <input type="submit"  class="btn" id="submitBtn" />
                    <input type="button" class="btn" onclick="history.go(-1)" value="返回">
                </td>
            </tr>
            </tfoot>
        </table>
    </form>
</div>
<script>
    //按钮先执行验证再提交表单
        $("#submitBtn").click(function(){

            $("#admin_form").submit();
        });
    //
    $(document).ready(function(){
        $("#admin_form").validate({
            errorPlacement: function(error, element){
                error.appendTo(element.parent().parent().prev().find('td:first'));
            },
            rules : {
                old_pw : {
                    required : true
                },
                new_pw : {
                    required : true,
                    minlength: 6,
                    maxlength: 20
                },
                new_pw2 : {
                    required : true,
                    minlength: 6,
                    maxlength: 20,
                    equalTo: '#new_pw'
                }
            },
            messages : {
                old_pw : {
                    required : '<?php echo lang('admin_add_password_null');?>'
                },
                new_pw : {
                    required : '<?php echo lang('admin_add_password_null');?>',
                    minlength: '<?php echo lang('admin_add_password_max');?>',
                    maxlength: '<?php echo lang('admin_add_password_max');?>'
                },
                new_pw2 : {
                    required : '<?php echo lang('admin_add_password_null');?>',
                    minlength: '<?php echo lang('admin_add_password_max');?>',
                    maxlength: '<?php echo lang('admin_add_password_max');?>',
                    equalTo:   '<?php echo lang('admin_edit_repeat_error');?>'
                }
            }
        });
    });
</script>

</body>
</html>
