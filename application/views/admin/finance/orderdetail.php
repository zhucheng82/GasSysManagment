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
    <style>
        .th25 {
            line-height: 25px;
            width: 300px;
        }

        .w100 {
            width: 120px;
            height: 25px;
            display: inline-block;
        }

        .address {
            display: inline-block;
            width: 180px;
            vertical-align: top;
        }
    </style>

</head>
<body>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <h3>借款详情</h3>
        </div>
    </div>
    <div class="fixed-empty"></div>
    <form id="add_form" method="post">
        <table class="table tb-type2 nobdb" style="table-layout:fixed;">
            <tbody>
            <tr class="rzxx">
                <td colspan="5"><h5>认证信息</h5></td>
            </tr>
            <tr>

                <td>
                    <img style="width: 150px;height: 150px;" src="<?php echo $portrait; ?>"/>
                </td>
                <td>
                    <div class="th25">
                        <span class="w100">姓名</span><label><?php echo $user_name; ?></label>
                    </div>
                    <div class="th25">
                        <span class="w100">电话</span><label><?php echo $name; ?></label>
                    </div>
                    <div class="th25">
                        <span class="w100">身份证号</span><label><?php echo $user_identify_card; ?></label>

                    </div>
                    <div class="th25">
                        <span
                            class="w100">职业</span><label><?php echo $work == 0 ? '学生' : ($work == 1 ? '上班族' : '未知'); ?></label>
                    </div>
                    <div class="th25">
                        <span
                            class="w100">单位/院校</span><label><?php echo isset($school_name) ? $school_name : $company_name; ?></label>

                    </div>
                    <div class="th25">
                        <span
                            class="w100">职务/学历</span><label><?php echo isset($education) ? $education : $post; ?></label>

                    </div>
                    <div class="th25">
                        <span class="w100">入职/入学</span><label><?php echo $entry_time; ?></label>
                    </div>
                    <div class="th25">
                        <span
                            class="w100">状态</span><label><?php echo ($authentication_status == 1)?'已认证':($authentication_status == 2?'审核中':'未认证'); ?></label>
                    </div>
                </td>


            </tr>
            <tr>
                <td colspan="5"><h5>借款信息</h5></td>
            </tr>
            <tr>
                <td>
                    <div class="th25">
                        <span class="w100">产品类型</span><label><?php echo $order['productType']; ?></label>
                    </div>
                    <div class="th25">
                        <span class="w100">产品名称</span><label><?php echo $order['product_name']; ?></label>
                    </div>
                    <div class="th25">
                        <span class="w100">申请金额</span><label>￥<?php echo $order['apply_amount']; ?></label>

                    </div>
                    <div class="th25">
                        <span class="w100">分期</span><label><?php echo $order['credit_cycle']; ?></label>

                    </div>
                    <div class="th25">
                        <span class="w100">利率</span><label><?php echo $order['rate']; ?></label>

                    </div>
                    <div class="th25">
                        <span class="w100">申请时间</span><label><?php echo date("Y-m-d H:i:s",$order['applied_time']); ?></label>

                    </div>
                    <div class="th25">
                        <span class="w100">状态</span><label><?php echo $order['status_name']; ?></label>

                    </div>

                    <?php if($order['status'] >= 1){?>
                        <div class="th25">
                            <span class="w100">审批时间</span><label><?php echo date("Y-m-d H:i:s",$order['approved_time']); ?></label>

                        </div>
                        <div class="th25">
                            <span class="w100">审批金额</span><label>￥<?php echo $order['approve_amount']; ?></label>

                        </div>
                    <?php }?>

                </td>
            </tr>
            <?php if(isset($order['ebike_info'])){?>
            <tr>
                <td colspan="5"><h5>车辆信息</h5></td>
            </tr>
            <tr>
                <td>
                    <div class="th25">
                        <span class="w100">车辆品牌</span><label><?php echo $order['ebike_info']['brand']; ?></label>
                    </div>
                    <div class="th25">
                        <span class="w100">车辆价格</span><label>￥<?php echo $order['ebike_info']['price']; ?></label>
                    </div>
                    <div class="th25">
                        <span class="w100">商家名称</span><label><?php echo $order['ebike_info']['merchant_name']; ?></label>

                    </div>
                    <div class="th25">
                        <span class="w100">商家地址</span><label><?php echo $order['ebike_info']['merchant_addr']; ?></label>

                    </div>
                    <div class="th25">
                        <span class="w100">商家电话</span><label><?php echo $order['ebike_info']['merchant_tel']; ?></label>

                    </div>
                    <div class="th25">
                        <span class="w100">电动车照片</span><label><img style="width:200px;" src="<?php echo _get_image_url($order['ebike_info']['photo_url']); ?>"></label>

                    </div>

                </td>
            </tr>
            <?php }?>
            <?php if(isset($order['renting_house_info'])){?>
            <tr>
                <td colspan="5"><h5>住房信息</h5></td>
            </tr>
            <tr>
                <td>
                    <div class="th25">
                        <span class="w100">小区名称</span><label><?php echo $order['renting_house_info']['community_name']; ?></label>
                    </div>
                    <div class="th25">
                        <span class="w100">租房详细地址</span><label><?php echo $order['renting_house_info']['address']; ?></label>
                    </div>
                    <div class="th25">
                        <span class="w100">房租</span><label>￥<?php echo $order['renting_house_info']['price']; ?></label>

                    </div>
                    <div class="th25">
                        <span class="w100">支付方式</span><label><?php echo $order['renting_house_info']['pay_way']; ?></label>

                    </div>
                    <div class="th25">
                        <span class="w100">房东姓名</span><label><?php echo $order['renting_house_info']['landlord_name']; ?></label>

                    </div>
                    <div class="th25">
                        <span class="w100">房东电话</span><label><?php echo $order['renting_house_info']['landlord_tel']; ?></label>

                    </div>
                    <?php if($order['renting_house_info']['has_intermediary'] == 1) {?>
                    <div class="th25">
                        <span class="w100">中介姓名</span><label><?php echo $order['renting_house_info']['intermediary_name']; ?></label>
                    </div>
                    <div class="th25">
                        <span class="w100">中介电话</span><label><?php echo $order['renting_house_info']['intermediary_tel']; ?></label>
                    </div>
                    <?php }?>

                    

                </td>
            </tr>
            <?php }?>
            <?php if($order['status'] == 0){?>
                <tr>
                    <td  colspan="5">
                        <a href="JavaScript:void(0);" class="btn" id="agreeBtn" oid="<?php echo $order['id'];?>"><span>同意</span></a>
                        <a href="JavaScript:void(0);" class="btn" id="refuseBtn" oid="<?php echo $order['id'];?>"><span>拒绝</span></a>
                    </td>
                </tr>
            <?php }?>

            <tfoot>
            <tr class="tfoot">
                <td colspan="5"></td>
            </tr>
            </tfoot>

            </tbody>

        </table>
    </form>
    <div id="sure_div" style="display:none;position:fixed;left:0;top:0;width:100%;height: 100%;z-index:99;background:#000;background:rgba(0, 0, 0, 0.6)!important;filter:Alpha(opacity=60);background:#000; text-align:center;">
        <div style="width: 25%;left: 35%;top: 20%;position: absolute;background: #fff" >
            <table class="table tb-type2 tb-type1" style="width: 100%;height: 100%">
                <tr class="space">
                    <th colspan="3" id="tips">添加到活动</th>
                </tr>
                <tr class="space">
                    <td colspan="3">
                        <input type="text" maxlength="10" id="agree_money"/>
                        <textarea type="text" maxlength="200" id="refuse_reason" ></textarea>
                    </td>
                </tr>
                <tr class="tfoot">
                    <td colspan="3"><a class="btn" id="submitBtn" ><span>确定</span></a><a class="btn" id="cancelBtn"><span>取消</span></a></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div >

</div>
<script>
    $(function(){
        var currentID ;
        var type = 1;
        var apply_amount = <?php echo $order['apply_amount']; ?>;
        $('#agreeBtn').click(function(){
            currentID = $(this).attr('oid');
            $('#sure_div').show();
            $('#tips').text('请输入放款金额');
            $('#agree_money').show();
            $('#refuse_reason').hide();
            $('refuse_reason').val('');
            type = 1;

        })
        $('#refuseBtn').click(function(){
            currentID = $(this).attr('oid');
            $('#sure_div').show();
            $('#tips').text('请输入拒绝理由');
            $('#agree_money').hide();
            $('#refuse_reason').show();
            $('#agree_money').val('');
            type = 2;
        })
        $('#cancelBtn').click(function(){
            $('#sure_div').hide();
            $('refuse_reason').val('');
            $('#agree_money').val('');
        })
        $('#agree_money').on('keyup change',function(){
            var str = $(this).val();
            $(this).val(str.replace(/[^\d{1,}\.\d{1,}|\d{1,}]/g, ''))
        })
        $('#submitBtn').click(function(){
            var money = Number($('#agree_money').val());
            var remark = $('#refuse_reason').val();

            if(type == 1 && money<=0 ){
                showTips('请输入金额');
                return;
            }

            if(money >apply_amount){
                showTips('审批金额不能大于申请金额');
                return;
            }

            if(type == 2 && !remark){
                showTips('请输入拒绝理由');
                return;
            }
            $('#sure_div').hide();
            $('#agreeBtn').hide();
            $('#refuseBtn').hide();
            sendPostData({type:type,id:currentID,money:money,remark:remark},'<?php echo ADMIN_SITE_URL.'/finance/orderdeal'?>',function(res){
                if(res.code == 1){
                    if(type == 1){
                        showTips('放款成功',3,function(){
                            location.reload();
                        });

                    }else if(type == 2){
                        showTips('已拒绝贷款',3,function(){
                            location.reload();
                        });
                    }

                }else{
                    $('#agreeBtn').show();
                    $('#refuseBtn').show();
                }
            })
            $('refuse_reason').val('');
            $('#agree_money').val('');
        })
    })

</script>
</body>
</html>
