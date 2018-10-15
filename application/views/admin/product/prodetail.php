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
            <h3>编辑产品</h3>
            <dl>
                <input type="hidden"  name="id" id="id" value="<?php if(isset($id))echo $id;?>" >
                <dt>产品名称：</dt>
                <dd class="rowform">
                    <input type="text"  name="product_name" id="product_name" maxlength="20" value="<?php if(isset($product_name)) echo $product_name;?>"/>
                </dd>
            </dl>
            <dl>
                <dt>图片上传：</dt>
                <dd class="rowform">
                <div class="upload_block">
                    <div class="f_note">
                        <input type="hidden"  name="picture_url" id="picture_url" value="<?php if(isset($img_flag_url)){ echo $img_flag_url;}?>">
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
                    <div class="type-file-preview"><img id="preview_picture_url" src="<?php if(isset($img_flag_url)){ echo BASE_SITE_URL.'/'.$img_flag_url;}?>" path="<?php if(isset($img_flag_url)){ echo BASE_SITE_URL.'/'.$img_flag_url;}?>" onload="javascript:DrawImage(this,500,500);"></div>
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
                    <label style="padding: 2px 15px;display: inline-block">最少金额:</label><input type="text" id="credit_limit" name="credit_limit" maxlength="10" value="<?php if(isset($credit_limit)) echo $credit_limit ;?>"/>元
                    <label style="padding: 2px 15px;display: inline-block">最多金额:</label><input type="text"  id="credit_upper_limit" name="credit_upper_limit" maxlength="10" value="<?php if(isset($credit_upper_limit))echo $credit_upper_limit ;?>"/>元

                </dd>
            </dl>
            <dl>
                <dt>周期类型：</dt>
                <dd>
                    <select name="repay_type" id="repay_type">
                        <option value="">请选择...</option>
                        <?php foreach ($repay_type as $k => $v) { ?>
                            <option value="<?php echo $k; ?>" <?php if(isset($repaymentType)){if($repaymentType == $k)echo 'selected';}?>><?php echo $v; ?></option>
                        <?php } ?>

                    </select>
                </dd>

            </dl>
            <dl>
                <dt>还款周期：</dt>
                <dd>
                    <label style="padding: 2px 15px;display: inline-block">最少周期:</label><input type="text" id="repayment_cycle_limit" name="repayment_cycle_limit" maxlength="10" value="<?php if(isset($repayment_cycle_limit))echo $repayment_cycle_limit;?>"/>
                    <label style="padding: 2px 15px;display: inline-block">最多周期:</label><input type="text"  id="repayment_cycle_upper_limit" name="repayment_cycle_upper_limit" maxlength="10" value="<?php if(isset($repayment_cycle_upper_limit))echo $repayment_cycle_upper_limit;?>"/>

                </dd>
            </dl>
            <dl id="cycle_days">
                <dt>周期间隔：</dt>
                <dd class="rowform">
                    <input type="text" name="repayment_cycle_days"  id="repayment_cycle_days" maxlength="10" value="<?php if(isset($repaymentType)){echo ($repaymentType ==1?'1':($repaymentType ==2?'1':$repayment_cycle_days));} ?>"/><label id="repay_unit"><?php if(isset($repaymentType)){echo ($repaymentType ==1?'月':($repaymentType ==2?'周':'天'));} ?></label>
                </dd>
            </dl>
            <dl id="cycle_days1">
                <dt>周期间隔：</dt>
                <dd class="rowform">
                    <label id="repay_unit">一个月</label>
                </dd>
            </dl>
            <dl id="cycle_days2">
                <dt>周期间隔：</dt>
                <dd class="rowform">
                    <label id="repay_unit">一周</label>
                </dd>
            </dl>
            <dl>
                <dt>利率：</dt>
                <dd class="rowform">
                    <input type="text" name="rate"  id="rate" maxlength="10"  value="<?php if(isset($rate))echo $rate;?>"/><label>%</label>
                </dd>
            </dl>
            <dl>
                <dt>标语：</dt>
                <dd class="rowform">
                    <textarea name="bannar"  id="bannar" ><?php if(isset($product_banner)) echo $product_banner ;?></textarea>
                </dd>
            </dl>
            <dl>
                <dt>描述：</dt>
                <dd class="rowform">
                    <textarea name="desc"  id="desc" ><?php if(isset($describe))echo $describe;?></textarea>
                </dd>
            </dl>
            <dl>
                <dt>排序：</dt>
                <dd class="rowform">
                    <input type="text" name="sort"  id="sort" maxlength="10" value="<?php if(isset($sort)) echo $sort;?>"/>
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
        $('#cycle_days').hide();
        $('#cycle_days1').hide();
        $('#cycle_days2').hide();
        <?php if(isset($repaymentType)){?>
        <?php if($repaymentType==3){?>
            $('#cycle_days').show();
        <?php }else if($repaymentType==2){ ?>
            $('#cycle_days2').show();
        <?php } else if($repaymentType==1){?>
            $('#cycle_days1').show();
        <?php } ?>
        <?php } ?>
        $('#repayment_cycle_days').on('keyup change',function(){
            var str = $(this).val();
            $(this).val(str.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g, ''))
        })
        $('#repay_type').change(function(){
            $('#cycle_days').hide();
            $('#cycle_days1').hide();
            $('#cycle_days2').hide();
            if($(this).val() == 3){
                $('#cycle_days').show();
                $('#repayment_cycle_days').val('');
                $('#repay_unit').text('天');
            }else{
                if($(this).val() == 1){
                    $('#repay_unit').text('月');
                    $('#cycle_days1').show();
                }else{
                    $('#repay_unit').text('周');
                    $('#cycle_days2').show();
                }
                $('#cycle_days').hide();
                $('#repayment_cycle_days').val('');
            }
        })
        $('#newpro_form').validate({
            rules: {

                product_name: {
                    required: true
                },
                pro_type: {
                    required: true,
                    digits:true
                },
                repay_type: {
                    required: true,
                    digits:true,
                    range:[1,3]
                },
                credit_limit:{
                    required: true,
                    digits:true,
                    range:[0,10000]
                },
                credit_upper_limit:{
                    required: true,
                    range:[0,100000],
                    digits:true
                },
                repayment_cycle_limit:{
                    required: true,
                    range:[1,120],
                    digits:true
                },
                repayment_cycle_upper_limit:{
                    required: true,
                    range:[3,120],
                    digits:true
                },
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

                product_name: {
                    required: '输入产品名称',
                },
                pro_type: {
                    required: '请选择产品类型',
                    digits:'产品类型错误',
                },
                repay_type: {
                    required: '请选择还款周期',
                    digits:'还款周期错误',
                    range:'还款周期错误',
                },
                credit_limit:{
                    required: '请输入最小金额',
                    digits:'必须是一个整数',
                    range:'0-10000',
                },
                credit_upper_limit:{
                    required: '请输入最大金额',
                    digits:'必须是一个整数',
                    range:'0-100000',
                },
                repayment_cycle_limit:{
                    required: '请输入最小周期',
                    digits:'必须是一个整数',
                    range:'请输入1-120',
                },
                repayment_cycle_upper_limit:{
                    required: '请输入最小周期',
                    digits:'必须是一个整数',
                    range:'请输入3-120',
                },
                rate:{
                    required: '请输入利率',
                    range:'请输入0-100',
                },
                bannar:{
                    required: '请输入标语',
                },
                desc:{
                    required: '请输入说明',
                },

                sort:{
                    digits:'必须是一个整数',
                    range:'请输入0-10000'
                },
            },
            submitHandler: function () {
                var days =  Number($.trim($('#repayment_cycle_days').val()));
                if(!days){
                    $('#repayment_cycle_days').val('');
                }

                var data = $("#newpro_form").serialize();
                var min = Number($('#credit_limit').val());
                var max = Number($('#credit_upper_limit').val());
                var cmin = Number($('#repayment_cycle_limit').val());
                var cmax = Number($('#repayment_cycle_upper_limit').val());
                if(min >max){
                    showTips('最小金额不能大于最大金额',3);
                    return;
                }
                if(cmin >cmax){
                    showTips('最小周期不能大于最大周期',3);
                    return;
                }
                if($('#repay_type').val() == 3 && !$.trim($('#repayment_cycle_days').val())){
                    showTips('请输入周期天数',3);
                    return;
                }
                sendPostData(data, '/admin/product/eidtproduct', function (res) {

                    if (res.code == 1) {
                        showTips('修改产品成功',2,function(){
                            location.href = '/admin/product/productlist';
                        });
                    }

                })
                return false;
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
