<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>big city</title>
    <?php echo _get_html_cssjs('admin_js', 'jquery.js,jquery.validation.min.js,admincp.js,jquery.cookie.js,common.js', 'js'); ?>
    <link href="<?php echo _get_cfg_path('admin') . TPL_ADMIN_NAME; ?>css/skin_0.css" type="text/css" rel="stylesheet"
          id="cssfile"/>
    <?php echo _get_html_cssjs('admin_css', 'perfect-scrollbar.min.css', 'css'); ?>
    <?php echo _get_html_cssjs('lib', 'uploadify/uploadify.css', 'css'); ?>

    <?php echo _get_html_cssjs('admin', TPL_ADMIN_NAME . 'css/font-awesome.min.css', 'css'); ?>

    <!--[if IE 7]>
    <?php echo _get_html_cssjs('admin',TPL_ADMIN_NAME.'css/font-awesome-ie7.min.css','css');?>
    <![endif]-->
    <?php echo _get_html_cssjs('admin_js', 'perfect-scrollbar.min.js', 'js'); ?>
   <style >
       td {
           width: 200px;
       }
        .th25{
            line-height: 25px;
        }
   </style>

</head>
<body>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>用户详情</h3>
        </div>
    </div>
    <div class="fixed-empty"></div>
    <form id="add_form" method="post">
        <table class="table tb-type2 nobdb" style="table-layout:fixed;" >
            <tbody >

            <tr >
                <td  colspan="1">
                    <img style="width: 100px;height: 100px;"/>
                </td>
                <td  colspan="1">
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>
                    </div>
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>

                    </div>
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>

                    </div>
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>

                    </div>
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>

                    </div>
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>

                    </div>


                </td>
                <td colspan="1">
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>
                    </div>
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>

                    </div>
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>

                    </div>
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>

                    </div>
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>

                    </div>
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>
                        <span class="type-file-show" style="margin-right: 70px;" ><img class="show_image" src="<?php echo _get_cfg_path('admin_images');?>preview.png">
                        <div class="type-file-preview"><img id="preview_picurl" src="" onload="javascript:DrawImage(this,500,500);"></div>
                        </span>
                    </div>
                </td>
            </tr>
            <tr>
                <td>新生信息|借款记录</td>
            </tr>
            <tr>
                <td>
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>
                    </div>
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>

                    </div>
                </td>
            </tr>
            <tr >
                <td>
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>
                    </div>

                </td>
                <td>
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>
                    </div>

                </td>
                <td>
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>
                    </div>

                </td>
                <td>
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>
                    </div>

                </td>
                <td>
                    <div class="th25">
                        <span class="w100">姓名</span><label>xxxx</label>
                    </div>

                </td>
            </tr>
            <tfoot>
            <tr class="tfoot">
                <td colspan="5"></td>
            </tr>
            </tfoot>

            </tbody>

        </table>
    </form>
</div>
<script>


</script>
</body>
</html>
