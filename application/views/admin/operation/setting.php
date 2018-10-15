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
        .uintla {
            padding: 5px 30px;
            background-color: #4fbfff;
            margin: 0px 10px;
            color: #fff;
            cursor: pointer;
            border: solid 1px #4fbfff;
        }

        .uintla1 {
            padding: 5px 30px;
            background-color: #ffffff;
            margin: 0px 10px;
            color: #666;
            cursor: pointer;
            border: solid 1px grey;
        }

        ul li {
            float: left;
            margin: 4px 0px;
        }

        .input_span input {
            border: solid 0px !important;
            background-color: ghostwhite !important;;
        }

        .input_span .inputin {
            background-color: #fff !important;
        }

        .input_span .inputout {
            background-color: ghostwhite !important;;
        }

    </style>
</head>
<body>

<div class="item-publish" id="info_view">
    <form method="post" id="newpro_form" action="">
        <div class="ncsc-form-goods">
            <h3>设置</h3>
            <?php foreach($list as $k => $v){?>
            <dl id="cycle_days">
                <dt><?php echo $v['desc'];?></dt>
                <dd class="rowform">
                    <input type="text" name="element_id_<?php echo $v['id'];?>"  id="element_id_element_id_<?php echo $v['id'];?>" element_id="<?php echo $v['id'];?>" maxlength="10" value="<?php echo $v['val'];?>"/>
                </dd>
            </dl>
            <?php }?>

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
        $('input').on('keyup change cut paste',function(){
            var str = $(this).val();
            $(this).val(str.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g, ''))
        })

        $('#newpro_form').validate({
            rules: {


            },
            messages: {


            },
            submitHandler: function () {
                var ids = [];
                var vals = [];
                $('input[element_id]').each(function(){
                    ids.push($(this).attr('element_id'));
                    vals.push(Number($(this).val()));
                })
                var data = {ids:ids,vals:vals};
                sendPostData(data, '/admin/operation/submitsetting', function (res) {
                    if(res.code == 1){
                        showTips('更新设置成功')
                    }

                })
                return;
            }
        });


    })
</script>


</body>
</html>
