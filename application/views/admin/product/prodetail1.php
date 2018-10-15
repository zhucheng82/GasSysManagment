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
    <?php echo _get_html_cssjs('admin_js', 'jquery.js,common.js,jquery.tscookie.js,jquery.validation.min.js,admincp.js,jquery.supersized.min.js', 'js'); ?>
    <?php echo _get_html_cssjs('admin_js', 'perfect-scrollbar.min.js', 'js'); ?>
    <?php echo _get_html_cssjs('admin_css', 'seller_center.css', 'css'); ?>

    <style type="text/css">
        ul li {
            float: left;
            margin: 4px 0px;
        }


    </style>
</head>
<body>

<div class="item-publish" id="info_view">
    <form method="post" id="newpro_form" action="">
        <div class="ncsc-form-goods" >
            <h3>编辑产品</h3>
            <dl>
                <dt>产品名称：</dt>
                <dd class="rowform">
                    <input type="text"   name="product_name" id="product_name" maxlength="20" disabled="true" class="readonly"    value="<?php echo $product_name;?>"/>
                    <input type="hidden"  name="product_id" id="product_id" maxlength="20" value="<?php echo $id;?>"/>
                </dd>
            </dl>
            <dl>
                <dt>图片上传：</dt>
                <dd class="rowform">
                <div class="upload_block">
                    <div class="f_note">
                        <input type="hidden"  name="picture_url" id="picture_url" value="<?php if (!empty($img_flag_url)) echo $img_flag_url;?>">
                        <em><i class="icoPro16"></i></em>
                        <div class="file_but">
                            <input id="picture_url_upload" name="picture_url_upload" value="<?php echo lang('adv_upload_img')?>" type="file" >
                        </div>
                    </div>
                </div>
                </dd>
            </dl>
            <dl>
                <dt>图片预览：</dt>
                <dd class="rowform">
                <table >
                <tbody>
                    <tr>
                    <td>
                    <span class="type-file-show"><img class="show_image" src="<?php echo _get_cfg_path('admin_images');?>preview.png">
                    <div class="type-file-preview"><img id="preview_picture_url" src="<?php if(isset($img_flag_url)){ echo BASE_SITE_URL.'/'.$img_flag_url;}?>" onload="javascript:DrawImage(this,500,500);"></div>
                    </span>
                    </td>
                    </tr>
                    </tbody>
                </table>
                </dd>

            </dl>
            <dl>
                <dt>产品类型：</dt>
                <dd>
                    <select name="pro_type" id="pro_type">
                        <option value="">请选择...</option>
                        <?php foreach ($pro_type as $k => $v) { ?>
                            <option value="<?php echo $v['id']; ?>" <?php if (isset($productType)){if($productType ==$v['id']) echo 'selected';}?>><?php echo $v['name']; ?></option>
                        <?php } ?>

                    </select>

                </dd>
            </dl>
            <dl>
                <dt>贷款金额：</dt>
                <dd>
                    <label style="padding: 2px 15px;display: inline-block">最少金额:</label><input type="text" id="credit_limit" name="credit_limit" maxlength="10" disabled="true" class="readonly"    value="<?php echo $credit_limit ;?>"/>元
                    <label style="padding: 2px 15px;display: inline-block">最多金额:</label><input type="text"  id="credit_upper_limit" name="credit_upper_limit" maxlength="10" disabled="true" class="readonly"    value="<?php echo $credit_upper_limit;?>"/>元

                </dd>
            </dl>
            <dl>
                <dt>周期类型：</dt>
                <dd>
                    <input type="text" maxlength="10" disabled="true" class="readonly"    value="<?php echo $repay_type[$repaymentType];?>"/>
                </dd>
            </dl>
            <dl>
                <dt>还款周期：</dt>
                <dd>
                    <label style="padding: 2px 15px;display: inline-block">最少周期:</label><input type="text" maxlength="10" disabled="true" class="readonly"    value="<?php echo $repayment_cycle_limit;?>"/>
                    <label style="padding: 2px 15px;display: inline-block">最多周期:</label><input type="text" maxlength="10" disabled="true" class="readonly"    value="<?php echo $repayment_cycle_upper_limit;?>"/>

                </dd>
            </dl>
            <dl>
                <dt>周期天数：</dt>
                <dd>
                    <input type="text" maxlength="10" disabled="true" class="readonly"    value="<?php echo ($repaymentType ==1?'一月':($repaymentType ==2?'一周':$repayment_cycle_days.'天'));?>"/>
                </dd>
            </dl>
            <dl>
                <dt>利率：</dt>
                <dd class="rowform">
                    <input type="text" name="rate"  id="rate" maxlength="10"  value="<?php echo $rate;?>"/><label>%</label>
                </dd>
            </dl>
            <dl>
                <dt>标语：</dt>
                <dd class="rowform">
                    <textarea name="bannar"  id="bannar" ><?php echo $product_banner ;?></textarea>
                </dd>
            </dl>
            <dl>
                <dt>描述：</dt>
                <dd class="rowform">
                    <textarea name="desc"  id="desc" ><?php echo $describe;?></textarea>
                </dd>
            </dl>
            <dl>
                <dt>排序：</dt>
                <dd class="rowform">
                    <input type="text" name="sort"  id="sort" value="<?php echo $sort;?>"  maxlength="10"/>
                </dd>
            </dl>

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
        $('#newpro_form').validate({
            rules: {

//                product_name: {
//                    required: true,
//                },
//                pro_type: {
//                    required: true,
//                    digits:true,
//                    range:[1,3],
//                },
//                credit_limit:{
//                    required: true,
//                    digits:true,
//                    range:[1000,10000000],
//                },
//                credit_upper_limit:{
//                    required: true,
//                    range:[5000,10000000],
//                    digits:true
//                },
//                repayment_cycle_limit:{
//                    required: true,
//                    range:[1,120],
//                    digits:true
//                },
//                repayment_cycle_upper_limit:{
//                    required: true,
//                    range:[3,120],
//                    digits:true
//                },
                rate:{
                    required: true,
                    range:[0,100]
                },
                sort:{
                    range:[0,10000]
                },
                desc:{
                    required: true
                },
                bannar:{
                    required: true
                },


            },
            messages: {

//                product_name: {
//                    required: '输入产品名称',
//                },
//                pro_type: {
//                    required: '请选择产品类型',
//                    digits:'产品类型错误',
//                    range:'产品类型错误',
//                },
//                credit_limit:{
//                    required: '请输入最小金额',
//                    digits:'必须是一个整数',
//                    range:'请输入1000-10000000',
//                },
//                credit_upper_limit:{
//                    required: '请输入最大金额',
//                    digits:'必须是一个整数',
//                    range:'请输入5000-10000000',
//                },
//                repayment_cycle_limit:{
//                    required: '请输入最小周期',
//                    digits:'必须是一个整数',
//                    range:'请输入1-120',
//                },
//                repayment_cycle_upper_limit:{
//                    required: '请输入最小周期',
//                    digits:'必须是一个整数',
//                    range:'请输入3-120',
//                },
                rate:{
                    required: '请输入利率',
                    range:'请输入0-100'
                },
                bannar:{
                    required: '请输入标语'
                },
                desc:{
                    required: '请输入说明'
                },
                sort:{
                    digits:'必须是一个整数',
                    range:'请输入0-10000'
                },
            },
            submitHandler: function () {
//                var data = $("#newpro_form").serialize();
//                var min = Number($('#credit_limit').val());
//                var max = Number($('#credit_upper_limit').val());
//                var cmin = Number($('#repayment_cycle_limit').val());
//                var cmax = Number($('#repayment_cycle_upper_limit').val());
//                if(min >max){
//                    showTips('最小金额不能大于最大金额',3);
//                    return;
//                }
//                if(cmin >cmax){
//                    showTips('最小周期不能大于最大周期',3);
//                    return;
//                }
                var data = {};
                data.id = $('#product_id').val();
                data.desc= $('#desc').val();
                data.bannar = $('#bannar').val();
                data.sort = $('#sort').val();
                data.rate = $('#rate').val();
                data.picture_url = $('#picture_url').val();
                sendPostData(data, '/admin/product/eidtproduct', function (res) {

                    if (res.code == 1) {
                        showTips('修改产品成功',2,function(){
                            location.href = '/admin/product/productlist';
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
