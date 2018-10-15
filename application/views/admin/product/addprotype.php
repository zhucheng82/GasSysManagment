<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php echo lang('login_index_need_login'); ?></title>
    <link href="<?php echo _get_cfg_path('admin') . TPL_ADMIN_NAME; ?>css/skin_0.css" type="text/css" rel="stylesheet"
          id="cssfile"/>
    <?php echo _get_html_cssjs('admin_css', 'seller_center.css', 'css'); ?>
    <?php echo _get_html_cssjs('lib','uploadify/uploadify.css','css');?>
    <?php echo _get_html_cssjs('admin_js', 'jquery.js,common.js,jquery.tscookie.js,jquery.validation.min.js,jquery.supersized.min.js', 'js'); ?>
    <?php echo _get_html_cssjs('admin_js', 'perfect-scrollbar.min.js', 'js'); ?>
    <style type="text/css">


    </style>
</head>
<body>

<div class="item-publish" id="info_view">
    <form method="post" id="newpro_form" action="">
        <div class="ncsc-form-goods">
            <h3><?php if(empty($protype)){echo '新增产品类型';}else{echo '编辑';}?></h3>
            <input type="hidden"  name="pro_id" id="pro_id" value="<?php if($protype && $protype['id']){echo $protype['id']; }?>">
            <dl>
                <dt>产品类型名称：</dt>
                <dd class="rowform">
                    <input type="text"  name="product_name" id="product_name" maxlength="20" value="<?php if($protype && $protype['name']){echo $protype['name']; }?>"/>
                </dd>
            </dl>
            <dl>
                <dt>描述：</dt>
                <dd class="rowform">
                    <textarea name="desc"  id="desc" ><?php if($protype && $protype['desc']){echo $protype['desc']; }?></textarea>
                </dd>
            </dl>
<!--            <dl>-->
<!--                <dt>排序：</dt>-->
<!--                <dd class="rowform">-->
<!--                    <input type="text" name="sort"  id="sort" maxlength="10"/>-->
<!--                </dd>-->
<!--            </dl>-->

        </div>
        <div class="bottom tc hr32">
            <label class="submit-border">
                <input type="submit" class="submit"
                       value="提交"/>
            </label>
        </div>
    </form>
</div>
<script>
    $(function () {
        $('#repayment_cycle_days').on('keyup change',function(){
            var str = $(this).val();
            $(this).val(str.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g, ''))
        })
        $('#repay_type').change(function(){
            if($(this).val() == 3){
                $('#cycle_days').show();
                $('#repayment_cycle_days').val('');
            }else{
                $('#cycle_days').hide();
                $('#repayment_cycle_days').val('');
            }
        })
        $('#newpro_form').validate({
            rules: {

                product_name: {
                    required: true,
                },
                desc:{
                    required: true,
                    maxlength: 300
                },



            },
            messages: {

                product_name: {
                    required: '输入产品名称',
                },
                desc:{
                    required: '请输入说明',
                    maxlength: '最多不超过300个字'
                },
            },
            submitHandler: function () {

                sendPostData($('#newpro_form').serialize(), '/admin/product/addProductTypeSubmit', function (res) {

                    if (res.code == 1) {
                        var str = '';
                        if($('#pro_id').val()){
                            str = '修改产品类型成功';
                        }else{
                            str = '添加产品类型成功';
                        }
                        showTips(str,2,function(){
                            location.href = '/admin/product/producttype';
                        });
                    }

                })
                return;
            }
        });


    })
</script>

<script src="<?php echo _get_cfg_path('lib')?>uploadify/jquery.uploadify.min.js" type="text/javascript"></script>
<script type="text/javascript">
<?php $timestamp = time();?>
$(function() {
  upload_file('picture_url','picture_url','<?php echo $timestamp?>','<?php echo md5($this->config->item('encryption_key') . $timestamp );?>');
});
</script>

</body>
</html>
